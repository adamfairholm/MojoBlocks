<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Addon Controller
 *
 * @package		MojoBlocks
 * @subpackage	Addons
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class mb
{
	var $addon;
	
	var $addon_version 						= '0.1 Beta';
	
	var $dependencies_loaded 				= FALSE;
	
	var $page_data							= array();
	
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		$this->addon =& get_instance();
		
		// -------------------------------------
		// Dip out for CSS/JS
		// -------------------------------------

		$method = $this->addon->uri->segment( 3 ); 

		if( $method == 'css' || $method == 'js' ):
			
			$call_method = '_'.$method;
		
			$this->$call_method();
			
			die();
		
		endif;

		// -------------------------------------
		
		$this->addon->load->library('Blocks');
		
		$this->addon->load->model('blocks_mdl');
		
		//Make sure that we load all the assets. But just once, and only if someone is signed in.
		
		if( $this->addon->session->userdata('group_id') ):
		
			if( $this->dependencies_loaded == FALSE ):
			
				$assets = $this->_mb_dependencies();
				
				foreach( $assets as $asset ):
			
					$this->addon->cp->appended_output[] = $asset;
				
				endforeach;
				
				$this->dependencies_loaded = TRUE;
			
			endif;
		
		endif;
		
		//We need a database table. Since everything is supposed to be super simple, why not just check for it
		//and install it if we don't see it there. Everyone is happy and sugar cubes for dinner.
		
		$this->addon->blocks_mdl->check_database();	
		
		// If we are loading a MojoMotor page, let's get the data
		
		if( class_exists('Page_model') ):
		
			$this->page_info = $this->addon->page_model->get_page_by_url_title($this->addon->mojomotor_parser->url_title);
			
			$this->page_data = $this->addon->blocks_mdl->retrieve_page_blocks( $this->page_info->url_title, $this->page_info->layout_id );
		
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Displays a specific block
	 */
	function block( $tag_data = array() )
	{
		// Load up the block
		
		$block = $this->addon->blocks->load_block($tag_data['parameters']['type']);
		
		// Get the data if there is any
		
		if( isset($this->page_data[$tag_data['parameters']['id']]) ):
		
			$block_data = $this->page_data[$tag_data['parameters']['id']]['block_content'];
			
		else:
		
			$block_data = array();
		
		endif;
		
		// Return the render function
		
		return $block->render( $block_data );
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays the editor for the block. Accessed via AJAX.
	 *
	 * @access	public
	 * @return 	echo
	 */
	function editor()
	{
		$block = $this->addon->input->post('block_type');
		
		// Load up the block
		
		$block = $this->addon->blocks->load_block($block);
		
		// -------------------------------------
		// Validation
		// -------------------------------------
		
		$this->addon->load->library('form_validation');
		
		$this->addon->load->helper('form');
		
		// Go through, set validation, and make the form fields.

		foreach( $block->block_fields as $slug => $data ):
		
			$this->addon->form_validation->set_rules($slug, $data['label'], $data['validation']);
					
		endforeach;
		
		$this->addon->form_validation->set_error_delimiters('<p class="error">', '</p>');

		// -------------------------------------
		
		//I think we may be able to delete this
		$region_data = $_POST;

		// Was this submitted before and kicked back for a validation error?
		// If so we need to go ahead and get some data.

		if( isset($_POST['form_submit']) && $_POST['form_submit'] == 'true' ):
		
			$form_data = $this->addon->blocks->clean_form_input_data( $_POST );
			
			$validated = TRUE;
			
		else:
		
			// Otherwise, is this saved in the DB?
			
			$single_block = $this->addon->blocks_mdl->get_single_block( $region_data['page_url_title'], $region_data['layout_id'], $region_data['region_id'] );
			
			if( $single_block ):
			
				$form_data = $this->addon->blocks->clean_db_input_data( $single_block );
			
			endif;
			
			// Let's see if there is a form pre-process edit thing
			
			if( method_exists($block, 'pre_editor') ):
			
				$processed = $block->pre_editor( $form_data['form_fields'] );
				
				if( is_array($processed) ):
				
					$form_data['form_fields'] = $processed;
				
				endif;
			
			endif;
		
		endif;
		
		$validated = FALSE;
				
		// Return the render function or process the form
		
		if ( $this->addon->form_validation->run() == FALSE ):
			
			// Did this get kicked back? Ok, we need some info to pass	
			
			if( isset($form_data) ): 
			
				$validation_data['field_values'] 	= $form_data['form_fields'];
				$validation_data['validated'] 		= $validated;
				
				$this->addon->blocks->render_editor( $block, $region_data, $validation_data );
			
			else:
			
				$this->addon->blocks->render_editor( $block, $region_data );
			
			endif;
		
		else:
		
			// So it's good. We just need to process the form and add the data back in
			
			$this->_form_process( $block, $form_data );
			
			echo 'BLOCKS_FORM_INPUT_SUCCESS';
		
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Process the form data
	 *
	 * @access	public
	 * @param	obj
	 * @param	array
	 * @return	bool
	 */
	function _form_process( $block, $form_data )
	{
		// We will use a block process function if there is one.
			
		if( method_exists($block, 'process_form') ):
		
			$processed = $block->process_form( $form_data['form_fields'] );
			
			if( is_array($processed) ):
			
				$form_data['form_fields'] = $processed;
			
			endif;
		
		endif;
		
		// Save the data
		
		return $this->addon->blocks_mdl->save_block_data( $form_data );
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns JS and CSS needs for MojoBlocks.
	 *
	 * @access	private
	 * @return	array
	 */
	function _mb_dependencies()
	{
		$dependencies[] = '<link type="text/css" rel="stylesheet" href="'.site_url('addons/mb/css/mojoblock_style.css').'" /> ';
		$dependencies[] = '<script charset="utf-8" type="text/javascript" src="'.site_url('addons/mb/js/blocks.js').'"></script>';
		
		return $dependencies;
	}

	// --------------------------------------------------------------------

	/**
	 * Output a JS file. Just like that.
	 */
	function _js()
	{
		$file = $this->addon->uri->segment(4);
		
		header("Content-Type: text/javascript");
	
		echo file_get_contents( APPPATH . 'third_party/mb/javascript/'.$file);
	}

	// --------------------------------------------------------------------

	/**
	 * Output a CSS file. Also just like that.
	 */
	function _css()
	{
		$file = $this->addon->uri->segment(4);
		
		header("Content-Type: text/css");
	
		echo file_get_contents( APPPATH . 'third_party/mb/views/themes/css/'.$file);
	}

}

/* End of file mb.php */
/* Location: /third_party/block/libraries/mb.php */
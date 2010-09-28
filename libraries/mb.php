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

		if( $method == 'css' || $method == 'js' || $method == 'images' ):
			
			$call_method = '_'.$method;
		
			$this->$call_method();
			
			die();
		
		endif;

		// -------------------------------------
		// Load our code assets
		// -------------------------------------
		
		$this->addon->load->library('Blocks');
		
		$this->addon->load->helper('block');

		$this->addon->load->helper('cache');
		
		$this->addon->lang->load('mb', '', FALSE, TRUE, APPPATH.'third_party/mb/');
		
		$this->addon->load->model('blocks_mdl');
		
		// -------------------------------------
		// Load JS and CSS assets
		// -------------------------------------
		// But just once, and only if someone is signed in.
		// -------------------------------------
		
		if( $this->addon->session->userdata('group_id') ):
		
			if( $this->dependencies_loaded == FALSE ):
			
				$assets = $this->_mb_dependencies();
				
				foreach( $assets as $asset ):
			
					$this->addon->cp->appended_output[] = $asset;
				
				endforeach;
				
				$this->dependencies_loaded = TRUE;
			
			endif;
		
		endif;

		// -------------------------------------
		// Check for DB
		// -------------------------------------
		// We need a database table. Since everything is supposed to be super simple, why not just check for it
		// and install it if we don't see it there. Everyone is happy and sugar cubes for dinner.
		// -------------------------------------
		
		$this->addon->blocks_mdl->check_database();	
		
		// -------------------------------------
		// Load Page Assets
		// -------------------------------------
		// If we are loading a MojoMotor page, let's get the data
		// -------------------------------------
		
		if( class_exists('Page_model') ):
		
			$this->page_info = $this->addon->page_model->get_page_by_url_title($this->addon->mojomotor_parser->url_title);
			
			$this->page_data = $this->addon->blocks_mdl->retrieve_page_blocks( $this->page_info->url_title, $this->page_info->layout_id );
		
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders a specific block based on the tag data
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function block( $tag_data = array() )
	{
		// -------------------------------------		
		// Load the block
		// -------------------------------------		
		
		$block = $this->addon->blocks->load_block($tag_data['parameters']['type']);

		// -------------------------------------		
		// Update the tag settings if needed
		// -------------------------------------
		
		$tag_params = $tag_data['parameters'];

		unset($tag_params['type'], $tag_params['id']); // Get these babies out
		
		// Run a diff and see if they are the same. If there is a change, save the data
		
		if( count($tag_params) != 0 ):
		
			$diff = array_diff($tag_params, $this->page_data[$tag_data['parameters']['id']]['tag_settings']);
			
			if( count($diff) != 0 ):
			
				$this->addon->blocks_mdl->save_tag_settings( $tag_params, $this->page_data[$tag_data['parameters']['id']]['row_id'] );
			
			endif;
		
		endif;

		// -------------------------------------				
		// Get the data if there is any
		// Else, return a description of the 
		// -------------------------------------	
		
		$cache = FALSE;	
		
		if( isset($this->page_data[$tag_data['parameters']['id']]) ):
		
			$block_data = $this->page_data[$tag_data['parameters']['id']]['block_content'];
			
			// -------------------------------------		
			// Cache
			// -------------------------------------
			
			// Is cache enabled for this block?
			
			if( isset($block->cache_output) && $block->cache_output == TRUE  ):
			
				// See if the cache is valid, if so, return the cache
			
				$cache = validate_cache( $this->page_data[$tag_data['parameters']['id']]['cache'], $this->page_data[$tag_data['parameters']['id']]['cache_process'], $this->page_data[$tag_data['parameters']['id']]['cache_expire'] );
				
				if( $cache ):
				
					// See if this is page cache or a function cache
					
					if( method_exists($block, 'cache_data_call') ):
					
						// This has a cache data call, so before we call the render function below
						// we need to set the cache data in the class to the cache data
						
						$block->cache_data = $cache;
						
						// That is all we need to do, the render function will do the rest.
					
					else:
					
						// This is a page cache output, and we can just end it here by
						// sending back the full cached rendered data and bypass render()

						return $cache;
										
					endif;
				
				endif;
			
			endif;
			
		else:
		
			return '<p>'.$block->block_name.': '.$block->block_desc.'</p>';
		
		endif;
		
		// -------------------------------------
		// Block Render
		// -------------------------------------				
		// Return the block render output to the page
		// -------------------------------------
		
		if( method_exists($block, 'render') ):

			$rendered_output = $block->render( $block_data );
			
			// If the cache is invalid, let's write a new one
			
			if( $cache === FALSE && ( isset($block->cache_output) && $block->cache_output == TRUE ) ):
			
				write_cache( $block, $rendered_output, $this->page_data[$tag_data['parameters']['id']]['row_id'], $block_data );
			
			endif;
			
			return $rendered_output;
		
		else:
		
			return $this->show_error( 'no_render_function' );
		
		endif;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Displays block via AJAX call
	 *
	 * Really just meant to pull the data in. It bypasses a lot of the 
	 * above block functions like cache. It also clears the cache.
	 *
	 * @access	public
	 * @return	void
	 */
	function ajax_block()
	{
		// -------------------------------------
		// Retrieve and validate POST data
		// -------------------------------------
		
		$block_id = $this->addon->input->post('block_id');
		
		if( ! $block_id ):
		
			echo $this->show_error( 'invalid_post_data' );
		
		endif;

		// -------------------------------------
		// Validate ID
		// -------------------------------------

		if( ! $block_id || ! is_numeric($block_id) ):
		
			echo $this->show_error( 'invalid_block_id' );
		
		endif;
		
		// -------------------------------------
		// Get block data
		// -------------------------------------
		
		$block_data = $this->addon->blocks_mdl->get_single_block_by_id( $block_id );

		// -------------------------------------
		// Load and render block, or return error
		// -------------------------------------
		
		if( $block_data ):
		
			$block = $this->addon->blocks->load_block( $block_data['block_type'] );

			echo $block->render( $block_data['block_content'] );
	
		else:
		
			echo $this->show_error( 'invalid_block_id' );
	
		endif;
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
		// -------------------------------------
		// Retrieve and validate POST data
		// -------------------------------------
		
		$block = $this->addon->input->post('block_type');
		
		if( ! $block ):
		
			echo $this->show_error( 'invalid_post_data' );
		
		endif;
		
		// -------------------------------------
		// Load Block
		// -------------------------------------
		
		$block = $this->addon->blocks->load_block($block);
		
		if( ! $block ):
		
			echo $this->show_error( 'failed_to_load_block' );
		
		endif;
		
		// -------------------------------------
		// Validation
		// -------------------------------------
		
		$this->addon->load->library('form_validation');
		
		$this->addon->load->helper('form');
		
		// -------------------------------------
		// Set validation and create form fields
		// -------------------------------------

		foreach( $block->block_fields as $slug => $data ):
		
			$this->addon->form_validation->set_rules($slug, $data['label'], $data['validation']);
					
		endforeach;
		
		$this->addon->form_validation->set_error_delimiters('<p class="error">', '</p>');

		// -------------------------------------
		// Was this submitted before and kicked back for a validation error?
		// If so we need to go ahead and get some data.
		// -------------------------------------

		$validated = FALSE;

		if( isset($_POST['form_submit']) && $_POST['form_submit'] == 'true' ):
		
			$form_data = $this->addon->blocks->clean_form_input_data( $_POST );
			
			$validated = TRUE;
			
		else:

			// -------------------------------------		
			// Otherwise, is this saved in the DB?
			// -------------------------------------
			
			$single_block = $this->addon->blocks_mdl->get_single_block(
					$this->addon->input->post('page_url_title'),
					$this->addon->input->post('layout_id'),
					$this->addon->input->post('region_id')
				);
			
			if( $single_block ):
			
				$form_data = $this->addon->blocks->clean_db_input_data( $single_block );

				// -------------------------------------			
				// Form pre-editng processing
				// -------------------------------------			
				// If this is saved in the DB, then we 
				// can run it through the pre-processor
				// in the block if defined. 			
				// -------------------------------------
				
				if( method_exists($block, 'pre_editor') ):
				
					$processed = $block->pre_editor( $form_data['form_fields'] );
					
					if( is_array($processed) ):
					
						$form_data['form_fields'] = $processed;
					
					endif;
							
				endif;
				
				// -------------------------------------
		
			endif;

			// -------------------------------------

		endif;
		
		// -------------------------------------
		// Tag Parameters Processing
		// -------------------------------------
		// Add tag parameters 
		// -------------------------------------
		
		// Did we get the block data already on the validation run?
		// If not, get it.
		
		if( !isset($single_block) ):
		
			$single_block = $this->addon->blocks_mdl->get_single_block(
					$this->addon->input->post('page_url_title'),
					$this->addon->input->post('layout_id'),
					$this->addon->input->post('region_id')
				);
		
		endif;
			
		if( $single_block ):

			$hidden = $single_block['tag_settings'];

		else:
		
			$hidden = array();
		
		endif;
				
		// -------------------------------------
		// Form Validation and Processing
		// -------------------------------------
		// Return the render function or process the form
		// -------------------------------------
		
		if ( $this->addon->form_validation->run() == FALSE ):
		
			// Did this get kicked back? Ok, we need some info to pass	
			
			if( isset($form_data) ): 
			
				$validation_data['field_values'] 	= $form_data['form_fields'];
				$validation_data['validated'] 		= $validated;
				
				// Pass the row ID if we need to
				
				if( isset($form_data['page_data']['row_id']) ):
				
					$_POST['row_id'] = $form_data['page_data']['row_id'];
				
				endif;
				
				$this->addon->blocks->render_editor( $block, $_POST, $validation_data, $hidden );
			
			else:
			
				$this->addon->blocks->render_editor( $block, $_POST, array(), $hidden );
			
			endif;
		
		else:
		
			// So it's good. We just need to process the form and add the data back in
			
			$result = $this->_form_process( $block, $form_data );
			
			if( $result && is_numeric($result) ):
			
				echo $result;
			
			else:
			
				echo 'BLOCKS_FORM_INPUT_FAILURE';
			
			endif;
		
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
		$dependencies[] = '<script charset="utf-8" type="text/javascript" src="'.site_url('addons/mb/js/blocks.functions.js').'"></script>';
		$dependencies[] = '<script charset="utf-8" type="text/javascript" src="'.site_url('addons/mb/js/blocks.js').'"></script>';
		
		return $dependencies;
	}

	// --------------------------------------------------------------------

	/**
	 * Output a JS file. Just like that.
	 *
	 * @access	private
	 * @return	js
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
	 *
	 * @access	private
	 * @return	css
	 */
	function _css()
	{
		$file = $this->addon->uri->segment(4);
		
		header("Content-Type: text/css");
	
		echo file_get_contents( APPPATH . 'third_party/mb/views/themes/css/'.$file);
	}

	// --------------------------------------------------------------------

	/**
	 * Output a IMG file.
	 *
	 * @access	private
	 * @return	img
	 */
	function _images()
	{
		$file = $this->addon->uri->segment(4);
		
		$this->addon->load->helper('file');

		$mime = get_mime_by_extension($file);
		
		//Is this even an image?
		
		if( strpos($mime, 'image') === FALSE )
			return FALSE;
		
		header('Content-type: '.$mime);
		
		$img_file = APPPATH.'third_party/mb/views/themes/images/'.$file;

		if ( file_exists($img_file) )
		{
			exit(file_get_contents($img_file));
		}
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Display an error
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @return	string
	 */
	function show_error( $error, $tag = 'p' )
	{
		return "<$tag>".$this->addon->lang->line('mb_error').': '.$this->addon->lang->line('mb_error_'.$error)."</$tag>";
	}
}

/* End of file mb.php */
/* Location: /third_party/block/libraries/mb.php */
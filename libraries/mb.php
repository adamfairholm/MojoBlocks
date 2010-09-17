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
		
		//Make sure that we load all the assets. But just once.
		
		if( $this->dependencies_loaded == FALSE ):
		
			$assets = $this->_mb_dependencies();
			
			foreach( $assets as $asset ):
		
				$this->addon->cp->appended_output[] = $asset;
			
			endforeach;
			
			$this->dependencies_loaded = TRUE;
		
		endif;
		
		//We need a database table. Since everything is supposed to be super simple, why not just check for it
		//and install it if we don't see it there. Everyone is happy and sugar cubes for dinner.
		
		$this->addon->blocks_mdl->check_database();
	}

	// --------------------------------------------------------------------

	/**
	 * Displays a specific block
	 */
	function block( $tag_data = array() )
	{
		//Load up the block
		
		$block = $this->addon->blocks->load_block($tag_data['parameters']['type']);
		
		//Return the render function
		
		return $block->render();
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
		$block = $this->addon->uri->segment(4);
	
		//Load up the block
		
		$block = $this->addon->blocks->load_block($block);
		
		// -------------------------------------
		// Validation
		// -------------------------------------
		
		$this->CI->load->library('form_validation');
		
		$this->CI->load->helper('form');
		
		//We need validation
		
		if( empty($validation) )
			return null;
			
		// Go through, set validation, and make the form fields.
		
		foreach( $block->block_fields as $slug => $data ):
				
			$this->CI->form_validation->set_rules($slug, $data['label'], $data['validation']);
					
		endforeach;

		// -------------------------------------
		
		//Return the render function
		
		if ( $this->form_validation->run() == FALSE ):
		
			if( method_exists($block, 'editor') ):
			
				echo $block->editor();
				
			else:
			
				echo $this->addon->blocks->render_editor( $block->block_fields, $block->block_name );
			
			endif;
		
		else:
		
			//WTF do we do here?
			
			echo 'BLOCKS_FORM_INPUT_SUCCESS';
		
		endif;
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
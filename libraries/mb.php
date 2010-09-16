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
		
		$this->addon->load->library('Blocks');
		
		//Make sure that we load all the assets. But just once.
		
		if( $this->dependencies_loaded == FALSE ):
		
			$assets = $this->_mb_dependencies();
			
			foreach( $assets as $asset ):
		
				$this->addon->cp->appended_output[] = $asset;
			
			endforeach;
			
			$this->dependencies_loaded = TRUE;
		
		endif;
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
		
		//Return the render function
		
		if( method_exists($block, 'editor') ):
		
			echo $block->editor();
			
		else:
		
			echo $this->addon->blocks->render_editor( $block->block_fields, $block->block_name );
		
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
	function js()
	{
		$file = $this->addon->uri->segment(4);
		
		header("Content-Type: text/javascript");
	
		echo file_get_contents( APPPATH . 'third_party/mb/javascript/'.$file);
	}

	// --------------------------------------------------------------------

	/**
	 * Output a CSS file. Also just like that.
	 */
	function css()
	{
		$file = $this->addon->uri->segment(4);
		
		header("Content-Type: text/css");
	
		echo file_get_contents( APPPATH . 'third_party/mb/views/themes/css/'.$file);
	}
}

/* End of file mb.php */
/* Location: /third_party/robots/libraries/mb.php */
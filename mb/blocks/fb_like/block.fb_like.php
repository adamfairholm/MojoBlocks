<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Facebook Like Block
 *
 * Create and edit a Facebook Like Button
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2011, Parse19
 * @author		Parse19
 * @license		http://parse19.com/mojoblocks/docs/license
 * @link		http://parse19.com/mojoblocks
 */

class block_fb_like
{
	var $block_name				= "Facebook Like";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "fb_like";
	
	var $block_desc				= "Add a Facebook like button";

	var $block_fields			= array(
		'layout'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Button Layout",
				'validation'	=> "trim|required",
				'values'		=> array( 'standard' => 'Standard', 'button_count' => 'Button Count', 'box_count' => 'Box Count')),
		'width'			=> array(
				'label'			=> "Width of the Like button.",
				'validation'	=> "trim|numeric"),
		'action'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Action Word",
				'validation'	=> "trim|required",
				'values'		=> array( 'like' => '"Like"', 'recommend' => '"Recommend"')),
		'font'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Font",
				'validation'	=> "trim|required",
				'values'		=> array( 'arial' => 'Arial', 'lucida grande' => 'Lucida Grande', 'segoe ui' => 'Segoe Ui', 'tahoma' => 'Tahoma', 'trebuchet ms' => 'Trebuchet MS', 'verdana' => 'Verdana')),
		'colorscheme'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Action Word",
				'validation'	=> "trim|required",
				'values'		=> array( 'light' => 'Light', 'dark' => 'Dark')),
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{	
		$this->block =& get_instance();
	}

	// --------------------------------------------------------------------	
	
	/**
	 * Render the Vimeo embed
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{
		if( empty($block_data) )
			return "FB Like Button";
			
		// Get the URL and put it in the proper format
		
		$url = $this->_url_encode( current_url() );
		
		// Return the iframe
		
		return '<iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout='.$block_data['layout'].'&amp;show_faces=true&amp;width='.$block_data['width'].'&amp;action='.$block_data['action'].'&amp;font='.$block_data['font'].'&amp;colorscheme='.$block_data['colorscheme'].'&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$block_data['width'].'; height:65px;" allowTransparency="true"></iframe>';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Encode the URL properly for FB
	 * 
	 * From davis dot peixoto at gmail dot com
	 *
	 * @access 	private
	 * @param	string
	 * @return	string
	 */
	function _url_encode( $current_url )
	{
    	$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    
    	$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    
    	return str_replace($replacements, $entities, urlencode($current_url));
	}
	
}

/* End of file block.fb_like.php */
/* Location: system/mojomotor/third_party/mb/blocks/fb_like/block.fb_like.php */
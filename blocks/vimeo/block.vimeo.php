<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Vimeo Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_vimeo
{
	var $block_name				= "Vimeo";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "vimeo";
	
	var $block_desc				= "Add a Vimeo video embed";

	var $block_fields			= array(
		'video_id' 	=> array(
				'label'			=> "Video ID or URL",
				'validation'	=> "required"),
		'width'			=> array(
				'label'			=> "Width of video",
				'validation'	=> "trim|numeric"),
		'height'		=> array(
				'label'			=> "Height of video",
				'validation'	=> "trim|numeric")
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
			return "Vimeo Embed";
	
		return '<iframe src="http://player.vimeo.com/video/'.$block_data['video_id'].'" width="'.$block_data['width'].'" height="'.$block_data['height'].'" frameborder="0"></iframe>';
	}

	// --------------------------------------------------------------------
	
}

/* End of file block.vimeo.php */
/* Location: system/mojomotor/third_party/block/blocks/vimeo/block.vimeo.php */
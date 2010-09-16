<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks YouTube Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_youtube
{
	var $block_name				= "YouTube Embed";
	
	var $block_desc				= "Insert YouTube videos by ID or URL";

	var $block_fields			= array(
		'youtube_data' 	=> array(
				'label'			=> "YouTube ID or URL",
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
	 * Render the YouTube embed
	 */
	function render()
	{
		return "This is a YouTube video";
	}

	// --------------------------------------------------------------------
	
}

/* End of file block.youtube.php */
/* Location: system/mojomotor/third_party/block/blocks/block.youtube.php */
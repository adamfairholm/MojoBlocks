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
	
	var $block_slug				= "youtube";
	
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
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{
		if( empty($block_data) )
			return "YouTube Embed";
	
		$youtube_embed = '<object width="'.$block_data['width'].'" height="'.$block_data['width'].'"><param name="movie" value="http://www.youtube.com/v/'.$block_data['youtube_data'].'?fs=1&amp;hl=en_US"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$block_data['youtube_data'].'?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$block_data['width'].'" height="'.$block_data['height'].'"></embed></object>';
		
		return $youtube_embed;
	}

	// --------------------------------------------------------------------
	
}

/* End of file block.youtube.php */
/* Location: system/mojomotor/third_party/block/blocks/youtube/block.youtube.php */
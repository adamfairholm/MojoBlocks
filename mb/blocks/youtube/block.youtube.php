<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks YouTube Embed Block
 *
 * Display a YouTube video embed
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2011, Parse19
 * @author		Parse19
 * @license		http://parse19.com/mojoblocks/docs/license
 * @link		http://parse19.com/mojoblocks
 */
 
class block_youtube
{
	var $block_name				= "YouTube Embed";

	var $block_version			= "v1.0";
	
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
		$youtube_id = $this->clean_youtube_input( $block_data['youtube_data'] );
		
		if( !$youtube_id )
			return "Error";
		
		$youtube_embed = '<object width="'.$block_data['width'].'" height="'.$block_data['width'].'"><param name="movie" value="http://www.youtube.com/v/'.$youtube_id.'?fs=1&amp;hl=en_US"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$youtube_id.'?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$block_data['width'].'" height="'.$block_data['height'].'"></embed></object>';
		
		return $youtube_embed;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Clean the YouTube input to get ID if the user
	 * has submitted a URL
	 *
	 * Adapted from http://snipplr.com/view.php?codeview&id=19232
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function clean_youtube_input( $input )
	{
		// If the thing is a YouTube ID, then just return it
		if( strlen($input) == 11 ):
		
			return $input;
		
		endif;
		
		// ID starts afeter "v"
		$starting_id = strpos($input, "?v=");
		
		// Alternate ID placement
		if($starting_id === FALSE)
			$starting_id = strpos($input, "&v=");
			
		// If still FALSE, URL doesn't have a vid ID
		if($starting_id === FALSE)
			return FALSE;
		
		// Offset the start location to match the beginning of the ID string
		$starting_id +=3;
		
		// Get the ID string and return it
		return substr($input, $starting_id, 11);
	}
}

/* End of file block.youtube.php */
/* Location: system/mojomotor/third_party/mb/blocks/youtube/block.youtube.php */
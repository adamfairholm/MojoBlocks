<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Twitter Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_vimeo
{
	var $block_name				= "Twitter";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "twitter";
	
	var $block_desc				= "Show tweets from a user";

	var $block_fields			= array(
		'twitter_name' 	=> array(
				'label'			=> "Twitter Name",
				'validation'	=> "required|max_length[40]"),
		'num_of_tweets'	=> array(
				'label'			=> "Number of Tweets to show",
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
			return "Twitter Feed";
	
		return null;
	}

	// --------------------------------------------------------------------
	
}

/* End of file block.twitter.php */
/* Location: system/mojomotor/third_party/block/blocks/twitter/block.twitter.php */
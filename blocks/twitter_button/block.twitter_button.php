<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Twitter Button Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_twitter_button
{
	var $block_name				= "Twitter Button";
	
	var $block_version			= "v0.1";
	
	var $block_slug				= "twitter_button";
	
	var $block_desc				= "Add a Twitter button to your site";

	var $block_fields			= array(
		'button_type'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Button Type",
				'validation'	=> "trim|required",
				'values'		=> array( 'vertical' => 'Vertical Count', 'horizontal' => 'Horizontal Count', 'none' => 'No Count')),
		'tweet_text_option'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Tweet Text Choice",
				'validation'	=> "trim|required",
				'values'		=> array( 'current_url_title' => 'Current URL Title', 'text' => 'My Own Title Text')),
		'tweet_text'			=> array(
				'label'			=> "My Own Title Text",
				'validation'	=> "trim"),
		'user_to_follow'			=> array(
				'label'			=> "User to Follow",
				'validation'	=> "trim"),
		'related_user_1'			=> array(
				'label'			=> "Related User 1",
				'validation'	=> "trim"),
		'related_user_2'			=> array(
				'label'			=> "Related User 2",
				'validation'	=> "trim"),
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
	 * Render the Twitter button
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{
		// Get the URL and put it in the proper format
		
		$url = current_url();
		
		// Return the button
		
		$tweet_button = '<a href="http://twitter.com/share" class="twitter-share-button" data-url="'.$url.'" ';
		
		// Custom text
		
		if( $block_data['tweet_text_option'] == 'text' && $block_data['tweet_text'] != '' ):
		
			$tweet_button .= 'data-text="'.$block_data['tweet_text'].'" ';
		
		endif;

		// User to follow primary
		
		if( isset($block_data['user_to_follow']) && $block_data['user_to_follow'] != '' ):
		
			$tweet_button .= 'data-via="'.$block_data['user_to_follow'].'" ';
		
		endif;

		// User to follow 1
		
		if( isset($block_data['related_user_1']) && $block_data['related_user_1'] != '' ):
		
			$tweet_button .= 'data-via="'.$block_data['related_user_1'];

			// User to follow 2
	
			if( isset($block_data['related_user_2']) && $block_data['related_user_2'] != '' ):
			
				$tweet_button .= ':'.$block_data['related_user_2'];
			
			endif;
			
			$tweet_button .= '"';
		
		endif;
		
		$tweet_button .= 'data-count="'.$block_data['button_type'].'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		 
		return $tweet_button;
	}

}

/* End of file block.twitter_button.php */
/* Location: system/mojomotor/third_party/mb/blocks/twitter_button/block.twitter_button.php */
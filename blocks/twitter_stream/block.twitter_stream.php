<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Twitter Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_twitter_stream
{
	var $block_name				= "Twitter Stream";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "twitter_stream";
	
	var $block_desc				= "Show tweets from a user";

	var $block_fields			= array(
		'twitter_name' 	=> array(
				'label'			=> "Twitter Name",
				'validation'	=> "required|max_length[40]"),
		'num_of_tweets'	=> array(
				'label'			=> "Number of Tweets to show",
				'validation'	=> "trim|numeric"),
		'layout'		=> array(
				'type'			=> "layout",
				'label'			=> "Layout",
				'validation'	=> "required"
		)
	);

	// --------------------------------------------------------------------
	// Cache variables
	// --------------------------------------------------------------------

	var $cache_output			= TRUE;

	var $cache_expire			= '+1 hour';

	var $cache_data				= '';

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
	 * Render the Twitter stream
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{	
		$tweets = $this->cache_data_call( $block_data );
		
		if( ! $tweets )
			return "<p>Tweets didn't load.</p>";
			
		// -------------------------------------
		// Go through Twitter data and put
		// into an array
		// -------------------------------------	
			
		$twitter_data = array();
		
		$count = 0;
			
		foreach( $tweets as $tweet_obj ):
		
			foreach( $tweet_obj as $key => $value ):
			
				if( ! is_object($value) ){
			
					$twitter_data[$count][$key] = $value;
				
				}else if( $key == "user" ){
								
					foreach( $value as $user_key => $user_value ):
					
						$twitter_data[$count]['user_'.$user_key] = $user_value;
					
					endforeach;
				
				}
			
			endforeach;

			// -------------------------------------			
			// Tweet Link
			// -------------------------------------

			$twitter_data[$count]['tweet_url'] = 'http://twitter.com/'.$twitter_data[$count]['user_screen_name'].'/status/'.$twitter_data[$count]['id'];
			
			// -------------------------------------			
			// Username Link
			// -------------------------------------
			
			$twitter_data[$count]['username_link'] = 'http://twitter.com/'.$twitter_data[$count]['user_screen_name'];

			// -------------------------------------			
			// How long ago
			// -------------------------------------
			
			$twitter_data[$count]['how_long_ago'] = $this->_time_since( strtotime($twitter_data[$count]['created_at']) ).' ago';

			// -------------------------------------
			// Text with links and no links
			// -------------------------------------
			
			$text = $twitter_data[$count]['text'];
			
			$twitter_data[$count]['text'] = $this->_process_tweet($twitter_data[$count]['text']);
			
			$twitter_data[$count]['text_no_links'] = $text;

			// -------------------------------------
			
			$count++;
		
		endforeach;
		
		// -------------------------------------	
		
		$template_data['tweets'] = $twitter_data;
				
		return parse_block_template( $this->block_slug, $template_data, $block_data['layout'] );
	}

	// --------------------------------------------------------------------
	
	/**
	 * Cache data call
	 *
	 * Exists if you want to cache JUST a small piece of data and still
	 * call the render() function
	 *
	 * @access	public
	 * @return	mixed
	 */
	function cache_data_call( $block_data )
	{
		if( $this->cache_data ):
			
			return $this->cache_data;
		
		else:
		
			return json_decode(file_get_contents('http://twitter.com/statuses/user_timeline/'.$block_data['twitter_name'].'.json?count='.$block_data['num_of_tweets']));
	
		endif;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Make tweet have linkts to people, links, and hashtags
	 *
	 * From: http://www.snipe.net/2009/09/php-twitter-clickable-links/
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _process_tweet( $tweet_text )
	{
  		$tweet_text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $tweet_text);
  		$tweet_text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $tweet_text);
  		$tweet_text = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $tweet_text);
		$tweet_text = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $tweet_text);
	
		return $tweet_text;
	}

	// --------------------------------------------------------------------------

	/**
	 * Works out the time since the entry post, takes a an argument in unix time (seconds)
	 * 
	 * From: http://www.dreamincode.net/code/snippet86.htm
	 *
	 * @access	public
	 * @param	string
	 * @return 	string
	 */
	function _time_since($original)
	{
	    $chunks = array(
	        array(60 * 60 * 24 * 365 , 'year'),
	        array(60 * 60 * 24 * 30 , 'month'),
	        array(60 * 60 * 24 * 7, 'week'),
	        array(60 * 60 * 24 , 'day'),
	        array(60 * 60 , 'hour'),
	        array(60 , 'minute'),
	    );
    
    	$today = time();
		$since = $today - $original;
    
	    // $j saves performing the count function each time around the loop
	    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
	        
	        $seconds = $chunks[$i][0];
	        $name = $chunks[$i][1];
	        
	        // finding the biggest chunk (if the chunk fits, break)
	        if (($count = floor($since / $seconds)) != 0) {
	            // DEBUG print "<!-- It's $name -->\n";
	            break;
	        }
	    }
    
	    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
	    
	    if ($i + 1 < $j) {
	        // now getting the second item
	        $seconds2 = $chunks[$i + 1][0];
	        $name2 = $chunks[$i + 1][1];
	        
	        // add second item if it's greater than 0
	        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
	            $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
	        }
	    }
	    
	    return $print;
	}
}

/* End of file block.twitter.php */
/* Location: system/mojomotor/third_party/block/blocks/twitter/block.twitter.php */
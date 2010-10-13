<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Twitter Search Block
 *
 * Display results of a Twitter search
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2010, Green Egg Media
 * @author		Green Egg Media
 * @license		http://www.greeneggmedia.com/MojoBlocks_License.txt
 * @link		http://www.greeneggmedia.com/mojoblocks
 */
 
class block_twitter_search
{
	var $block_name				= "Twitter Search";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "twitter_search";
	
	var $block_desc				= "Search and Display Tweets for a term";

	var $block_fields			= array(
		'search_term' 	=> array(
				'label'			=> "Search Term",
				'validation'	=> "trim|required"),
		'num_of_tweets'	=> array(
				'label'			=> "Number of Tweets to show",
				'validation'	=> "trim|numeric"),
		'layout'		=> array(
				'type'			=> "layout",
				'label'			=> "Layout",
				'validation'	=> "required"),
		'result_type'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Return Type:",
				'validation'	=> "trim|required",
				'values'		=> array( 'mixed' => 'Popular and Recent Tweets', 'recent' => 'The most recent Tweets', 'popular' => 'The most popular Tweets' ) )
	);

	// --------------------------------------------------------------------
	// Cache variables
	// --------------------------------------------------------------------

	var $cache_output			= FALSE;

	var $cache_expire			= '+1 hour';

	var $cache_data				= '';

	// --------------------------------------------------------------------

	// Variables we want to save about the user for each tweet
	var $user_tweet_info 		= array('screen_name', 'name', 'url', 'profile_image_url');

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
	 * Render the Block
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{	
		$tweets = $this->cache_data_call( $block_data );
				
		if( ! $tweets )
			return "<p>There was an error in loading the tweets.</p>";
			
		// -------------------------------------
		// Get general data
		// -------------------------------------
			
		$general = array();
		
		$general['search_term']	= $block_data['search_term'];	
			
		// -------------------------------------
		// Go through Twitter data and put
		// tweets into an array
		// -------------------------------------	
			
		$twitter_data = array();
		
		$count = 0;
			
		foreach( $tweets->results as $tweet_obj ):
		
			foreach( $tweet_obj as $key => $value ):
			
				if( ! is_object($value) ){
			
					$twitter_data[$count][$key] = $value;
				
				}else if( $key == "user" ) {
								
					foreach( $value as $user_key => $user_value ):
					
						if( in_array($user_key, $this->user_tweet_info) ):
					
							$twitter_data[$count]['user_'.$user_key] = $user_value;
					
						endif;
					
					endforeach;
				
				}
			
			endforeach;

			// -------------------------------------			
			// Tweet Source
			// -------------------------------------

			$twitter_data[$count]['source'] = htmlspecialchars_decode($twitter_data[$count]['source']);

			// -------------------------------------			
			// Tweet Link
			// -------------------------------------

			$twitter_data[$count]['tweet_url'] = 'http://twitter.com/'.$twitter_data[$count]['from_user'].'/status/'.$twitter_data[$count]['id'];
			
			// -------------------------------------			
			// Username Link
			// -------------------------------------
			
			$twitter_data[$count]['username_link'] = 'http://twitter.com/'.$twitter_data[$count]['from_user'];

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
		
		$temp['tweets'] = $twitter_data;
		
		$template_data = array_merge($general, $temp);
		
		if( count($twitter_data) == 0 ):
			
			return "<p>No tweets were found.</p>";
		
		endif;
				
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
		
			// Clean the hashtag / other content
			
			$block_data['search_term'] = $this->_encode( $block_data['search_term'] );
		
			$url = 'http://search.twitter.com/search.json?q='.$block_data['search_term'].'&rpp='.$block_data['num_of_tweets'].'&result_type='.$block_data['result_type'];
		
			return json_decode(file_get_contents($url));
	
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

	// --------------------------------------------------------------------
	
	/**
	 * Encode for Twitter
	 * 
	 * From davis dot peixoto at gmail dot com
	 *
	 * @access 	private
	 * @param	string
	 * @return	string
	 */
	function _encode( $string )
	{
    	$entities = array('%25', '%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D');
    
    	$replacements = array("%", '!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "#", "[", "]");
    
    	return str_replace($replacements, $entities, $string);
	}

}

/* End of file block.twitter_search.php */
/* Location: system/mojomotor/third_party/mb/blocks/twitter_search/block.twitter_search.php */
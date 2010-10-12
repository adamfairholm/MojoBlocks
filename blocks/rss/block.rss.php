<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks RSS Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_rss
{
	var $block_name				= "RSS";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "rss";
	
	var $block_desc				= "Show an RSS feed";

	var $block_fields			= array(
		'feed_url' 	=> array(
				'label'			=> "Feed URL",
				'validation'	=> "trim|required"),
		'show_number' 	=> array(
				'label'			=> "Number of items to show",
				'validation'	=> "trim|required|numerical"),
		'preview_length' 	=> array(
				'label'			=> "Post Preview Length (words)",
				'validation'	=> "trim|numerical"),
		'layout'		=> array(
				'type'			=> "layout",
				'label'			=> "Layout",
				'validation'	=> "required")
	);

	// --------------------------------------------------------------------
	
	var $social_media = array('blinklist', 'blogmarks', 'delicious', 'digg', 'furl', 'magnolia', 'newsvine', 'reddit', 'segnalo', 'simpy', 'spurl', 'wists');

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
	 * Render the H tag
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{
		if( !isset($block_data['preview_length']) || !is_numeric($block_data['preview_length']) ):

			$block_data['preview_length'] = 100;

		endif;
	
		$this->block->load->helper('text');

		// -------------------------------------		
		// Cache
		// -------------------------------------
		
		// Don't have a cache folder? Make one
		
		$cache_folder = APPPATH.'cache/rss_block';
		
		if( !file_exists($cache_folder) ):
		
			@mkdir($cache_folder, 0777);

			if( !file_exists($cache_folder) ):
			
				return "<p>Please make your mojomotor/cache directory writable</p>";
			
			endif;
		
		endif;
		
		// Get feed
		
		include('simplepie.inc');
	
		$feed = new SimplePie();
		
		$feed->enable_cache( TRUE );
	
		$feed->set_cache_location($cache_folder);

		$feed->set_cache_duration(3600);
		
		$feed->set_feed_url( $block_data['feed_url'] );

		$feed->init();
	
		// General RSS feed info

		$feed_data['feed_title'] 		= $feed->get_title();
		$feed_data['copyright'] 		= $feed->get_copyright();
		$feed_data['description'] 		= $feed->get_description();
		$feed_data['encoding'] 			= $feed->get_encoding();
		$feed_data['total_items']		= $feed->get_item_quantity();
		$feed_data['language']			= $feed->get_language();
		$feed_data['feed_type']			= $feed->get_type();
		$feed_data['feed_url']			= $feed->subscribe_url();

		if( isset($block_data['show_number']) && is_numeric($block_data['show_number']) ):

			$items = $feed->get_items(0, $block_data['show_number']);

		else:
		
			$items = $feed->get_items();

		endif;
		
		$count = 0;
		
		foreach( $items as $item ):
		
			$feed_data['items'][$count]['title'] 			= $item->get_title();
			$feed_data['items'][$count]['description'] 		= $item->get_description();
			$feed_data['items'][$count]['content'] 			= $item->get_content();
			$feed_data['items'][$count]['content_plain'] 	= strip_tags($item->get_content(), '<strong><em>');
			
			// Preview
			
			$feed_data['items'][$count]['preview'] = word_limiter(
				$feed_data['items'][$count]['content_plain'],
				$block_data['preview_length']);
			
			$feed_data['items'][$count]['permalink'] 		= $item->get_permalink();
			$feed_data['items'][$count]['date_posted'] 		= $item->get_date();
			$feed_data['items'][$count]['id']				= $item->get_id();
			
			// Social media links
			
			foreach( $this->social_media as $social ):
			
				$function = 'add_to_'.$social;
			
				$feed_data['items'][$count][$social.'_link']				= $item->$function();
			
			endforeach;
			
			// Author stuff
			
			$author = $item->get_author();
			
			$feed_data['items'][$count]['author_name']		= $author->get_name();
			$feed_data['items'][$count]['author_email']		= $author->get_email();
			$feed_data['items'][$count]['author_link']		= $author->get_link();
			
			$count++;
					
		endforeach;

		return parse_block_template( $this->block_slug, $feed_data, $block_data['layout'] );					
	}

}

/* End of file block.rss.php */
/* Location: system/mojomotor/third_party/mb/blocks/rss/block.h.php */
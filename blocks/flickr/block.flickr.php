<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Flickr Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class block_flickr
{
	var $block_name				= "Flickr Images";
	
	var $block_version			= "v0.1";
	
	var $block_slug				= "flickr";
	
	var $block_desc				= "Show images from a Flickr user.";

	var $block_fields			= array(
		'method'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Display:",
				'validation'	=> "trim|required",
				'values'		=> array( 'user' => 'Photos from a User', 'gallery' => 'Photos from a Gallery', 'photoset' => 'Photos from a Photoset')),
		'api_key' 	=> array(
				'label'			=> "Flickr API Key",
				'validation'	=> "trim|required"),
		'item_id'	=> array(
				'label'			=> "ID",
				'validation'	=> "trim|required"),
		'num_of_photos'	=> array(
				'label'			=> " Number of Photos to show",
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
	 * Render the Flickr photos
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{	
		// -------------------------------------
		// Get the data from cache or from API
		// -------------------------------------	

		$photos = $this->cache_data_call( $block_data );
		
		if( ! $photos )
			return "<p>Photos didn't load.</p>";
			
		if( $photos->photos->total == 0 )
			return "<p>No photos returned</p>";	
			
		// -------------------------------------
		// Go through Flickr data and put
		// into an array for layout
		// -------------------------------------
			
		$template_data = array();
		
		// Data for each photo
		
		$count = 0;
		
		foreach( $photos->photos->photo as $photo ):
		
			$img_url = 'http://farm'.$photo->farm.'.static.flickr.com/'.$photo->server.'/'.$photo->id.'_'.$photo->secret;
		
			$template_data['photos'][$count]['id'] 					= $photo->id;
			$template_data['photos'][$count]['owner'] 				= $photo->owner;
			$template_data['photos'][$count]['server'] 				= $photo->server;
			$template_data['photos'][$count]['farm'] 				= $photo->farm;
			$template_data['photos'][$count]['title'] 				= $photo->title;
			$template_data['photos'][$count]['user_url']			= 'http://flickr.com/photos/'.$photo->owner;
			$template_data['photos'][$count]['square_image']		= $img_url.'_s.jpg';
			$template_data['photos'][$count]['thumbnail_image']		= $img_url.'_t.jpg';
			$template_data['photos'][$count]['small_image']			= $img_url.'_m.jpg';
			$template_data['photos'][$count]['medium_image']		= $img_url.'jpg';
			$template_data['photos'][$count]['large_image']			= $img_url.'_b.jpg';
			$template_data['photos'][$count]['original_image']		= $img_url.'_o.jpg';
			$template_data['photos'][$count]['image_url']			= 'http://www.flickr.com/photos/'.$photo->owner.'/'.$photo->id;
			
			$count++;
		
		endforeach;
		
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
		
			// -------------------------------------
			// Organize data
			// -------------------------------------
			
			$api_key = $block_data['api_key'];

			$item_id = $block_data['item_id'];
			
			// Method
			
			if( $block_data['method'] == "user" )
			{
				$method 	= 'flickr.people.getPublicPhotos';
				$item		= 'user_id';
			}
			else if( $block_data['method'] == "gallery" )
			{
				$method = '';
			}
			else
			{
				$method = 'photoset';
			}
			
			// Number of photos
			
			if( $block_data['num_of_photos'] != '' ):
			
				$per_page = $block_data['num_of_photos'];
			
			else:
			
				$per_page = 8;
			
			endif;
		
			// -------------------------------------
			
			$rest_call = "http://api.flickr.com/services/rest/?method=$method&format=json&api_key=$api_key&$item=$item_id&per_page=$per_page";
		
			$returned_call_data = file_get_contents($rest_call);
			
			// Yeah who guessed Flickr JSON was a lie?
			// Thanks to http://stackoverflow.com/questions/2752439/decode-json-string-returned-from-flickr-api-using-php-curl
			$returned_call_data = str_replace( 'jsonFlickrApi(', '', $returned_call_data );
			$returned_call_data = substr( $returned_call_data, 0, strlen( $returned_call_data ) - 1 );
			
			return json_decode($returned_call_data);
	
		endif;
	}

}

/* End of file block.twitter.php */
/* Location: system/mojomotor/third_party/block/blocks/twitter/block.twitter.php */
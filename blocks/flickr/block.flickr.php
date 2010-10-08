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
	
	var $block_version			= "v0.9";
	
	var $block_slug				= "flickr";
	
	var $block_desc				= "Show images from a Flickr user.";

	var $block_fields			= array(
		'method'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Display:",
				'validation'	=> "trim|required",
				'values'		=> array( 'user' => 'Photos from a User', 'set' => 'Photos from a Set' ) ),
		'api_key' 	=> array(
				'label'			=> "Flickr API Key",
				'validation'	=> "trim|required"),
		'item_id'	=> array(
				'label'			=> "User name or Set ID",
				'validation'	=> "trim|required"),
		'num_of_photos'	=> array(
				'label'			=> "Number of Photos to show",
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

	var $cache_expire			= '+2 hour';

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

		$flickr = $this->cache_data_call( $block_data );
		
		if( ! $flickr )
			return "<p>Photos didn't load.</p>";
			
		if( isset($flickr->stat) && $flickr->stat == 'fail' )
			return "<p>".$flickr->message."</p>";
			
		// -------------------------------------
		// Go through Flickr data and put
		// into an array for layout
		// -------------------------------------
			
		$template_data = array();
		
		switch( $block_data['method'] )
		{
			case 'user':
				$template_data = $this->parse_user_photos( $flickr );
				break;
			case 'set':
				$template_data = $this->parse_set_photos( $flickr );
				break;
		}
				
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
			
			// Method & Prep
			
			if( $block_data['method'] == "user" )
			{
				$method 	= 'flickr.people.getPublicPhotos';
				$item		= 'user_id';
								
				// We need to get the NSID from the user name, to make it simpler for the user
				
				$user_rest_call = "http://api.flickr.com/services/rest/?method=flickr.urls.lookupUser&format=json&api_key=$api_key&url=http://www.flickr.com/people/".$block_data['item_id']."/";
			
				$user = $this->_make_call( $user_rest_call );
				
				if( isset($user->user->id) ):
				
					$item_id = $user->user->id;
				
				endif;
			}
			else if( $block_data['method'] == "set" )
			{
				$method 	= 'flickr.photosets.getPhotos';
				$item		= 'photoset_id';
			}
			
			// Number of photos
			
			if( $block_data['num_of_photos'] != '' ):
			
				$per_page = $block_data['num_of_photos'];
			
			else:
			
				$per_page = 8;
			
			endif;
		
			// -------------------------------------
			
			$rest_call = "http://api.flickr.com/services/rest/?method=$method&format=json&api_key=$api_key&$item=$item_id&per_page=$per_page&extras=description,owner_name,tags,original_format,geo,views,path_alias";
		
			return $this->_make_call( $rest_call );
	
		endif;
	}

	// --------------------------------------------------------------------------	

	/**
	 * Yeah who guessed Flickr JSON was a lie?
	 *
	 * Thanks to http://stackoverflow.com/questions/2752439/decode-json-string-returned-from-flickr-api-using-php-curl
	 *
	 * @access	private
	 * @param	string
	 * @return	array
	 */
	function _clean_flickr_json( $json )
	{
		$json = str_replace( 'jsonFlickrApi(', '', $json );
		$json = substr( $json, 0, strlen( $json ) - 1 );
		
		return json_decode( $json );
	}

	// --------------------------------------------------------------------------	

	/**
	 * Make call
	 *
	 * @access	private
	 * @param	string
	 * @return	array
	 */
	function _make_call( $url_call )
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url_call);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
		$returned_call_data = curl_exec($ch);
		curl_close($ch);
		
		return $this->_clean_flickr_json( $returned_call_data );
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * Parse an image object and return array of variables
	 *
	 * @access	public
	 * @param	obj
	 * @return	array
	 */
	function parse_image( $photo, $owner = FALSE )
	{
		$image = array();
		
		// Determine the Owner
		
		if( !$owner ):
		
			$owner = $photo->owner;
		
		endif;
		
		$img_url = 'http://farm'.$photo->farm.'.static.flickr.com/'.$photo->server.'/'.$photo->id.'_'.$photo->secret;
	
		$image['id'] 					= $photo->id;
		$image['owner'] 				= $owner;
		$image['server'] 				= $photo->server;
		$image['farm'] 					= $photo->farm;
		$image['title'] 				= $photo->title;
		$image['user_url']				= 'http://flickr.com/photos/'.$owner;
		$image['square_image']			= $img_url.'_s.jpg';
		$image['thumbnail_image']		= $img_url.'_t.jpg';
		$image['small_image']			= $img_url.'_m.jpg';
		$image['medium_image']			= $img_url.'jpg';
		$image['large_image']			= $img_url.'_b.jpg';
		$image['original_image']		= $img_url.'_o.jpg';
		$image['image_url']				= 'http://www.flickr.com/photos/'.$owner.'/'.$photo->id;
		$image['description']			= $photo->description->_content;
		$image['views']					= $photo->views;
		$image['path_alias']			= $photo->pathalias;
	
		// Original Format

		if( isset($photo->originalformat) )
			$image['original_format']		= $photo->originalformat;
		
		// Owner name
		
		if( isset($photo->ownername) )
			$image['owner_name']		= $photo->ownername;

		// Tags
		
		if( isset($photo->tags) ):
			
			$tags = explode(" ", $photo->tags);
			
			$tag_count = 0;
			
			foreach( $tags as $tag ):
			
				$image['tags'][$tag_count]['tag']				= $tag;
				$image['tags'][$tag_count]['tag_url']			= 'http://www.flickr.com/photos/tags/'.$tag;
				$image['tags'][$tag_count]['tag_user_url']		= 'http://www.flickr.com/photos/'.$photo->pathalias.'/tags/'.$tag;
				
				$tag_count++;
			
			endforeach;				

		endif;

		// Geo Location
		
		if( isset($photo->geo_is_public) && $photo->geo_is_public == 1 ):
		
			$image['latitude']		= $photo->latitude;
			$image['longitude']		= $photo->longitude;
			$image['place_id']		= $photo->place_id;
			$image['woeid']			= $photo->woeid;
		
		endif;
		
		// Return the Image array
		
		return $image;
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * Parse data for user's photos
	 *
	 * @access	public
	 * @param	obj
	 * @return 	array
	 */
	function parse_user_photos( $photos )
	{
		$template_data = array();
		
		$count = 0;
	
		foreach( $photos->photos->photo as $photo ):
		
			$template_data['photos'][$count] = $this->parse_image( $photo );

			$count++;
		
		endforeach;

		return $template_data;	
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * Parse set data
	 *
	 * @access	public
	 * @param	obj
	 * @return 	array
	 */
	function parse_set_photos( $set )
	{
		$template_data = array();

		$count = 0;
		
		// Get the general data
		
		$template_data['set_id']			= $set->photoset->id;
		$template_data['set_owner_id']		= $set->photoset->owner;
		$template_data['set_owner_name']	= $set->photoset->ownername;
		$template_data['total_images']		= $set->photoset->total;
		
		// Get the photos
	
		foreach( $set->photoset->photo as $photo ):
		
			$template_data['photos'][$count] = $this->parse_image( $photo, $set->photoset->owner );

			$count++;
		
		endforeach;

		return $template_data;	
	}

	// --------------------------------------------------------------------
	
	/**
	 * Encode the URL properly for FB
	 * 
	 * From davis dot peixoto at gmail dot com
	 *
	 * @access 	private
	 * @param	string
	 * @return	string
	 */
	function _url_encode( $current_url )
	{
    	$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    
    	$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    
    	return str_replace($replacements, $entities, urlencode($current_url));
	}

}

/* End of file block.flickr.php */
/* Location: system/mojomotor/third_party/mb/blocks/twitter/block.flickr.php */
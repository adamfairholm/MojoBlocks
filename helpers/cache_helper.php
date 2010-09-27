<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Cache Helper
 *
 * Helps with cache 
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */

// --------------------------------------------------------------------------

/**
 * Validate data.
 *
 * @param	string
 * @param	[string] 	cache time in seconds
 * @return 	mixed
 */
function validate_cache( $cache_data, $cache_process, $cache_expire )
{
	$cache_data = trim( $cache_data );

	// If there is no cache data, then we need to fill it

	if( $cache_data == '' ):
	
		return FALSE;
	
	else:

		// Is it expired?
			
		if( $cache_expire == '' )
			return FALSE;	

		$now = time();
		
		if( $now > $cache_expire )
			return FALSE;

		// Do we need to process it?
		
		if( $cache_process == 'unserialize' ):
		
			$cache_data = unserialize($cache_data);
		
		endif;

		// Return the cache data

		return $cache_data;

	endif;
}

// --------------------------------------------------------------------------

/**
 * Write to the cache
 *
 * @param	obj
 * @param	string
 * @param	int
 * return 	bool
 */
function write_cache( $block, $render_output, $block_row_id, $block_data = array() )
{
	// Check to see what kind of cache this is
	
	if( method_exists($block, 'cache_data_call') ):
	
		// We need to just grab to the output of this.
	
		$data_to_cache = $block->cache_data_call( $block_data );
	
	else:
	
		// We need to cache the entire output

		$data_to_cache = $render_output;
	
	endif;
	
	// The time has come to write the cache
	
	$CI =& get_instance();
	
	// Figure out what type of data it is.
	
	if( is_array($data_to_cache) || is_object($data_to_cache) ):
	
		$data_to_cache = serialize($data_to_cache);
		
		$update_data['cache_process'] = 'unserialize';
		
	endif;
	
	// Set cache expiration
	
	if( !isset($block->cache_expire) || $block->cache_expire != '' ):
	
		$block->cache_expire = '+1 hour';
		
	endif;

	$refresh = strtotime( $block->cache_expire );
	
	if( ! $refresh ):
	
		$refresh = strtotime( '+1 hour' );
	
	endif;
	
	$update_data['cache_expire'] = $refresh;
	
	// Update cache

	$update_data['cache'] = $data_to_cache;

	$CI->db->where('id', $block_row_id);
	
	return $CI->db->update( $CI->blocks_mdl->table_name, $update_data );
}

// --------------------------------------------------------------------------

/**
 * Clear the cache with the row ID
 *
 * @param	int
 * @return 	bool
 */
function clear_cache( $block_row_id )
{
	if( !is_numeric($block_row_id) ):
	
		return FALSE;
	
	endif;
	
	$CI =& get_instance();

	$update_data['cache'] 					= '';
	//$update_data['cache_process'] 		= '';
	//$update_data['cache_expire'] 			= '';

	$CI->db->where('id', $block_row_id);
	
	return $CI->db->update( $CI->blocks_mdl->table_name, $update_data );
}

/* End of file cache_helper.php */
/* Location: ./third_party/mb/helpers/cache_helper.php */
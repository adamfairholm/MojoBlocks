<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Twitter Helper
 *
 * Helpful functions for blocks 
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */

// --------------------------------------------------------------------------

/**
 * Load a view for a block
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	array
 * @return	string
 */
function load_block_view( $view_name, $block_slug, $data = array() )
{
	$CI =& get_instance();

	return $CI->blocks->load_view($view_name, $data, 'blocks/'.$block_slug.'/views/');
}

// --------------------------------------------------------------------------

/**
 * Load a view for a block
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	array
 * @return	string
 */
function parse_block_template( $block_slug, $template_data = array(), $template_name = '' )
{
	$CI =& get_instance();
	
	$CI->load->library('parser');

	$orig_view_path = $CI->load->_ci_view_path;
	
	// Check to see if 
	
	$CI->load->_ci_view_path = APPPATH.'third_party/mb/blocks/'.$block_slug.'/';
	
	if( !file_exists($CI->load->_ci_view_path) ):
	
		$CI->load->_ci_view_path = APPPATH.'third_party/mb/third_party/'.$block_slug.'/';
		
		if( !file_exists($CI->load->_ci_view_path) ):
		
			// Looks like we have no directory
		
			return FALSE;
		
		endif;
	
	endif;
	
	// -------------------------------------
	// Deal with Templates
	// -------------------------------------

	// If the template is null or default, go with the default. But make sure it exists first
	
	if( ( $template_name == '' || $template_name == 'default' ) && file_exists($CI->load->_ci_view_path.'layout.php'))
	{
		$template_name = 'layout';

		$parsed_template = $CI->parser->parse( $template_name, $template_data, TRUE );
	
	} else if( is_numeric($template_name) ) {
	
		// Since it's numeric, it is a DB template
		// we need to grab the template and go from there
		
		// There may be a better way to do this - maybe just get ALL
		// the layouts and put them into a widely accessible array
		// and just check against that.
		// We still need to get the value from the DB if we are doing this 
		// from AJAX.
		
		$CI->load->model('layout_model');
		
		$CI->db->limit(1); // Adding this in here...
	
		$obj = $CI->layout_model->get_layout( $template_name );
		
		if( $obj === FALSE ){
		
			$parsed_template = parsed_template_error();

		} else {

			$template_arr = $obj->row_array();
				
			$parsed_template = $CI->parser->parse_string( $template_arr['layout_content'], $template_data, TRUE );
		}
	
	} else {

		// Looks like there is nothing..so..I guess...just blank it?
		
		$parsed_template = parsed_template_error();

	}

	// -------------------------------------

	$CI->load->_ci_view_path = $orig_view_path;

	return $parsed_template;
}

// --------------------------------------------------------------------------

/**
 * Add a JS file to the stack
 */
function add_block_js_file( $block_slug, $file )
{
	$CI =& get_instance();
	
	$CI->cp->appended_output[] = '<script charset="utf-8" type="text/javascript" src="'.site_url('addons/mb/js/block/'.$block_slug.'/'.$file).'"></script>';
}

// --------------------------------------------------------------------------

/**
 * Add a some JS
 */
function add_block_js( $js )
{
	$CI =& get_instance();
	
	$CI->cp->appended_output[] = '<script type="text/javascript">'.$js.'</script>';
}


// --------------------------------------------------------------------------

function parsed_template_error()
{
	return 'ERROR';
}

/* End of file block_helper.php */
/* Location: ./third_party/mb/helpers/block_helper.php */
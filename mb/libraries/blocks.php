<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Block Library
 *
 * Handles various MojoBlocks functions 
 *
 * @package		MojoBlocks
 * @subpackage	Libraries
 * @copyright	Copyright (c) 2013 Adam Fairholm
 * @author		Adam Fairholm
 * @license		MIT
 * @link		http://adamfairholm.com/code/mojoblocks
 */ 
 
class blocks
{
	var $clean_input						= array('layout_id', 'page_url_title', 'region_id', 'block_type', 'row_id');

	function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Load up a block
	 *
	 * @access	public
	 * @param	string
	 * @return 	obj
	 */
	function load_block( $block )
	{
		$block = strtolower($block);
	
		$filepath = APPPATH.'third_party/mb/blocks/'.$block.'/';
		
		if( ! file_exists($filepath.'block.'.$block.EXT) ):
			
			// If no file exists, then check the third_party 
			// folder for third party blocks
			
			$filepath = APPPATH.'third_party/mb/third_party/'.$block.'/';

			if( ! file_exists($filepath.'block.'.$block.EXT) ):
			
				return FALSE;
			
			endif;
			
		endif;
		
		//We've got the all clear. Load.
		require_once($filepath.'block.'.$block.EXT);
		
		$classname = 'block_'.$block;
		
		$obj = new $classname();
		
		return $obj;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Render the editor
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @param	[array]
	 * @param	[array]
	 * @return	string
	 */
	function render_editor( $block, $region_data, $validation_data = array(), $hidden = array() )
	{
		$form_data = array();
		
		if( !$hidden )
			$hidden = array();
		
		$count = 0;
		
		// Let's build the heading:
		
		$img_url = SYSDIR.'/mojomotor/third_party/mb/blocks/'.$block->block_slug.'/icon.png';
		
		if( !file_exists($img_url) ):
		
			$img_url = SYSDIR.'/mojomotor/third_party/mb/third_party/'.$block->block_slug.'/icon.png';
			
			if( !file_exists($img_url) ):
			
				$img_url = FALSE;
			
			endif;
		
		endif;
		
		if( $img_url ):
		
			$form_data['icon'] 				= '<img src="'.base_url().$img_url.'" alt="'.$block->block_name.'" />';
		
		endif;
		
		$form_data['block_name']		= $block->block_name;
		
		// Let's grab the stuff for the editor and organize it
		
		foreach( $block->block_fields as $slug => $data ):
		
			if( !array_key_exists($slug, $hidden) ):

				$form_data['fields'][$count]['slug'] 	= $slug;
				$form_data['fields'][$count]['label'] 	= $data['label'];
				$form_data['fields'][$count]['input'] 	= $this->_create_field( $slug, $data, $validation_data );
				
				// If we have some validation data, let's show it
				
				if( isset($validation_data['validated']) && $validation_data['validated'] == TRUE && form_error($slug) ):
				
					$form_data['fields'][$count]['error']	= form_error($slug);
					
				else:
				
					$form_data['fields'][$count]['error']	= null;
					
				endif;
						
				$count++;
				
			endif;
					
		endforeach;
		
		// -------------------------------------		
		// Hidden values
		// -------------------------------------		
		
		$form_data['hidden_inputs'] 	= null;
		
		foreach( $hidden as $slug => $value ):

			$form_data['hidden_inputs'] .= '<input type="hidden" name="'.$slug.'" id="'.$slug.'" value="'.$value.'" />' . "\n";		

		endforeach; 
		
		// This can be replaced because we already have this data.
		
		$form_data['layout_id'] 		= $region_data['layout_id'];
		$form_data['page_url_title']	= $region_data['page_url_title'];
		$form_data['region_id']			= $region_data['region_id'];
		$form_data['block_type']		= $region_data['block_type'];
		
		if( $region_data['global'] == TRUE ):
		
			$form_data['region_class']	= 'mojoblock_global_region';
		
		else:

			$form_data['region_class']	= 'mojoblock_region';
		
		endif;
		
		// Do the row ID if we have it
		
		if( isset($region_data['row_id']) ):
		
			$form_data['row_id']	= $region_data['row_id'];
			
		else:
		
			$form_data['row_id']	= "NA";
		
		endif;
				
		// We'll use the parser for this.
		
		$this->CI->load->library('parser');
		
		$orig_view_path = $this->CI->load->_ci_view_path;

		$this->CI->load->_ci_view_path = APPPATH.'third_party/mb/views/';
		
		echo $this->CI->parser->parse('default_editor', $form_data, TRUE);

		$this->CI->load->_ci_view_path = $orig_view_path;		
	}

	// --------------------------------------------------------------------------

	/**
	 * Creates a field based on the type. Right now it's input, textbox, and dropdown
	 *
	 * @access	private
	 * @param	string
	 * @param	array
	 * @return	string
	 */	
	function _create_field( $slug, $data, $validation_data = array() )
	{
		$field = null;
	
		// If we don't get a type, set it to "input"
	
		if( !isset($data['type']) || $data['type'] == '' ):
		
			$data['type'] = "input";
		
		endif;
		
		// Set our name and ID, that'll be the same for each.
				
		$input_config['name'] 	= $slug;
		$input_config['id']		= $slug;
		
		// If we have a value for this field, let's use it!
		
		if( isset($validation_data['field_values'][$slug]) ):
		
			$input_config['value']	= $validation_data['field_values'][$slug];
		
		endif;
		
		// Create the type of field we want
		
		switch( $data['type'] )
		{
			case "input":				
			
				$input_config['class'] = 'mojoblock_input';
				
				$field .= form_input( $input_config );
				
				break;
				
			case "dropdown":
			
				$additional = 'id="'.$input_config['id'].'" class="mojoblock_dropdown"';
				
				if( isset($input_config['value']) && $input_config['value'] ):
				
					$current = $input_config['value'];
				
				else:
				
					$current = null;
				
				endif;
				
				$field .= form_dropdown( $slug, $data['values'], $current );
				
				break;
			
			case "textbox":

				$input_config['class'] = 'mojoblock_textbox';
			
				$field .= form_textarea( $input_config );
			
				break;
				
			case "file":
			
				$field .= form_upload( $input_config );
				
				break;
				
			case "layout":

				if( isset($input_config['value']) && $input_config['value'] ):
				
					$current = $input_config['value'];
				
				else:
				
					$current = null;
				
				endif;
				
				// Load the layouts model and get layouts.
				
				$this->CI->load->model('layout_model');

				$layouts['default']		= 'Block Default';
				
				$layout_obj = $this->CI->layout_model->get_layouts();
				
				$temp = $layout_obj->result_array();
				
				foreach( $temp as $item ):
				
					$layouts[$item['id']] = $item['layout_name'];
				
				endforeach;
			
				$field .= form_dropdown( $slug, $layouts, $current );
		}
		
		return $field;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Clean form input data
	 *
	 * Takes the editor form data and returns a nice associative array:
	 *
	 * array('form_fields' => *, 'page_data' => *)
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function clean_form_input_data( $post )
	{
		if( empty($post) )
			return array();
			
		$return = array();
		
		$temp = $post;
		
		// Clean the layout_id & make something clean to send to the process function
		
		foreach( $this->clean_input as $input ):
		
			$return['page_data'][$input] = $post[$input];
			
			unset($temp[$input]);
			
		endforeach;
		
		$return['form_fields'] = $temp;
		
		return $return;
	}

	// --------------------------------------------------------------------------

	/**
	 * Clean form input data for new data that isn't post or db data
	 *
	 * array('form_fields' => *, 'page_data' => *)
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function clean_new_data( $post )
	{
		if( empty($post) )
			return array();

		$return = array();
	
		$return['form_fields'] = array();
		
		foreach( $this->clean_input as $input ):
		
			if( $input != 'row_id' ):
		
			$return['page_data'][$input] = $post[$input];
			
			endif;
			
		endforeach;
		
		$return['page_data']['row_id'] = "NA";
		
		return $return;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Clean db input data
	 *
	 * Takes the database data and returns a nice associative array:
	 *
	 * array('form_fields' => *, 'page_data' => *)
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function clean_db_input_data( $database_arr )
	{
		if( empty($database_arr) )
			return array();
			
		$return = array();
		
		// Really need to figure out a way to not have to do this.
		$database_arr['region_id'] = $database_arr['block_id'];
		unset($database_arr['block_id']);
		
		$temp = $database_arr;
				
		// Clean the layout_id & make something clean to send to the process function
		
		foreach( $this->clean_input as $input ):
		
			if( $input != 'row_id' ):
		
				$return['page_data'][$input] = $database_arr[$input];
				
				unset($temp[$input]);
			
			endif;
			
		endforeach;
		
		// We want the row_id this time
		
		$return['page_data']['row_id'] = $database_arr['id'];
		
		$return['form_fields'] = $temp['block_content'];
		
		return $return;
	}

	// --------------------------------------------------------------------------

	/**
	 * Loads a view.
	 *
	 * This one is from Dan Horrigan's Equipment MojoMotor addon.
	 *
	 * @access	public
	 * @param	string
	 * @param	array	The data for the view
	 * @param	bool	Where to return the results
	 * @return	string	The view contents
	 */
	function load_view($view, $data = array(), $view_path = 'views/')
	{
		$orig_view_path = $this->CI->load->_ci_view_path;
		
		$this->CI->load->_ci_view_path = APPPATH.'third_party/mb/'.$view_path;

		$view_result = $this->CI->load->view($view, $data, TRUE);

		$this->CI->load->_ci_view_path = $orig_view_path;

		return $view_result;
	}

}

/* End of file blocks.php */
/* Location: ./system/mojomotor/third_party/mb/libraries/blocks.php */
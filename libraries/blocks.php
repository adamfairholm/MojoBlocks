<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Addon Library
 *
 * Handles various MojoBlocks functions.
 *
 * @package		MojoBlocks
 * @subpackage	Addons
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */
class blocks
{
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
	
		$filepath = APPPATH.'third_party/mb/blocks/';
		
		//If there is no file then...no
		if( ! file_exists($filepath.'block.'.$block.EXT) ):
			
			return FALSE;
			
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
	 * @param	array
	 * @return	string
	 */
	function render_editor( $fields, $name, $region_data )
	{
		$form_data = array();
		
		$count = 0;
		
		foreach( $fields as $slug => $data ):

			$form_data['fields'][$count]['slug'] 	= $slug;
			$form_data['fields'][$count]['label'] 	= $data['label'];
			$form_data['fields'][$count]['input'] 	= $this->_create_field( $slug, $data );
					
			$count++;		
					
		endforeach;
		
		// We need some data
		
		$form_data['layout_id'] 		= $region_data['layout_id'];
		$form_data['page_url_title']	= $region_data['page_url_title'];
		$form_data['region_id']			= $region_data['region_id'];
		$form_data['block_type']		= $region_data['block_type'];
		
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
	function _create_field( $slug, $data )
	{
		$field = null;
	
		//If we don't get a type, set it to "input"
	
		if( !isset($data['type']) || $data['type'] == '' ):
		
			$data['type'] = "input";
		
		endif;
				
		$input_config['name'] 	= $slug;
		$input_config['id']		= $slug;
		
		switch( $data['type'] )
		{
			case "input":				
				$field .= form_input( $input_config );
		}
		
		return $field;
	}

	/**
	 * Loads a view.
	 *
	 * This one is from Dan Horrigan's Equipment MojoMotor addon.
	 *
	 * @access	private
	 * @param	string	The view to load MUST include the folder (i.e. views/index)
	 * @param	array	The data for the view
	 * @param	bool	Where to return the results
	 * @return	string	The view contents
	 */
	private function load_view($view, $data = array(), $return = TRUE)
	{
		$orig_view_path = $this->CI->load->_ci_view_path;
		
		$this->CI->load->_ci_view_path = APPPATH.'third_party/mb/views/';

		$return = $this->CI->load->view($view, $data, $return);

		$this->CI->load->_ci_view_path = $orig_view_path;

		return $return;
	}

}

/* End of file blocks.php */
/* Location: ./system/mojomotor/third_party/mb/libraries/blocks.php */
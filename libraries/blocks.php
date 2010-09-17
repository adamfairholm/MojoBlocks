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
	 * @return	string
	 */
	function render_editor( $validation, $name )
	{
		$html = null;
		
		//Run Validation
	
		$this->CI->load->library('form_validation');
		
		$this->CI->load->helper('form');
		
		if( empty($validation) )
			return null;
			
		//Get the form started
		$html .= form_open();
		
		foreach( $validation as $slug => $data ):
		
			//Setup validation
		
			$this->CI->form_validation->set_rules($slug, $data['label'], $data['validation']);
			
			//Create field
			
			$html .= $this->_create_field( $slug, $data );
		
		endforeach;
		
		$html .= form_close();
		
		return $html;
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
		
		//Do the label
		$field .= '<p><label for="'.$slug.'">'.$data['label'].'</label>';
		
		$input_config['name'] 	= $slug;
		$input_config['id']		= $slug;
		
		switch( $data['type'] )
		{
			case "input":				
				$field .= form_input( $input_config );
		}
		
		return $field .= '</p>';
	}
}

/* End of file blocks.php */
/* Location: ./system/mojomotor/third_party/mb/libraries/blocks.php */
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks H Block
 *
 * Create and edit H tags
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2011, Parse19
 * @author		Parse19
 * @license		http://parse19.com/mojoblocks/docs/license
 * @link		http://parse19.com/mojoblocks
 */
 
class block_h
{
	var $block_name				= "H Tag";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "h";
	
	var $block_desc				= "Add a header";

	var $block_fields			= array(
		'h_content' 	=> array(
				'label'			=> "Header Text",
				'validation'	=> "trim"),
		'header_type'	=> array(
				'type'			=> "dropdown",
				'label'			=> "Header Type",
				'validation'	=> "trim|required",
				'values'		=> array( 'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6'))
	);

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
		return "<".$block_data['header_type'].">".$block_data['h_content']."</".$block_data['header_type'].">";
	}

}

/* End of file block.h.php */
/* Location: system/mojomotor/third_party/mb/blocks/h/block.h.php */
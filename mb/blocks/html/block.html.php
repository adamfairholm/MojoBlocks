<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks HTML Block
 *
 * Display and edit a block of HTML
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2011, Addict Add-ons
 * @author		Addict Add-ons
 * @license		http://www.addictaddons.com/licenses/mojoblocks_license.txt
 * @link		http://www.addictaddons.com/mojoblocks
 */
 
class block_html
{
	var $block_name				= "HTML";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "html";
	
	var $block_desc				= "Save and display HTML code";

	var $block_fields			= array(
		'html_code'	=> array(
				'type'			=> "textbox",
				'label'			=> "HTML Code",
				'validation'	=> "trim")
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
	 * Process the HTML code
	 */
	function process_form( $block_data )
	{
		$block_data['html_code'] = htmlspecialchars( $block_data['html_code'] );
	
		return $block_data;
	}

	// --------------------------------------------------------------------

	/**
	 * We need to get the actual HTML to edit it
	 */
	function pre_editor( $block_data )
	{
		$block_data['html_code'] = htmlspecialchars_decode( $block_data['html_code'] );
	
		return $block_data;
	}

	// --------------------------------------------------------------------	
	
	/**
	 * Render the HTML
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{
		return htmlspecialchars_decode($block_data['html_code']);
	}

}

/* End of file block.html.php */
/* Location: system/mojomotor/third_party/mb/blocks/html/block.html.php */
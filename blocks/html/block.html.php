<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks HTML Block
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
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
				'validation'	=> "required")
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
		if( empty($block_data) )
			return "HTML Block";
	
		return htmlspecialchars_decode($block_data['html_code']);
	}

	// --------------------------------------------------------------------
	
}

/* End of file block.h.php */
/* Location: system/mojomotor/third_party/block/blocks/h/block.h.php */
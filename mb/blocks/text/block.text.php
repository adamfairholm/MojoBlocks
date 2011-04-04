<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Text Block
 *
 * Create and edit text
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2011, Addict Add-ons
 * @author		Addict Add-ons
 * @license		http://www.addictaddons.com/licenses/mojoblocks_license.txt
 * @link		http://www.addictaddons.com/mojoblocks
 */
 
class block_text
{
	var $block_name				= "Text";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "text";
	
	var $block_desc				= "Add plain text";

	var $block_fields			= array(
		'text_content' 	=> array(
				'type'			=> "textbox",
				'label'			=> "Content",
				'validation'	=> "trim"),
		'open_tag'	=> array(
				'label'			=> "Opening Tag",
				'validation'	=> "trim"),
		'close_tag'	=> array(
				'label'			=> "Closing Tag",
				'validation'	=> "trim"),
	);

	// --------------------------------------------------------------------	

	/**
	 * Process the form data
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function process_form( $block_data )
	{
		$block_data['text_content'] = strip_tags( $block_data['text_content'] );
	
		return $block_data;
	}

	// --------------------------------------------------------------------	
	
	/**
	 * Render the Block
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{
		if( trim($block_data['open_tag']) == '' ):
		
			$block_data['open_tag'] = "<p>";
		
		endif;

		if( trim($block_data['close_tag']) == '' ):
		
			$block_data['close_tag'] = "</p>";
		
		endif;
	
		return $block_data['open_tag'].$block_data['text_content'].$block_data['close_tag'];
	}

}

/* End of file block.h.php */
/* Location: system/mojomotor/third_party/mb/blocks/h/block.h.php */
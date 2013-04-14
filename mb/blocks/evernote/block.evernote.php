<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Evernote Site Memory Block
 *
 * Create and edit an Evernote Site Memory Button
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2013 Adam Fairholm
 * @author		Adam Fairholm
 * @license		MIT
 * @link		http://adamfairholm.com/code/mojoblocks
 */ 
 
class block_evernote
{
	var $block_name				= "Evernote Site Memory";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "evernote";
	
	var $block_desc				= "Add an Evernote Site Memory button";

	var $block_fields			= array(
		'button_type'		=> array(
				'type'			=> "dropdown",
				'label'			=> "Button Type",
				'validation'	=> "trim|required",
				'values'		=> array( 'article-clipper' => 'Horizontal "Clip"', 'article-clipper-remember' => 'Horizontal "Remember"', 'article-clipper-vert' => 'Vertical "Clip"', 'site-mem-36' => 'Icon (36)', 'site-mem-32' => 'Icon (32)', 'site-mem-22' => 'Icon (22)', 'site-mem-16' => 'Icon (16)')),
		'suggested_notebook'=> array(
				'label'			=> "Suggested Notebook",
				'validation'	=> "trim"),
		'referral_code'=> array(
				'label'			=> "Referral Code",
				'validation'	=> "trim"),
		'content_id'=> array(
				'label'			=> "HTML ID of Content",
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
	 * Render the Vimeo embed
	 *
	 * @access	public
	 * @param	array
	 * @return 	string
	 */
	function render( $block_data )
	{			
		// Get the URL and put it in the proper format
		
		$url = current_url();
		
		// Grab the site name from the settings
		
		$this->block->load->database();
		
		$this->block->db->select('site_name');
		$this->block->db->where('id', 1);
		$obj = $this->block->db->get( 'site_settings' );
		
		$site_data = $obj->row_array();
		
		// Return the iframe
		
		$evernote = '<a href="javascript:" onclick="Evernote.doClip({url: \''.$url.'\',';
	      
	    // Referral code  
	      
	    if( isset($block_data['referral_code']) && $block_data['referral_code'] != '' ):
	    
	    	$evernote .= "code: '".$block_data['referral_code']."',";
	    
	    endif;
	      
	   	// Content ID

	    if( isset($block_data['content_id']) && $block_data['content_id'] != '' ):
	    
	    	$evernote .= "contentId: '".$block_data['content_id']."',";
	    
	    endif;
	    
	    // Suggested Notebook
	    
	    if( isset($block_data['suggested_notebook']) && $block_data['suggested_notebook'] != '' ):
	    
	    	$evernote .= "suggestNotebook: '".$block_data['suggested_notebook']."',";
	    
	    endif;
	    
	   	$evernote .= 'providerName: \''.$site_data['site_name'].'\' });return false"><img src="http://static.evernote.com/'.$block_data['button_type'].'.png" /></a>
	  	<script type="text/javascript" src="http://static.evernote.com/noteit.js"></script>';

		return $evernote;
	}

}

/* End of file block.evernote.php */
/* Location: system/mojomotor/third_party/mb/blocks/evernote/block.evernote.php */
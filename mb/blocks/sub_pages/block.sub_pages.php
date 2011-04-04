<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Sub Pages Block
 *
 * Display sub pages from a given page
 *
 * @package		MojoBlocks
 * @subpackage	Blocks
 * @copyright	Copyright (c) 2011, Addict Add-ons
 * @author		Addict Add-ons
 * @license		http://www.addictaddons.com/licenses/mojoblocks_license.txt
 * @link		http://www.addictaddons.com/mojoblocks
 */
 
class block_sub_pages
{
	var $block_name				= "Sub Pages";
	
	var $block_version			= "v1.0";
	
	var $block_slug				= "sub_pages";
	
	var $block_desc				= "Display sub pages";

	var $block_fields			= array(
		'start_from'		=> array(
				'type'			=> "dropdown",
				'label'			=> "Start From",
				'validation'	=> "required",
				'values'		=> array() ),
		'depth'		=> array(
				'type'			=> "dropdown",
				'label'			=> "Depth",
				'validation'	=> "numeric|required",
				'values'		=> array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5') ),
		'id'		=> array(
				'label'			=> "Top Level ID",
				'validation'	=> "trim")
	);

	// --------------------------------------------------------------------

	var $pages					= array();
	
	var $count					= 1;
	
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

		$this->block->load->helper( array('url', 'page', 'array') );

		$this->block->load->model('site_model');

		$this->block->load->model('page_model');
		
		$this->page_list = $this->block->page_model->get_all_pages_info( TRUE );
		
		$this->site_structure = $this->block->site_model->get_setting('site_structure');
		
		// Populate "Start From"
		
		$pages_list = array();
		
		foreach( $this->page_list as $key => $value ):
		
			$pages_list[$key] = $value['page_title'];
		
		endforeach;
		
		$this->block_fields['start_from']['values'] = $pages_list;
		
		// Get the current URL
		
		if( MM_VERSION >= 1.1 ):
		
			$this->current_segment = $this->block->uri->segment(1);
		
		else:
		
			$this->current_segment = $this->block->uri->segment(2);
		
		endif;
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
		// Get the page ID
		
		$page_id = $block_data['start_from'];
		
		if( !is_numeric($page_id) ):
		
			// If the page ID is not numeric, a url_title must've been passed.
			// We need to get the ID from that.
			
			$page_data = $this->block->page_model->get_page_by_url_title( strtolower($page_id) );
			
			if( isset( $page_data->id ) ):
			
				$page_id = $page_data->id;
			
			else:
			
				return null;
		
			endif;
		
		endif;
		
		// Find & set the depth
		
		if( !isset($block_data['depth']) || !is_numeric($block_data['depth']) ):
		
			$this->depth = 1;
		
		else:
			
			$this->depth = $block_data['depth'];
			
		endif;

		$site_structure = array_find_element_by_key($page_id, $this->site_structure);
		
		if( empty($site_structure) ):
		
			return null;
		
		endif;
		
		// Build UL
		
		if( $block_data['id'] ):
		
			$out = "\n" . '<ul id="' . $block_data['id'] . '">' . "\n";

		else:
		
			$out = "\n" . '<ul>' . "\n";
		
		endif;
		
		if( $site_structure && is_array($site_structure) ):
		
			foreach( $site_structure as $key => $value ):
			
				$out .= $this->create_ul_element( $key, $value );
			
			endforeach;
		
		endif;
		
		return $out;
	}

	// --------------------------------------------------------------------	

	/**
	 * Recursive function to create UL element
	 *
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function create_ul_element( $key, $var )
	{
		$out = '';

		if( $this->count <= $this->depth ):
		
		// Increment before things get crazy
			
		if( !is_array($var) || (isset($var[0]) && $var[0] == '') ):
		
			if( (isset($var[0]) && $var[0] == '') ):
			
				$node = $key;
			
			else:
			
				$node = $var;
			
			endif;
			
			// -------------------------------------			
			// Should this be a current?
			// -------------------------------------
			
			if( $this->page_list[$node]['url_title'] == $this->current_segment ):
			
				$class = ' class="current"';
			
			else:
			
				$class = null;
			
			endif;
		
			// -------------------------------------
			
			if( MM_VERSION >= 1.1 ):
		
				$out .= '<li><a href="'.site_url($this->page_list[$node]['url_title']).'"'.$class.'>'.$this->page_list[$node]['page_title'].'</a></li>' . "\n";
			
			else:

				$out .= '<li><a href="'.site_url('page/'.$this->page_list[$node]['url_title']).'"'.$class.'>'.$this->page_list[$node]['page_title'].'</a></li>' . "\n";
			
			endif;
		
		else:
		
		// It is an array
			
			// Make a new UL

			// -------------------------------------			
			// Should this be a current?
			// -------------------------------------
			
			if( $this->page_list[$key]['url_title'] == $this->current_segment ):
			
				$class = ' class="current"';
			
			else:
			
				$class = null;
			
			endif;
		
			// -------------------------------------
		
			$out .= '<li><a href="'.site_url('page/'.$this->page_list[$key]['url_title']).'"'.$class.'>'.$this->page_list[$key]['page_title'].'</a>' . "\n";
			
			// Make a new UL inside the LI

			$out .= '<ul>' . "\n";
		
			foreach( $var as $key => $var ):
			
				$this->count++;
			
				$out .= $this->create_ul_element( $key, $var );
				
				$this->count--;
			
			endforeach;
			
			$out .= '</li>' . "\n" . '</ul>' . "\n";
		
		endif;
			
		return $out;
		
		endif;
	}

}

/* End of file block.sub_pages.php */
/* Location: system/mojomotor/third_party/mb/blocks/sub_pages/block.sub_pages.php */
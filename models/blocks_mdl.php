<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MojoBlocks Addon Controller
 *
 * @package		MojoBlocks
 * @subpackage	Addons
 * @author		Green Egg Media
 * @link		http://www.greeneggmedia.com
 */

class Blocks_mdl extends CI_Model {
	
	/**
	 * Name for our blocks table
	 */
	var $table_name					= 'blocks';
	
	/**
	 * Table makeup
	 */
	var $table_structure 			=  array(
		'created'					=> array('type' => 'DATETIME'),
		'updated'					=> array('type' => 'DATETIME'),
		'block_type'				=> array('type' => 'VARCHAR', 'constraint' => '50'),
		'block_id'					=> array('type' => 'VARCHAR', 'constraint' => '100'),
		'block_content'				=> array('type' => 'BLOB'),
		'page_url_title'			=> array('type' => 'VARCHAR', 'constraint' => '100'),
		'layout_id'					=> array('type' => 'INT', 'constraint' => '5'),
		'cache'						=> array('type' => 'LONGBLOB'),
		'cache_process'				=> array('type' => 'VARCHAR', 'constraint' => '200'),
		'cache_expire'				=> array('type' => 'VARCHAR', 'constraint' => '60')
	);

   // --------------------------------------------------------------------------

    function Blocks_mdl()
    {
        parent::CI_Model();

		$this->load->database();
    }
    
    // --------------------------------------------------------------------------

	/**
	 * Check the database and see if our table is there. If not, why not make one!
	 *
	 * @access	public
	 */
	function check_database()
	{
		if( ! $this->db->table_exists( $this->table_name ) ):
		
			$this->load->dbforge();

			$this->dbforge->add_field( 'id' );			
			
			$this->dbforge->add_field( $this->table_structure );
			
			$this->dbforge->add_key( 'id' );
			
			$this->dbforge->create_table( $this->table_name );
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Add block data
	 *
	 * @access	public
	 * @param	array
	 * @param	bool
	 * @return	bool
	 */
	function save_block_data( $form_data, $global )
	{
		$block_data['updated'] 				= date('Y-m-d H:i:s');
		
		// Remove 'form_submit' before we serialize so it doesn't get saved
		unset($form_data['form_fields']['form_submit']);
		
		$block_data['block_content']		= serialize( $form_data['form_fields'] );

		// See if we need to update or add
		
		$this->load->database();
		
		$this->db->where('layout_id', $form_data['page_data']['layout_id']);
		$this->db->where('block_type', $form_data['page_data']['block_type']);
		$this->db->where('block_id', $form_data['page_data']['region_id']);

		if( !$global ):

			$this->db->where('page_url_title', $form_data['page_data']['page_url_title']);

		endif;
		
		$obj = $this->db->get($this->table_name);
		
		if( $obj->num_rows() == 0 ):
				
			// We need to add to the db
			
			$block_data['created'] 				= date('Y-m-d H:i:s');
			$block_data['layout_id']			= $form_data['page_data']['layout_id'];
			$block_data['block_type']			= $form_data['page_data']['block_type'];
			$block_data['block_id']				= $form_data['page_data']['region_id'];
	
			// Is this global or local? If global, we don't need the page_url_title in there
	
			if( $global === FALSE ):
				
				$block_data['page_url_title']		= $form_data['page_data']['page_url_title'];
				$block_data['block_reach']			= 'local';
	
			else:
			
				$block_data['block_reach']			= 'global';
			
			endif;
		
			$result = $this->db->insert($this->table_name, $block_data);
			
			$operation = 'insert';
		
		else:
		
			// We need to update
			
			$this->db->where('id', $form_data['page_data']['row_id']);

			$block_data['cache']				= ''; // Wipe out the cache

			// Global/Local stuff

			if( !$global ):
				
				$block_data['page_url_title']		= '';
				$block_data['block_reach']			= 'local';
	
			else:
			
				$block_data['page_url_title']		= ''; // If global, we don't need this anyone
				$block_data['block_reach']			= 'global';
			
			endif;
			
			$result = $this->db->update($this->table_name, $block_data);
			
			$operation = 'update';
	
		endif;
		
		// Return ID or FALSE
		
		if( $result ):
		
			if( $operation == 'insert' ):
		
				return $this->db->insert_id();
			
			else:
			
				return $form_data['page_data']['row_id'];
				
			endif;
			
		else:
		
			return FALSE;
		
		endif;
		
	}

	// --------------------------------------------------------------------------

	/**
	 * Get all the blocks for a given page from the url title and layout id
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	array
	 */
	function retrieve_page_blocks( $page_url_title, $layout_id )
	{
		$this->db->where('page_url_title', $page_url_title);
		$this->db->where('layout_id', $layout_id);
		
		$obj = $this->db->get( $this->table_name );
		
		$data = $obj->result_array();
		
		// Go through and make a pretty array
		
		$return = array();
		
		foreach( $data as $row ):
		
			$return[$row['block_id']]['block_type'] 		= $row['block_type'];
			$return[$row['block_id']]['block_content'] 		= unserialize($row['block_content']);			
			$return[$row['block_id']]['page_url_title'] 	= $row['page_url_title'];
			$return[$row['block_id']]['layout_id'] 			= $row['layout_id'];
			$return[$row['block_id']]['row_id'] 			= $row['id'];
			$return[$row['block_id']]['cache'] 				= $row['cache'];
			$return[$row['block_id']]['cache_process'] 		= $row['cache_process'];
			$return[$row['block_id']]['cache_expire'] 		= $row['cache_expire'];
			$return[$row['block_id']]['block_reach'] 		= $row['block_reach'];
			
			if( $row['tag_settings'] != '' ):
			
				$return[$row['block_id']]['tag_settings'] 		= unserialize($row['tag_settings']);
				
			else:
			
				$return[$row['block_id']]['tag_settings'] 		= array();
			
			endif;
		
		endforeach;
		
		return $return;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get the data for a single block by non-ID data
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @param	int
	 * @return	array
	 */
	function get_single_block( $page_url_title, $layout_id, $region_id, $global = FALSE )
	{
		$this->db->where('layout_id', $layout_id);
		$this->db->where('block_id', $region_id);

		if( $global == FALSE ):
		
			$this->db->where('page_url_title', $page_url_title);
		
		endif;
	
		$obj = $this->db->get( $this->table_name );
		
		if( $obj->num_rows() == 0 )
			return FALSE;
		
		$block = $obj->row_array();
		
		$block['block_content'] 	= unserialize($block['block_content']);
		$block['tag_settings'] 		= unserialize($block['tag_settings']);
		
		return $block;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Get the data for a single global block by the block type and
	 * region ID
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function get_global_block_by_region_id( $region_id, $block_type )
	{
		$this->db->where('block_id', $region_id);
		$this->db->where('block_type', $block_type);
		$this->db->where('block_reach', 'global');
		
		$obj = $this->db->get( $this->table_name );
		
		if( $obj->num_rows() == 0 )
			return FALSE;
		
		$block = $obj->row_array();
		
		$block['block_content'] 	= unserialize($block['block_content']);
		$block['tag_settings'] 		= unserialize($block['tag_settings']);
		
		return $block;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Get the data for a single block.
	 *
	 * @access	public
	 * @param	int
	 * @return	array
	 */
	function get_single_block_by_id( $id )
	{
		$this->db->where('id', $id);
	
		$obj = $this->db->get( $this->table_name );
		
		if( $obj->num_rows() == 0 )
			return FALSE;
		
		$block = $obj->row_array();
		
		$block['block_content'] 	= unserialize($block['block_content']);
		$block['tag_settings'] 		= unserialize($block['tag_settings']);
		
		return $block;
	}

	// --------------------------------------------------------------------------

	/**
	 * Save tag settings to the DB
	 *
	 * @access	public
	 * @param	array
	 * @param	int
	 * @return 	bool
	 */	
	function save_tag_settings( $tag_settings, $block_row_id )
	{
		$this->db->where('id', $block_row_id);
	
		$update_data['tag_settings'] 	= serialize( $tag_settings );
	
		return $this->db->update( $this->table_name,  $update_data);
	}

}

/* End of file blocks_mdl.php */
/* Location: /third_party/mb/block/models/blocks_mdl.php */
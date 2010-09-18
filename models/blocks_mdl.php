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
		'block_content'				=> array('type' => 'BLOB'),
		'page_url_title'			=> array('type' => 'VARCHAR', 'constraint' => '100'),
		'layout_id'					=> array('type' => 'INT', 'constraint' => '5')
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
	 * Add data for the layout
	 *
	 * @access	public
	 * @param	array
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function save_block_data( $form_data )
	{
		$block_data['updated'] 				= date('Y-m-d H:i:s');
		$block_data['block_content']		= serialize( $form_data['form_fields'] );

		// See if we need to update or add
		
		$this->load->database();
		
		$this->db->where('layout_id', $form_data['page_data']['layout_id']);
		$obj = $this->db->get($this->table_name);
		
		if( $obj->num_rows() == 0 ):
		
			// We need to add to the db
			
			$block_data['created'] 				= date('Y-m-d H:i:s');
			$block_data['layout_id']			= $form_data['page_data']['layout_id'];
			$block_data['block_type']			= $form_data['page_data']['block_type'];
			$block_data['page_url_title']		= $form_data['page_data']['page_url_title'];
			$block_data['block_id']				= $form_data['page_data']['region_id'];
			
			$result = $this->db->insert($this->table_name, $block_data);
		
		else:
		
			// We need to update

			$this->db->where('layout_id', $form_data['page_data']['layout_id']);
			
			$result = $this->db->update($this->table_name, $block_data);
	
		endif;
		
		return $result;
	}

	// --------------------------------------------------------------------------

	function retrieve_page_data( $page_url_title, $layout_id )
	{
		$this->db->where('page_url_title', $page_url_title);
		$this->db->where('layout_id', $layout_id);
		
		$obj = $this->db->get( $this->table_name );
		
		$data = $obj->result_array();
		
		//Go through and make a pretty array
		
		$return = array();
		
		foreach( $data as $row ):
		
			$return[$row['block_id']]['block_type'] 		= $row['block_type'];
			$return[$row['block_id']]['block_content'] 		= unserialize($row['block_content']);
			$return[$row['block_id']]['page_url_title'] 	= $row['page_url_title'];
			$return[$row['block_id']]['layout_id'] 			= $row['layout_id'];
		
		endforeach;
		
		return $return;
	}

}

/* End of file blocks_mdl.php */
/* Location: /third_party/block/models/blocks_mdl.php */
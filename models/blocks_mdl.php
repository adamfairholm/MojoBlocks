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
		'layout_id'					=> array('type' => 'INT', 'constraint' => '5')
	);

   // --------------------------------------------------------------------------

    function Blocks_mdl()
    {
        parent::CI_Model();
    }
    
    // --------------------------------------------------------------------------

	/**
	 * Check the database and see if our table is there. If not, why not make one!
	 *
	 * @access	public
	 */
	function check_database()
	{
		$this->load->database();
	
		if( ! $this->db->table_exists( $this->table_name ) ):
		
			$this->load->dbforge();

			$this->dbforge->add_field( 'id' );			
			
			$this->dbforge->add_field( $this->table_structure );
			
			$this->dbforge->add_key( 'id' );
			
			$this->dbforge->create_table( $this->table_name );
		
		endif;
	}
	
	/**
	 * Add data for the layout
	 *
	 * @access	public
	 * @param	array
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function save_block_data( $form_data, $layout_id, $block_name )
	{
		$block_data['updated'] 				= date('Y-m-d H:i:s');
		$block_data['block_content']		= serialize( $form_data );

		// See if we need to update or add
		
		$this->load->database();
		
		$this->db->where('layout_id', $layout_id);
		$obj = $this->db->get($this->table_name);
		
		if( $obj->num_rows() == 0 ):
		
			// We need to add to the db
			
			$block_data['created'] 				= date('Y-m-d H:i:s');
			$block_data['layout_id']			= $layout_id;
			$block_data['block_type']			= $block_name;
			
			$result = $this->db->insert($this->table_name, $block_data);
		
		else:
		
			// We need to update

			$this->db->where('layout_id', $layout_id);
			
			$result = $this->db->update($this->table_name, $block_data);
	
		endif;
		
		return $result;
	}

}

/* End of file blocks_mdl.php */
/* Location: /third_party/block/models/blocks_mdl.php */
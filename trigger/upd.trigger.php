<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_upd { 

    var $version        = '0.1 Alpha'; 
     
    function Trigger_upd() 
    { 
		$this->EE =& get_instance();
    }

	// --------------------------------------------------------------------------
	   
    function install()
    {
		$outcome = TRUE;
		
		$this->EE->load->dbforge();

		// -------------------------------------
		// Create the Log Table
		// -------------------------------------
			
		$log_fields = array(
            'log_time' 		=> array( 'type' => 'INT', 'constraint' => 11 ),
            'user_id' 		=> array( 'type' => 'INT', 'constraint' => 6 ),
            'command' 		=> array( 'type' =>'VARCHAR', 'constraint' => '255'),
            'result' 		=> array( 'type' => 'TEXT'));
            
        $this->dbforge->add_field( $log_fields );
            
		$this->dbforge->add_field( 'id' );
		
		$outcome = $this->dbforge->create_table('trigger_log');

		// -------------------------------------
		// Create the Scratch Table
		// -------------------------------------
	
		$scratch_fields = array(
            'created' 		=> array( 'type' => 'INT', 'constraint' => 11 ),
            'user_id' 		=> array( 'type' => 'INT', 'constraint' => 6 ),
            'cache_data' 	=> array( 'type' => 'BLOB'));
            
        $this->dbforge->add_field( $scratch_fields );
            
		$this->dbforge->add_field( 'id' );
		
		$outcome = $this->dbforge->create_table('trigger_scratch');

		// -------------------------------------
		// Create the Trigger Sequences Table
		// -------------------------------------
	
		$sequence_fields = array(
			'site_id' 		=> array( 'type' => 'INT', 'constraint' => 11 ),
			'created' 		=> array( 'type' => 'DATETIME' ),
			'title' 		=> array( 'type' => 'VARCHAR', 'constraint' => 200 ),
			'name' 			=> array( 'type' => 'VARCHAR', 'constraint' => 200 ),
			'description' 	=> array( 'type' => 'VARCHAR', 'constraint' => 255 ),
			'lines' 		=> array( 'type' => 'INT', 'constraint' => 6 ),
			'created_by' 	=> array( 'type' => 'VARCHAR', 'constraint' => 200 ),
 			'sequence' 		=> array( 'type' => 'LONGTEXT' ));
          
        $this->dbforge->add_field($sequence_fields);
            
		$this->dbforge->add_field('id');
		
		$outcome = $this->dbforge->create_table('trigger_sequences');
	
		// -------------------------------------
		// Register the Module
		// -------------------------------------
	
		$data = array(
			'module_name' => 'Trigger' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		$outcome = $this->EE->db->insert('modules', $data);
		
		return $outcome;
    }

	// --------------------------------------------------------------------------

	function update($current = '')
	{
	
		return FALSE;
	}

	// --------------------------------------------------------------------------
	
	function uninstall()
	{
		$outcome = TRUE;
		
		// Drop log table
	
		$outcome = $this->dbforge->drop_table('trigger_log');

		// Drop scratch table

		$outcome = $this->dbforge->drop_table('trigger_scratch');
	
		return $outcome;
	}
	
}

/* End of file upd.trigger.php */
/* Location: ./expressionengine/third_party/trigger/upd.trigger.php */
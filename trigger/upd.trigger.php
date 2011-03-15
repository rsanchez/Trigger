<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_upd { 

    var $version        = '0.1'; 
     
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
	
		return $outcome;
	}
	
}

/* End of file upd.trigger.php */
/* Location: ./expressionengine/third_party/trigger/upd.trigger.php */
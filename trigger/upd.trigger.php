<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Update
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */

class Trigger_upd { 

    var $version        = '0.8'; 
     
    function Trigger_upd() 
    { 
		$this->EE =& get_instance();

		$this->EE->load->dbforge();
    }

	// --------------------------------------------------------------------------
	   
    function install()
    {
		$outcome = TRUE;
		
		// -------------------------------------
		// Create the Log Table
		// -------------------------------------
			
		$log_fields = array(
            'log_time' 		=> array('type' => 'INT', 'constraint' => 11),
            'user_id' 		=> array('type' => 'INT', 'constraint' => 6),
            'command' 		=> array('type' =>'VARCHAR', 'constraint' => '255'),
            'type' 			=> array('type' =>'VARCHAR', 'constraint' => '20'),
            'result' 		=> array('type' => 'TEXT'));
            
        $this->EE->dbforge->add_field($log_fields);
            
		$this->EE->dbforge->add_field('id');
		
		$outcome = $this->EE->dbforge->create_table('trigger_log');
	
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
		
		// Drop from the modules table
		$outcome = $this->EE->db->where('module_name', 'Trigger')->delete('modules');
		
		// Drop log table	
		$outcome = $this->EE->dbforge->drop_table('trigger_log');
	
		return $outcome;
	}
	
}

/* End of file upd.trigger.php */
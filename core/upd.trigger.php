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
		$data = array(
			'module_name' => 'Trigger' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);
		
		return TRUE;
    }

	// --------------------------------------------------------------------------

	function update($current = '')
	{
		return FALSE;
	}

	// --------------------------------------------------------------------------
	
	function uninstall()
	{
		return TRUE;
	}
	
}

/* End of file upd.trigger.php */
/* Location: ./expressionengine/third_party/trigger/upd.trigger.php */
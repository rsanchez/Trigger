<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_mcp {

	function Trigger_mcp()
	{
		$this->EE =& get_instance();
	}
	
	function index()
	{
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_module_name'));

		return $this->EE->load->view('window', '', TRUE); 
	}
}

/* End of file mcp.trigger.php */
/* Location: ./system/expressionengine/third_party/trigger/mcp.trigger.php */

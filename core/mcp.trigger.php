<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_mcp {

	var $context = array();

	function Trigger_mcp()
	{
		$this->EE =& get_instance();

		$theme_url = $this->EE->config->item('theme_folder_url') . 'third_party/trigger';
		
		$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/css/trigger.css'>");

		if ( ! isset($this->EE->session->cache['trigger']['context']) ):
			
			$this->EE->session->cache['trigger']['context'] = array('ee');
			
		endif;
	}

	// --------------------------------------------------------------------------
	
	function index()
	{
		$this->EE->load->library('javascript');

		$this->EE->cp->load_package_js('trigger');

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_module_name'));
		
		$this->EE->cp->set_variable('context', $this->EE->session->cache['trigger']['context']);

		return $this->EE->load->view('window', '', TRUE); 
	}

	// --------------------------------------------------------------------------
	
	function parse_trigger_output()
	{
		$result = null;
		
		$output = null;
	
		// Get Context
		
		$text = $this->EE->input->post('line');
		
		// Get last line
		
		$lines = explode("\n", $text);
		
		$lines = array_reverse($lines);
		
		foreach( $lines as $individ_line ):
		
			if( trim($individ_line) != '' ):
			
				$line = $individ_line;
			
			endif;
		
		endforeach;
		
		// Get context
		
		$parts = explode(":", $line);
		
		$driver = trim($parts[1]);
		
		// Load driver
		
		include(PATH_THIRD . '/trigger/drivers/'.$driver.'/commands.'.$driver.'.php');
		
		$driver_class = 'Commands_'.$driver;
		
		$obj = new $driver_class();
		
		// Set driver
		
		if( $driver != '' ):
		
			$this->context[0] = 'ee';
			$this->context[1] = $driver;
		
		endif;
		
		if( isset($parts[2]) && $parts[2]!='' ):
		
			// We have a method
			
			$method = trim($parts[2]);
			
			$method = str_replace(" ", "_", $method);
		
			if( method_exists($obj, $method) ):
			
				$result = $obj->$method();
			
			endif;
		
		endif;
		
		// Get result out there
		
		if( $result ):
		
			$output = $result . "\n";
		
		endif;
		
		// Set context

		foreach( $this->context as $cont ):
		
			$output .= $cont . " : ";
		
		endforeach;

		// Save context
		
		$this->EE->session->cache['trigger']['context'] = $this->context;
	
		$this->_output_response($output);
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Output a response by bypassing the control panel
	 */	
	function _output_response( $output )
	{
		$this->EE->output->enable_profiler(FALSE);

		if ($this->EE->config->item('send_headers') == 'y')
		{
			@header('Content-Type: text/html; charset=UTF-8');	
		}
		
		exit( $output );
	}
}

/* End of file mcp.trigger.php */
/* Location: ./system/expressionengine/third_party/trigger/mcp.trigger.php */

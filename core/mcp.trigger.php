<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_mcp {

	var $context 	= array();
	
	var $vars		= array();

	function Trigger_mcp()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('Trigger');

		// -------------------------------------
		// Catch the session cache data. Whatever.
		// -------------------------------------
		
		
		if( ! isset($this->EE->session->cache['Trigger_mcp']['vars']) ):
		
			$this->vars = array();
			
		else:
		
			$this->vars = $this->EE->session->cache['Trigger_mcp']['vars'];
		
		endif;

		// -------------------------------------

		$theme_url = $this->EE->config->item('theme_folder_url') . 'third_party/trigger';
		
		$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/css/trigger.css'>");

		// -------------------------------------
		// Set default context if none set
		// -------------------------------------

		if ( ! isset($this->EE->session->cache['trigger']['context']) ):
			
			$this->EE->session->cache['trigger']['context'] = array('ee');
			
		endif;		
	}

	// --------------------------------------------------------------------------
	
	function index()
	{
		// -------------------------------------
		// Get some javascript
		// -------------------------------------
		
		$this->EE->load->library('javascript');

		$this->EE->cp->load_package_js('trigger');

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_module_name'));

		// -------------------------------------
		// Set the context for the view
		// -------------------------------------
		
		$this->EE->cp->set_variable('context', $this->EE->session->cache['trigger']['context']);

		// -------------------------------------
		// Load trigger edit window
		// -------------------------------------

		return $this->EE->load->view('window', '', TRUE); 
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Handles the trigger input and output
	 */
	function parse_trigger_output()
	{
		$result = null;
		
		$output = null;
		
		$error = null;
	
		// -------------------------------------
		// Get the line in question
		// -------------------------------------
		
		$text = $this->EE->input->post('line');
		
		// Get last line
		
		$lines = explode("\n", $text);
		
		foreach( $lines as $individ_line ):
		
			if( trim($individ_line) != '' ):
			
				$line = $individ_line;
			
			endif;
		
		endforeach;
		
		// -------------------------------------
		// Get context
		// -------------------------------------

		$this->context[0] = 'ee';
		
		$parts = explode(":", $line);

		// -------------------------------------
		// Easy out for "root" in the 1 context
		// -------------------------------------
		
		if( trim($parts[1]) == 'root' ):
		
			$this->context = array('ee');
			
			$this->_output_response( $this->EE->trigger->output_context( $this->context ) );
					
		endif;
		
		// -------------------------------------
		// Check for driver
		// -------------------------------------
		
		if( trim($parts[1]) == '' ):
		
			// -------------------------------------
			// If there is no driver, exit to error
			// -------------------------------------
			
			$error = "Please specify a driver\n";
			
			$this->EE->session->cache['trigger']['context'] = array('ee');
			
			$this->_output_response( $error . $this->EE->trigger->output_context( $this->context ) );
			
		endif;
		
		$driver = trim($parts[1]);
		
		// -------------------------------------
		// Validate Driver
		// -------------------------------------
		
		if( ! file_exists(PATH_THIRD . '/trigger/drivers/'.$driver.'/commands.'.$driver.'.php') ):
		
			// -------------------------------------
			// If there is no driver, exit to error
			// -------------------------------------
			
			$error = "'" . $driver . "' driver does not exist\n";
			
			$this->EE->session->cache['trigger']['context'] = array('ee');
			
			$this->_output_response( $error . $this->EE->trigger->output_context( $this->context ) );
		
		endif;
		
		// -------------------------------------
		// Load driver
		// -------------------------------------
		
		@include(PATH_THIRD . '/trigger/drivers/'.$driver.'/commands.'.$driver.'.php');
		
		$driver_class = 'Commands_'.$driver;
		
		$obj = new $driver_class();
		
		// Set driver to driver context position
		
		if( $driver != '' ):
		
			$this->context[1] = $driver;
		
		endif;

		// -------------------------------------
		// Determine Action
		// -------------------------------------
		
		if( isset($parts[2]) ):
		
			$rest = trim($parts[2]);
			
		else:
			
			$rest = FALSE;
		
		endif;
		
		if( $rest ):
		
			$segs = explode(" ", $rest);
		
			$action = trim($segs[0]);
		
			switch( $action )
			{
				// -------------------------------------
				// Go back to the root
				// -------------------------------------
			
				case 'root':
				
					$this->context = array('ee');
					
					$this->_output_response( $this->EE->trigger->output_context( $this->context ) );
					
					break;

				// -------------------------------------
				// Set a variable
				// -------------------------------------

				case 'set':
				
					$this->context = array('ee', $driver);
					
					if( ! $this->EE->trigger->set_variable( $segs ) ):
					
						$error = "Unable to set variable\n";
					
					endif;
					
					$this->_output_response( $error . $this->EE->trigger->output_context( $this->context ) );
					
					break;

				default:
				
					// Must be a command
				
					$call = str_replace(" ", "_", $rest);
					$call = strtolower($call);
					
					if( !method_exists($obj, $call) ):

						$this->_output_response( "Invalid Command\n" . $this->EE->trigger->output_context( $this->context ) );
					else:

						$this->_output_response( $obj->$call() . "\n" . $this->EE->trigger->output_context( $this->context ) );
					
					endif;
				
				
					break;
			}
		
		endif;
		
		// -------------------------------------
		// All line ending to result
		// -------------------------------------
		
		if( $result ):
		
			$result = $result . "\n";
		
		endif;
		
		// -------------------------------------
		// Output Data
		// -------------------------------------
	
		$this->_output_response( $result . $this->EE->trigger->output_context( $this->context ) );
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

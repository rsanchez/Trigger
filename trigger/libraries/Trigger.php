<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger
{
	var $context 		= array();

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();	
	}

	// --------------------------------------------------------------------------

	/**
	 * Parses & executes the line input from the controller
	 *
	 * @access	public
	 * @param	line
	 */
	function process_line( $line )
	{
		$result = null;
	
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
			
			$this->_output_response( $this->output_context( $this->context ) );
					
		endif;
		
		// -------------------------------------
		// Check for driver
		// -------------------------------------
		
		if( trim($parts[1]) == '' ):
		
			// -------------------------------------
			// If there is no driver, exit to error
			// -------------------------------------
			
			$error = "Please specify a driver\n";
			
			//$this->EE->session->cache['trigger']['context'] = array('ee');
			
			$this->_output_response( $error . $this->output_context( $this->context ) );
			
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
			
			$this->_output_response( $error . $this->EE->trigger->output_context( $this->context ) );
		
		endif;

		// -------------------------------------
		// Load driver language
		// -------------------------------------
		
		$lang_file = PATH_THIRD . 'trigger/drivers/'.$driver.'/langauge/'.$this->EE->config->item('deft_lang').'/lang.'.$driver.'.php';
		
		if( ! file_exists($lang_file) ):
		
			// Looks like there is no language file. That's no good!
			
			$error = "no language file found for $driver driver";
		
			write_log($line, $error);

			$this->_output_response( "$error\n" . $this->output_context( $this->context ) );
			
		else:
		
			@include($lang_file);
		
		endif;

		// -------------------------------------
		// Load master language & merge
		// -------------------------------------
		
		@include(PATH_THIRD . 'trigger/language/'.$this->EE->config->item('deft_lang').'/lang.trigger.php');
		
		$all_lang = array_merge($command_lang, $lang);
		
		// -------------------------------------
		// Load driver
		// -------------------------------------
		
		@include(PATH_THIRD . '/trigger/drivers/'.$driver.'/commands.'.$driver.'.php');
		
		$driver_class = 'Commands_'.$driver;
		
		$obj = new $driver_class();
		
		// Set the language
		
		$obj->lang = $all_lang;
		
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

			// -------------------------------------
			// Parse which type of action
			// -------------------------------------
		
			switch( $action )
			{
				// -------------------------------------
				// Go back to the root
				// -------------------------------------
			
				case 'root':
				
					$this->context = array('ee');

					write_log($line, "(context set to root)");
					
					$this->_output_response( $this->output_context( $this->context ) );
					
					break;

				// -------------------------------------
				// Set a variable
				// -------------------------------------

				case 'set':
				
					$this->context = array('ee', $driver);
					
					if( ! $this->set_variable( $segs, $driver ) ):
					
						$msg = "Unable to set variable\n";
						
					else:
					
						$msg = "Variable set successfully\n";
					
					endif;
					
					$this->_output_response( $msg . $this->output_context( $this->context ) );
					
					break;
					
				case 'create':
				
					$this->context = array('ee', $driver);
					
					$call = 'create_'.$segs[1];
					
					if( method_exists($obj, $call) ):
					
						// Get the stack data & run call
						
						$this->EE->db->limit(1);
						$this->EE->db->where('user_id', $this->EE->session->userdata('member_id'));
						$this->EE->db->where('driver', $driver);
						
						$db = $this->EE->db->get('trigger_scratch');
						
						$raw = $db->row();
					
						$msg = $obj->$call( unserialize($raw->cache_data) )."\n";
						
						// Get rid of the stack
						
						$this->EE->db->where('id', $raw->id);
						
						$this->EE->db->delete();
					
					else:
					
						$msg = "Invalid create command.\n";
					
					endif;
					
					$this->_output_response( $msg . $this->output_context( $this->context ) );
					
					break;

				default:
				
					// Must be a command
				
					$call = str_replace(" ", "_", $rest);
					$call = strtolower($call);
					
					// Check to see if the command exists. Issue error if it doesn't.
					// Otherwise, run the command.
					
					if( !method_exists($obj, $call) ):
					
						write_log($line, "invalid command");

						$this->_output_response( "invalid command\n" . $this->output_context( $this->context ) );
						
					else:
					
						$response = $obj->$call();

						write_log($line, $response);

						$this->_output_response( $response . "\n" . $this->output_context( $this->context ) );
					
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
	 *
	 * @access	private
	 * @param	string
	 * @return	void
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

	// --------------------------------------------------------------------------
	
	/**
	 * Outputs the context in the correct format
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function output_context( $context )
	{
		$this->EE->session->cache['trigger']['context'] == $context;
	
		$output = null;
	
		foreach( $context as $cont ):
		
			$output .= $cont . " : ";
		
		endforeach;

		return $output;
	}

	// --------------------------------------------------------------------------
	
	function set_variable( $segs, $driver )
	{
		$pair = $segs[1];
		
		$vals = explode("=", $pair);
		
		if(count($vals) != 2):
		
			return FALSE;
		
		endif;
		
		// Get the scratch if there is one
		
		$this->EE->db->limit(1);
		$this->EE->db->where('user_id', $this->EE->session->userdata('member_id'));
		$this->EE->db->where('driver', $driver);
		
		$obj = $this->EE->db->get('trigger_scratch');
		
		// If there is no scratch, create one
		
		if( $obj->num_rows() == 0 ):
		
			$insert_data['created'] 		= time();
			$insert_data['user_id']			= $this->EE->session->userdata('member_id');
			$insert_data['driver']			= $driver;
		
			$this->EE->db->insert('trigger_scratch', $insert_data);
			
			$scratch_id = $this->EE->db->insert_id();
			
			$cache = array();
		
		else:
			
			// Otherwise, pull the data
		
			$scratch = $obj->row();
		
			$scratch_id = $scratch->id;
			
			if( trim($scratch->cache_data) == '' ):
			
				$cache = array();
				
			else:
			
				$cache = unserialize($scratch->cache_data);
		
			endif;
		
		endif;
		
		// Merge the cache. Will this overwrite existing array keys? Hmm...
		
		$new_cache = array_merge($cache, array(trim($vals[0]) => trim($vals[1])));

		// Update the scratch
		
		$update_data['cache_data'] = serialize($new_cache);
		
		$this->EE->db->update('trigger_scratch', $update_data);		
		
		return TRUE;
	}

}

/* Trigger.php */
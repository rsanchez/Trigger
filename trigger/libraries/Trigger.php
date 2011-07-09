<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Class
 *
 * Contains line parsing logic.
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */

class Trigger
{
	/**
	 * Context
	 *
	 * Array that determines the string output
	 * when exiting the function
	 */
	public $context 				= array();

	// --------------------------------------------------------------------------
	
	/**
	 * Out
	 *
	 * Contains the string we want to return
	 */
	public $out					= TRUE;

	// --------------------------------------------------------------------------
	
	/**
	 * Bracketed Variable
	 *
	 * If there is a bracketed variable it will be
	 * found and put into this variable.
	 */
	public $variable			= array();

	// --------------------------------------------------------------------------
	
	/**
	 * System Commands
	 *
	 * Array of commands that the system
	 * understands and parses
	 */
	public $system_commands 		= array('drivers');

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
		
		// Define some things
		define('TRIGGER_BUFFER', '------------------');
		define('VARS_LEFT', '(');
		define('VARS_RIGHT', ')');
		define('VAR_SEP', ',');	
	}

	// --------------------------------------------------------------------------

	/**
	 * Parses & executes the line input from the controller
	 *
	 * @access	public
	 * @param	line
	 * @param	bool
	 */
	function process_line( $line, $hey = '' )
	{
		$this->line 	= $line;

		// -------------------------------------
		// Set 0 context to 'ee'
		// -------------------------------------

		$this->context[0] = 'ee';
			
		// -------------------------------------
		// Explode and Clean Line
		// -------------------------------------
		
		$parts = explode(":", $line, 3);
		
		foreach( $parts as $key => $part ):
		
			$parts[$key] = trim($part);
		
		endforeach;

		// -------------------------------------
		// Easy out for "root"
		// -------------------------------------
		// Root is special because it just means
		// get me out of here and forget it.
		// -------------------------------------
		
		if( isset($parts[2]) and ($parts[2] == 'root' or $parts[2] == 'r') ):
		
			$this->context = array('ee');
			
			return;
					
		endif;

		// -------------------------------------
		// Load our Variables
		// -------------------------------------
		
		$this->EE->load->library('Vars');

		// -------------------------------------
		// Insert Variables
		// -------------------------------------
		// Replaces {} curly braced variables
		// With system variables 
		// -------------------------------------
		
		$this->system_var_methods = get_class_methods($this->EE->vars);
		
		foreach( $this->system_var_methods as $method ):
		
			$variable_val = $this->EE->vars->$method();
			
			foreach( $parts as $key => $part ):
			
				$parts[$key] = str_replace(LD.$method.RD, $variable_val, $part);
			
			endforeach;
		
		endforeach;
		
		// -------------------------------------
		// Check Segment Numbers
		// -------------------------------------

		$total_segments = count($parts);

		// -------------------------------------
		// Single Segment Processing
		// -------------------------------------
	
		if( $total_segments == 2 ):
		
			$segment = $parts[1];
			
			// Is this a system variable?
			if($this->_is_variable($segment)):
			
				return $this->out;
			
			endif;
	
			// See if we have a parenthesis () variable
			// If we do, we get the info from it
			$segment = $this->_extract_var($segment);

			// Ex: d becomes delete
			$segment = $this->_expand_shortcuts($segment);
						
			// Maybe a system command?
			if($this->_is_system_command($segment, array('drivers'))):
			
				return $this->out;
			
			endif;
	
			if( ! $this->_load_driver($segment) ):

				// Not a driver?
				// Well, looks like the command could not be
				// understood. Bummer.
				return "unknown command";
			
			else:
				
				// We will go quiety with no errors.
				return;
			
			endif;
		
		elseif( $total_segments == 3 ):

		// -------------------------------------
		// Double Segment Processing
		// -------------------------------------

			$driver_slug = $parts[1];
			
			$segment = $parts[2];

			// We know there has to be a driver.
			// If there isn't throw dem flagz up!			
			if(!$this->_load_driver($driver_slug)):

				if($this->out != ''):
				
					return $this->out;
				
				else:
				
					return "$driver_slug driver not found";
				
				endif;
			
			endif;
			
			// Find the bracketed variable
			$segment = $this->_extract_var($segment);

			$segment = $this->_expand_shortcuts($segment);
			
			// Set the context to the driver. Other functions
			// will set it back if need be.
			$this->context = array( 'ee', $this->driver->driver_slug );
			
			// Could this be a system command?
			if($this->_is_system_command($segment)):
			
				return $this->out;
			
			endif;
						
			// Could very well possibly be a singular command.
			if($this->_is_singular_command( $segment )):
			
				return $this->out;
			
			endif;

			// This is an unknown command. Just write a log
			// entry and get out of here.
			write_log($this->line, "unknown command");
			return "unknown command";
		
		// End Segment Processing
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Is this a Singular Command?
	 *
	 * @access	private
	 * @param	string
	 * @return	void
	 */
	private function _is_singular_command( $segment )
	{
		$call = str_replace(" ", "_", $segment);
		
		// All commands have a prefix so we can use things like list
		// and new without gettin' out shit all wickity-wacked up
		$call = '_comm_'.strtolower($call);
		
		// Check to see if the command exists. Return flase to issue error
		// if it doesn't. Otherwise, run the command.
		if( method_exists($this->driver, $call) ):
		
			// We can return up to 10 variables
			$args_num = count($this->variable);
			
			// Get our vars
			extract($this->variable, EXTR_PREFIX_INVALID, 'arg');
			
			// This may get boring. Sorry. Not very elegant.
			switch($args_num):
				case 0:
					$msg = $this->driver->$call(); break;
				case 1:
					$msg = $this->driver->$call($arg_0); break;
				case 2:
					$msg = $this->driver->$call($arg_0, $arg_1); break;
				case 3:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2); break;
				case 4:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3); break;
				case 5:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3, $arg_4); break;
				case 6:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3, $arg_4, $arg_5); break;
				case 7:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3, $arg_4, $arg_5, $arg_6); break;
				case 8:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3, $arg_4, $arg_5, $arg_6, $arg_7); break;
				case 9:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3, $arg_4, $arg_5, $arg_6, $arg_7, $arg_8); break;
				case 10:
					$msg = $this->driver->$call($arg_0, $arg_1, $arg_2, $arg_3, $arg_4, $arg_5, $arg_6, $arg_7, $arg_8, $arg_9); break;
				default:
					break;
			endswitch;
				
			write_log($this->line, $msg);
			
			$this->out = $msg;
			
			return TRUE;
		
		endif;
		
		return FALSE;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Is This a System Command?
	 *
	 * Checks to see if the input is a system command
	 * and if it is allowed to be run in this spot
	 *
	 * @access	private
	 * @param	string
	 * @param	[array]
	 * @return	mixed
	 */
	private function _is_system_command( $segment, $allowed = array() )
	{
		if( empty($allowed) ):
		
			$allowed = $this->system_commands;
		
		endif;
		
		// Does this command exist and is allowed?
		// If so, run it.
		
		if( in_array($segment, $allowed) ):
		
			$call = 'system_'.$segment;
		
			$this->$call();
			
			return TRUE;
		
		endif;
		
		return FALSE;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * See if something is a variable and return the value if needed
	 *
	 * @access	private
	 * @param	string
	 * @param	obj
	 * @return	string
	 */
	private function _is_variable($segment = '')
	{			
		if( in_array($segment, $this->system_var_methods) ):
		
			$this->context = array('ee');
		
			$this->out = $this->EE->vars->$segment();
			
			return TRUE;
					
		endif;
		
		return FALSE;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Loads a driver
	 *
	 * Checks to see if there is a driver folder before loading
	 * all of the necessary items.
	 *
	 * @access	private
	 * @param	string
	 * @return	mixed
	 */
	private function _load_driver($driver_slug)
	{
		// -------------------------------------
		// Add-on Check
		// -------------------------------------
		// Check to see if this driver actually exists 
		// and also if it is an addon driver
		// This allows us to have addon drivers
		// -------------------------------------
		
		if(is_dir(PATH_THIRD.'trigger/drivers/'.$driver_slug)):
		
			$driver_folder = PATH_THIRD.'trigger/drivers/';
		
		elseif(is_dir(TRIGGER_ADDONS_FOLDER.'/drivers/'.$driver_slug)):
		
			$driver_folder = TRIGGER_ADDONS_FOLDER.'/drivers/';
		
		else:
		
			// Can't find it anywhere.
			return FALSE;
		
		endif;

		// -------------------------------------
		// Set driver folder as constant
		// -------------------------------------

		define('DRIVER_PATH', $driver_folder);

		// -------------------------------------
		// Add as package
		// -------------------------------------
		
		$this->EE->load->remove_package_path(DRIVER_PATH.$driver_slug);
		
		// -------------------------------------
		// Load driver file
		// -------------------------------------
		
		$driver_file = $driver_folder.$driver_slug.'/'.$driver_slug.'.driver.php';
		
		if(file_exists($driver_file)):

			@require_once($driver_file);

			$driver_class = 'Driver_'.$driver_slug;
			
			$this->driver = new $driver_class();
	
		else:
	
			// We can't go on without a driver file
			$this->out = "missing driver file";
			
			return FALSE;
		
		endif;

		// -------------------------------------
		// See if we have commands
		// -------------------------------------
		// Commands are just methods in the
		// driver file.
		// -------------------------------------
		
		if(get_class_methods($this->driver)):
		
			$this->driver->has_commands = TRUE;
		
		else:
			
			$this->driver->has_commands = FALSE;
		
		endif;
	
		// -------------------------------------
		// Load driver language
		// -------------------------------------
		
		$lang_file = $driver_folder.$driver_slug.'/language/'.$this->EE->config->item('deft_lang').'/lang.'.$driver_slug.'.php';
		
		if( ! file_exists($lang_file) ):
		
			// Looks like there is no language file. That's no good!
			
			$error = "no language file found for $driver_slug driver";
		
			write_log($this->line, $error);

			$this->out = $error;
			
			return;
			
		else:
		
			$this->load_trigger_lang($this->driver->driver_slug);
		
		endif;

		// -------------------------------------
		// Load master language
		// -------------------------------------

		$this->EE->load->language('trigger');
								
		// -------------------------------------
		// Set up some class variables
		// -------------------------------------

		$this->driver->driver_name 	= $this->EE->lang->line($this->driver->driver_slug.'.driver_name');
		$this->driver->driver_desc 	= $this->EE->lang->line($this->driver->driver_slug.'.driver_desc');
					
		// -------------------------------------
		// Set driver to driver context position
		// -------------------------------------
		
		$this->context[1] = $driver_slug;
		
		return TRUE;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Find the bracketed variable, and find the
	 * comma-separated variables within.
	 *
	 * @access	public
	 * @param	string
	 * @return 	string
	 */
	private function _extract_var($string)
	{
		// Find the vars between the markers and then explode
		// The values therein into an array
		if(strpos($string, VARS_LEFT) !== FALSE && strpos($string, VARS_RIGHT) !== FALSE):
		
			$open 	= strpos($string, VARS_LEFT, 0) + strlen(VARS_LEFT);
			$close 	= strpos($string, VARS_RIGHT, 0);
			
			$tmp_var = trim(substr($string, $open, $close-$open));
			
			// Even if it's just one, we still use it in
			// sending the values to the function
			$this->variable = explode(VAR_SEP, $tmp_var);
			
			// Trim our vars
			foreach($this->variable as $k => $v):
			
				$this->variable[$k] = trim($v);
			
			endforeach;
			
			// -------------------------------------
			// Process Vars			
			// -------------------------------------	
			// Now we are going to see if these
			// vars are package files or anything
			// else that might be special 
			// -------------------------------------	
			
			$this->EE->load->helper('file');
			
			foreach($this->variable as $key => $val):
			
				if(!trim($val)):
				
					continue;
				
				endif;
			
				// Do we have a package file call?
				$p_call = substr($val, 0, 2);
				
				if($p_call == 'p.'):
				
					$call = substr($val, 2);
					
					// If they didn't put an extension on there, we add in
					if(strpos($call, '.html') === FALSE and strpos($call, '.txt') === FALSE):
					
						$call .= '.html';
					
					endif;
					
					if($file_contents = read_file(APPPATH.'third_party/trigger/packages/'.$call)):
					
						$this->variable[$key] = $file_contents;
						
					else:
					
						// Setting this to something unique so someone doesn't
						// set this as template content or something
						$this->variable[$key] = 'TRIGGER_FILE_READ_ERR';
					
					endif;
				
				endif;
			
			endforeach;
					
			// Get rid of the variable stuff so we can just call the command
			return trim(str_replace(VARS_LEFT.$tmp_var.VARS_RIGHT, '', $string));
		
		endif;
		
		return $string;
	}

	// --------------------------------------------------------------------------

	/**
	 * Expand Shortcuts
	 *
	 * Shortcuts are:
	 *
	 * l => list
	 * d => delete
	 * da => delete all
	 * n => new
	 */	
	private function _expand_shortcuts($segment)
	{
		switch($segment):
			case 'l':
				return 'list';
				break;
			case 'd':
				return 'delete';
				break;
			case 'da':
				return 'delete all';
				break;
			case 'n':
				return 'new';
				break;
			default:
				return $segment;
		endswitch;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Output a response by bypassing the control panel
	 *
	 * @access	private
	 * @param	string
	 * @return	void
	 */	
	function output_response( $output )
	{
		$this->EE->output->enable_profiler(FALSE);

		if ($this->EE->config->item('send_headers') == 'y'):
		
			@header('Content-Type: text/html; charset=UTF-8');	
		
		endif;
		
		if(trim($output) == ''):
		
			exit($this->output_context());
		
		else:

			exit($output."\n".$this->output_context());
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Outputs the context in the correct format
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	function output_context()
	{
		$output = null;
		
		// Just root if none provided
		if( empty($this->context) ):
		
			$this->context = array('ee');
		
		endif;
		
		// Output the context
		foreach( $this->context as $cont ):
		
			$output .= $cont . " : ";
		
		endforeach;

		return $output;
	}
	
	/**
	 * Loads a trigger language file
	 *
	 * @access	public
	 * @param	string - driver slug
	 * @return	bool
	 */
	function load_trigger_lang($driver_slug)
	{
		$this->EE->load->library('security');
		
		if(isset($this->EE->session->userdata['language']) and $this->EE->session->userdata['language'] != ''):
		
			$user_lang = $this->EE->session->userdata['language'];
		
		else:
		
			if ($this->EE->input->cookie('language')):
			
				$user_lang = $this->EE->input->cookie('language');
			
			elseif ($this->EE->config->item('deft_lang') != ''):
			
				$user_lang = $this->EE->config->item('deft_lang');
			
			else:
			
				$user_lang = 'english';
			
			endif;
			
		endif;

		$deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
		
		$user_lang = $this->EE->security->sanitize_filename($user_lang);
	
		$this->EE->lang->load($driver_slug, $user_lang, FALSE, TRUE, DRIVER_PATH.$driver_slug.'/');
	}

	// --------------------------------------------------------------------------
	// System Commands	
	// --------------------------------------------------------------------------	
	
	/**
	 * Drivers
	 *
	 * Lists out the drivers that are installed
	 */
	public function system_drivers()
	{
		// Load up the drivers directory and display them.
		// Not that hard.
		
		$this->EE->load->helper('directory');

		$drivers = directory_map(PATH_THIRD.'trigger/drivers/', 1);

		$out = TRIGGER_BUFFER."\n";
		
		foreach($drivers as $driver):
		
			$out .= $driver."\n";
		
		endforeach;
		
		$this->out = $out.TRIGGER_BUFFER;
	}

}

/* Trigger.php */
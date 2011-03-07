<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Sequences Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_logs
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "logs";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear the logs
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_clear()
	{
		clear_logs();
		
		return trigger_lang('logs_cleared');
	}	

}

/* End of file logs.driver.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Templates Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_templates
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "templates";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
		
		// -------------------------------------
		// Load the Channel Template API
		// -------------------------------------
		// We're using the API for this dealio
		// -------------------------------------
		
		$this->EE->load->library( 'Api' );
		
		$this->EE->api->instantiate( 'template_structure' );
	}

	// --------------------------------------------------------------------------

	/**
	 * Sync Templates
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_sync()
	{
		// -------------------------------------
		// Pre-sync Checks
		// -------------------------------------
	
		if ( $this->EE->config->item('save_tmpl_files') != 'y' ):
		
			return trigger_lang('no_saved_as_files');
		
		endif;

		if ( $this->EE->config->item('tmpl_file_basepath') == '' ):
		
			return trigger_lang('basepath_not_set');
		
		endif;
		
		$this->EE->load->library('api/Sync');
		
		$this->EE->sync->sync_all();
		
		return trigger_lang('templates_synced');
	}

}

/* End of file templates.driver.php */
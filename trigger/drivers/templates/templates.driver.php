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

	// --------------------------------------------------------------------------

	/**
	 * Set templates to be allowed as files
	 */	
	function _comm_allow_as_files()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates'))
		{
			return "no";
		}
		
		if($this->_change_preference('save_tmpl_files_options', 'y')):
		
			return "templates can be saved as files now";
		
		else:
		
			return "templates can already be saved as files";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set templates to be allowed as files
	 */	
	function _comm_disallow_as_files()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates'))
		{
			return "no";
		}
		
		if($this->_change_preference('save_tmpl_files_options', 'n')):
		
			return "templates cannot be saved as files now";
		
		else:
		
			return "templates are already not able to be saved as files";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the templates base
	 */	
	function _comm_set_base($base)
	{
		if(!$base):
		
			return "no basepath provided";
		
		endif;
	
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		$config = array('tmpl_file_basepath' => $base);

		$this->EE->config->update_site_prefs($config);
		
		return "template basepath set";
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Change a template preference
	 */
	function _change_preference( $item, $new_status )
	{
		$current_status = $this->EE->config->item($item);
	
		if( $current_status == $new_status ):
		
			return FALSE;
		
		endif;

		$config = array($item => $new_status);

		$this->EE->config->update_site_prefs($config);
		
		return TRUE;
	}

}

/* End of file templates.driver.php */
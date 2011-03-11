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
	 * New Template
	 *
	 * Creates a template and sometimes groups.
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */	
	public function _comm_new($template_name, $group = '', $template_data = '', $template_as_file = 'n', $template_type = 'webpage')
	{
		// We need a template name
		if(!$template_name):
		
			return "no template name provided";
		
		endif;
	
		// If we have a template read error, then we need to get
		// out of here:
		if($template_data == 'TRIGGER_FILE_READ_ERR'):
		
			return "error reading template file";
		
		endif;
		
		$insert_data['site_id'] 			= $this->EE->config->item('site_id');
		$insert_data['template_name']		= $template_name;
		$insert_data['save_template_file']	= $template_as_file;
		$insert_data['template_data'] 		= $template_data;
		$insert_data['template_type'] 		= $template_type;
		
		// -------------------------------------		
		// Template Group Processing
		// -------------------------------------		
		
		// An option is to pass a numeric value for the group.
		if(is_numeric($group)):
		
			// See if the group exists
			$query = $this->EE->db->limit(1)->get_where('template_groups', array('group_id' => $group));

			if($query->num_rows() == 0):
			
				return "unable to find template group";
			
			endif;

			$insert_data['group_id'] 	= $group;
			
		elseif(!$group):
		
			// They have passed us a blank value. We are now going
			// to use the default group
			$query = $this->EE->db->limit(1)->get_where('template_groups', array('is_site_default' => 'y'));
			
			if($query->num_rows() == 0):
			
				return "unable to find default template group";
			
			endif;
			
			$row = $query->row();
			$insert_data['group_id'] 	= $row->group_id;
			
		else:

			// This must be a name
			$group = strtolower($group);
			$query = $this->EE->db->limit(1)->get_where('template_groups', array('group_name' => $group));
		
			if($query->num_rows() == 0):
			
				// Normally we'd just give up, but we're better
				// than that. This time, we are going to create the group.
				// Hardcore. I just spent a whole comment line on this word.
				$this->EE->load->model('template_model');
				
				$group_data['is_site_default']		= 'n';
				$group_data['group_name']			= strtolower($group);
				$group_data['site_id']				= $this->EE->config->item('site_id');
				
				$insert_data['group_id'] = $this->EE->template_model->create_group($group_data);
				
			else:

				$row = $query->row();
				$insert_data['group_id'] 	= $row->group_id;
			
			endif;

		endif;

		// Damn. We're ready. Do it!
		if(!$this->EE->db->insert('templates', $insert_data)):
		
			return "error creating template";
		
		else:
		
			return "template created";
		
		endif;
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
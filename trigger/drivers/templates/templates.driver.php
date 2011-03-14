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
	 * @param	string - group_name/template, group_id/template or just template
	 * @param	[string] - data to put put into the template
	 * @param	[string] - template type
	 * @param	[string] - save template as file? y/n
	 * @return	string
	 */	
	public function _comm_new($template_group_name, $template_data = '', $template_type = 'webpage', $template_as_file = 'n')
	{
		// We need a template name
		if(!$template_group_name):
		
			return "no template name provided";
		
		endif;
	
		// If we have a template read error, then we need to get
		// out of here:
		if($template_data == 'TRIGGER_FILE_READ_ERR'):
		
			return "error reading template file";
		
		endif;
		
		// Separate group and name
		$pieces = explode('/', $template_group_name);
		
		if(count($pieces)==1):
		
			// No group name provided
			$template_name = trim($template_group_name);
			$group = FALSE;
		
		else:
		
			$template_name = $pieces[1];
			$group = $pieces[0];
		
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
		
			// They have not specified a group. We are now going
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
	 * Delete a template
	 */
	public function _comm_delete($template, $group)
	{
		if(!$group):
		
			return "no group provided";
		
		endif;

		if(!$template):
		
			return "no template data provided";
		
		endif;
		
		// Make sure the group exists and get the id
		$query = $this->EE->db->where('group_name', strtolower($group))->get('template_groups');
		
		if($query->num_rows()==0):
		
			return "group not found";
		
		endif;
		
		$row = $query->row();
		$group_id = $row->group_id;
		
		// We can either take an id or a string
		if(is_numeric($template)):
		
			$check = $this->EE->db->where('template_id', $template)->get('templates');
			
			if($check->num_rows()==0):
			
				return "template not found";
			
			endif;
			
			$this->EE->db->where('template_id', $template);
		
		else:
			
			$template = strtolower($template);
		
			$check = $this->EE->db
								->where('template_name', $template)
								->where('group_id', $group_id)
								->get('templates');
			
			if($check->num_rows()==0):
			
				return "template not found";
			
			endif;
			
			$this->EE->db	
					->where('group_id', $group_id)
					->where('template_name', $template);

		endif;
		
		$this->EE->db->delete('templates');
		
		return "template deleted";
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
		
		if($this->_change_preference('save_tmpl_files', 'y')):
		
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
		
		if($this->_change_preference('save_tmpl_files', 'n')):
		
			return "templates cannot be saved as files now";
		
		else:
		
			return "templates are already not able to be saved as files";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable Strict URLS
	 */	
	function _comm_enable_strict_urls()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates'))
		{
			return "no";
		}
		
		if($this->_change_preference('strict_urls', 'y')):
		
			return "strict urls enabled";
		
		else:
		
			return "strict urls are already enabled";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable Strict URLS
	 */	
	function _comm_disable_strict_urls()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates'))
		{
			return "no";
		}
		
		if($this->_change_preference('strict_urls', 'n')):
		
			return "strict urls disabled";
		
		else:
		
			return "strict urls are already disabled";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Save Template Revisions
	 */	
	function _comm_save_template_revs()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates'))
		{
			return "no";
		}
		
		if($this->_change_preference('save_tmpl_revisions', 'y')):
		
			return "now saving template revisions";
		
		else:
		
			return "already saving template revisions";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Save Template Revisions
	 */	
	function _comm_dont_save_template_revs()
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates'))
		{
			return "no";
		}
		
		if($this->_change_preference('save_tmpl_revisions', 'n')):
		
			return "now not saving template revisions";
		
		else:
		
			return "already not saving template revisions";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the templates base
	 */	
	function _comm_max_number_of_revs($number_of_revs)
	{
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		if(!$number_of_revs):
		
			return "no max number provided";
		
		endif;	

		$config = array('max_tmpl_revisions' => $number_of_revs);

		$this->EE->config->update_site_prefs($config);
		
		return "max number of revisions set to $number_of_revs";
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
	function _change_preference($item, $new_status)
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
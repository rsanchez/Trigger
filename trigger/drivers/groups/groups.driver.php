<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Groups Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_groups
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "groups";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();		
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * New Group
	 *
	 * Creates a group and a 
	 *
	 * @access	public
	 * @param	string - name of the group
	 * @param	[string] - default group y/n
	 * @return	string
	 */	
	public function _comm_new($group_name, $is_site_default='n')
	{
		if(!$group_name):
		
			return "no group name provided";
		
		endif;
		
		// Does it exist already?
		$query = $this->EE->db->limit(1)->get_where('template_groups', array('group_name' => $group_name));

		if($query->num_rows() == 1):
		
			return "template group already exists";
		
		endif;
		
		// Default the is site default to no
		if($is_site_default != 'n' and $is_site_default != 'y'):
		
			$is_site_default = 'n';
		
		endif;
		
		// Create the group
		$group_data['is_site_default'] = $is_site_default;
		$group_data['site_id'] = $this->EE->config->item('site_id');
		$group_data['group_name'] = $group_name;
		
		$this->EE->load->model('template_model');
	
		$group_id = $this->EE->template_model->create_group($group_data);
	
		// Create the index template
		$template_data['site_id'] 				= $this->EE->config->item('site_id');
		$template_data['template_name']			= 'index';
		$template_data['save_template_file']	= 'n';
		$template_data['template_data'] 		= '';
		$template_data['template_type'] 		= 'webpage';
		$template_data['group_id'] 				= $group_id;
		
		if(!$this->EE->db->insert('templates', $template_data)):
		
			return "group created. error creating template";
		
		else:
		
			return "group and index template created";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete a group
	 */
	public function _comm_delete($group)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;

		return $this->_delete_group($group);
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete a group function used by a few
	 * of the group functions
	 */
	function _delete_group($group)
	{
		if(!$group):
		
			return "no group provided";
		
		endif;

		// Get the group ID
		$query = $this->EE->db->limit(1)->where('group_name', $group)->get('template_groups');
		
		if($query->num_rows() == 0):
		
			return "group not found";
		
		endif;
		
		$row = $query->row();
		$group_id = $row->group_id;
							
		// Delete the group folder and files if we need to
		if($this->EE->config->item('save_tmpl_files') == 'y' and $this->EE->config->item('tmpl_file_basepath') != ''):
		
			$this->EE->load->library('api/Templates');
		
			$this->EE->templates->delete_group_folder($group);
		
		endif;
		
		// Remove template stuff from the misc places.
		$query = $this->EE->db->select('template_id')->where('group_id', $group_id)->get('templates');
		
		$num_of_deleted_templates = 0;
		
		if ($query->num_rows() > 0):
		
			// Revision Tracker shit is the first to go.
			foreach ($query->result() as $row):
			
				$this->EE->db->or_where('item_id', $row->template_id);
			
			endforeach;
			$this->EE->db->delete('revision_tracker');
			
			// No access stuff. Keeping things nice and tidy.
			foreach ($query->result() as $row):
			
				$this->EE->db->or_where('template_id', $row->template_id);
			
			endforeach;
			$this->EE->db->delete('template_no_access');
			
			// Delete templates from the database
			$this->EE->db->where('group_id', $group_id)->delete('templates');
			
			$num_of_deleted_templates = $this->EE->db->affected_rows();
	
		endif;

		// Delete groups from the database. Goodbye!
		$this->EE->db->where('group_id', $group_id)->delete('template_groups');
					
		return "$group group and ".$num_of_deleted_templates." templates deleted";
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * List the groups
	 */
	public function _comm_list()
	{
		// Get our snippets
		$db_obj = $this->EE->db->where('site_id', $this->EE->config->item('site_id'))->get('template_groups');
		
		$total = $db_obj->num_rows();
		
		if($total == 0):
		
			return "no groups found";
		
		endif;
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach($db_obj->result() as $group):
		
			$out .= $group->group_name."\n";
		
		endforeach;
		
		return $out .= TRIGGER_BUFFER;
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete all the groups
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_delete_all()
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;

		$query = $this->EE->db->where('site_id', $this->EE->config->item('site_id'))->get('template_groups');
	
		if($query->num_rows() == 0):
		
			return "no groups";
		
		endif;
	
		// Go through the groups and delete all the templates
		$count = 0;	
		foreach($query->result() as $group):
			
			$this->_delete_group(trim($group->group_name));
			$count++;
			
		endforeach;
		
		return "$count groups deleted";
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Set a group as the default group
	 *
	 * @access	public
	 * @param	string - the group you want to set as default
	 * @return	string
	 */	
	public function _comm_set_default($group)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;

		// Find the group's ID
		$query = $this->EE->db->limit(1)->where('group_name', $group)->get('template_groups');
		
		if($query->num_rows() == 0):
		
			return "group not found";
		
		endif;
		
		$row = $query->row();
		$group_id = $row->group_id;

		// Set all of the groups as not the site default
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$this->EE->db->update('template_groups', array('is_site_default' => 'n'));
		
		// Set our new site
		$this->EE->db->where('group_id', $group_id)->limit(1);
		$this->EE->db->update('template_groups', array('is_site_default' => 'y'));
		
		return "$group set as site default group";
	}
}

/* End of file groups.driver.php */
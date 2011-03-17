<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Globals Templates Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_globals
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "globals";

	// --------------------------------------------------------------------------
	
	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * List snippets
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_list()
	{
		// Get our snippets
		$db_obj = $this->EE->db->where('site_id', $this->EE->config->item('site_id'))->get('global_variables');
		
		$total = $db_obj->num_rows();
		
		if($total == 0):
		
			return $this->EE->lang->line('globals.no_variables');
		
		endif;
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach($db_obj->result() as $variable):
		
			$out .= $variable->variable_name.' (';
			
			if(trim($variable->variable_data)==''):
			
				$out .= 'NULL';
			
			else:
			
				$out .= $variable->variable_data;
			
			endif;
			
			$out .= ')'."\n";
			
		endforeach;

		$out .= TRIGGER_BUFFER;
		
		return $out;
	}	

	// --------------------------------------------------------------------------

	/**
	 * Create a global variable
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_new($global_name, $global_value = '')
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;
	
		// Check for a global name at least
		if(!$global_name):
		
			return $this->EE->lang->line('globals.no_data');
		
		endif;
		
		$site_id = $this->EE->config->item('site_id');
				
		// Make sure it doesn't exist
		$query = $this->EE->db
						->where('site_id', $site_id)
						->where('variable_name', $global_name)
						->limit(1)
						->get('global_variables');
		
		if($query->num_rows() == 1):
		
			return $this->EE->lang->line('globals.var_already_exits');
		
		endif;
		
		// Add it
		$global_data['site_id']			= $site_id;
		$global_data['variable_name']	= $global_name;
		
		// Do we have a value as well?
		if($global_value):
		
			$global_data['variable_data']	= $global_value;
		
		endif;
		
		if($this->EE->db->insert('global_variables', $global_data)):
		
			return $this->EE->lang->line('globals.global_add_success');
		
		else:
		
			return $this->EE->lang->line('globals.global_add_error');
		
		endif;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Delete a global var
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_delete($global_name)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;

		// Check for data
		if(!$global_name):
		
			return $this->EE->lang->line('globals.no_data');
		
		endif;
		
		$this->EE->db
					->where('site_id', $this->EE->config->item('site_id'))
					->where('variable_name', $global_name)
					->delete('global_variables');
		
		return $this->EE->lang->line('globals.globals_delete_success');
	}

	// --------------------------------------------------------------------------

	/**
	 * Create a global variable
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_set($global_name, $global_value = '')
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;
	
		// Check for the right data
		// We do NOT check for the value because you can set it
		// without a second value to clear it
		if(!$global_name):
		
			return $this->EE->lang->line('globals.no_data');
		
		endif;

		$site_id = $this->EE->config->item('site_id');
		
		// Make sure it exists
		$db_obj = $this->EE->db
						->where('site_id', $site_id)
						->where('variable_name', $global_name)
						->limit(1)
						->get('global_variables');
		
		if($db_obj->num_rows() == 0):
		
			return $this->EE->lang->line('globals.var_not_found');
		
		endif;
		
		// Update it
		$this->EE->db->where('site_id', $site_id)->where('variable_name', $global_name);
		
		$global_data['variable_data']	= $global_value;
		
		if($this->EE->db->update('global_variables', $global_data)):
		
			return $this->EE->lang->line('globals.global_set_success');
		
		else:
		
			return $this->EE->lang->line('globals.global_set_error');
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete all global variables
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
	
		// Delete 'em all
		$this->EE->db
					->where('site_id', $this->EE->config->item('site_id'))
					->delete('global_variables');

		return $this->EE->lang->line('globals.all_globals_deleted');
	}

}

/* End of file globals.driver.php */
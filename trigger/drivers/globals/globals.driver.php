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
		
			return trigger_lang('no_variables');
		
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
	public function _comm_new($var_data)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return trigger_lang('trigger_no_access');
			
		endif;
	
		// Check for data
		if(!$var_data):
		
			return trigger_lang('no_data');
		
		endif;
		
		// We need our site ID
		$site_id = $this->EE->config->item('site_id');

		if( (is_array($var_data) and !isset($var_data[0])) or $var_data == '' ):
		
			return trigger_lang('no_name');
		
		endif;
		
		// Set global name
		if(is_array($var_data)):
		
			$global_name = $var_data[0];
		
		else:
		
			$global_name = $var_data;
		
		endif;
		
		// Make sure it doesn't exist
		$this->EE->db->where('site_id', $site_id)->where('variable_name', $global_name);
		$db_obj = $this->EE->db->limit(1)->get('global_variables');
		
		if($db_obj->num_rows() == 1):
		
			return trigger_lang('var_already_exits');
		
		endif;
		
		// Add it
		$global_data['site_id']			= $site_id;
		$global_data['variable_name']	= $global_name;
		
		// Add data if it was provided
		if(is_array($var_data) and isset($var_data[1])):
		
			$global_data['variable_data']	= trim($var_data[1]);
		
		endif;
		
		if($this->EE->db->insert('global_variables', $global_data)):
		
			return trigger_lang('global_add_success');
		
		else:
		
			return trigger_lang('global_add_error');
		
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

			return trigger_lang('trigger_no_access');
			
		endif;

		// Check for data
		if(!$global_name or is_array($global_name)):
		
			return trigger_lang('no_data');
		
		endif;
		
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$this->EE->db->where('variable_name', $global_name);
		$this->EE->db->delete('global_variables');
		
		return trigger_lang('globals_delete_success');
	}

	// --------------------------------------------------------------------------

	/**
	 * Create a global variable
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_set($var_data)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return trigger_lang('trigger_no_access');
			
		endif;
	
		// Check for the right data
		if(!$var_data or !is_array($var_data) or count($var_data)!=2):
		
			return trigger_lang('no_data');
		
		endif;

		$site_id = $this->EE->config->item('site_id');
		
		// Make sure it exists
		$this->EE->db->where('site_id', $site_id)->where('variable_name', $var_data[0]);
		$db_obj = $this->EE->db->limit(1)->get('global_variables');
		
		if($db_obj->num_rows() == 0):
		
			return trigger_lang('var_not_found');
		
		endif;
		
		// Update it
		$this->EE->db->where('site_id', $site_id)->where('variable_name', $var_data[0]);
		$global_data['variable_data']	= trim($var_data[1]);
		
		if($this->EE->db->update('global_variables', $global_data)):
		
			return trigger_lang('global_set_success');
		
		else:
		
			return trigger_lang('global_set_error');
		
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

			return trigger_lang('trigger_no_access');
			
		endif;
	
		// Delete 'em all
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$this->EE->db->delete('global_variables');

		return trigger_lang('all_globals_deleted');
	}

}

/* End of file globals.driver.php */
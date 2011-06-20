<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Status Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_status
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "status";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->model('status_model');
		
		// -------------------------------------
		// Load the Channel Structure API
		// -------------------------------------
		// We're using the API for this dealio
		// -------------------------------------
		
		$this->EE->load->library('Api');
		
		$this->EE->api->instantiate('channel_structure');
	}

	// --------------------------------------------------------------------------

	/**
	 * Outline
	 *
	 * rename group (old name, new name)
	 * delete group (name)
	 * new status (group/name, [access='all'], [order=auto], [color=''])
	 * delete status (group/name)
	 * grant access (group/name, members groups)
	 * deny access (group/name, members groups)
	 * open access (group/name)
	 */ 

	// --------------------------------------------------------------------------

	/**
	 * List status groups
	 *
	 * @access	param
	 * @return	string
	 */
	public function _comm_list_groups()
	{
		$groups = $this->EE->status_model->get_status_groups();
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach( $groups->result() as $group ):
		
			$out .= $group->group_name."\n";
		
		endforeach;
		
		return $out .= TRIGGER_BUFFER;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Create a status group
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_new_group($group_name)
	{
		if(!$group_name):

			return "no group name provided";
		
		endif;

		// Check to see if this already exists
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$this->EE->db->where('group_name', $group_name);
		
		$obj = $this->EE->db->get('status_groups');
		
		if($obj->num_rows() > 0) return "group name already exists";
		
		// Create the group
		$insert_data = array(
			'site_id' => $this->EE->config->item('site_id'),
			'group_name' => $group_name
		);		
		
		if($this->EE->db->insert('status_groups', $insert_data)):
		
			return "group added successfully";
			
		else:
		
			return "error in adding group";
		
		endif;
	}
	


}

/* End of file status.driver.php */
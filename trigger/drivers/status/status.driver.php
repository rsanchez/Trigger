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
	}

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
	
	// --------------------------------------------------------------------------
	
	/**
	 * Rename a status group
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_rename_group($old_name, $new_name)
	{
		if(!$old_name) return "no old group name provided";

		if(!$new_name) return "no new group name provided";
		
		if( ! $row = is_object($this->does_group_exist($group_name)) ) return $row;

		// Check to see if the new group name already exists
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'))->where('group_name', $new_name);
		$new_obj = $this->EE->db->get('status_groups');
		
		if($new_obj->num_rows() > 0) return "new group name already exists";
		
		// Update the group
		$update_data = array('group_name' => $new_name);		
		
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'))->where('group_id', $row->group_id);
		
		if($this->EE->db->update('status_groups', $update_data)):
		
			return "group renamed successfully";
			
		else:
		
			return "error in renamed group";
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Delete a status group
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_delete_group($group_name)
	{
		if(!$group_name) return "no group name provided";
		
		if( ! $row = is_object($this->does_group_exist($group_name)) ) return $row;
		
		$this->EE->status_model->delete_status_group($row->group_id);
	
		return "status group deleted successfully";	
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Delete a status group
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_new_status($group_status, $highlight = '')
	{
		// Check the group status
		if( ! is_array($check_group_status = $this->check_group_status($group_status)) ) return $check_group_status;

		extract($check_group_status);
		
		if( !  is_object($row = $this->does_group_exist($group_name)) ) return $row;
		
		// Add the status
		$insert_data = array(
			'status'			=> $status_name,
			'site_id'			=> $this->EE->config->item('site_id'),
			'group_id'			=> $row->group_id,
			'status_order' 		=> $this->EE->status_model->get_next_status_order($row->group_id),
			'highlight'			=> $highlight
		);
		
		if($this->EE->db->insert('statuses', $insert_data)):
		
			return "status added successfully";
			
		else:
		
			return "error in adding status";
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Delete a status
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_delete_status($group_status)
	{
		if( ! is_array($check_group_status = $this->check_group_status($group_status)) ) return $check_group_status;
		
		extract($check_group_status);
		
		if( ! is_object($row = $this->does_group_exist($group_name)) ) return $row;
		
		// Add the status
		$insert_data = array(
			'status'			=> $status_name,
			'site_id'			=> $this->EE->config->item('site_id'),
			'group_id'			=> $row->group_id,
			'status_order' 		=> $this->EE->status_model->get_next_status_order($row->group_id),
			'highlight'			=> $highlight
		);
		
		if($this->EE->db->insert('statuses', $insert_data)):
		
			return "status added successfully";
			
		else:
		
			return "error in adding status";
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Delete a status
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_rename_status($group_status, $new_name)
	{
		if(!$new_name) return "no new status name provided";
	
		if( ! is_array($check_group_status = $this->check_group_status($group_status)) ) return $check_group_status;
		
		extract($check_group_status);
		
		if( ! is_object($group = $this->does_group_exist($group_name)) ) return $group;
		
		// Get the status
		$obj = $this->EE->db->limit(1)->where('status', $status_name)->get('statuses');
		
		if($obj->num_rows() == 0) return "status not found";
		
		$status = $obj->row();
		
		// Add the status
		$update_data = array('status' => $new_name);
		
		$this->EE->db->where('status_id', $status->status_id);
		if($this->EE->db->update('statuses', $update_data)):
		
			return "status renamed successfully";
			
		else:
		
			return "error in renaming status";
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Check the group/status pair in the string
	 *
	 * @access	private
	 * @param	string
	 * @return	mixed
	 */
	private function check_group_status($group_status)
	{
		// Separate the groups
		$pieces = explode('/', $group_status);
		
		if(count($pieces) != 2) return "no group name provided";
		
		$group_name = trim($pieces[0]);
		$status_name = trim($pieces[1]);
	
		// We can't have spaces 
		if(!$group_name) return "blank group name not allowed";
		if(!$status_name) return "blank status name not allowed";
		
		return array('group_name'=>$group_name, 'status_name'=>$status_name);
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Does this group exist?
	 *
	 * @access	private
	 * @param	string
	 * @return	mixed
	 */
	private function does_group_exist($group_name)
	{
		// Check to make sure the status group exists
		$this->EE->db->limit(1)->where('site_id', $this->EE->config->item('site_id'))->where('group_name', $group_name);
		$old_obj = $this->EE->db->get('status_groups');
		
		if($old_obj->num_rows() == 0) return "group name not found";
		
		return $old_obj->row();
	}	

}

/* End of file status.driver.php */
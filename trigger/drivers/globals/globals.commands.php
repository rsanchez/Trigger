<?php

class Commands_globals
{

	function Commands_globals()
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
		
		$out = '--------'."\n";
		
		foreach($db_obj->result() as $variable):
		
			$out .= $variable->variable_name.' ('.$variable->variable_data.')'."\n";
			
		endforeach;

		$out .= '--------';
		
		return $out;
	}	

	// --------------------------------------------------------------------------

	/**
	 * Create a snippet
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
		
		// Break up the values
		$vals = explode(",", $var_data);
		
		if(!isset($vals[0])):
		
			return trigger_lang('no_name');
		
		endif;
		
		// Make sure it doesn't exist
		$this->EE->db->where('site_id', $site_id)->where('variable_name', $vals[0]);
		$db_obj = $this->EE->db->limit(1)->get('global_variables');
		
		if($db_obj->num_rows() == 1):
		
			return trigger_lang('var_already_exits');
		
		endif;
		
		// Add it
		$global_data['site_id']			= $site_id;
		$global_data['variable_name']	= $vals[0];
		
		// Add data if it was provided
		if(isset($vals[1])):
		
			$global_data['variable_data']	= trim($vals[1]);
		
		endif;
		
		if($this->EE->db->insert('global_variables', $global_data)):
		
			return trigger_lang('global_add_success');
		
		else:
		
			return trigger_lang('global_add_error');
		
		endif;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Delete a snippet
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_delete($snippet_name)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return trigger_lang('trigger_no_access');
			
		endif;
	
		// Check for data
		if(!$snippet_name):
		
			return trigger_lang('trigger_no_data');
		
		endif;
		
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$this->EE->db->where('snippet_name', $snippet_name);
		$this->EE->db->delete('snippets');
		
		return trigger_lang('snippet_delete_success');
	}
	
}

/* End of file commands.snippets.php */
/* Location: ./trigger/core/drivers/channels/commands.snippets.php */
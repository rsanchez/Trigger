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
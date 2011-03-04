<?php

class Commands_snippets
{

	function Commands_snippets()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * Create a snippet
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_new($snippet_name)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return trigger_lang('trigger_no_access');
			
		endif;
	
		// Check for data
		if(!$snippet_name):
		
			return trigger_lang('no_name');
		
		endif;
		
		// We need our site ID
		$site_id = $this->EE->config->item('site_id');
		
		// Make sure it doesn't exist
		$this->EE->db->where('site_id', $site_id)->where('snippet_name', $snippet_name);
		$db_obj = $this->EE->db->limit(1)->get('snippets');
		
		if($db_obj->num_rows() == 1):
		
			return trigger_lang('snippet_already_exits');
		
		endif;
		
		// Add it
		$snippet_data['snippet_name']		= $snippet_name;
		$snippet_data['site_id']			= $site_id;
		$snippet_data['snippet_contents']	= '';
		
		if($this->EE->db->insert('snippets', $snippet_data)):
		
			return trigger_lang('snippet_add_success');
		
		else:
		
			return trigger_lang('snippet_add_error');
		
		endif;
	}
	
}

/* End of file commands.channels.php */
/* Location: ./trigger/core/drivers/channels/commands.channels.php */
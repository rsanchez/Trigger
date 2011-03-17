<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Snippets Templates Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_snippets
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "snippets";

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
		$db_obj = $this->EE->db->where('site_id', $this->EE->config->item('site_id'))->get('snippets');
		
		$total = $db_obj->num_rows();
		
		if($total == 0):
		
			return $this->EE->lang->line('snippets.no_snippets');
		
		endif;
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach($db_obj->result() as $snippet):
		
			$out .= $snippet->snippet_name."\n";
		
		endforeach;
		
		return $out .= TRIGGER_BUFFER;
	}	

	// --------------------------------------------------------------------------

	/**
	 * Create a snippet
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_new($snippet_name)
	{
		// Check for access
		if ( ! $this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):

			return $this->EE->lang->line('trigger_no_access');
			
		endif;
	
		// Check for data
		if(!$snippet_name):
		
			return $this->EE->lang->line('snippets.no_name');
		
		endif;
		
		// We need our site ID
		$site_id = $this->EE->config->item('site_id');
		
		// Make sure it doesn't exist
		$db_obj = $this->EE->db
					->where('site_id', $site_id)
					->where('snippet_name', $snippet_name)
					->limit(1)
					->get('snippets');
		
		if($db_obj->num_rows() == 1):
		
			return $this->EE->lang->line('snippets.snippet_already_exits');
		
		endif;
		
		// Add it
		$snippet_data['snippet_name']		= $snippet_name;
		$snippet_data['site_id']			= $site_id;
		$snippet_data['snippet_contents']	= '';
		
		if($this->EE->db->insert('snippets', $snippet_data)):
		
			return $this->EE->lang->line('snippets.snippet_add_success');
		
		else:
		
			return $this->EE->lang->line('snippets.snippet_add_error');
		
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

			return $this->EE->lang->line('trigger_no_access');
			
		endif;
	
		// Check for data
		if(!$snippet_name):
		
			return $this->EE->lang->line('snippets.trigger_no_data');
		
		endif;
		
		$this->EE->db
					->where('site_id', $this->EE->config->item('site_id'))
					->where('snippet_name', $snippet_name)
					->delete('snippets');
		
		return $this->EE->lang->line('snippets.snippet_delete_success');
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete all the snippets
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
	
		$this->EE->db
					->where('site_id', $this->EE->config->item('site_id'))
					->delete('snippets');
		
		return $this->EE->lang->line('snippets.all_snippets_deleted');
	}

}

/* End of file snippets.driver.php */
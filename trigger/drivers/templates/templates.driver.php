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

	public $extensions = array('.html', '.feed', '.css', '.js', '.xml');

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
		
		// -------------------------------------
		// Load the Channel Template API
		// -------------------------------------
		// We're using the API for this dealio
		// -------------------------------------
		
		$this->EE->load->library('Api');
		
		$this->EE->api->instantiate('template_structure');

		// This is our homebrew template action lib.
		// It does maaaagical things. Maybe.
		$this->EE->load->library('api/Templates');
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
	
		if ($this->EE->config->item('save_tmpl_files') != 'y'):
		
			return $this->EE->lang->line('templates.no_saved_as_files');
		
		endif;

		if ($this->EE->config->item('tmpl_file_basepath') == ''):
		
			return $this->EE->lang->line('templates.basepath_not_set');
		
		endif;
				
		$this->EE->templates->sync_all();
		
		return $this->EE->lang->line('templates.templates_synced');
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
	public function _comm_new($template_group_name, $template_data = '', $template_type = 'webpage', $template_as_file = '')
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
		
		// Obey the template as file config
		if(!$template_as_file):
		
			$template_as_file = $this->EE->config->item('save_tmpl_files');
		
		endif;
		
		// Last check. You never know
		if($template_as_file != 'y' and $template_as_file != 'n'):
		
			$template_as_file = 'n';
		
		endif;
		
		// TODO: Validate the Template type
		
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
			
		elseif($group === FALSE):
		
			// They have not specified a group. We are now going
			// to use the default group
			$query = $this->EE->db->limit(1)->get_where('template_groups', array('is_site_default' => 'y'));
			
			if($query->num_rows() == 0):
			
				return "no default template group - please specify a group";
			
			endif;
			
			$row = $query->row();
			$insert_data['group_id'] 	= $row->group_id;
			
		else:

			// They must've given us a group name.
			// First of all, does it actuall exist?
			$query = $this->EE->db->limit(1)->get_where('template_groups', array('group_name' => $group));
		
			if($query->num_rows() == 0):
			
				// They named a group that doesn't exist.
				// Normally we'd just give up, but we're better
				// than that. This time, we are going to create the group.
				// Hardcore <-- I just spent a whole comment line on this word.
				$this->EE->load->model('template_model');
				
				$group_data['is_site_default']		= 'n';
				$group_data['group_name']			= $group;
				$group_data['site_id']				= $this->EE->config->item('site_id');
				
				$insert_data['group_id'] = $this->EE->template_model->create_group($group_data);
				
			else:

				// They anmed a group that exists. Get the ID and on
				// with the show.
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
	 *
	 * @access	public
	 * @param	string - group/template name
	 * @return	string
	 */
	public function _comm_delete($template_data)
	{
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->db
				->where('group_id', $group['group_id'])
				->where('template_name', $template['template_name'])
				->delete('templates');
		
		// Are we saving as files? If so, if there are any files
		// Get rid of them.
		if($this->EE->config->item('save_tmpl_files') == 'y'):
		
			$this->EE->templates->delete_template_file($group, $template['template_name'], $template['template_type']);
		
		endif;
		
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
	 * Set the max number of revisions
	 *
	 * @access	public
	 * @param	int
	 * @return	string
	 */	
	public function _comm_max_number_of_revs($number_of_revs)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
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
	 * Set the template base path
	 *
	 * @access	public
	 * @param	int
	 * @return	string
	 */	
	function _comm_set_base($base)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		if(!$base):
		
			return "no basepath provided";
		
		endif;
	
		$config = array('tmpl_file_basepath' => $base);

		$this->EE->config->update_site_prefs($config);
		
		return "template basepath set";
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the template base path
	 *
	 * @access	public
	 * @param	int
	 * @return	string
	 */	
	function _comm_set_404($template_data)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;
		
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$uri = $group['group_name'].'/'.$template['template_name'];

		$config = array('site_404' => $uri);

		$this->EE->config->update_site_prefs($config);
		
		return "404 set to $uri";
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable the cache and set the refresh interval
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	string
	 */	
	function _comm_enable_cache($template_data, $refresh)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// Check for refresh interval
		if(!$refresh):
		
			return "please provide a refresh interval";
		
		endif;
		
		// Check to make sure refresh interval is a number
		if(!is_numeric($refresh)):
		
			return "refresh interval needs to be numeric";
		
		endif;
		
		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('cache'=>'y', 'refresh'=>$refresh);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		return "cache enabled for $uri with a refresh interval of $refresh";
	}

	// --------------------------------------------------------------------------

	/**
	 * Disable the cache and reset the refresh interval to 0
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function _comm_disable_cache($template_data)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('cache'=>'n', 'refresh'=>0);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);
		
		$uri = $group['group_name'].'/'.$template['template_name'];
		
		return "cache disabled for $uri";
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the refresh interval for a template
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	string
	 */	
	function _comm_set_refresh($template_data, $refresh = '')
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// Check for refresh interval
		if(!$refresh):
		
			return "please provide a refresh interval";
		
		endif;
		
		// Check to make sure refresh interval is a number
		if(!is_numeric($refresh)):
		
			return "refresh interval needs to be numeric";
		
		endif;
		
		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('refresh'=>$refresh);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		return "refresh interval for $uri set to $refresh";
	}

	// --------------------------------------------------------------------------

	/**
	 * Allow PHP in a template and set php parsing stage
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	string
	 */	
	function _comm_allow_php($template_data, $stage = 'o')
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// We only need the first letter
		$stage = strtolower($stage{0});
	
		// Check for valid parsing stage
		if($stage != 'o' and $stage != 'i'):
		
			return "please provide a valid parsing stage (input or output)";
		
		endif;
		
		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('allow_php'=>'y', 'php_parse_location'=>$stage);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		($stage == 'o') ? $php_stage = 'output' : $php_stage = 'input' ;
		
		return "php is now allowed for $uri in the $php_stage stage";
	}

	// --------------------------------------------------------------------------

	/**
	 * Disallow PHP in a template
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	string
	 */	
	function _comm_disallow_php($template_data)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('allow_php'=>'n');

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		return "php is now disallowed for $uri";
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the php parse stage for a template
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	string
	 */	
	function _comm_set_php_stage($template_data, $stage = 0)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// We only need the first letter
		$stage = strtolower($stage{0});
	
		// Check for valid parsing stage
		if($stage != 'o' and $stage != 'i'):
		
			return "please provide a valid parsing stage (input or output)";
		
		endif;
		
		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('php_parse_location'=>$stage);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		($stage == 'o') ? $php_stage = 'output' : $php_stage = 'input' ;
		
		return "php parsing stage for $uri now set to $php_stage";
	}

	// --------------------------------------------------------------------------

	/**
	 * Change the type of a template
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _comm_change_type($template_data, $new_type)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;
		
		// Make sure new type is correct
		if(!array_key_exists($new_type, $this->EE->api_template_structure->file_extensions)):
		
			return 'invalid type';
		
		endif;

		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('template_type'=>$new_type);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);
		
		// Rename the file if we are dealing with files
		if($this->EE->config->item('save_tmpl_files') == 'y'):
		
			$this->_rename_template_file($group['group_name'], $template['template_type'], $new_type, $template['template_name'], $template['template_name']);
		
		endif;

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		return "type for $uri set to $new_type";
	}

	// --------------------------------------------------------------------------

	/**
	 * Change the type of a template
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function _comm_rename($template_data, $new_name)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;
		
		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
				
		// See if the template already exists.
		$check = $this->EE->db
							->limit(1)
							->where('template_name', $new_name)
							->where('group_id', $group['group_id'])
							->get('templates');
		

		if($check->num_rows()==1):
		
			return "a template with this name already exists in this group";
		
		endif;	
	
	
		$this->EE->load->model('template_model');

		$data = array('template_name'=>$new_name);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);
		
		// Rename the file if we are dealing with files
		if($this->EE->config->item('save_tmpl_files') == 'y'):
		
			$this->_rename_template_file($group['group_name'], $template['template_type'], $template['template_type'], $template['template_name'], $new_name);
		
		endif;

		$uri = $group['group_name'].'/'.$template['template_name'];
		$new_uri = $group['group_name'].'/'.$new_name;
		
		return "renamed $uri set to $new_name";
	}

	// --------------------------------------------------------------------------

	/**
	 * Reset the hits to 0 for a template
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function _comm_reset_hits($template_data)
	{
		if(!$this->EE->cp->allowed_group('can_access_design') OR ! $this->EE->cp->allowed_group('can_admin_templates')):
		
			return "no";
		
		endif;

		// Parse group/template
		if(is_string($tmp = $this->_separate_template_data($template_data))):
		
			return $tmp;
		
		else:
		
			extract($tmp);
		
		endif;
		
		$this->EE->load->model('template_model');

		$data = array('hits'=>0);

		$this->EE->template_model->update_template_ajax($template['template_id'], $data);

		$uri = $group['group_name'].'/'.$template['template_name'];
		
		return "hits set to 0 for $uri";
	}

	// --------------------------------------------------------------------------

	/**
	 * Separates and sanitizes the group/template data
	 *
	 * @access	private
	 * @param	string
	 * @return	mixed - array on success, string on error
	 */	
	private function _separate_template_data($template_data)
	{
		// Make sure there is 2 bits of data
		$pieces = explode('/', $template_data, 2);
	
		if(count($pieces) != 2):
		
			return (string)"no group provided";
		
		endif;

		$group_name = $pieces[0];
		$template_name = $pieces[1];
		
		// Make sure the group exists
		$check_group = $this->EE->db->limit(1)->get_where('template_groups', array('group_name' => $group_name));
		
		if($check_group->num_rows() == 0):
		
			return (string)"group not found";
		
		endif;
		
		$group = $check_group->row_array();
		
		// Sanitize the template name by going through template
		// extensions and checking to see if that's what's at the
		// end of the name
		/*foreach($this->extensions as $ext):
		
			if(substr($template_name, -1, count($ext)) == $ext):
			
				// How do we turn this negative? I guess we could do
				// it this way??
				$neg = count($ext);
				$neg = $neg-($neg*2);
				
				$template_name = substr($template_name, $neg);
			
			endif;
		
		endforeach;*/
		
		// Make sure the template exists
		$check_tmpl = $this->EE->db
							->limit(1)
							->where('template_name', $template_name)
							->where('group_id', $group['group_id'])
							->get('templates');
		
		if($check_tmpl->num_rows()==0):
		
			return (string)"template not found";
		
		endif;
		
		$template = $check_tmpl->row_array();
		
		// Return array of data
		return array('group'=>$group, 'template'=>$template);
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

	// --------------------------------------------------------------------------

	/**
	 * Rename Template File
	 *
	 * This is a modified function from the template model. This one
	 * just takes into account the fact that the extension may have changed
	 *
	 * @access	public
	 * @return	bool
	 */
	private function _rename_template_file($template_group, $old_template_type, $new_template_type, $old_name, $new_name)
	{
		$new_ext = $this->EE->api_template_structure->file_extensions($new_template_type);
		$old_ext = $this->EE->api_template_structure->file_extensions($old_template_type);
		
		$basepath  = $this->EE->config->slash_item('tmpl_file_basepath');
		$basepath .= $this->EE->config->item('site_short_name');
		$basepath .= '/'.$template_group.'.group';
		
		$existing_path = $basepath.'/'.$old_name.$old_ext;
		
		if(!file_exists($existing_path)):
		
			return FALSE;
		
		endif;
		
		return rename($existing_path, $basepath.'/'.$new_name.$new_ext);
	}

}

/* End of file templates.driver.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Templates
{
	var $templates 				= array();

	var $groups					= array();
	
	var $reserved_names 		= array('act', 'css');

	var $template_directory 	= '';

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library( 'Api' );
				
		$this->EE->api->instantiate( 'template_structure' );
		
		// Get template directory
		
		$basepath = $this->EE->config->slash_item('tmpl_file_basepath').$this->EE->config->item('site_short_name');
		
		$this->template_directory = $basepath;	
	}

	// --------------------------------------------------------------------------

	/**
	 * Syncs all templates
	 */
	public function sync_all()
	{
		// Before shit gets crazy, create a site folder if it doesn't exist.
		
		if(!is_dir($this->template_directory)):
		
			if (!@mkdir($this->template_directory, DIR_WRITE_MODE)):
			
				return FALSE;
			
			endif;
			
			@chmod($this->template_directory, DIR_WRITE_MODE); 
		
		endif;
	
		$this->EE->load->model( 'template_model' );
	
		// -------------------------------------
		// Get all templates & groups in the DB
		// -------------------------------------
		// This merges everything from the db
		// and the filesystem into one array for
		// us to walk through.
		// -------------------------------------
		 
		$this->get_db_templates();
		
		$this->check_against_files();
	
		// -------------------------------------
		// Get groups
		// -------------------------------------

		$groups_db = $this->EE->db->get('template_groups');
		
		$groups_tmp = $groups_db->result_array();
		
		foreach( $groups_tmp as $group ):
		
			$this->groups[$group['group_name']] = $group;
		
		endforeach;
	
		// -------------------------------------
		// Run through templates
		// -------------------------------------
		
		foreach($this->templates as $group => $templates ):

			$group_path = $this->template_directory . '/' . $group . '.group';

			// -------------------------------------
			// Group Folder Logic
			// -------------------------------------
		
			// Does the group exist in DB? If not, create it
			
			if( ! array_key_exists($group, $this->groups) ):
			
				$data = array(
								'group_name'		=> $group,
								'is_site_default'	=> 'n',
								'site_id'			=> $this->EE->config->item('site_id')
							);
			
				$group_id = $this->EE->template_model->create_group($data);
				
			else:
			
				$group_id = $this->groups[$group]['group_id'];
			
			endif;
			
			// Does the group exist in the file system? If not, create it
			
			$this->create_group_folder($group);
			
			// -------------------------------------
			// Template Logic
			// -------------------------------------
			
			foreach( $templates as $template_slug => $template_data ):
			
				$template_path = 	$group_path . '/' . 
									$template_slug . 
									$this->EE->api_template_structure->file_extensions($template_data['template_type']);
									
				// -------------------------------------
				// Create template file if it doesn't
				// exist in the filesystem. The group
				// folder should already have been created
				// -------------------------------------	
				
				if(!file_exists($template_path)):	
				
					if($fp = @fopen($template_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)):
					
						flock($fp, LOCK_EX);
						fwrite($fp, $template_data['template_data']);
						flock($fp, LOCK_UN);
						fclose($fp);
											
						@chmod($template_path, FILE_WRITE_MODE);
					
					endif;
				
				endif;

				// -------------------------------------
				// Verify Template with DB
				// -------------------------------------				
				// Does the template exist in the database?
				// -------------------------------------				

				// We check it by seeing if the ID is set
				if( isset( $template_data['template_id'] ) ):
				
					// It's all good, we just want the ID
					$template_id = $template_data['template_id'];
				
				else:
					
					// Looks like it does NOT exist in the database!
					// We need to create the db entry.
					$tmp_data = array(
						'site_id'				=> $this->EE->config->item('site_id'),
						'group_id'				=> $group_id,
						'template_name'			=> $template_slug,
						'template_type'			=> $template_data['template_type'],
						'save_template_file'	=> 'y',
						'template_data'			=> $template_data['template_data'],
						'edit_date'				=> $this->EE->localize->now
					);
					
					$template_id = $this->EE->template_model->create_template( $tmp_data );
					
				endif;
				// At the end of this we have our template_id
				
				// -------------------------------------
				// Update database with file content
				// -------------------------------------				
				// Make sure the template in the database
				// is up to date with the database version
				// -------------------------------------				
				
				if( $template_data['from'] == 'db' ):
				
					$update_data['template_data'] = file_get_contents($template_path);
								
					$this->EE->db->where('template_id', $template_id)->update('templates', $update_data);

				endif;
			
			endforeach;
			
		endforeach;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Create group folder
	 *
	 * Creates a group folder plus the index.html file
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function create_group_folder( $group )
	{
		$group_path = $this->template_directory . '/' . $group . '.group';

		// If the file exists, then what's the deal? Let's get out of here.
	
		if( @is_dir( $group_path ) ):
			
			return FALSE;		
			
		endif;
		
		// Add the template group folder
		
		if ( ! is_dir($group_path))
		{
			if ( ! @mkdir($group_path, DIR_WRITE_MODE))
			{
				return FALSE;
			}
			@chmod($group_path, DIR_WRITE_MODE); 
		}
		
		// Create an empty index.html file
		
		$index_file = 'index'.$this->EE->api_template_structure->file_extensions('webpage');
		
		if ( ! $fp = @fopen($group_path . '/' . $index_file, FOPEN_WRITE_CREATE_DESTRUCTIVE))
		{
			return FALSE;
		}
		else
		{
			flock($fp, LOCK_EX);
			fwrite($fp, '');
			flock($fp, LOCK_UN);
			fclose($fp);
			
			@chmod($group_path . '/' . $index_file, FILE_WRITE_MODE); 
		}

		return TRUE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Add DB templates to array
	 *
	 * @access	public
	 * @return	void
	 */
	public function get_db_templates()
	{
		$this->EE->db->select(array('group_name', 'template_name', 'template_id', 'template_type', 'save_template_file', 'template_data'));
		$this->EE->db->join('template_groups', 'template_groups.group_id = templates.group_id');
		$this->EE->db->where('templates.site_id', $this->EE->config->item('site_id'));
		$this->EE->db->order_by('group_name, template_name', 'ASC');
		
		$query = $this->EE->db->get('templates');
		
		$db = $query->result();
		
		// -------------------------------------
		// Create assoc array of templates/groups
		// -------------------------------------
		
		foreach( $db as $template ):

			$this->templates[$template->group_name][$template->template_name] = 
			array(
				'template_type' 		=> $template->template_type,
				'save_template_file'	=> $template->save_template_file,
				'template_id'			=> $template->template_id,
				'template_data'			=> $template->template_data,
				'from'					=> 'db'
			);
		
		endforeach;		
	}

	// --------------------------------------------------------------------------
	
	function check_against_files()
	{
		$this->EE->load->helper('directory');
		
		$files = directory_map($this->template_directory, 0, 1);

		if ($files !== FALSE)
		{
			foreach ($files as $group => $templates)
			{
				// -------------------------------------
				// Process Group
				// -------------------------------------

				// If this is not a group, then forget about it
			
				if (substr($group, -6) != '.group')
				{
					continue;
				}

				// Remove the .group from our $group variable

				$group_name = substr($group, 0, -6);

				if ( ! preg_match("#^[a-zA-Z0-9_\-]+$#i", $group_name))
				{
					continue;
				}
				
				// If we don't have a array key for this, make it
				
				if( !array_key_exists($group_name, $this->templates) ):
				
					$this->templates[$group_name] = array();
				
				endif;

				// -------------------------------------
				// Process Templates
				// -------------------------------------
			
				foreach( $templates as $template ):
				
					// No subdirectories
					
					if(is_array($template))
					{
						continue;
					}
					
					// None of those dumb .. and . files
					
					if(strrpos($template, '.') == FALSE)
					{
						continue;
					}
					
					// Get the extension
					
					$ext = strtolower(ltrim(strrchr($template, '.'), '.')); 
							
					// We only want approved file extensions
						
					if (!in_array('.'.$ext, $this->EE->api_template_structure->file_extensions) )
					{
						continue;
					}

					$ext_length = strlen($ext)+1;
					
					$template_name = substr($template, 0, -$ext_length);
					
					$template_type = array_search('.'.$ext, $this->EE->api_template_structure->file_extensions);
					
					// Do we already have it? I don't know, but the following code does.

					if( isset($this->templates[$group_name][$template_name]) )
					{
						continue;
					}
					
					// Is the URL safe if we're going to use it?

					if ( ! $this->EE->api->is_url_safe($template_name))
					{
						continue;
					}
					
					// Save our template data

					$this->templates[$group_name][$template_name] = 
					array(
						'template_type' 		=> $template_type,
						'save_template_file'	=> 'y',
						'template_data'			=> file_get_contents($this->template_directory.'/'.$group.'/'.$template),
						'from'					=> 'files'
					);
				
				endforeach;
			}					
		}
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Delete a template file
	 *
	 * @access	public
	 * @param	string - name of the group
	 * @param	string - template name
	 * @param	string - template type
	 * @return	void
	 */
	public function delete_template_file($group_name, $template_name, $template_type = 'webpage')
	{
		$ext = $this->EE->api_template_structure->file_extensions($template_type);
		$group_folder = $group_name.'.group';
	
		if(is_file($this->template_directory.'/'.$group_folder.'/'.$template_name.$ext)):
		
			@unlink($this->template_directory.'/'.$group_folder.'/'.$template_name.$ext);
		
		endif;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Delete a group folder
	 *
	 * @access	public
	 * @param	string - name of the group
	 * @return	void
	 */
	public function delete_group_folder($group_name)
	{
		$group_folder = $group_name.'.group';
	
		echo $this->template_directory.'/'.$group_folder;
		die();
	
		if(is_dir($this->template_directory.'/'.$group_folder)):
		
			@rmdir($this->template_directory.'/'.$group_folder);
		
		endif;
	}
}

/* End of file Templates.php */
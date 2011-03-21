<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Site Driver
 *
 * @package		Trigger
 * @author		Adam Fairholm (Green Egg Media)
 * @copyright	Copyright (c) 2010 - 2011, Green Egg Media
 * @license		
 * @link		
 */
class Driver_site
{
	public $driver_version		= "0.9 Beta";
	
	public $driver_slug			= "site";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site online
	 */
	function _comm_online()
	{
		if( $this->_change_preference('is_system_on', 'y') ):
		
			return $this->EE->lang->line('site.site_online');
		
		else:
		
			return $this->EE->lang->line('site.site_already_online');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site offline
	 */
	function _comm_offline()
	{
		if( $this->_change_preference('is_system_on', 'n') ):
		
			return $this->EE->lang->line('site.site_offline');
		
		else:
		
			return $this->EE->lang->line('site.site_already_offline');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable the site profiler
	 */
	function _comm_enable_op()
	{
		if( $this->_change_preference('show_profiler', 'y') ):
		
			return $this->EE->lang->line('site.profiler_enabled');
		
		else:
		
			return $this->EE->lang->line('site.profiler_already_enabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable the site profiler
	 */
	function _comm_disable_op()
	{
		if( $this->_change_preference('show_profiler', 'n') ):
		
			return $this->EE->lang->line('site.profiler_disabled');
		
		else:
		
			return $this->EE->lang->line('site.profiler_already_disabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable template debugging
	 */
	function _comm_enable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'y') ):
		
			return $this->EE->lang->line('site.templ_debug_enabled');
		
		else:
		
			return $this->EE->lang->line('site.templ_debug_already_enabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable template debugging
	 */
	function _comm_disable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'n') ):
		
			return $this->EE->lang->line('site.templ_debug_disabled');
		
		else:
		
			return $this->EE->lang->line('site.templ_debug_already_disabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Set the debug level
	 */
	function _comm_set_debug($debug_level)
	{
		// Make sure the value is cool
	
		$valid_levels = array('1', '2', '3');
		
		if(!is_numeric($debug_level) or !in_array($debug_level, $valid_levels)):
		
			return "invalid level";
		
		endif;
	
		if( $this->_change_preference('debug', $debug_level) ):
		
			return "debug level set to $debug_level";
		
		else:
		
			return "debug level already set to $debug_level";
	
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear all the cache files
	 */
	function _comm_clear_cache()
	{
		$this->_cache_clear( 'all' );

		return $this->EE->lang->line('site.cache_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear page cache files
	 */
	function _comm_clear_page_cache()
	{
		$this->_cache_clear( 'page' );

		return $this->EE->lang->line('site.cache_page_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear database cache files
	 */
	function _comm_clear_db_cache()
	{
		$this->_cache_clear( 'db' );

		return $this->EE->lang->line('site.cache_db_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear relationship cache files
	 */
	function _comm_clear_rel_cache()
	{
		$this->_cache_clear( 'relationships' );

		return $this->EE->lang->line('site.cache_rel_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear tag cache files
	 */
	function _comm_clear_tag_cache()
	{
		$this->_cache_clear( 'tag' );

		return $this->EE->lang->line('site.cache_tag_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the site name
	 */
	function _comm_set_site_name($site_name)
	{
		if(!$site_name){
			
			return "invalid site name";
		}
	
		$this->_change_preference('site_name', $site_name);

		return "site name has been set to $site_name";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the license number
	 */
	function _comm_set_license($license_num)
	{
		if(!$license_num){
			
			return "invalid license number";
		}
		
		$this->_change_preference('license_number', $license_num);

		return "site license number has been set to $license_num";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the site index
	 */
	function _comm_set_index($index_file)
	{
		// Note: index can be blank
		if($license_num == 'blank'){
			
			$index_file = '';
		}
			
		$this->_change_preference('site_index', $index_file);
		
		if(!$index_file){
			return "site index page has been set to blank";			
		}

		return "site index page has been set to $index_file";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the root URL
	 */
	function _comm_set_root($root_url)
	{
		if(!$root_url){
			
			return "invalid root url";
		}
		
		$this->_change_preference('site_url', $root_url);

		return "site root url has been set to $root_url";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the control panel index
	 */
	function _comm_set_cp_index($url)
	{
		if(!$url){
			
			return "invalid url";
		}
		
		$this->_change_preference('cp_url', $url);

		return "site cp index has been set to $url";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the theme URL
	 */
	function _comm_set_theme_url($url)
	{
		if(!$url){
			
			return "invalid url";
		}
		
		$this->_change_preference('theme_folder_url', $url);

		return "site theme folder url has been set to $url";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the theme path
	 */
	function _comm_set_theme_path($path)
	{
		if(!$path){
			
			return "invalid path";
		}
		
		$this->_change_preference('theme_folder_path', $path);

		return "site theme path has been set to $path";		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Does the actual clearing of the cache
	 */
	function _cache_clear( $type )
	{
		if ( ! $this->EE->cp->allowed_group('can_access_tools') OR ! $this->EE->cp->allowed_group('can_access_data')):
		
			return trigger_lang('trigger_no_access');
		
		endif;

		$this->EE->functions->clear_caching($type, '', TRUE);		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Change a site preference.
	 *
	 * @access	private
	 * @param	string - param key
	 * @param	string - the new preference value
	 * @param	bool - should we check to see if the value is the same as the existing one?
	 * @return	bool
	 */
	private function _change_preference( $item, $new_status, $check_value = TRUE )
	{
		if($check_value){
			$current_status = $this->EE->config->item($item);
		
			if( $current_status == $new_status ){
			
				return FALSE;
			}
		}

		$config = array($item => $new_status);

		$this->EE->config->update_site_prefs($config);
		
		return TRUE;
	}

}

/* End of file site.driver.php */
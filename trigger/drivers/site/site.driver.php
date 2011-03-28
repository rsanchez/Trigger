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
	 * Set the site default language
	 */
	function _comm_set_lang($language)
	{
		if(!$language){
			
			return "invalid language";
		}
		
		$this->_change_preference('deft_lang', $language);

		return "site default language has been set to $language";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the site default XML language
	 */
	function _comm_set_xml_lang($language)
	{
		if(!$language or is_numeric($language) or count($language!=2)){
			
			return "invalid language";
		}
		
		$language = strtolower($language);
		
		$this->_change_preference('xml_lang', $language);

		return "site default xml language has been set to $language";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the max cacheable urls
	 */
	function _comm_set_max_cache_urls($max)
	{
		if(!is_numeric($max)){
			
			return "invalid number";
		}
	
		$this->_change_preference('max_caches', $max);

		return "max number of cachable urls has been set to $max";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable new version autocheck
	 */
	function _comm_enable_version_autocheck()
	{
		$this->_change_preference('new_version_check', 'y');

		return "ee will now autocheck for new versions";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable new version autocheck
	 */
	function _comm_disable_version_autocheck()
	{
		$this->_change_preference('new_version_check', 'n');

		return "ee will now not autocheck for new versions";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the docs URL
	 */
	function _comm_set_docs_url($url)
	{
		if(!$url){
			
			return "invalid url";
		}
	
		$this->_change_preference('doc_url', $max);

		return "the documentation url has been set to $url";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the server offset
	 */
	function _comm_set_server_offset($offset)
	{
		if(!is_numeric($offset)){
			
			return "invalid input";
		}
	
		$this->_change_preference('server_offset', $offset);

		return "the server offset has been set to $offset";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set the default time format
	 */
	function _comm_set_time_format($format)
	{
		if($format != 'eu' and $format != 'us'){
			
			return "format must be 'us' or 'eu'";
		}
	
		$this->_change_preference('time_format', $format);

		return "the default time offset has been set to $format";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Daylight savings time on
	 */
	function _comm_dst_on()
	{
		$this->_change_preference('daylight_savings', 'y');

		return "daylight savings time on";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Daylight savings time off
	 */
	function _comm_dst_off()
	{
		$this->_change_preference('daylight_savings', 'm');

		return "daylight savings time off";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable HTTP page headers
	 */
	function _comm_enable_page_headers()
	{
		$this->_change_preference('send_headers', 'y');

		return "http page headers are now enabled";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Disable HTTP page headers
	 */
	function _comm_disable_page_headers()
	{
		$this->_change_preference('send_headers', 'n');

		return "http page headers are now disabled";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable GZIP Output
	 */
	function _comm_enable_gzip()
	{
		$this->_change_preference('gzip_output', 'y');

		return "gzip output is now enabled";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable GZIP Output
	 */
	function _comm_disable_gzip()
	{
		$this->_change_preference('gzip_output', 'n');

		return "gzip ouput is now disabled";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Force URL query strings
	 */
	function _comm_force_query_strings()
	{
		$this->_change_preference('force_query_string', 'y');

		return "force query strings enabled";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Don't force URL query strings
	 */
	function _comm_dont_force_query_strings()
	{
		$this->_change_preference('force_query_string', 'n');

		return "force query strings disabled";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set redirection method
	 */
	function _comm_set_redirection_method($method)
	{
		if($method != 'redirect' and $method != 'refresh'){
			
			return "value must be 'redirect' or 'refresh'";
		}
	
		$this->_change_preference('redirect_method', $method);

		return "the redirection has been set to $method";		
	}

	// --------------------------------------------------------------------------

	/**
	 * Set server time zone
	 */
	function _comm_set_time_zone($zone)
	{
		$zone = strtoupper($zone);
	
		$codes = array('UM12', 'UM11', 'UM10', 'UM95', 'UM9', 'UM8', 'UM7', 'UM6', 'UM5', 'UM45', 'UM4', 'UM35', 'UM3', 'UM2', 'UM1', 'UTC', 'UP1', 'UP2', 'UP3', 'UP35', 'UP4', 'UP45', 'UP5', 'UP55', 'UP575', 'UP6', 'UP65', 'UP7', 'UP8', 'UP875', 'UP9', 'UP95', 'UP10', 'UP105', 'UP11', 'UP115', 'UP12', 'UP1275', 'UP13', 'UP14');
	
		if(!in_array($zone, $codes)){
			
			return "invalid format";
		}
	
		$this->_change_preference('server_timezone', $zone);

		return "the server time zone has been set to $zone";		
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
<?php

/**
 * Trigger Site Driver
 *
 * Contains commands to change site settings and variables
 *
 * @package		Trigger
 * @author		Adam Fairholm (Green Egg Media)
 * @copyright	Copyright (c) 2010 - 2011, Green Egg Media
 * @license		
 * @link		
 */

class Commands_site
{
	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site offline
	 */
	function _comm_offline()
	{
		if( $this->_change_preference('is_system_on', 'n') ):
		
			return trigger_lang('site_offline');
		
		else:
		
			return trigger_lang('site_already_offline');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site online
	 */
	function _comm_online()
	{
		if( $this->_change_preference('is_system_on', 'y') ):
		
			return trigger_lang('site_online');
		
		else:
		
			return trigger_lang('site_already_online');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable the site profiler
	 */
	function _comm_enable_output_profiler()
	{
		if( $this->_change_preference('show_profiler', 'y') ):
		
			return trigger_lang('profiler_enabled');
		
		else:
		
			return trigger_lang('profiler_already_enabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable the site profiler
	 */
	function _comm_disable_output_profiler()
	{
		if( $this->_change_preference('show_profiler', 'n') ):
		
			return trigger_lang('profiler_disabled');
		
		else:
		
			return trigger_lang('profiler_already_disabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable template debugging
	 */
	function _comm_enable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'y') ):
		
			return trigger_lang('templ_debug_enabled');
		
		else:
		
			return trigger_lang('templ_debug_already_enabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable template debugging
	 */
	function _comm_disable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'n') ):
		
			return trigger_lang('templ_debug_disabled');
		
		else:
		
			return trigger_lang('templ_debug_already_disabled');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function _comm_debug_0()
	{
		if( $this->_change_preference('debug', '0') ):
		
			return trigger_lang('debug_set_0');
		
		else:
		
			return trigger_lang('debug_already_set_0');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function _comm_debug_1()
	{
		if( $this->_change_preference('debug', '1') ):
		
			return trigger_lang('debug_set_1');
		
		else:
		
			return trigger_lang('debug_already_set_1');
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function _comm_debug_2()
	{
		if( $this->_change_preference('debug', '2') ):
		
			return trigger_lang('debug_set_2');
		
		else:
		
			return trigger_lang('debug_already_set_2');
	
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear all the cache files
	 */
	function _comm_clear_cache()
	{
		$this->_cache_clear( 'all' );

		return trigger_lang('cache_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear page cache files
	 */
	function _comm_clear_page_cache()
	{
		$this->_cache_clear( 'page' );

		return trigger_lang('cache_page_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear database cache files
	 */
	function _comm_clear_db_cache()
	{
		$this->_cache_clear( 'db' );

		return trigger_lang('cache_db_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear relationship cache files
	 */
	function _comm_clear_rel_cache()
	{
		$this->_cache_clear( 'relationships' );

		return trigger_lang('cache_rel_files_deleted');
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear tag cache files
	 */
	function _comm_clear_tag_cache()
	{
		$this->_cache_clear( 'tag' );

		return trigger_lang('cache_tag_files_deleted');
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
	 * Change a site preference
	 */
	function _change_preference( $item, $new_status )
	{
		$current_status = $this->EE->config->item($item);
	
		if( $current_status == $new_status ):
		
			return FALSE;
		
		endif;

		$config = array($item => $new_status);

		$this->EE->config->update_site_prefs($config);
		
		return TRUE;
	}

}

/* End of file commands.site.php */
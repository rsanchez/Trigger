<?php

/**
 * Trigger Site Driver
 *
 * Contains commands to change site settings and variables
 *
 * @package		Trigger
 * @author		Adam Fairholm (Green Egg Media)
 * @copyright	Copyright (c) 2010 - 2010, Green Egg Media
 * @license		
 * @link		http://trigger.ee
 */

class Commands_site
{
	/**
	 * Driver slug
	 */
	var $driver 			= 'site';
	
	var $lang				= array();

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site offline
	 */
	function offline()
	{
		if( $this->_change_preference('is_system_on', 'n') ):
		
			return $this->lang['site_offline'];
		
		else:
		
			return $this->lang['site_already_offline'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site online
	 */
	function online()
	{
		if( $this->_change_preference('is_system_on', 'y') ):
		
			return $this->lang['site_online'];
		
		else:
		
			return $this->lang['site_already_online'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable the site profiler
	 */
	function enable_output_profiler()
	{
		if( $this->_change_preference('show_profiler', 'y') ):
		
			return $this->lang['profiler_enabled'];
		
		else:
		
			return $this->lang['profiler_already_enabled'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable the site profiler
	 */
	function disable_output_profiler()
	{
		if( $this->_change_preference('show_profiler', 'n') ):
		
			return $this->lang['profiler_disabled'];
		
		else:
		
			return $this->lang['profiler_already_disabled'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable template debugging
	 */
	function enable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'y') ):
		
			return $this->lang['templ_debug_enabled'];
		
		else:
		
			return $this->lang['templ_debug_already_enabled'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable template debugging
	 */
	function disable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'n') ):
		
			return $this->lang['templ_debug_disabled'];
		
		else:
		
			return $this->lang['templ_debug_already_disabled'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function debug_0()
	{
		if( $this->_change_preference('debug', '0') ):
		
			return $this->lang['debug_set_0'];
		
		else:
		
			return $this->lang['debug_already_set_0'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function debug_1()
	{
		if( $this->_change_preference('debug', '1') ):
		
			return $this->lang['debug_set_1'];
		
		else:
		
			return $this->lang['debug_already_set_1'];
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function debug_2()
	{
		if( $this->_change_preference('debug', '2') ):
		
			return $this->lang['debug_set_2'];
		
		else:
		
			return $this->lang['debug_already_set_2'];
	
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear all the cache files
	 */
	function clear_cache()
	{
		$this->_cache_clear( 'all' );

		return $this->lang['cache_files_deleted'];
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear page cache files
	 */
	function clear_page_cache()
	{
		$this->_cache_clear( 'page' );

		return $this->lang['cache_page_files_deleted'];
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear database cache files
	 */
	function clear_db_cache()
	{
		$this->_cache_clear( 'db' );

		return $this->lang['cache_db_files_deleted'];
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear relationship cache files
	 */
	function clear_rel_cache()
	{
		$this->_cache_clear( 'relationships' );

		return $this->lang['cache_rel_files_deleted'];
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear tag cache files
	 */
	function clear_tag_cache()
	{
		$this->_cache_clear( 'tag' );

		return $this->lang['cache_tag_files_deleted'];
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Does the actual clearing of the cache
	 */
	function _cache_clear( $type )
	{
		if ( ! $this->EE->cp->allowed_group('can_access_tools') OR ! $this->EE->cp->allowed_group('can_access_data')):
		
			return $this->lang['trigger_no_access'];
		
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
/* Location: ./trigger/core/drivers/channels/commands.site.php */
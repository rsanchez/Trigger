<?php

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
	function offline()
	{
		if( $this->_change_preference('is_system_on', 'n') ):
		
			return "site is now offline";
		
		else:
		
			return "site is already offline";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site online
	 */
	function online()
	{
		if( $this->_change_preference('is_system_on', 'y') ):
		
			return "site is now online";
		
		else:
		
			return "site is already online";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable the site profiler
	 */
	function enable_output_profiler()
	{
		if( $this->_change_preference('show_profiler', 'y') ):
		
			return "profiler enabled";
		
		else:
		
			return "profiler is already enabled";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable the site profiler
	 */
	function disable_output_profiler()
	{
		if( $this->_change_preference('show_profiler', 'n') ):
		
			return "profiler disabled";
		
		else:
		
			return "profiler is already disabled";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Enable template debugging
	 */
	function enable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'y') ):
		
			return "template debugging enabled";
		
		else:
		
			return "template debugging is already enabled";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Disable template debugging
	 */
	function disable_template_debug()
	{
		if( $this->_change_preference('template_debugging', 'n') ):
		
			return "template debugging disabled";
		
		else:
		
			return "template debugging is already disabled";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function debug_0()
	{
		if( $this->_change_preference('debug', '0') ):
		
			return "debugging set to 0";
		
		else:
		
			return "debugging is already set to 0";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function debug_1()
	{
		if( $this->_change_preference('debug', '1') ):
		
			return "debugging set to 1";
		
		else:
		
			return "debugging is already set to 1";
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function debug_2()
	{
		if( $this->_change_preference('debug', '2') ):
		
			return "debugging set to 2";
		
		else:
		
			return "debugging is already set to 2";
	
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear all the cache files
	 */
	function clear_cache()
	{
		$this->_cache_clear( 'all' );

		return "cache files has been deleted";
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear page cache files
	 */
	function clear_page_cache()
	{
		$this->_cache_clear( 'page' );

		return "page cache files has been deleted";
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear database cache files
	 */
	function clear_db_cache()
	{
		$this->_cache_clear( 'db' );

		return "database cache files has been deleted";
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear relationship cache files
	 */
	function clear_rel_cache()
	{
		$this->_cache_clear( 'relationships' );

		return "the relationships entries cache has been deleted";
	}

	// --------------------------------------------------------------------------

	/**
	 * Clear tag cache files
	 */
	function clear_tag_cache()
	{
		$this->_cache_clear( 'tag' );

		return "tag cache files have been deleted";
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Does the actual clearing of the cache
	 */
	function _cache_clear( $type )
	{
		if ( ! $this->EE->cp->allowed_group('can_access_tools') OR ! $this->EE->cp->allowed_group('can_access_data')):
		
			return "You do not have access to this function";
		
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
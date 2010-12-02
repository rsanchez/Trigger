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
		if( $this->_toggle_online_status('n') ):
		
			return 'site is now offline';
		
		else:
		
			return 'site is already offline';
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Take the site online
	 */
	function online()
	{
		if( $this->_toggle_online_status('y') ):
		
			return 'site is now online';
		
		else:
		
			return 'site is already online';
	
		endif;
	}

	// --------------------------------------------------------------------------
	
	function _toggle_online_status( $new_status )
	{
		$current_status = $this->EE->config->item('is_system_on');;
	
		if( $current_status == $new_status ):
		
			return FALSE;
		
		endif;

		$config = array('is_system_on' => $new_status);

		$this->EE->config->update_site_prefs($config);
		
		return TRUE;
	}

}

/* End of file commands.site.php */
/* Location: ./trigger/core/drivers/channels/commands.site.php */
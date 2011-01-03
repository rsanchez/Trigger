<?php

class Commands_templates
{

	function Commands_templates()
	{
		$this->EE =& get_instance();
		
		// -------------------------------------
		// Load the Channel Template API
		// -------------------------------------
		// We're using the API for this dealio
		// -------------------------------------
		
		$this->EE->load->library( 'Api' );
		
		$this->EE->api->instantiate( 'template_structure' );
	}

	// --------------------------------------------------------------------------

	/**
	 * Sync Templates
	 *
	 * @access	public
	 * @return	string
	 */	
	function sync()
	{
		// -------------------------------------
		// Pre-sync Checks
		// -------------------------------------
	
		if ( $this->EE->config->item('save_tmpl_files') != 'y' ):
		
			return "templates cannot be saved as files";
		
		endif;

		if ( $this->EE->config->item('tmpl_file_basepath') == '' ):
		
			return "templates file basepath not set";
		
		endif;
		
		$this->EE->load->library('api/Sync');
		
		$this->EE->sync->sync_all();
		
		return "templates synced";
	}

}

/* End of file commands.channels.php */
/* Location: ./trigger/core/drivers/channels/commands.channels.php */
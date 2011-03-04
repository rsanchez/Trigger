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
	function _comm_sync()
	{
		// -------------------------------------
		// Pre-sync Checks
		// -------------------------------------
	
		if ( $this->EE->config->item('save_tmpl_files') != 'y' ):
		
			return trigger_lang('no_saved_as_files');
		
		endif;

		if ( $this->EE->config->item('tmpl_file_basepath') == '' ):
		
			return trigger_lang('basepath_not_set');
		
		endif;
		
		$this->EE->load->library('api/Sync');
		
		$this->EE->sync->sync_all();
		
		return trigger_lang('templates_synced');
	}

}

/* End of file commands.channels.php */
/* Location: ./trigger/core/drivers/channels/commands.channels.php */
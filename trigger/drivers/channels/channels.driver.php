<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Channels Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_channels
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "channels";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
		
		// -------------------------------------
		// Load the Channel Structure API
		// -------------------------------------
		// We're using the API for this dealio
		// -------------------------------------
		
		$this->EE->load->library('Api');
		
		$this->EE->api->instantiate('channel_structure');
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Create a channel
	 *
	 * @access	public
	 * @param	string
	 * @param	[string]
	 * @param	[string]
	 * @return	string
	 */
	public function _comm_new($channel_title, $channel_name = '', $channel_content = '')
	{
		if(!$channel_title):

			return "no channel name provided";
		
		endif;

		$channel_data['site_id']		= $this->EE->config->item('site_id');
		$channel_data['channel_title'] 	= $channel_title;

		// We will just guess the name if we don't have it
		if(!$channel_name):

			$this->EE->load->helper('url');

			$channel_data['channel_name']	= url_title($channel_title, 'dash', TRUE);
		
		else:
		
			$channel_data['channel_name']	= $channel_name;
		
		endif;
		
		// TODO: Check and see if this already exists
		
		// Create the channel	
		if($this->EE->api_channel_structure->create_channel($channel_data)):
		
			return "channel added successfully";
			
		else:
		
			return "error in adding channel";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * List Channels
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_list()
	{	
		$call = $this->EE->api_channel_structure->get_channels($this->EE->config->item('site_id'));
		
		if( !$call ):
		
			return "no channels found";
		
		endif;
		
		$channels = $call->result();
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach( $channels as $channel ):
		
			$out .= "$channel->channel_title ($channel->channel_name)\n";
		
		endforeach;
		
		return $out .= TRIGGER_BUFFER;
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete Channel
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_delete($channel_name)
	{
		// Get the ID
		$query = $this->EE->db
							->where('site_id', $this->EE->config->item('site_id'))
							->limit(1)
							->where('channel_name', $channel_name)
							->get('channels');
	
		if($query->num_rows() == 0):
		
			return "cannot find channel";
		
		endif;
		
		$row = $query->row();
		$channel_id = $row->channel_id;
	
		if( $this->EE->api_channel_structure->delete_channel($channel_id, $this->EE->config->item('site_id')) === FALSE ):
		
			return "error encountered when deleting this channel";
		
		else:
		
			return "channel deleted successfully";
		
		endif;
	}

}

/* End of file channels.driver.php */
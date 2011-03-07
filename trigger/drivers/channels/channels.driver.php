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
	 * @param	array
	 * @return	string
	 */
	public function _comm_new( $data )
	{
		$this->EE->load->helper('url');

		// Use the current site ID
		$channel_data['site_id']		= $this->EE->config->item('site_id');
	
		if(!is_array($data)):
		
			// Looks like we just have a title. Use it as a string.
			$channel_data['channel_title'] 	= $data;
			$channel_data['channel_name']	= url_title($data, 'underscore', TRUE);
		
		elseif(isset($data[0]) && isset($data[1])):
		
			$channel_data['channel_name'] 	= $data[0];
			$channel_data['channel_title']	= $data[1];
		
		else:
		
			return "insufficient data";

		endif;
	
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
		$this->EE->db->where('site_id', $this->EE->config->item('site_id'));
		$query = $this->EE->db->limit(1)->where('channel_name', $channel_name)->get('channels');
	
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
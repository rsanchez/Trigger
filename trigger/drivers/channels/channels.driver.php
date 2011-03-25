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
	public function _comm_new($channel_title, $channel_name = '', $channel_description = '')
	{
		if(!$channel_title):

			return "no channel name provided";
		
		endif;

		$channel_data['site_id']				= $this->EE->config->item('site_id');
		$channel_data['channel_title'] 			= $channel_title;
		$channel_data['channel_description'] 	= $channel_description;

		// We will just guess the name if we don't have it
		if(!$channel_name):

			$this->EE->load->helper('url');

			$channel_data['channel_name']	= url_title($channel_title, 'dash', TRUE);
		
		else:
		
			$channel_data['channel_name']	= $channel_name;
		
		endif;
		
		// Check and see if this already exists
		$query = $this->EE->db->where('channel_name', $channel_data['channel_name'])->get('channels');
		
		if($query->num_rows() > 0):
		
			return "this channel already exists";
		
		endif;
		
		// We need to reset this before we create a channel because
		// it remembers if we have any errors from any other previous calls
		// and stops anything from working bc it checks the class error array
		// So, there's that.
		$this->EE->api_channel_structure->errors = array();
		
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
		if(is_string($tmp = $this->_check_channel($channel_name))):
		
			return $tmp;
		
		else:
		
			$channel = $tmp;
		
		endif;

		if( $this->EE->api_channel_structure->delete_channel($channel['channel_id'], $this->EE->config->item('site_id')) === FALSE ):
		
			return "error encountered when deleting this channel";
		
		else:
		
			return "channel deleted successfully";
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable Versioning
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_enable_versioning($channel_name)
	{
		$messages = array(
			'success' => "versioning enabled for $channel_name",
			'failure' => "versioning already enabled for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'enable_versioning', 'y', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Disable Versioning
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_disable_versioning($channel_name)
	{
		$messages = array(
			'success' => "versioning disabled for $channel_name",
			'failure' => "versioning already disabled for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'enable_versioning', 'n', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Display Rich Format Buttons
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_enable_format_buttons($channel_name)
	{
		$messages = array(
			'success' => "rich formatting buttons enabled for $channel_name",
			'failure' => "rich formatting buttons already enabled for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'show_button_cluster', 'y', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Don't Display Rich Format Buttons
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_disable_format_buttons($channel_name)
	{
		$messages = array(
			'success' => "rich formatting buttons disabled for $channel_name",
			'failure' => "rich formatting buttons already disabled for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'show_button_cluster', 'n', $messages);
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Enable Comments
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_enable_comments($channel_name)
	{
		$messages = array(
			'success' => "comments enabled for $channel_name",
			'failure' => "comments already enabeld for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'comment_system_enabled', 'y', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Disable Comments
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_disable_comments($channel_name)
	{
		$messages = array(
			'success' => "comments disabled for $channel_name",
			'failure' => "comments already disabeld for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'comment_system_enabled', 'n', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Enable CAPTCHA
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_enable_captcha($channel_name)
	{
		$messages = array(
			'success' => "captcha enabled for $channel_name",
			'failure' => "captcha already enabled for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'comment_use_captcha', 'y', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Disable CAPTCHA
	 *
	 * @access	public
	 * @return	string
	 */	
	function _comm_disable_captcha($channel_name)
	{
		$messages = array(
			'success' => "captcha disabled for $channel_name",
			'failure' => "captcha already disabled for $channel_name"
		);
		
		return $this->_change_channel_preference($channel_name, 'comment_use_captcha', 'n', $messages);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check the channel
	 *
	 * @access	private
	 * @param	string
	 * @retun 	mixed - array on success, string on failure
	 */
	private function _check_channel($channel_name)
	{
		$query = $this->EE->db
							->where('site_id', $this->EE->config->item('site_id'))
							->limit(1)
							->where('channel_name', $channel_name)
							->get('channels');
							
		if($query->num_rows() == 0):
		
			return (string)"cannot find channel";
		
		endif;
		
		// We must've found it
		return $query->row_array();
	}

	// --------------------------------------------------------------------------

	/**
	 * Change channel preference
	 *
	 * Function that most of the commands call to change a setting withn
	 * a channel
	 *
	 * @access	private
	 * @param	string - the preference to change
	 * @param	array - success and failure messages in an assoc array
	 */	
	function _change_channel_preference($channel_name, $preference, $new_value, $messages)
	{
		if(is_string($tmp = $this->_check_channel($channel_name))):
		
			return $tmp;
		
		else:
		
			$channel = $tmp;
		
		endif;

		// Checking to see if the value is already set to this
		if( $channel[$preference] == $new_value ):
		
			return $messages['failure'];
		
		endif;
		
		$update_data = array($preference => $new_value);
		
		$this->EE->db->limit(1)->where('channel_id', $channel['channel_id'])->update('channels', $update_data);

		return $messages['success'];
	}

}

/* End of file channels.driver.php */
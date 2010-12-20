<?php

class Commands_channels
{

	function Commands_channels()
	{
		$this->EE =& get_instance();
	}

	function create_channel( $params )
	{
		$insert_data['channel_name'] 	= $params['channel_name'];
		$insert_data['channel_title']	= $params['channel_title'];
		
		if( $this->EE->db->insert('channels', $insert_data) ):
		
			return "Channel added successfully.";
			
		else:
		
			return "Error in adding channel.";
		
		endif;
	}

}

/* End of file commands.channels.php */
/* Location: ./trigger/core/drivers/channels/commands.channels.php */
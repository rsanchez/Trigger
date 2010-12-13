<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Write a log file
 *
 * @access	public
 * @param	string
 * @param	string
 * @return	void
 */
function write_log( $line, $result = '' )
{
	$EE =& get_instance();

	$log_data['log_time']	= time();
	$log_data['user_id']	= $EE->session->userdata('member_id');
	$log_data['command']	= $line;
	$log_data['result']		= $result;
	
	$sql = $EE->db->insert_string('trigger_log', $log_data);
	
	$EE->db->query( $sql );
}

/* End of file log_helper.php */
/* Location: ./helpers/log_helper.php */
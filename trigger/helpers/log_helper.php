<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------------------------------

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
	$log_data['type']		= 'comm';
	$log_data['command']	= $line;
	$log_data['result']		= $result;
	
	$sql = $EE->db->insert_string('trigger_log', $log_data);
	
	$EE->db->query( $sql );
}

// --------------------------------------------------------------------------

/**
 * Write Mark Log Entry
 *
 * @access	public
 * @param	string (start or end)
 * @param	string
 * @return	void
 */
function write_log_mark( $type, $sequence_name )
{
	$EE =& get_instance();

	$log_data['log_time']	= time();
	$log_data['user_id']	= $EE->session->userdata('member_id');
	$log_data['type']		= $type;
	$log_data['command']	= $sequence_name . ' run ' . $type;
	
	$sql = $EE->db->insert_string('trigger_log', $log_data);
	
	$EE->db->query( $sql );
	
	// Return the insert id
	
	return $EE->db->insert_id();
}


// --------------------------------------------------------------------------

/**
 * Clear Logs
 *
 * @access	public
 * @return	void
 */
function clear_logs()
{
	$EE =& get_instance();

	$EE->db->empty_table('exp_trigger_log');
}


/* End of file log_helper.php */
/* Location: ./helpers/log_helper.php */
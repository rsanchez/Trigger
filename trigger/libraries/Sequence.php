<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sequence
{
	function __construct()
	{
		$this->EE =& get_instance();	
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Run Sequence
	 *
	 * Takes sequence data, runs it, and returns the results.
	 *
	 * @param	array
	 * @return	obj
	 */
	function run_sequence( $seq )
	{
		// -------------------------------------
		// Break down into lines
		// -------------------------------------
		
		$lines = explode("\n", trim($seq['sequence']));

		// -------------------------------------
		// Write the start mark
		// -------------------------------------
		
		$start_id = write_log_mark( 'start', $seq['name'] );
		
		// -------------------------------------
		// Feed each line through the processor
		// -------------------------------------
		
		foreach( $lines as $line ):
		
			$line = trim($line);
		
			if( $line != '' ):
		
				$this->EE->trigger->process_line( $line, FALSE );
			
			endif;
		
		endforeach;

		// -------------------------------------
		// Write the end mark
		// -------------------------------------
		
		$end_id = write_log_mark( 'end', $seq['name'] );
		
		// -------------------------------------
		// Get logs from start to finish
		// -------------------------------------
	
		$this->EE->db->order_by('log_time', 'desc');		
		$this->EE->db->where('id >=', $start_id);
		$this->EE->db->where('id <=', $end_id);
		
		$db_obj = $this->EE->db->get('trigger_log');
		
		return $db_obj->result();
	}
	
}

/* End of file Sequence.php */
/* Location: ./Trigger/trigger/libraries/Sequence.php */
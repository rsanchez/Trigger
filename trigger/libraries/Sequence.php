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
	 * @param	string 'log' or 'log_string'
	 * @return	obj
	 */
	function run_sequence( $seq, $output = 'log' )
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
		
		$out = '';
		
		foreach( $lines as $line ):
		
			$line = trim($line);
		
			if( $line != '' ):
			
				$out .= $line."\n";
		
				$out .= $this->EE->trigger->process_line($line)."\n";
			
			endif;
		
		endforeach;

		// -------------------------------------
		// Write the end mark
		// -------------------------------------
		
		$end_id = write_log_mark( 'end', $seq['name'] );
		
		if($output == 'log'):
		
			// -------------------------------------
			// Get logs from start to finish
			// -------------------------------------
		
			$this->EE->db->order_by('log_time', 'desc');		
			$this->EE->db->where('id >=', $start_id);
			$this->EE->db->where('id <=', $end_id);
			
			$db_obj = $this->EE->db->get('trigger_log');
			
			return $db_obj->result();
		
		else:
		
			return $out;
		
		endif;
	}
	
}

/* End of file Sequence.php */
/* Location: ./Trigger/trigger/libraries/Sequence.php */
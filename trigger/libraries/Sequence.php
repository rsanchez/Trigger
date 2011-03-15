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
	 * @param	string
	 * @param	string
	 * @param	string 'log' or 'log_string'
	 * @return	obj
	 */
	function run_sequence($raw_lines, $seq_name, $output = 'log')
	{
		// -------------------------------------
		// Write the start mark
		// -------------------------------------
		
		$start_id = write_log_mark('start', trim($seq_name));
		
		// -------------------------------------
		// Feed each line through the processor
		// -------------------------------------
		
		// Cut up the lines
		$lines = explode("\n", $raw_lines);
		
		$out = '';
		
		foreach($lines as $line):
		
			$line = trim($line);
		
			if( $line != '' ):
			
				$out .= $line."\n";
		
				$out .= $this->EE->trigger->process_line($line)."\n";
			
			endif;
		
		endforeach;

		// -------------------------------------
		// Write the end mark
		// -------------------------------------
		
		$end_id = write_log_mark('end', trim($seq_name));
		
		if($output == 'log'):
		
			// -------------------------------------
			// Get logs from start to finish
			// -------------------------------------
		
			$this->EE->db->order_by('id', 'asc');		
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
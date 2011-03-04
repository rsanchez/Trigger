<?php

class Commands_seq
{

	function Commands_seq()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * List sequences available
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_list()
	{
		// Get our snippets
		$db_obj = $this->EE->db->where('site_id', $this->EE->config->item('site_id'))->get('trigger_sequences');
		
		$total = $db_obj->num_rows();
		
		if($total == 0):
		
			return trigger_lang('no_sequences');
		
		endif;
		
		$out = '';
		$count = 1;
		
		foreach($db_obj->result() as $sequence):
		
			$out = $sequence->name;
			
			if($total > $count):
			
				$out .= "\n";
		
			endif;
			
			$count++;
		
		endforeach;
		
		return $out;
	}	

	// --------------------------------------------------------------------------

	/**
	 * Run a sequence from the Trigger window
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_run($sequence_name)
	{
		// Check for data
		if(!$sequence_name):
		
			return trigger_lang('no_name');
		
		endif;
		
		// We need our site ID
		$site_id = $this->EE->config->item('site_id');
		
		// Get the sequence
		$this->EE->db->where('site_id', $site_id)->where('name', $sequence_name);
		$db_obj = $this->EE->db->limit(1)->get('trigger_sequences');
		
		if($db_obj->num_rows() == 0):
		
			return trigger_lang('invalid_sequence_name');
		
		endif;
				
		$sequence = $db_obj->row_array();
		
		// Run the sequence

		$this->EE->load->library('Sequence');
		
		$out = $this->EE->sequence->run_sequence($sequence, 'log_string');
		
		return $out.trigger_lang('sequence_run');
	}
		
}

/* End of file seq.commands.php */
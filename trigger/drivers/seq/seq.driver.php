<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Sequences Driver
 *
 * @package		Trigger
 * @author		Adam Fairholm (Green Egg Media)
 * @copyright	Copyright (c) 2010 - 2011, Green Egg Media
 * @license		
 * @link		
 */
 
class Driver_seq
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "seq";

	// --------------------------------------------------------------------------

	function __construct()
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
		$this->EE->load->model('sequences_mdl');
	
		$sequences = $this->EE->sequences_mdl->get_sequences();

		$total = count($sequences);
		
		if($total == 0 or $sequences === FALSE):
		
			return trigger_lang('no_sequences');
		
		endif;
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach($sequences as $sequence_name => $data):
		
			$out .= $sequence_name."\n";
		
		endforeach;
		
		return $out .= TRIGGER_BUFFER;
	}	

	// --------------------------------------------------------------------------

	/**
	 * Run a sequence from the Trigger window
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_run($sequence_data)
	{
		// Check for data
		if(!$sequence_data):
		
			return trigger_lang('no_name');
		
		endif;
		
		$this->EE->load->model('sequences_mdl');

		// We could be passed a single string in which
		// case we look in the sequences folder. Otherwise,
		// we look into the packages
		$items = explode('/', $sequence_data);

		if(count($items) == 2):
		
			$seq_name = trim($parts[1]);
		
			// We have a package sequence
			$sequence = $this->EE->sequences_mdl->read_sequence_file_data($seq_name, $parts[0]);
		
		elseif(count($items) == 1):
	
			$seq_name = $sequence_data;
	
			// We have a package sequence
			$sequence = $this->EE->sequences_mdl->read_sequence_file_data($sequence_data, 'seqs');	
		
		else:
		
			return trigger_lang('invalid_sequence_name');
		
		endif;

		// Run the sequence

		$this->EE->load->library('Sequence');
		
		$out = $this->EE->sequence->run_sequence($sequence['commands'], $seq_name, 'log_string');
		
		// Reset the context.
		// It is usually reset by the trigger library
		$this->EE->trigger->context[1] = 'seq';
		
		return $out.trigger_lang('sequence_run');
	}

}

/* End of file seq.driver.php */
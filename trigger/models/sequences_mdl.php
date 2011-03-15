<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sequences_mdl extends CI_Model
{
	public $folder = 'sequences';

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper(array('file', 'directory'));
    }
    
	// --------------------------------------------------------------------------
	
	/**
	 * Get all the packages in the package
	 * folder. /packages
	 *
	 * @access	public
	 * @return	array
	 */
	function get_sequences()
	{	
		$sequences = array();

		// Get them from the sequences folder

		$sequence_files = directory_map(APPPATH.'third_party/trigger/'.$this->folder.'/', 1);		
		
		foreach($sequence_files as $file):
		
			if(substr($file, 0, 4) == 'seq.'):
			
				$sequences[$this->find_filename_slug($file)] = array('loc'=>'seqs');
			
			endif;
		
		endforeach;
		
		// Get them from the packages
		
		$packages = directory_map(APPPATH.'third_party/trigger/packages/', 2);
	
		foreach($packages as $folder => $package):
		
			// We only want package folders
			if(is_array($package)):
			
				foreach($package as $item):
				
					if(substr($item, 0, 4) == 'seq.'):
					
						$sequences[$this->find_filename_slug($item)] = array('loc'=>$folder);
					
					endif;
				
				endforeach;
			
			endif;
		
		endforeach;
		
		ksort($sequences);
		
		return $sequences;
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * Read Sequence File Data
	 *
	 * Read the file and parse the data
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	public function read_sequence_file_data($seq_name, $location)
	{
		// Where is the location?
		if($location == 'seqs'):
		
			$file = 'sequences/seq.'.$seq_name.'.txt';
	
		else:
		
			$file = 'packages/'.$location.'/seq.'.$seq_name.'.txt';
		
		endif;
				
		if(!file_exists(APPPATH.'third_party/trigger/'.$file)):
					
			return FALSE;
		
		endif;
	
		// Open the file
	 	$sequence = read_file(APPPATH.'third_party/trigger/'.$file);
		
		$seq_data = array();
		
		// Parse the lines up to the first blank one
		$lines = explode("\n", $sequence);
		$seq_data['pre_checks'] = array();
		
		// Get the data and the pre-checks
		foreach($lines as $line):
		
			if(trim($line) == ''):
				
				continue;
			
			endif;
			
			$parts = explode(':', $line);
			
			if(count($parts) == 2):
			
				$seq_data[str_replace(' ', '_', $parts[0])] = $parts[1];
			
			endif;
			
			// Get the pre-check
			if(substr($line, 0, 6) == '#check'):
			
				$seq_data['pre_checks'][] = trim($line);
			
			endif;
		
		endforeach;
		
		$count = 0;
		
		// Get the lines
		preg_match("#TRIGGER SEQUENCE START([^<]+)TRIGGER SEQUENCE END#", $sequence, $matches);
		
		$seq_data['commands'] = trim($matches[1]);
		
		// Count the lines
		$exp = explode("\n", $seq_data['commands']);
		
		$seq_data['lines'] = count($exp);
	
		// Return the sequence data
		return $seq_data;
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * Find the filename slug from the
	 * full filename
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function find_filename_slug($filename)
	{
		// Strip the .seq and the .txt
		$file_pieces = explode('.', $filename);
		
		return $file_pieces[1];
	}

}

/* End of file sequences_mdl.php */
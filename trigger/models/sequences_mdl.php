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
	
	// Return array
	function read_sequence_file_data($seq_name, $location)
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
		
		// Get that data.
		foreach($lines as $line):
		
			if(trim($line) == ''):
				
				continue;
			
			endif;
			
			$parts = explode(':', $line);
			
			if(count($parts) == 2):
			
				$seq_data[str_replace(' ', '_', $parts[0])] = $parts[1];
			
			endif;
		
		endforeach;
		
		$count = 1;
		
		// Count the lines
		foreach($lines as $line):
		
			if(trim($line) == 'TRIGGER SEQUENCE START'):
			
				$count = 1;
			
			endif;
			
			$count++;
			
			if(trim($line) == 'TRIGGER SEQUENCE END'):
			
				$seq_data['lines'] = $count;
			
			endif;
	
		endforeach;
	
		// Return the sequence data
		return $seq_data;
	}

	// --------------------------------------------------------------------------	
	
	/**
	 * Find the filename slug from the
	 * full filename
	 */
	function find_filename_slug($filename)
	{
		// Strip the .seq and the .txt
		$file_pieces = explode('.', $filename);
		
		return $file_pieces[1];
	}

}

/* End of file sequences_mdl.php */
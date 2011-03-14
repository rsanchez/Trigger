<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Package_mdl extends CI_Model
{
	public $folder = 'packages';

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('file');
    }
    
	// --------------------------------------------------------------------------
	
	/**
	 * Get all the packages in the package
	 * folder. /packages
	 *
	 * @access	public
	 * @return	array
	 */
	function get_packages($img_base)
	{
		$this->img_base = $img_base;
	
		$this->load->helper('directory');
	
		$types = new stdClass;

		$files = directory_map(APPPATH.'third_party/trigger/'.$this->folder.'/', 1);
		
		$return = array();
		
		foreach($files as $file):
		
			if($file != '' and $file != 'index.html'):
			
				if($details = $this->parse_package_details($file)):
			
					$return[$file] = $details;
				
				endif;
			
			endif;
		
		endforeach;
		
		return $return;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Read and parse the package packages.txt
	 * details file.
	 *
	 * @access	public
	 * @param	slug
	 * @return	mixed
	 */
	public function parse_package_details($package_slug)
	{
		$package_file = APPPATH.'third_party/trigger/'.$this->folder.'/'.$package_slug.'/package.txt';
		
		if(!$package_info = read_file($package_file)):
		
			return FALSE;
		
		endif;
		
		// Trim up our details
		$package_info = trim($package_info);
			
		// Break up into lines
		$lines = explode("\n", $package_info);
		
		// Start off info array with slug
		$info = array('slug'=>$package_slug);
		
		// Break up into key/vals
		foreach($lines as $line):
		
			$line_info = explode(":", trim($line));
		
			if(count($line_info)==2):
			
				$info[$line_info[0]] = $line_info[1];
			
			endif;
		
		endforeach;
		
		// See if we have an icon
		
		if(is_file(APPPATH.'third_party/trigger/'.$this->folder.'/'.$package_slug.'/icon.png')):
	
			$info['icon'] = $this->config->item('base_url').SYSDIR.'/expressionengine/third_party/trigger/packages/'.$package_slug.'/icon.png';
		
		else:
		
			$info['icon'] = $this->img_base.'block.png';
		
		endif;
		
		return $info;
	}
}

/* End of file package_mdl.php */
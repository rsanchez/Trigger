<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Packages Driver
 *
 * @package		Trigger
 * @author		Adam Fairholm (Addict Add-ons)
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
 
class Driver_pack
{
	public $driver_version		= '0.9';
	
	public $driver_slug			= 'pack';

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------

	/**
	 * List packages available
	 *
	 * @access	public
	 * @return	string
	 */	
	public function _comm_list()
	{
		$this->EE->load->model('package_mdl');
	
		$packages = $this->EE->package_mdl->get_packages();

		$total = count($packages);
		
		if($total == 0 or $packages === FALSE):
		
			return $this->EE->lang->line('pack.no_packages');
		
		endif;
		
		$out = TRIGGER_BUFFER."\n";
		
		foreach($packages as $package_name => $data):
		
			$out .= $data['name']." ($package_name)\n";
		
		endforeach;
		
		return $out .= TRIGGER_BUFFER;
	}	

}

/* End of file pack.driver.php */
<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Accessory
 *
 * @package		Trigger
 * @category	accessories
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2011, Addict Add-ons
 */
 
class Trigger_acc {

	var $name			= 'Trigger';
	var $id				= 'trigger';
	var $version		= '0.9';
	var $description	= 'Quickly access the Trigger command line.';

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Set Sections
	 *
	 * Set content for the accessory
	 *
	 * @access	public
	 * @return	void
	 */
	function set_sections()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">#trigger_acc_content {background: none; border: none; padding: 2px; display: inline; color: #838D94; height: 200px; width: 700px;}</style>');

		$this->EE->cp->load_package_js('trigger_acc');

		$this->sections['Trigger'] = '<form id="trigger_form"><textarea name="trigger_acc_content" id="trigger_acc_content">ee : </textarea><div id="trigger_acc_target"></div></form>'; 
	}
}

/* End of file acc.trigger.php */
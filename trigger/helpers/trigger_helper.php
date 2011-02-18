<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------------------------------

/**
 * Return a Trigger language line
 *
 * @access	public
 * @param	string
 * @return 	string
 */
function trigger_lang( $key )
{
	$EE =& get_instance();
	
	if( isset($EE->trigger_lang[$key]) ):
	
		return $EE->trigger_lang[$key];
	
	endif;
	
	return '';
}

/* End of file trigger_helper.php */
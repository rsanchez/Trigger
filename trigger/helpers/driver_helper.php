<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------------------------------

/**
 * Validate a language input
 *
 * @access	public
 * @param	string
 * @return	mixed - language (short) or FALSE on fail
 */
function validate_language($lang)
{
	//$EE =& get_instance();
	
	require_once(APPPATH.'config/languages'.EXT);	

	// Did they provide the short name? If so, good
	// Just spit it back to them
	
	if(in_array($lang, $languages)):
	
		$full = array_search($lang, $languages);
	
		return array('code'=>$lang, 'full'=>$full);
	
	endif;
	
	// Maybe they gave us the full name
	if(array_key_exists($lang, $languages)):
	
		return array('code'=>$languages[$lang], 'full'=>$lang);
	
	endif;
	
	return FALSE;
}

/* End of file driver_helper.php */
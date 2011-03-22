<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Files Driver
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */
class Driver_files
{
	public $driver_version		= "0.9";
	
	public $driver_slug			= "files";

	// --------------------------------------------------------------------------

	function __construct()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Outline:
	 *
	 * new dir (directory)
	 * new upload dest (name, [folder], [images only(n)]) <--Checks for the folder
	 * delete upload dest (name)
	 * new file (file_original, new_desination)
	 */

	// --------------------------------------------------------------------------
	
	/**
	 * Create a directory
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function _comm_new_dir($directory)
	{
		if(!$directory):

			return "no directory provided";
		
		endif;
		
		// Put together the path
		$server = $_SERVER["SCRIPT_FILENAME"];
		$server = str_replace('/'.SYSDIR.'/index.php', '', $server);
		
		// Remove the '/'
		$directory = rtrim($directory, '/');
		$directory = ltrim($directory, '/');
		
		$dir = $server.'/'.$directory;
		
		// Does this already exist?
		if(@is_dir($dir)):
		
			return "this directory already exists";
		
		endif;
		
		// Make the directory
		
		if(!@mkdir($dir, DIR_WRITE_MODE))
		{
			return "unable to create directory";
		}
		@chmod($dir, DIR_WRITE_MODE);
		
		return "directory created";
	}


	/*
	
		// -------------------------------------
		// Check the path
		// -------------------------------------

		// Does this directory already exist?
		// Maybe they provided the whole path?
		if(@is_dir($directory)):
			
			// They gave us the directory
			$path = $directory;
			
		elseif(!@is_dir($directory)):	
			
			// Try to put together the directory
			$server = $_SERVER["SCRIPT_FILENAME"];
			$server = str_replace('/'.SYSDIR.'/index.php', '', $server);
			
			$directory = rtrim($directory, '/'):
			
			$try_dir = $server.'/'.$directory;
			
			if(!@is_dir($try_dir)):
			
				// We couldn't 
				return FALSE;
			
			else:
			
			
			endif;
			
		endif;
	
	*/

}

/* End of file files.driver.php */
<?php

class Vars
{

	function Vars()
	{
		$this->EE =& get_instance();		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Server Path
	 *
	 * Returns the server path
	 *
	 * @access	public
	 * @return	string
	 */
	public function server_path()
	{
		return $_SERVER["DOCUMENT_ROOT"];
	}

	// --------------------------------------------------------------------------
	
	/**
	 * EE Server Path
	 *
	 * Returns the EE server path
	 *
	 * @access	public
	 * @return	string
	 */
	public function ee_path()
	{
		$server = str_replace('index.php', '', $_SERVER["SCRIPT_FILENAME"]);
	
		return $server.'expressionengine';
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Server Name
	 *
	 * Returns the name of the server from SERVER_NAME
	 *
	 * @access	public
	 * @return	string
	 */
	public function server_name()
	{
		return $_SERVER["SERVER_NAME"];
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Last ID
	 *
	 * Returns the last insert ID
	 *
	 * @access	public
	 * @return	string
	 */
	public function last_id()
	{
		return $this->EE->db->insert_id();
	}

	// --------------------------------------------------------------------------
	
	/**
	 * License Number
	 *
	 * Returns the name of the EE license number
	 *
	 * @access	public
	 * @return	string
	 */
	public function license_key()
	{
		$key = $this->EE->config->item('license_number');
	
		if(!$key):
		
			return "key is empty";
		
		else:
		
			return $key;
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Debug Level
	 *
	 * Returns the current debug level
	 *
	 * @access	public
	 * @return	string
	 */
	public function debug_level()
	{
		return $this->EE->config->item('debug');
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Site Name
	 *
	 * Returns the current debug level
	 *
	 * @access	public
	 * @return	string
	 */
	public function site_name()
	{
		return $this->EE->config->item('site_name');
	}

}

/* End of file commands.php.php */
/* Location: ./trigger/core/drivers/php/commands.php.php */
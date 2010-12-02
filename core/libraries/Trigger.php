<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class trigger
{
	function __construct()
	{
		$this->EE =& get_instance();	
	}
	
	function output_context( $context )
	{
		$this->EE->session->cache['trigger']['context'] == $context;
	
		$output = null;
	
		foreach( $context as $cont ):
		
			$output .= $cont . " : ";
		
		endforeach;

		return $output;
	}
	
	function set_variable( $segs )
	{
		$pair = $segs[1];
		
		$vals = explode("=", $pair);
		
		if(count($vals) != 2):
		
			return FALSE;
		
		endif;
		
		if( ! isset($this->EE->session->cache['Trigger_mcp']['vars']) ):
		
			$existing = array();
			
		else:
		
			$existing = $this->EE->session->cache['Trigger_mcp']['vars'];
		
		endif;
		
		$all = array_merge($existing, array(trim($vals[0]) => trim($vals[1])));
		
		$this->EE->session->cache['Trigger_mcp']['vars'] = $all;
		
		return TRUE;
	}

}

/* Trigger.php */
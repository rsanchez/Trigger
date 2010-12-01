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

}

/* Trigger.php */
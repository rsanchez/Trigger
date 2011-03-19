<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trigger Sequence Class
 *
 * Contains sequence running logic.
 *
 * @package		Trigger
 * @author		Addict Add-ons Dev Team
 * @copyright	Copyright (c) 2010 - 2011, Addict Add-ons
 * @license		
 * @link		
 */

class Sequence
{
	private $checks = array('global_var', 'snippet', 'template');

	// Right now we just support checking if it doesn't exist
	// To add:  'exists', 'is null', 'is numeric'
	private $conditions = array('does not exist');

	function __construct()
	{
		$this->EE =& get_instance();	
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Run Sequence
	 *
	 * Takes sequence data, runs it, and returns the results.
	 *
	 * @param	string
	 * @param	string
	 * @param	array of lines
	 * @param	string 'log' or 'log_string'
	 * @return	obj
	 */
	function run_sequence($raw_lines, $seq_name, $pre_checks = array(), $output = 'log')
	{
		// -------------------------------------
		// Do pre-checks
		// -------------------------------------

		if(!empty($pre_checks)):

			foreach($pre_checks as $check):
			
				$check_result = $this->run_pre_check($check);
				
				if($check_result !== TRUE):
				
					return (string)'#'.$check_result;
				
				endif;
			
			endforeach;
		
		endif;

		// -------------------------------------
		// Write the start mark
		// -------------------------------------
		
		$start_id = write_log_mark('start', trim($seq_name));
		
		// -------------------------------------
		// Feed each line through the processor
		// -------------------------------------
		
		// Cut up the lines
		$lines = explode("\n", $raw_lines);
		
		$out = '';
		
		foreach($lines as $line):
		
			$line = trim($line);
		
			if( $line != '' ):
			
				$out .= $line."\n";
		
				$out .= $this->EE->trigger->process_line($line)."\n";
			
			endif;
		
		endforeach;

		// -------------------------------------
		// Write the end mark
		// -------------------------------------
		
		$end_id = write_log_mark('end', trim($seq_name));
		
		if($output == 'log'):
		
			// -------------------------------------
			// Get logs from start to finish
			// -------------------------------------
		
			$this->EE->db->order_by('id', 'asc');		
			$this->EE->db->where('id >=', $start_id);
			$this->EE->db->where('id <=', $end_id);
			
			$db_obj = $this->EE->db->get('trigger_log');
			
			return $db_obj->result();
		
		else:
		
			return $out;
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Pre-checks
	 *
	 * Parses and runs each check and returns 
	 * a value.
	 *
	 * @access	private
	 * @param	string - check
	 * @return	mixed - TRUE on pass and string error on no passie.
	 */	
	private function run_pre_check($check)
	{
		// Break up into chunks
		$pieces = explode(' ', $check, 3);
		
		if(count($pieces) != 3):
			
			// We need all of these formats
			return 'invalid format';
		
		endif;
	
		if(!in_array(trim($pieces[0]), $this->checks)):
		
			// We don't understand it.
			return 'invalid type';
		
		endif;
				
		// Separate and format
		$item_type = strtolower(trim($pieces[0]));
		$item_value = trim($pieces[1]);
		$item_condition = strtolower(trim($pieces[2]));
				
		if(!in_array($item_condition, $this->conditions)):
		
			return 'invalid condition';
		
		endif;
		
		return $this->{'check_'.str_replace(' ', '_', $item_condition)}($item_type, $item_value);
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Check to make sure an item does not exist
	 *
	 * @access	private
	 * @param	string - the type we're looking for
	 * @param	string - value
	 * @return	mixed - error string or true
	 */
	private function check_does_not_exist($item_type, $item_value)
	{
		// Check for snippet
		if($item_type == 'snippet'):
		
			$query = $this->EE->db->where('snippet_name', $item_value)->get('snippets');
		
			if($query->num_rows() > 0):
			
				return "snippet \"$item_value\" already exists";
			
			endif;
		
		// Check for global variable
		elseif($item_type == 'global_var'):
		
			$query = $this->EE->db->where('variable_name', $item_value)->get('global_variables');
		
			if($query->num_rows() > 0):
			
				return "global variable \"$item_value\" already exists";
			
			endif;
					
		// Check for template
		elseif($item_type == 'template'):
		
			$pieces = explode('/', $item_value);
			
			if(count($pieces)!=2):
			
				// We need the groups
				return "no group specified";
			
			endif;
			
			// Find the group ID
			$query = $this->EE->db->limit(1)->where('group_name', $pieces[0])->get('template_groups');
			
			if($query->num_rows(0)):
			
				// The group doesn't even exist, which means we're good
				return TRUE;
			
			endif;
			
			// However, the group may exist and the template doesn't, so let's see...
			$group = $query->row();
			
			$tmpl_query = $this->EE->db->limit(1)->where('group_id', $group->id)->where('template_name', $pieces[1])->get('templates');
		
			if($tmpl_query->num_rows() == 1):
			
				"template \"".$pieces[1]."\" already exists";
			
			endif;
		
		endif;
		
		return TRUE;
	}
	
}

/* End of file Sequence.php */
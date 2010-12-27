<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_mcp {

	var $context 	= array();
	
	var $vars		= array();
	
	function Trigger_mcp()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('Trigger');
		
		$this->EE->load->helper('log');
		
		$this->module_base = $this->EE->config->item('base_url').'admin/'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger';

		// -------------------------------------
		// Set the top right nav.
		// -------------------------------------		

		$this->nav 		= array(
						'command_window' => 
				BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger',
						'trigger_logs' => 
				BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=logs'
			);
		
		// -------------------------------------
		// Catch the session cache data. Whatever.
		// -------------------------------------		
		
		if( ! isset($this->EE->session->cache['Trigger_mcp']['vars']) ):
		
			$this->vars = array();
			
		else:
		
			$this->vars = $this->EE->session->cache['Trigger_mcp']['vars'];
		
		endif;

		// -------------------------------------

		$theme_url = $this->EE->config->item('theme_folder_url') . 'third_party/trigger';
		
		$this->EE->cp->add_to_head("<link rel='stylesheet' href='{$theme_url}/css/trigger.css'>");

		// -------------------------------------
		// Set default context if none set
		// -------------------------------------

		if ( ! isset($this->EE->session->cache['trigger']['context']) ):
			
			$this->EE->session->cache['trigger']['context'] = array('ee');
			
		endif;		
	}

	// --------------------------------------------------------------------------
	
	function index()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">#trigger_content {background: none; border: none; padding: 2px; display: inline; color: #838D94; height: 200px;}</style>');
	
		$this->EE->cp->set_right_nav( $this->nav );	
	
		// -------------------------------------
		// Get some javascript
		// -------------------------------------
		
		$this->EE->load->library('javascript');

		$this->EE->cp->load_package_js('trigger');
		$this->EE->cp->load_package_js('elastic');
		
		$this->EE->javascript->output("$('textarea#trigger_content').elastic();");
		
		$this->EE->javascript->compile();

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_module_name'));

		// -------------------------------------
		// Set the context for the view
		// -------------------------------------
		
		$this->EE->cp->set_variable('context', $this->EE->session->cache['trigger']['context']);

		// -------------------------------------
		// Load trigger edit window
		// -------------------------------------

		return $this->EE->load->view('window', '', TRUE); 
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Handles the trigger input and output
	 */
	function parse_trigger_output()
	{
		$result = null;
		
		$output = null;
		
		$error = null;
	
		// -------------------------------------
		// Get the line in question
		// -------------------------------------
		
		$text = $this->EE->input->post('line');
		
		// Get last line
		
		$lines = explode("\n", $text);
		
		foreach( $lines as $individ_line ):
		
			if( trim($individ_line) != '' ):
			
				$line = $individ_line;
			
			endif;
		
		endforeach;
		
		$this->EE->trigger->process_line( $line );
	}

	// --------------------------------------------------------------------------
	
	/**
	 * View Trigger logs
	 */
	function logs()
	{
		$this->EE->cp->set_right_nav( $this->nav );	
		
		$vars['module_base'] = $this->module_base;
	
		// -------------------------------------
		// Get some javascript
		// -------------------------------------
		
		$this->EE->load->library('javascript');

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_logs'));

		// -------------------------------------
		// Get an array of users
		// -------------------------------------

		$members_obj = $this->EE->db->get('members');

		$members = $members_obj->result();

		$vars['members'] = array();
		
		foreach( $members as $member ):
		
			$vars['members'][$member->member_id] = $member->screen_name;
		
		endforeach;

		// -------------------------------------
		// Get the Pagination and data
		// -------------------------------------

		if ( ! $rownum = $this->EE->input->get_post('rownum') )
		{		
			$rownum = 0;
		}
				
		$per_page = 25;

		$this->EE->load->library('pagination');
		
		$pag_config['base_url'] 				= BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=logs';
		$pag_config['total_rows'] 				= $this->EE->db->count_all('trigger_log');
		$pag_config['per_page'] 				= $per_page;
		$pag_config['page_query_string'] 		= TRUE;
		$pag_config['query_string_segment'] 	= 'rownum';
		$pag_config['full_tag_open'] 			= '<p id="paginationLinks">';
		$pag_config['full_tag_close'] 			= '</p>';
		$pag_config['prev_link'] 				= '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="<" />';
		$pag_config['next_link'] 				= '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt=">" />';
		$pag_config['first_link'] 				= '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="< <" />';
		$pag_config['last_link'] 				= '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="> >" />';
		
		$this->EE->pagination->initialize( $pag_config );
	
		$vars['pagination'] = $this->EE->pagination->create_links();
	
		$this->EE->db->order_by('log_time', 'desc');		
		$db_obj = $this->EE->db->get('trigger_log', $per_page, $rownum);
		
		$vars['log_lines'] = $db_obj->result();

		// -------------------------------------
		// Load Table Library for layout
		// -------------------------------------
		
		$this->EE->load->library('Table');
		
		// -------------------------------------
		// Load Logs
		// -------------------------------------

		$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));

		return $this->EE->load->view('logs', $vars, TRUE); 
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Clear Logs
	 */
	function clear_logs()
	{
		// -------------------------------------
		// Process Delete
		// -------------------------------------

		if( $this->EE->input->get_post('clear_confirm') == TRUE ):
		
			clear_logs();
			
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('trigger_logs_cleared'));
			
			$this->EE->functions->redirect($this->module_base.AMP.'&method=logs');

		else:

			$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));
			
			$this->EE->cp->set_breadcrumb($this->module_base.AMP.'method=logs', $this->EE->lang->line('trigger_logs'));
			
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_clear_logs'));				
			
			return $this->EE->load->view('clear_logs', '', TRUE);
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Export logs as a CSV file
	 */
	function export_log_csv()
	{
		// -------------------------------------
		// Get Logs
		// -------------------------------------

		$this->EE->db->order_by('log_time', 'desc');
		
		$db_obj = $this->EE->db->get('trigger_log');
		
		$log_lines = $db_obj->result();

		// -------------------------------------
		// Get an array of users
		// -------------------------------------

		$members_obj = $this->EE->db->get('members');

		$members_result = $members_obj->result();

		$members = array();
		
		foreach( $members_result as $member ):
		
			$members[$member->member_id] = $member->screen_name;
		
		endforeach;

		// -------------------------------------
		// Create CSV
		// -------------------------------------

		$terminate 			= "\n";
		$encap_left			= "";
		$encap_right		= "";
		$separate			= ", ";

		$csv = $encap_left.'ID'.$encap_right.$separate.$encap_left.'User'.$encap_right.$separate.$encap_left.'When'.$encap_right.$separate.$encap_left.'Command'.$encap_right.$separate.$encap_left.'Result'.$terminate;
		
		foreach( $log_lines as $line ):
		
			$csv .= $encap_left.$line->id.$encap_right.$separate.
			$encap_left.$members[$line->user_id].$encap_right.$separate.
			$encap_left.date('M j Y g:i:s a', $line->log_time).$encap_right.$separate.
			$encap_left.$line->command.$encap_right.$separate.
			$encap_left.$line->result.$encap_right.$terminate;
		
		endforeach;

		// -------------------------------------
		// Force Download
		// -------------------------------------
		
		$this->EE->load->helper('download');

		force_download('Trigger_Export_'.date('mdy').'.csv', $csv);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get information before exporting a Trigger Sequence
	 */
	function export_log_sequence()
	{	
		// -------------------------------------
		// Get Logs to Count Them
		// -------------------------------------

		$this->EE->db->order_by('log_time', 'desc');
		
		$db_obj = $this->EE->db->get('trigger_log');

		$vars['log_rows_count'] = $db_obj->num_rows();

		// -------------------------------------
		// Load Page
		// -------------------------------------

		$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));
		
		$this->EE->cp->set_breadcrumb($this->module_base.AMP.'method=logs', $this->EE->lang->line('trigger_logs'));
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_export_logs_as_seq'));				
		
		return $this->EE->load->view('export_trigger_sequence', $vars, TRUE);
	}

	// --------------------------------------------------------------------------

	/**
	 * Export a log into a Trigger Sequence
	 */	
	function do_log_sequence_export()
	{
		// -------------------------------------
		// Check the sequence title
		// -------------------------------------
	
		if( $this->EE->input->get_post('sequence_name') == '' ):
		
			show_error("The Sequence Title field is required.");
		
		endif;
		
		// -------------------------------------
		// Get log items
		// -------------------------------------

		$this->EE->db->order_by('log_time', 'desc');
		
		$db_obj = $this->EE->db->get('trigger_log');

		$log_lines = $db_obj->result();

		// -------------------------------------
		// Create Header
		// -------------------------------------

		$term = "\n";

		$seq  = 'sequence name: '.$this->EE->input->get_post('sequence_name').$term;
		
		$seq .= 'lines: '.$db_obj->num_rows().$term;
		
		$seq .= 'created by: '.$this->EE->session->userdata('screen_name').$term;

		$seq .= 'created: '.date('M j Y g:i:s a').$term;
		
		$seq .= $term;

		// -------------------------------------
		// Create Body
		// -------------------------------------

		$seq .= 'TRIGGER SEQUENCE START'.$term;
		
		foreach( $log_lines as $line ):
		
			$seq .= $line->command.$term;
		
		endforeach;
		
		$seq .= 'TRIGGER SEQUENCE END';

		// -------------------------------------
		// Force Download
		// -------------------------------------
		
		$this->EE->load->helper('download');

		force_download('Trigger_Sequence_'.date('mdy').'.txt', $seq);
	}
}

/* End of file mcp.trigger.php */
/* Location: ./system/expressionengine/third_party/trigger/mcp.trigger.php */

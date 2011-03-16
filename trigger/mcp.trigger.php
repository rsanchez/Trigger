<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Trigger_mcp {

	var $context 	= array();
	
	var $vars		= array();

	// --------------------------------------------------------------------------
	
	function Trigger_mcp()
	{
		$this->EE =& get_instance();
		
		$this->EE->load->library('Trigger');
		
		$this->EE->load->helper( array('log', 'trigger') );
		
		$this->EE->load->model('package_mdl');
		
		$this->module_base = $this->EE->config->item('base_url').'admin/'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger';
		
		define('TRIGGER_IMG_URL', $this->EE->config->item('base_url').SYSDIR.'/expressionengine/third_party/trigger/img/');

		// -------------------------------------
		// Set the top right nav.
		// -------------------------------------		

		$this->nav = array(
			'command_window' => 
				$this->module_base,
			'trigger_packages' =>
				$this->module_base . AMP . 'method=packages',
			'trigger_sequences' =>
				$this->module_base . AMP . 'method=sequences',
			'trigger_logs' => 
				$this->module_base . AMP . 'method=logs'
		);

		// -------------------------------------
		// Default pagination config
		// -------------------------------------		
		
		$this->page_config = array(
			'page_query_string' 	=> TRUE,
			'query_string_segment' 	=> 'rownum',
			'full_tag_open'			=> '<p id="paginationLinks">',
			'full_tag_close'		=> '</p>',
			'per_page'				=> 25,
			'prev_link'				=> '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="<" />',
			'next_link'				=> '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt=">" />',
			'first_link'			=> '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="< <" />',
			'last_link'				=> '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="> >" />'
		);

		// -------------------------------------

		$theme_url = $this->EE->config->item('theme_folder_url') . 'third_party/trigger';		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Trigger Command Window
	 */
	function index()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">
		
		#trigger_content {background: none; border: none; padding: 2px; display: inline; color: #838D94; height: 200px;}
		table.trigger_table td {padding: 5px; background: red;}
			
		</style>');
	
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
		// Load trigger edit window
		// -------------------------------------

		return $this->EE->load->view('window', '', TRUE); 
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Parse the Ouput
	 *
	 * Handles the trigger input and output
	 *
	 * Accessed via AJAX
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
		
		$output = $this->EE->trigger->process_line($line);
		
		// Get the output out there
		$this->EE->trigger->output_response($output);
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Logs
	 *
	 * View Trigger logs
	 */
	function logs()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">pre {margin: 0;}</style>');
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">'.$this->EE->load->view('css/table', '', TRUE).'</style>');

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
				
		$this->EE->load->library('pagination');
		
		$this->page_config['base_url'] 		= $this->module_base . AMP . 'method=logs';
		$this->page_config['total_rows'] 	= $this->EE->db->count_all('trigger_log');
		
		$this->EE->pagination->initialize( $this->page_config );
	
		$vars['pagination'] = $this->EE->pagination->create_links();
	
		$this->EE->db->order_by('log_time', 'desc');		
		$db_obj = $this->EE->db->get('trigger_log', $this->page_config['per_page'], $rownum);
		
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
	 * Get information before exporting a Trigger Sequence
	 */
	function export()
	{	
		$vars['to'] = 'file';
	
		// Set page title accordingly
		
		if( $vars['to'] == 'sequences' ):

			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_export_to_seqs'));				
		
		else:

			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_export_logs_as_seq_file'));
		
		endif;

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
	
		if( $this->EE->input->get_post('title') == '' ):
		
			show_error("The Sequence Title field is required.");
		
		endif;

		if( $this->EE->input->get_post('name') == '' ):
		
			show_error("The Sequence Name field is required.");
		
		endif;
		
		// -------------------------------------
		// Get log items
		// -------------------------------------

		$this->EE->db->order_by('log_time', 'desc');
		
		$db_obj = $this->EE->db->get('trigger_log');

		$log_lines = $db_obj->result();

		$term = "\n";

		// -------------------------------------
		// Export to a File
		// -------------------------------------

		if( $this->EE->input->get_post('to') == 'file' ):

			// -------------------------------------
			// Create Header
			// -------------------------------------
	
			$seq  = 'sequence title: '.$this->EE->input->get_post('title').$term;

			$seq  = 'sequence name: '.$this->EE->input->get_post('name').$term;

			$seq  = 'sequence description: '.$this->EE->input->get_post('description').$term;
			
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
			
		else:
		
		// -------------------------------------
		// Export to Sequences
		// -------------------------------------

			// -------------------------------------
			// Get the Sequence
			// -------------------------------------
	
			$seq = '';
		
			foreach( $log_lines as $line ):
			
				$seq .= $line->command.$term;
			
			endforeach;
			
			// Save to the Sequences table
			
			$insert_data['title'] 			= $this->EE->input->get_post('title');
			$insert_data['name'] 			= $this->EE->input->get_post('name');
			$insert_data['description'] 	= $this->EE->input->get_post('description');
			$insert_data['lines']			= $db_obj->num_rows();
			$insert_data['created_by']		= $this->EE->session->userdata('screen_name');
			$insert_data['created']			= date('Y-m-d H:i:s');
			$insert_data['sequence']		= $seq;
			
			$sql = $this->EE->db->insert_string('trigger_sequences', $insert_data);

			$this->EE->db->query( $sql );
			
			// -------------------------------------
			// Redirect To Sequence
			// -------------------------------------
		
			$this->EE->functions->redirect($this->module_base.AMP.'method=sequences');
		
		endif;
	}

	// --------------------------------------------------------------------------

	/**
	 * Sequence
	 *
	 * View sequences.
	 */	
	function sequences()
	{
		$this->EE->load->model('sequences_mdl');
	
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">'.$this->EE->load->view('css/table', '', TRUE).'</style>');

		$this->EE->cp->set_right_nav( $this->nav );

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_sequences'));
		
		$vars['module_base'] = $this->module_base;

		// -------------------------------------
		// Get sequences & paginate
		// -------------------------------------

		if ( ! $rownum = $this->EE->input->get_post('rownum') )
		{		
			$rownum = 0;
		}
				
		$this->EE->load->library('pagination');
		
		$this->page_config['base_url'] 		= $this->module_base . AMP . 'method=sequences';
		$this->page_config['total_rows'] 	= $this->EE->db->count_all('trigger_sequences');
		
		$this->EE->pagination->initialize( $this->page_config );
	
		$vars['pagination'] = $this->EE->pagination->create_links();
		
		$vars['sequences'] = $this->EE->sequences_mdl->get_sequences();

		// -------------------------------------
		// Load view sequences window
		// -------------------------------------

		$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));

		return $this->EE->load->view('sequences/list_sequences', $vars, TRUE); 		
	}

	// --------------------------------------------------------------------------

	/**
	 * Packages
	 *
	 * List packages
	 */	
	function packages()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">'.$this->EE->load->view('css/table', '', TRUE).'</style>');

		$this->EE->cp->set_right_nav($this->nav);

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_packages'));
		
		$vars['module_base'] = $this->module_base;

		// -------------------------------------
		// Get Packages
		// -------------------------------------

		$vars['packages'] = $this->EE->package_mdl->get_packages();
		
		$vars['default_package_icon'] = TRIGGER_IMG_URL.'block.png';
		
		// -------------------------------------
		// Load list of packages
		// -------------------------------------

		$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));

		return $this->EE->load->view('packages/list_packages', $vars, TRUE); 		
	}

	// --------------------------------------------------------------------------

	/**
	 * List Packages Contents
	 *
	 * Show what is in the different packages.
	 */	
	function package_contents()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">'.$this->EE->load->view('css/table', '', TRUE).'</style>');

		$this->EE->cp->set_right_nav($this->nav);
		
		$this->EE->load->helper('inflector');
		
		$vars['module_base'] = $this->module_base;

		// -------------------------------------
		// Get Package Contents
		// -------------------------------------

		$package = $this->EE->input->get_post('package');
		
		if(!$package):
		
			show_error("Invalid package.");
		
		endif;
		
		$vars['details'] = $this->EE->package_mdl->parse_package_details($package);

		$vars['contents'] = $this->EE->package_mdl->get_package_contents($package);
		
		// -------------------------------------
		// Load list of packages
		// -------------------------------------

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_package_contents').': '.$vars['details']['name']);

		$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));
		$this->EE->cp->set_breadcrumb($this->module_base.AMP.'method=packages', $this->EE->lang->line('trigger_packages'));

		return $this->EE->load->view('packages/list_package_contents', $vars, TRUE);
	}

	// --------------------------------------------------------------------------

	/**
	 * Presents a choice before running a sequence
	 */
	function run_sequence()
	{
		$this->EE->load->model('sequences_mdl');
	
		$vars['sequence'] = $this->EE->sequences_mdl->read_sequence_file_data($this->EE->input->get_post('sequence'), $this->EE->input->get_post('location'));
	
		$vars['sequence_slug'] = $this->EE->input->get_post('sequence');
		$vars['location'] = $this->EE->input->get_post('location');
	
		// -------------------------------------
		// Ask if we want to run it
		// -------------------------------------

		$this->EE->cp->set_breadcrumb($this->module_base, $this->EE->lang->line('trigger_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_run_sequence'));

		return $this->EE->load->view('sequences/run_sequence', $vars, TRUE); 		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Do the actual sequence running
	 */
	function do_run_sequence()
	{
		$this->EE->cp->add_to_head('<style type="text/css" media="screen">pre {margin: 0;}</style>');

		$this->EE->load->model('sequences_mdl');

		$vars['sequence'] = $this->EE->sequences_mdl->read_sequence_file_data($this->EE->input->get_post('sequence'), $this->EE->input->get_post('location'));

		// -------------------------------------
		// Send it to the Sequences library
		// to run
		// -------------------------------------
		
		$this->EE->load->library('Sequence');
		
		$vars['log_lines'] = $this->EE->sequence->run_sequence($vars['sequence']['commands'], $vars['sequence']['title']);

		// -------------------------------------
		// Load Table Library for layout
		// -------------------------------------
		
		$this->EE->load->library('Table');

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
		// Display the Results
		// -------------------------------------

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('trigger_sequence_results'));

		return $this->EE->load->view('sequences/run_sequence_results', $vars, TRUE); 		
	}

}

/* End of file mcp.trigger.php */
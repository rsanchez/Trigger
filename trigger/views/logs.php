<?php if( $log_lines ): ?>

	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=clear_logs"><?=lang('trigger_clear_logs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export&to=sequences"><?=lang('trigger_export_to_seqs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export&to=file"><?=lang('trigger_export_logs_as_seq_file');?></a> 
	</div> 

<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		'ID', 'User', 'When', 'Command', 'Result'
	);
	
	foreach($log_lines as $line)
	{
		$start	= '';
		$end 	= '';
		
		if( $line->type == 'start' ):
		
			$start 	= '<span class="go_notice">';
			$end	= '</span>';
		
		elseif( $line->type == 'end' ):
	
			$start 	= '<span class="notice">';
			$end	= '</span>';
		
		endif;

		$this->table->add_row(
				$line->id,
				$members[$line->user_id],
				date('M j Y g:i:s a', $line->log_time),
				$start.$line->command.$end,
				'<pre>'.trim($line->result).'</pre>'
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>

<?php else: ?>

	<p>There are no log items to display.</p>

<?php endif; ?>
<?php if( $log_lines ): ?>

	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=clear_logs"><?=lang('trigger_clear_logs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=dsf"><?=lang('trigger_export_to_seqs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export_log_sequence"><?=lang('trigger_export_logs_as_seq_file');?></a> 
	</div> 

<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		'ID', 'User', 'When', 'Comand', 'Result'
	);

	foreach($log_lines as $line)
	{
		$this->table->add_row(
				$line->id,
				$members[$line->user_id],
				date('M j Y g:i:s a', $line->log_time),
				$line->command,
				'<pre>'.trim($line->result).'</pre>'
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>

<?php else: ?>

	<p>There are no log items to display.</p>

<?php endif; ?>
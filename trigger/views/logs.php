<?php if( $log_lines ): ?>

	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=clear_logs"><?=lang('trigger_clear_logs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export_log_csv"><?=lang('trigger_export_logs_as_csv');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="index.php?S=c1b83ff5126f9f23fa0925befedc7f1b9d2d1738&D=cp&D=cp&C=addons_modules&M=show_module_cp&module=freeform&method=manage_entries"><?=lang('trigger_export_logs_as_seq');?></a> 
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
				$line->result
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>

<?php else: ?>

	<p>There are no log items to display.</p>

<?php endif; ?>
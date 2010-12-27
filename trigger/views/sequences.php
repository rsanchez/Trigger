<?php if( $sequences ): ?>

	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=clear_logs"><?=lang('trigger_clear_logs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export_log_csv"><?=lang('trigger_export_logs_as_csv');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export_log_sequence"><?=lang('trigger_export_logs_as_seq');?></a> 
	</div> 

<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		'Sequence Name'
	);

	foreach($sequences as $sequence)
	{
		$this->table->add_row(
				$sequence->sequence_name
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>

<?php else: ?>

	<p>There are no sequences to display.</p>

<?php endif; ?>
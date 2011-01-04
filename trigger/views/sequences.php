<?php if( $sequences ): ?>
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export_log_csv"><?=lang('trigger_import_sequence');?></a> 
	</div> 


<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		'Sequence Name', 'Lines', 'Created By', ''
	);

	foreach($sequences as $sequence)
	{
		$this->table->add_row(
				$sequence->sequence_name,
				$sequence->lines,
				$sequence->created_by,
				''
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>

<?php else: ?>

	<p>There are no sequences to display. You can <a href="<?=$module_base.AMP;?>method=import">import one</a> to get started.</p>

<?php endif; ?>
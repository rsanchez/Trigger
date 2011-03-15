<?php if( $sequences ): ?>
	
	<div class="cp_button"> 
		<a href="<?=$module_base.AMP;?>method=export_log_csv"><?=lang('trigger_import_sequence');?></a> 
	</div> 

<?php
	$this->table->set_template(array('table_open'  => '<table cellpadding="0" cellspacing="0" class="trigger_table">'));
	$this->table->set_heading(
		'Sequence Title', 'Sequence Name', 'Sequence Description', 'Lines', 'Created By', ''
	);
	
	foreach($sequences as $seq_name => $sequence)
	{
		$sequence = $this->sequences_mdl->read_sequence_file_data($seq_name, $sequence['loc']);
		
		$this->table->add_row(
				$sequence['title'],
				$seq_name,
				$sequence['desc'],
				$sequence['lines'],
				$sequence['created_by'],
				'<a href="'.$module_base.AMP.'method=run_sequence'.AMP.'sequence_id='.$seq_name.'">Run</a>'
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>

<?php else: ?>

	<p>There are no sequences to display. You can <a href="<?=$module_base.AMP;?>method=import">import one</a> to get started.</p>

<?php endif; ?>
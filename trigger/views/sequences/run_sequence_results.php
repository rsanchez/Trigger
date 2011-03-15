<?php if( $log_lines ): ?>

<p>Sequence was run successfully. Below are the log results.</p><br />
	
<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		'ID', 'User', 'When', 'Command', 'Result'
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

<?php else: ?>

	<p>There are no sequences to display. You can <a href="<?=$module_base.AMP;?>method=import">import one</a> to get started.</p>

<?php endif; ?>
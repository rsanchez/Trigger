<?php
	$this->table->set_template($cp_table_template);
	$this->table->set_heading(
		'User', 'When', 'Comand', 'Result'
	);

	foreach($log_lines as $line)
	{
		$this->table->add_row(
				$members[$line->user_id],
				date('M j Y g:i:s a', $line->log_time),
				$line->command,
				$line->result
			);
	}
?>
<?=$this->table->generate();?>

<?=$pagination;?>
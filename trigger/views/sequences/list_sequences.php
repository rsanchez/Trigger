<?php if( $sequences ): ?>

<table cellpadding="0" cellspacing="0" class="trigger_table">
<thead>
	<tr>
		<th>Title</th>
		<th>Name</th>
		<th>Description</th>
		<th>Lines</th>
		<th>Created By</th>
		<th></th>
	</tr>
</thead>

	
<?php 	

	$count = 1;
	
	foreach($sequences as $seq_name => $seq):
	
	$sequence = $this->sequences_mdl->read_sequence_file_data($seq_name, $seq['loc']);
	
?>
		
<tr class="<?php if($count%2 == 0): echo 'even'; else: echo 'odd'; endif;?>">
	<td><?php echo $sequence['title']; ?></td>
	<td><?php echo $seq_name; ?></td>
	<td><?php echo $sequence['desc']; ?></td>
	<td><?php echo $sequence['lines']; ?></td>
	<td><?php echo $sequence['created_by']; ?></td>
	<td><a href="<?php echo $module_base.AMP.'method=run_sequence'.AMP.'sequence='.$seq_name.AMP.'location='.$seq['loc'];?>">Run</a></td>
</tr>		

<?php $count++; endforeach; ?>

</table>
		
<?=$pagination;?>

<?php else: ?>

	<p>There are no sequences to display. You can <a href="<?=$module_base.AMP;?>method=import">import one</a> to get started.</p>

<?php endif; ?>
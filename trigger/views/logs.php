<?php if( $log_lines ): ?>

	<div class="cp_button"> 
		<a href="<?php echo $module_base.AMP;?>method=clear_logs"><?php echo lang('trigger_clear_logs');?></a> 
	</div> 
	
	<div class="cp_button"> 
		<a href="<?php echo $module_base.AMP;?>method=export&to=file"><?php echo lang('trigger_export_logs_as_seq_file');?></a> 
	</div> 

<table cellpadding="0" cellspacing="0" class="trigger_table">
<thead>
	<tr>
		<th>ID</th>
		<th>User</th>
		<th>When</th>
		<th>Command</th>
		<th>Result</th>
	</tr>
</thead>

<?php

	$count = 1;
	
	foreach($log_lines as $line):

		$start	= '';
		$end 	= '';
		
		if( $line->type == 'start' ):
		
			$start 	= '<span class="go_notice">';
			$end	= '</span>';
		
		elseif( $line->type == 'end' ):
	
			$start 	= '<span class="notice">';
			$end	= '</span>';
		
		endif;

?>

<tr class="<?php if($count%2 == 0): echo 'even'; else: echo 'odd'; endif;?>">
	<td><?php echo $line->id; ?></td>
	<td><?php echo $members[$line->user_id]; ?></td>
	<td><?php echo date('M j Y g:i:s a', $line->log_time); ?></td>
	<td><?php echo $start.$line->command.$end; ?></td>
	<td><pre><?php echo trim($line->result); ?></pre></td>
</tr>

<?php $count++; endforeach; ?>
</table>

<?php echo $pagination;?>

<?php else: ?>

	<p>There are no log items to display.</p>

<?php endif; ?>
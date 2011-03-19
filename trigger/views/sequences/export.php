<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=do_log_sequence_export')?>

<table class="mainTable padTable" cellspacing="0" cellpadding="0" border="0">
<thead>
	<tr>
		<th colspan="2">
			Trigger Sequence Information
		</th>
	</tr>
</thead>
<tbody>
	<tr>
		<td width="40%">
			<strong>Sequence Title</strong><br />
			<div class="subtext">A title for your sequence</div>
		</td>
		<td><?php echo form_input(array('id'=>'title','name'=>'title','class'=>'fullfield'))?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Sequence Name</strong><br />
			<div class="subtext">All lower case, no spaces. Underscores allowed.</div>
		</td>
		<td><?php echo form_input(array('id'=>'name','name'=>'name','class'=>'fullfield'))?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Sequence Description</strong>
		</td>
		<td><?php echo form_input(array('id'=>'desc','name'=>'desc','class'=>'fullfield'))?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Number of Lines</strong> 
		</td>
		<td><?php echo $log_rows_count;?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Author</strong> 
		</td>
		<td><?php echo form_input(array('id'=>'author','name'=>'author','class'=>'fullfield'))?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Author URL</strong> 
		</td>
		<td><?php echo form_input(array('id'=>'author_url','name'=>'author_url','class'=>'fullfield'))?></td>
	</tr>

</tbody>	
</table>
		
<input type="hidden" name="to" value="<?php echo $to;?>" />
		
<p><?php echo form_submit('submit', lang('trigger_export_sequence'), 'class="submit"')?></p>

<?php echo form_close()?>
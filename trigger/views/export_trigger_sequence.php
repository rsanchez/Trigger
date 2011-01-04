<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=do_log_sequence_export')?>

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
		<td>
			<?=form_input(array('id'=>'title','name'=>'title','class'=>'fullfield'))?>
		</td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Sequence Name</strong><br />
			<div class="subtext">All lower case, no spaces. Underscores allowed.</div>
		</td>
		<td>
			<?=form_input(array('id'=>'name','name'=>'name','class'=>'fullfield'))?>
		</td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Sequence Description</strong>
		</td>
		<td>
			<?=form_input(array('id'=>'description','name'=>'description','class'=>'fullfield'))?>
		</td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Number of Lines</strong> 
		</td>
		<td><?=$log_rows_count;?></td>
	</tr>
	<tr>
		<td width="40%">
			<strong>Exported By</strong> 
		</td>
		<td><?=$this->session->userdata('screen_name');?></td>
	</tr>

</tbody>	
</table>
		
<input type="hidden" name="to" value="<?=$to;?>" />
		
<p><?=form_submit('submit', lang('trigger_export_sequence'), 'class="submit"')?></p>

<?=form_close()?>
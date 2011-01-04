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
			<strong>Sequence Name</strong> 
		</td>
		<td>
			<?=form_input(array('id'=>'sequence_name','name'=>'sequence_name','class'=>'fullfield'))?>
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
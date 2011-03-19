<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=do_run_sequence')?>
	
	<p>Are you sure you want to run the sequence titled <strong><?php echo $sequence['title'];?></strong>? It will run the following commands:</p>
	
	<pre><?php echo $sequence['commands'];?></pre>
	
	<input type="hidden" name="sequence" value="<?php echo $sequence_slug;?>" />
	<input type="hidden" name="location" value="<?php echo $location;?>" />
	
	<p><?php echo form_submit('submit', lang('trigger_run'), 'class="submit"')?></p>

<?php echo form_close()?>
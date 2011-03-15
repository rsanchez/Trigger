<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=do_run_sequence')?>
	
	<p>Are you sure you want to run the sequence titled <strong><?=$sequence['title'];?></strong>? It will run the following commands:</p>
	
	<pre><?=$sequence['commands'];?></pre>
	
	<input type="hidden" name="sequence" value="<?=$sequence_slug;?>" />
	<input type="hidden" name="location" value="<?=$location;?>" />
	
	<p><?=form_submit('submit', lang('trigger_run'), 'class="submit"')?></p>

<?=form_close()?>
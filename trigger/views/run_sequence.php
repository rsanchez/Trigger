<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=do_run_sequence')?>
	
	<p>Are you sure you want to run the sequence titled <strong><?=$sequence['title'];?></strong>? It will run the following commands:</p>
	
	<pre><?=$sequence['sequence'];?></pre>
	
	<p><input type="hidden" name="sequence_id" value="<?=$sequence['id'];?>" /></p>
	
	<p><?=form_submit('submit', lang('trigger_run'), 'class="submit"')?></p>

<?=form_close()?>
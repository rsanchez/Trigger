<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=import')?>
	
	<p>Import a sequence .txt file to your sequence library below.</p>
	
	<p><textarea name="userfile" id="userfile"></textarea></p>
	
	<p><?=form_submit('submit', lang('trigger_import'), 'class="submit"')?></p>

<?=form_close()?>
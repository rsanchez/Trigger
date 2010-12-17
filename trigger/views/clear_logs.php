<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=clear_logs');?>
	<?=form_hidden('clear_confirm', TRUE)?>
	<p><?=lang('trigger_clear_logs_confirm')?></p>
	<p><strong class="notice"><?=lang('action_can_not_be_undone')?></strong></p>
	<p><?=form_submit('panel', lang('trigger_clear_logs'), 'class="submit"')?></p>
<?=form_close()?>
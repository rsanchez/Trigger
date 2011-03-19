<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=trigger'.AMP.'method=clear_logs');?>
	<?php echo form_hidden('clear_confirm', TRUE)?>
	<p><?php echo lang('trigger_clear_logs_confirm')?></p>
	<p><strong class="notice"><?php echo lang('action_can_not_be_undone')?></strong></p>
	<p><?php echo form_submit('panel', lang('trigger_clear_logs'), 'class="submit"')?></p>
<?php echo form_close()?>
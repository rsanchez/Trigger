<table cellpadding="0" cellspacing="0" class="trigger_table">
<?php foreach($packages as $package):?>
	<tr>
		<td><img src="<?php echo $package_icon;?>" alt="<?=$package['name'];?> Package" /></td>
		<td><a href=""><?=$package['name'];?></a></td>
		<td><?=$package['description'];?></td>
		<td><?=$package['author'];?></td>
	</tr>
<?php endforeach; ?>
</table>
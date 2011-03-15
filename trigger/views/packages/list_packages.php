<table cellpadding="0" cellspacing="0" class="trigger_table">
<thead>
	<tr>
		<th></th>
		<th>Name</th>
		<th>Description</th>
		<th>Author</th>
	</tr>
</thead>
<?php foreach($packages as $package):?>

	<tr>
		<td><img src="<?php echo $package['icon']; ?>" alt="<?php echo $package['name'];?> Package" /></td>
		<td><a href="<?php echo $module_base; ?>&method=package_contents&package=<?php echo $package['slug'];?>"><?php echo $package['name'];?></a></td>
		<td><?php echo $package['description'];?></td>
		<td><?php echo $package['author'];?></td>
	</tr>
<?php endforeach; ?>
</table>
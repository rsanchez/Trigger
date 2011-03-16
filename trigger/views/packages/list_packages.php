<table cellpadding="0" cellspacing="0" class="trigger_table">
<thead>
	<tr>
		<th></th>
		<th>Name</th>
		<th>Description</th>
		<th>Author</th>
	</tr>
</thead>
<?php 

	$count = 1;

	foreach($packages as $package):
	
?>

<tr class="<?php if($count%2 == 0): echo 'even'; else: echo 'odd'; endif;?>">
	<td><img src="<?php echo $package['icon']; ?>" alt="<?php echo $package['name'];?> Package" /></td>
	<td><a href="<?php echo $module_base; ?>&method=package_contents&package=<?php echo $package['slug'];?>"><?php echo $package['name'];?></a></td>
	<td><?php echo $package['description'];?></td>
	<td><?php echo $package['author'];?></td>
</tr>
	
<?php $count++; endforeach; ?>
</table>
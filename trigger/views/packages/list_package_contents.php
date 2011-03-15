<p><strong><?php echo $details['name'];?></strong></p>
<p><?php echo $details['description'];?></p>
<p><?php echo $details['author'];?></p>

<br />

<table cellpadding="0" cellspacing="0" class="trigger_table">
<thead>
	<tr>
		<th></th>
		<th>Type</th>
		<th>File</th>
	</tr>
</thead>
<?php foreach($contents as $name => $files):?>

	<?php foreach($files as $file): ?>
	<tr>
		<td><img src="<?php echo TRIGGER_IMG_URL;?><?php echo $name;?>.png" alt="<?php echo ucwords($name);?>" /></td>
		<td><?php echo ucwords(singular($name));?></td>
		<td><?php echo $file;?></td>
	</tr>
	<?php endforeach; ?>
	
<?php endforeach; ?>
</table>

<p><a href="<?php echo $module_base;?>&method=packages">&#171; Back to Packagess</a></p>
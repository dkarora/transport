<?php
	if (empty($users))
	{
		echo $html->tag('strong', 'No users here!');
		return;
	}
?>

<?php
	if (!isset($tableType))
		$tableType = 'user';
?>

<table<?php if(isset($tableId)) { echo " id='$tableId'"; } ?>>
	<tr>
		<th>Name</th>
		<th>Email Address</th>
		
		<?php if ($isGroupAdmin && $tableType != 'admin') : ?>
			<th>Edit User Profile</th>
		<?php endif; ?>
	</tr>
	
	<?php $i = 0; ?>
	<?php foreach ($users as $index => $user) : ?>
	
	<tr<?php if($i % 2) { echo ' class="altrow"'; } $user = $user['User']; ?>>
		<td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
		<td><?php echo $user['email']; ?></td>
		
		<?php if ($isGroupAdmin && $tableType != 'admin') : ?>
			<td><?php echo $html->link('Edit User Profile', array('controller' => 'groups', 'action' => 'edit_member', 'userid' => $user['id'])); ?></td>
		<?php endif; ?>
	</tr>
	
	<?php $i++; ?>
	<?php endforeach; ?>
</table>
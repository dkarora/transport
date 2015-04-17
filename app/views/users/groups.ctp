<h2>My Groups</h2>

<?php if(empty($mygroups)): ?>
	You aren't in any groups!

<?php else: ?>
<table>
	
	<?php foreach($mygroups as $index => $group): ?>
		<tr>
			<td><?php echo $group['name'] ?></td>
		</tr>
	<?php endforeach; ?>

</table>

<?php endif; ?>


<h2>Other Groups</h2>

<?php if(empty($othergroups)): ?>
	You're in every group! Go you!

<?php else: ?>
<table>
	
	<?php foreach($othergroups as $index => $group): ?>
		<tr>
			<td><?php echo $group['Group']['name'] ?></td>
			<?php
				$admins = array();
				foreach($group['Admin'] as $index => $user)
					$admins[] = $user['first_name'] . ' ' . $user['last_name'];
			?>
			<td><?php echo implode($admins, ', '); ?></td>
		</tr>
	<?php endforeach; ?>

</table>

<?php endif; ?>
<?php if (!empty($attendees)) : ?>
	<table>
		<th>Name</th>
		<th>Affiliation</th>
		
		<?php foreach ($attendees as $a) : ?>
		<tr>
			<td><?php echo $a['User']['public_name']; ?></td>
			<td><?php echo $a['User']['affiliation']; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<strong>No one is attending this workshop!</strong>
<?php endif; ?>
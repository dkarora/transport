<?php $this->set('subnavcontent', $this->element('workshopsubnav')); ?>

<?php if (!$is_ajax) : ?>
	<h2>Attendee List</h2>
<?php endif; ?>

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
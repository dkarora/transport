<?php $this->set('subnavcontent', $this->element('userssubnav')); ?>

<h2>Upcoming Workshops</h2>
<?php if (!empty($upcoming)) : ?>
	<table>
		<tr>
			<th>Workshop</th>
			<th>Date</th>
			<th>Location</th>
			<th>City/Town</th>
			<th>Credits</th>
		</tr>
		
		<?php $i = 0; ?>
		<?php foreach ($upcoming as $key => $ws) : ?>
		<tr<?php if ($i % 2) : echo ' class="altrow"'; endif;?>>
			<td><?php echo $html->link($ws['Detail']['name'], $link->viewWorkshop($ws)); ?></td>
			<td><?php echo $timeFormatter->commonDate($ws['Workshop']['date']); ?></td>
			<td><?php echo $ws['Workshop']['location']; ?></td>
			<td><?php echo $ws['Workshop']['city']; ?></td>
			<td><?php echo $ws['Detail']['credits']; ?></td>
		</tr>
		<?php $i++; ?>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<p>No upcoming workshops!</p>
<?php endif; ?>

<?php if ($isGroupAdmin) : ?>
<h2>Upcoming Organization Workshops</h2>
<?php if (!empty($groupAttendees)) : ?>
	<table>
		<tr>
			<th>Workshop</th>
			<th>Date</th>
			<th>Attendee</th>
			<th>Location</th>
			<th>City/Town</th>
			<th>Credits</th>
		</tr>
		
		<?php $i = 0; ?>
		<?php foreach ($groupAttendees as $key => $ws) : ?>
		<tr<?php if ($i % 2) : echo ' class="altrow"'; endif;?>>
			<td><?php echo $html->link($ws['Detail']['name'], $link->viewWorkshop($ws)); ?></td>
			<td><?php echo $timeFormatter->commonDate($ws['Workshop']['date']); ?></td>
			<td><?php echo $ws['User']['full_name']; ?></td>
			<td><?php echo $ws['Workshop']['location']; ?></td>
			<td><?php echo $ws['Workshop']['city']; ?></td>
			<td><?php echo $ws['Detail']['credits']; ?></td>
		</tr>
		<?php $i++; ?>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<p>No upcoming workshops!</p>
<?php endif; ?>
<?php endif; ?>

<?php debug ($session->read()); ?>
<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'activity-logs'); ?>

<h2>Activity Logs</h2>

<?php echo $this->element('page-numbers'); ?>

<table>
	<?php
		$headers = array(
			'When' => 'ActivityLog.created',
			'Message' => 'ActivityLog.message',
			'Who' => 'User.username',
			'What' => 'ActivityLog.model_name',
			'How' => 'ActivityLog.action',
			'Where' => 'ActivityLog.url'
		);
		
		echo $this->element('table-headers', array('headers' => $headers));
	?>
	
	<?php foreach ($logs as $index => $l): ?>
	<tr <?php if ($index % 2) echo " class='altrow'";?> >
		<td><?php echo $timeFormatter->commonDateTime($l['ActivityLog']['created']); ?></td>
		<td><?php echo $l['ActivityLog']['message']; ?></td>
		<td><?php echo (empty($l['ActivityLog']['user_id']) ? '<span class="anonymous-user">(Anonymous User)</span>' : $l['User']['full_name']); ?></td>
		<td><?php echo $l['ActivityLog']['model_name'], ' (', $l['ActivityLog']['object_id'], ')'; ?></td>
		<td><?php echo Inflector::humanize($l['ActivityLog']['action']); ?></td>
		<td><?php echo $l['ActivityLog']['url']; ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo $this->element('page-numbers'); ?>
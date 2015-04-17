<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'attendance-manage'); ?>

<h2>Attendance</h2>

<?php echo $this->element('page-numbers'); ?>

<table id="workshops">
	<?php
		$headers = array(
			'Workshop' => 'Detail.name',
			'City' => 'Workshop.city',
			'Date' => 'Workshop.date',
			'Category',
			'Add Attendees',
			'Attendance',
		);
		
		echo $this->element('table-headers', array('headers' => $headers));
	?>
	
	<?php $i = 0; ?>
	<?php foreach ($workshops as $index => $workshop): ?>
	<tr
	<?php
		/* apply alt row colors on odd rows */
		if ($i % 2) echo " class='altrow'";
	?>>
		<td><?php echo $html->link($workshop['Detail']['name'], $link->viewWorkshop($workshop)); ?></td>
		<td><?php echo $workshop['Workshop']['city']; ?></td>
		<td><?php echo $timeFormatter->commonDateTime($workshop['Workshop']['date']); ?></td>
		<td><?php echo $workshop['Detail']['Category']['name']; ?></td>
		<td>
			<?php
				if (!$workshop['Workshop']['is_full'])
					echo $html->link('Add', array('controller' => 'attendees', 'action' => 'add', $workshop['Workshop']['id']), array('class' => 'button-link slim full-width'));
				else
					echo $html->div('button-link full-width slim disabled', 'Workshop Full');
			?>
		</td>
		<td>
			<?php
				if (!empty($workshop['Attendee']))
					echo $html->link('Administer', array('controller' => 'attendees', 'action' => 'manage_workshop', $workshop['Workshop']['id']), array('class' => 'button-link slim full-width'));
				else
					echo $html->div('button-link disabled full-width slim', 'No Attendees');
			?>
		</td>
	</tr>
	<?php $i++; ?>
	<?php endforeach; ?>
</table>

<?php echo $this->element('page-numbers'); ?>
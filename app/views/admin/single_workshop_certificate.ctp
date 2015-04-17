<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Print Single Workshop Certificate</h2>

<?php foreach ($attendees as $attendee) : ?>
	
	<?php echo $html->div('', $html->link($attendee['User']['full_name'], '/workshops/print_certificates/' . $workshop['Workshop']['id'] . '/' . $attendee['User']['id'])); ?>
	
<?php endforeach; ?>

<?php debug ($attendees); ?>
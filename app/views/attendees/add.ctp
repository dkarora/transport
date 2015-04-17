<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Edit Attendees: <?php printf('%s (%s)', $workshop['Detail']['name'], $timeFormatter->commonDateTime($workshop['Workshop']['date'])); ?></h2>

<h3>Add Attendee</h3>
<?php
	if (!empty($availableAttendees))
	{
		echo $form->create('Attendee', array('action' => 'add'));
		echo $form->input('Attendee.workshop_id', array('type' => 'hidden', 'value' => $workshop['Workshop']['id']));
		echo $form->input('Attendee.user_id', array('options' => $availableAttendees));
		echo $form->input('Attendee.attendance', array('type' => 'checkbox'));
		echo $form->end('Add');
	}
	else
		echo $html->div('', 'No available attendees.');
?>
<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Announcements</h2>
<h3>Add Announcement</h3>

<?php
	$rows = array(
		'Announcement.text' => array('type' => 'textarea')
	);
	
	echo $this->element('neat-form', array(
		'rows' => $rows,
		'model' => 'Announcement',
		'end' => 'Announce'
	));
?>
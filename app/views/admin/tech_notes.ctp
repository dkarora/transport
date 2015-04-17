<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Tech Notes</h2>

<h3>Upload</h3>

<?php
	$rows = array(
		'title' => array(),
		'summary' => array('type' => 'textarea'),
		'file' => array('type' => 'file')
	);
	
	echo $this->element('neat-form', array(
		'model' => 'TechNote',
		'form_opts' => array('action' => 'upload', 'type' => 'file'),
		'end' => 'Upload',
		'rows' => $rows
	));
?>
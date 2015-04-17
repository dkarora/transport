<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Submit Newsletter</h2>

<?php
	echo $form->create('Newsletter', array('action' => 'upload', 'type' => 'file'));
?>

<div>
	<?php
		echo $form->label('year', 'Year');
		echo $form->year('year', 1987, date('Y'), date('Y'), array(), false);
	?>
</div>

<?php
	echo $form->input('season', array('options' => array('Spring' => 'Spring', 'Summer' => 'Summer', 'Fall' => 'Fall', 'Winter' => 'Winter')));
	echo $form->input('summary', array('type' => 'textarea'));
	echo $form->input('file', array('type' => 'file'));
	echo $form->end('Submit');
?>
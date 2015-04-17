<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'legacy-associate'); ?>

<h2>Legacy Integration - Done!</h2>

<?php echo $this->element('steps', array('steps' => $steps, 'step' => $step)); ?>

<?php
	echo $html->div('paragraph', sprintf('%s %s\'s records have been integrated successfully. Rejoice!', $request['User']['first_name'], $request['User']['last_name']));
	echo $html->div('paragraph', $html->link('Click to integrate more.', '/admin/legacy_records/#requests'));
?>
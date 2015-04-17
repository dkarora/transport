<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Add Group Member</h2>

<?php
	$rows = array(
		'GroupMember.user_id' => array('options' => $usersList),
		'GroupMember.group_id' => array('options' => $groupsList),
		'GroupMember.permissions' => array('options' => array('Member', 'Admin'), 'type' => 'radio', 'legend' => false),
	);
	
	echo $this->element('neat-form', array(
		'model' => 'GroupMember',
		'rows' => $rows,
		'form_opts' => array('action' => 'create'),
	));
?>
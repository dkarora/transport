<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Delete Group Member</h2>

<?php
	$rows = array(
		'GroupMember.group_member_id' => array('options' => $groupMemberList)
	);
	
	echo $this->element('neat-form', array(
		'model' => 'GroupMember',
		'form_opts' => array('action' => 'delete'),
		'rows' => $rows
	));
?>
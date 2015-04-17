<?php
	$title = sprintf('Edit Member Information: %s %s', $user['first_name'], $user['last_name']);
	$this->set('subnavcontent', $this->element('orgsubnav', array('position' => 0, 'here' => $title, 'merge' => array($title => array('userid' => $this->params['named']['userid'])))));
?>

<h2>Edit Member Information</h2>

<?php
	$rows = array(
		'Account Information',
		'User.id' => array('type' => 'hidden'),
		'User.first_name' => array(),
		'User.nickname' => array('label' => 'Addressed as', 'after' => '<br />' . $html->tag('span', 'Mike for Michael, Liz for Elizabeth, etc.', array('class' => 'italics')), 'escape' => false),
		'User.middle_initial' => array(),
		'User.last_name' => array(),
		'User.suffix' => array('after' => '<br />' . $html->tag('span', 'III, Esq., Jr., etc.', array('class' => 'italics')), 'escape' => false),
		
		'Work Information',
		'User.affiliation' => array('disabled' => 'disabled', 'value' => $groupMembership['Group']['name']),
		'User.email' => array(),
		'User.job_title' => array(),
		'User.address_line1' => array('label' => 'Address Line 1'),
		'User.address_line2' => array('label' => 'Address Line 2'),
		'User.city' => array(),
		'User.state' => array('options' => $states),
		'User.zip' => array('label' => 'Zip Code'),
		'User.phone' => array('label' => 'Daytime Phone'),
		'User.fax' => array()
	);
	
	echo $this->element('neat-form', array(
		'rows' => $rows,
		'model' => 'Group',
		'form_opts' => array('url' => '/groups/edit_member/userid:' . $user['id']),
		'End' => 'Edit'
	));
?>
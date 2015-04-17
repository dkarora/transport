<?php
	$this->set('subnavcontent', $this->element('userssubnav'));
?>

<h2>Edit Account Information</h2>

<?php
	$rows = array(
		'Account Information',
		'User.first_name' => array(),
		'User.nickname' => array('label' => 'Addressed as', 'after' => '<br />' . $html->tag('span', 'Mike for Michael, Liz for Elizabeth, etc.', array('class' => 'italics')), 'escape' => false),
		'User.middle_initial' => array(),
		'User.last_name' => array(),
		'User.suffix' => array('after' => '<br />' . $html->tag('span', 'III, Esq., Jr., etc.', array('class' => 'italics')), 'escape' => false),
		
		'Work Information',
		'User.affiliation' => array_merge(
								$affiliationOptions,
								array('escape' => false, 'after' => '<br />' . $html->tag('span', 'For no work affiliation, use "Private".', array('class' => 'italics')))),
		'User.email' => array(),
		'User.job_title' => array(),
		'User.address_line1' => array('label' => 'Address Line 1'),
		'User.address_line2' => array('label' => 'Address Line 2'),
		'User.city' => array(),
		'User.state' => array('options' => $states),
		'User.zip' => array('label' => 'Zip Code'),
		'User.phone' => array('label' => 'Daytime Phone'),
		'User.fax' => array(),
	);
	
	$fOpts = array('controller' => 'users', 'action' => 'edit');
	if (!$editingOwn)
		$fOpts[] = $userId;
	
	echo $this->element('neat-form',
		array(
			'model' => 'User',
			'form_opts' => $fOpts,
			'rows' => $rows,
			'end' => 'Edit'
		)
	);
?>
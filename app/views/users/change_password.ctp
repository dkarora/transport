<?php
	$this->set('subnavcontent', $this->element('userssubnav'));
?>

<h2>Change Password</h2>

<?php
	$rows = array(
		'User.current_password' => array('label' => 'Current Password', 'type' => 'password'),
		'User.new_password' => array('label' => 'New Password', 'type' => 'password'),
		'User.new_password_confirm' => array('label' => 'New Password (Confirm)', 'type' => 'password')
	);
	
	echo $this->element('neat-form',
		array(
			'model' => 'User',
			'form_opts' => array('url' => '/users/change_password'),
			'rows' => $rows,
			'end' => 'Change'
		)
	);
	
	debug ($this->validationErrors);
?>
<?php
	if (!isset($options) || empty($options))
	{
		$options = array(
			'Dashboard' => '/users/index',
			'Edit Info' => '/users/edit',
			'Change Password' => '/users/change_password',
			'Legacy Integration' => '/users/legacy',
		);
	}
	
	$cd = array();
	
	if (empty($attr))
		$attr = $cd;
	else
		$attr = array_merge($attr, $cd);
	
	echo $this->element('subnavgeneric', array('options' => $options, 'attr' => $attr));
?>
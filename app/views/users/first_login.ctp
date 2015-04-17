<h2>First Login</h2>
<p>
	Welcome to the new Baystate Roads website.
	If you are here, then that means your username and password has been automatically generated from our past records.
	As such, <b>your password is insecure and must be changed before proceeding.</b>
</p>

<?php
	echo $form->create('User', array('action' => 'first_login'));
	echo $form->input('User.password');
	
	echo $form->input('User.password_repeat',
		array(
			'label' => 'Password (Verify)',
			'type' => 'password'
		)
	);
	
	echo $form->end('Update');
?>
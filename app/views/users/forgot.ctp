<h2>Password Reset</h2>
<p>Enter the username and the email address used to register your account. An automated email will be sent out containing your new password.</p>

<?php
	$rows = array(
		'User.username' => array(),
		'User.email' => array()
	);
	
	echo $this->element('neat-form',
		array(
			'model' => 'User',
			'form_opts' => array('action' => 'forgot'),
			'rows' => $rows,
			'end' => 'Reset Password'
		)
	);
?>

<h2>Forgot your username?</h2>
<p>Unfortunately, it is not possible to automatically retrieve usernames. If you have forgotten your username, please contact us at:</p>
<address>
	Baystate Roads Program<br />
	College of Engineering<br />
	University of Massachusetts<br />
	214 Marston Hall<br />
	Amherst, MA. 01003<br />
	phone: (413) 577-2762<br />
	fax: (413) 545-6471<br />
</address>
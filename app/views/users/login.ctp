<?php $html->css('login', null, array(), false); ?>
<?php
	$script =
<<<EOT
	$(function() {
		$('#UserUsername').focus();
	});
EOT;
	$javascript->codeBlock($script, array('inline' => false));
?>



<h2>Log in to Baystate Roads</h2>

<?php	
	$rows = array(
		'User.username' => array('tabindex' => '1'),
		'User.password' => array('tabindex' => '2')
	);
	
	if ($return_url)
		$rows = array_merge($rows, array('User.return_url' => array('type' => 'hidden', 'value' => $return_url)));
	
	echo $this->element('neat-form',
		array(
			'model' => 'User',
			'form_opts' => array('action' => 'login'),
			'rows' => $rows,
			'end' => array('label' => 'Log In', 'tabindex' => '3'),
			'css' => false,
			'table_class' => 'no-frills',
		)
	);
	
	echo $html->tag('p', $html->link('Forgot your username or password?', '/users/forgot/'), array('class' => 'forgot'));
	
	debug ($this->viewVars);
	debug ($rows);
?>


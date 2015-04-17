<?php $this->set('subnavcontent', $this->element('userssubnav')); ?>

<h2>Legacy Integration</h2>

<?php
	if (!$integrationRequested)
	{
		$rows = array(
			'User.integrate' => array('type' => 'checkbox', 'label' => 'I have taken workshops in the past.'),
		);
		
		echo $this->element('neat-form',
			array(
				'model' => 'User',
				'form_opts' => array('url' => '/users/legacy'),
				'rows' => $rows,
				'end' => 'Submit'
			)
		);
	}
	else
	{
		if (!$integrationFilled)
			echo $html->div('', 'Request submitted. An admin will review our legacy records shortly and attempt to restore your past workshops.');
		else
			echo $html->div('', "Your request has been reviewed and an admin has restored your past workshops. If we made an error, please contact us using the contact link above.");
	}
?>
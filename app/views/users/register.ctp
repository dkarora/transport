<?php $this->set('bodyClass', 'registration'); ?>

<h2>Register a Baystate Account</h2>

<div class="themoreyouknow">
	If this is your first visit to our website since February of 2011, please register by completing the form below.
	Please note: If you have attended a Baystate Roads workshop at any time after February of 2011, then an account already exists for you&mdash;please do not create a duplicate account!
	If you cannot remember your username or password, please email <a href="mailto:cindy@baystateroads.org">cindy@baystateroads.org</a> or call 413-545-5403, and we will assist you.
</div>

<div class="italics"><strong>Fields marked with <span class="required">*</span> are required.</strong></div>

<?php
	echo $this->element('neat-form', 
		array(
			'model' => 'User',
			'form_opts' => array('action' => 'register'),
			'end' => 'Register',
			'shift_back_before' => true,
			'submitInfo' => 'After clicking the "Register" button below, a Site Administrator will approve your registration, and a confirmation email will be sent to the email address entered for this account within 24 hours.',
			'rows' => array(
				'Account Information',
				'User.human' => array('type' => 'hidden'),
				'User.username' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.password' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'after' => '<br /><span class="italics">Minimum 6 characters.</span>'),
				'User.password_repeat' => array('label' => 'Password (Verify)', 'type' => 'password', 'before' => ' <span class="required">*</span>', 'escape' => false),
				'User.first_name' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.nickname' => array('label' => 'Nickname (first name for badges)', 'escape' => false),
				'User.middle_initial' => array('size' => 1),
				'User.last_name' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.suffix' => array('after' => '<br />' . $html->tag('span', 'III, Esq., Jr., etc.', array('class' => 'italics')), 'escape' => false),
				
				'Work Information',
				'User.affiliation' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'after' => '<br />' . $html->tag('span', 'For no work affiliation, use "Private".', array('class' => 'italics'))),
				'User.email' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'label' => 'Email Address', 'after' => '<br />' . $html->tag('span', 'If none, please use supervisor\'s.', array('class' => 'italics'))),
				'User.job_title' => array(),
				'User.address_line1' => array('label' => 'Address Line 1', 'before' => ' <span class="required">*</span>', 'escape' => false),
				'User.address_line2' => array('label' => 'Address Line 2'),
				'User.city' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.state' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'options' => $states),
				'User.zip' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'size' => 10, 'label' => 'Zip Code'),
				'User.phone' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'size' => 15, 'label' => 'Daytime Phone'),
				'User.fax' => array()
			)
		));
?>
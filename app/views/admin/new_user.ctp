<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'registration'); ?>

<?php if (!empty($created_user)) : ?>
	<h2>Created User Credentials</h2>
	
	<div id="created_user">
		<strong>Username:</strong> <?php echo $created_user['username']; ?> <br />
		<strong>Password:</strong> <?php echo $created_user['password']; ?>
	</div>
<?php endif; ?>

<h2>New User Registration</h2>
<p>Use this form to bypass email activation for a new user.</p>

<div class="italics"><strong>Fields marked with <span class="required">*</span> are required.</strong></div>

<?php
	echo $this->element('neat-form', 
		array(
			'model' => 'Admin',
			'form_opts' => array('url' => '/admin/new_user'),
			'end' => 'Create',
			'shift_back_before' => true,
			'rows' => array(
				'Account Information',
				'User.human' => array('type' => 'hidden'),
				'User.username' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.generate_password' => array('escape' => false, 'type' => 'checkbox', 'checked' => true, 'label' => 'Generate Password?', 'after' => '<br />' . $html->tag('span', 'Will override password entered below if checked.', array('class' => 'italics'))),
				'User.password' => array('after' => '<br />' . $html->tag('span', 'Not required if password is generated.', array('class' => 'italics')), 'before' => ' <span class="required">*</span>', 'escape' => false, 'type' => 'text'),
				'User.first_name' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.nickname' => array('label' => 'Addressed as', 'after' => '<br />' . $html->tag('span', '(Mike for Michael, Liz for Elizabeth, etc)', array('class' => 'italics')), 'escape' => false),
				'User.middle_initial' => array('size' => 1),
				'User.last_name' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.suffix' => array(),
				
				'Work Information',
				'User.affiliation' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'after' => '<br />' . $html->tag('span', 'For no work affiliation, use "Private".', array('class' => 'italics'))),
				'User.email' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'label' => 'Email Address'),
				'User.job_title' => array(),
				'User.address_line1' => array('label' => 'Address Line 1', 'before' => ' <span class="required">*</span>', 'escape' => false),
				'User.address_line2' => array('label' => 'Address Line 2'),
				'User.city' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.state' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'options' => $states),
				'User.zip' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'size' => 10, 'label' => 'Zip Code'),
				'User.phone' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'size' => 15, 'label' => 'Daytime Phone'),
				'User.fax' => array(),
				
				'Bypasses',
				'User.active' => array('label' => 'Set Active?', 'checked' => 'checked')
			)
		));
?>
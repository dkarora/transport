<?php $this->set('subnavcontent', $this->element('orgsubnav')); ?>
<?php $this->set('bodyClass', 'registration'); ?>

<h2>Register Group Member</h2>
 
<div class="italics"><strong>Fields marked with <span class="required">*</span> are required.</strong></div>

<?php
	echo $this->element('neat-form', 
		array(
			'model' => 'Group',
			'form_opts' => array('action' => 'newmember'),
			'end' => 'Register',
			'shift_back_before' => true,
			'rows' => array(
				'Account Information',
				'User.human' => array('type' => 'hidden'),
				'Group.set_admin' => array('type' => 'checkbox', 'label' => 'Grant Administrative Privileges?'),
				'User.username' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.password' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'after' => '<br /><span class="italics">Minimum 6 characters.</span>'),
				'User.password_repeat' => array('label' => 'Password (Verify)', 'type' => 'password', 'before' => ' <span class="required">*</span>', 'escape' => false),
				'User.first_name' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.nickname' => array('label' => 'Nickname (first name for badges)', 'escape' => false),
				'User.middle_initial' => array('size' => 1),
				'User.last_name' => array('before' => ' <span class="required">*</span>', 'escape' => false),
				'User.suffix' => array('after' => '<br />' . $html->tag('span', 'III, Esq., Jr., etc.', array('class' => 'italics')), 'escape' => false),
				
				'Work Information',
				'User.affiliation' => array('before' => ' <span class="required">*</span>', 'escape' => false, 'value' => $groupMembership['Group']['name'], 'disabled' => 'disabled'),
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
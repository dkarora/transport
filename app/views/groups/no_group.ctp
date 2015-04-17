<h2>You're not in an organization!</h2>

<?php if (!$invitePending) : ?>

	<p>You can request to join an existing organization or create a new one.</p>
	
	<h3>Request to join an existing organization</h3>
	
	<?php
		if (!empty($group_options))
		{
			$rows = array(
				'GroupInvite.group_id' => array('options' => $group_options, 'label' => 'Select Organization')
			);
			
			echo $this->element('neat-form', array(
				'rows' => $rows,
				'end' => 'Request',
				'model' => 'GroupInvite',
				'form_opts' => array('action' => 'request')
			));
		}
		else
		{
			echo $html->div('italics bottom-separator', $html->tag('strong', 'No groups exist! Please create a new one below.'));
		}
	?>
	
	<h3>Create a new organization</h3>
	
	<?php
		$rows = array(
			'Group.name' => array('escape' => false, 'label' => 'Organization name', 'after' => '<br /><span class="italics">e.g., "Baystate Roads, Website Team"</span>'),
		);
		
		echo $this->element('neat-form', array(
			'rows' => $rows,
			'model' => 'Group',
			'form_opts' => array('action' => 'create_new'),
			'end' => 'Create'
		));
	?>
	
<?php else : ?>
	
	<p>Your join request has been sent and is pending approval.</p>
	<p><?php echo $html->link('Cancel Request', '/group_invites/cancel_request', array(), 'Are you sure you want to cancel your request?'); ?></p>

<?php endif; ?>
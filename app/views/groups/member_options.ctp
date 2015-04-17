<?php $this->set('subnavcontent', $this->element('orgsubnav')); ?>

<h2>Member Options</h2>

<h3>Leave Organization</h3>

<?php if ($lastAdmin) : ?>
	<div class="text-warning"><p>You are the last administrator! Leaving the group will cause an immediate, irreversible deletion of the organization!</p></div>
<?php endif; ?>

<?php echo $html->link('Leave', '/groups/removemember', array(), 'Are you sure you want to leave the organization?'); ?>

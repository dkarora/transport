<?php
	if ($isGroupMember)
		$this->set('subnavcontent', $this->element('orgsubnav'));
?>

<h2><?php echo $groupMembership['Group']['name']; ?></h2>

<?php if (!empty($groupInvites)) : ?>
	<div class="notification group-invites-notification">
		<?php echo $html->link('There ' . (sizeof($groupInvites) == 1 ? 'is' : 'are') . ' ' . sizeof($groupInvites). ' pending request' . (sizeof($groupInvites) == 1 ? '' : 's') . '.', '/groups/admin_options/#requests'); ?>
	</div>
<?php endif; ?>

<h3>Group Administrators</h3>
<?php echo $this->element('usertable', array('users' => $admins, 'tableId' => 'admins', 'tableType' => 'admin')); ?>

<h3>Group Members</h3>
<?php echo $this->element('usertable', array('users' => $members, 'tableId' => 'users', 'tableType' => 'user')); ?>
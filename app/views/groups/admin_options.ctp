<?php $this->set('subnavcontent', $this->element('orgsubnav')); ?>

<h2>Admin Options</h2>

<h3 id="requests">Group Requests</h3>
<?php if (!empty($groupInvites)) : ?>
	<?php
		echo $form->create('GroupInvite', array('action' => 'respond'));
		
		echo $form->submit('Submit');
	?>
	<table>
		<tr>
			<th>Name</th>
			<th>Action</th>
		</tr>
		
		<?php foreach ($groupInvites as $index => $req) : ?>			
			<tr>
				<td><?php echo $req['User']['first_name'] . ' ' . $req['User']['last_name']; ?></td>
				<td>
					<?php echo $form->hidden("GroupInvite.$index.id", array('value' => $req['Invite']['id'])); ?>
					<?php echo $form->radio("GroupInvite.$index.action", array('accept' => 'Accept', 'reject' => 'Reject', 'none' => 'No Action'), array('legend' => false, 'default' => 'none')); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	
	<?php echo $form->end('Submit'); ?>

<?php else : ?>
	<p>No pending group requests.</p>
<?php endif; ?>

<h3>Remove Member</h3>
<?php if (!empty($memberList)) : ?>
	<table>
		<tr>
			<th>Name</th>
			<th>Remove</th>
		</tr>
		
		<?php foreach ($memberList as $m) : ?>
		<tr>
			<td><?php echo sprintf('%s %s', $m['User']['first_name'], $m['User']['last_name']); ?></td>
			<td><?php echo $html->link('Remove', array('controller' => 'groups', 'action' => 'removemember', $m['Member']['id']), array(), 'Are you sure you want to remove this member?'); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<p>No members!</p>
<?php endif; ?>
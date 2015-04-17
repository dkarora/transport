<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $javascript->link(array('select-all'), false); ?>

<h2>Pending Registrations</h2>

<?php if (empty($pending)) : ?>

	<p>No pending registrations!</p>
	
<?php else : ?>

	<?php echo $form->create('Admin', array('url' => '/admin/pending_registrations/')); ?>

	<table>
		<tr>
			<th>Name</th>
			<th>Username</th>
			<th>Email</th>
			<th>Select</th>
		</tr>
		
		<?php foreach ($pending as $key => $reg) : ?>
			<tr>
				<td><?php echo $reg['User']['first_name'], ' ', $reg['User']['last_name']; ?></td>
				<td><?php echo $reg['User']['username']; ?></td>
				<td><?php echo $reg['User']['email']; ?></td>
				<td>
					<?php echo $form->hidden("User.$key.id", array('value' => $reg['User']['id'])); ?>
					<?php echo $form->checkbox("User.$key.active"); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<?php echo $html->div('', $form->select('Admin.mode', array('accept' => 'Approve Selected Registrations', 'reject' => 'Reject Selected Registrations'), null, array(), false)); ?>
	<?php echo $form->end('Apply Changes'); ?>

<?php endif; ?>

<?php debug ($pending); ?>
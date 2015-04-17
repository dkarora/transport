<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $javascript->link('select-all', false); ?>

<h2>Unintegrated Users</h2>

<?php if (!empty($unintegratedUsers)) : ?>
	<?php echo $form->create('IntegrationRequest', array('action' => 'integrate')); ?>
	<table>
		<?php
			echo $this->element('table-headers', array('headers' =>
				array(
					'UnintegratedUser.last_name' => 'Name',
					'UnintegratedUser.affiliation' => 'Affiliation',
					'Select All' . $form->input('X.select_all', array('div' => false, 'label' => false, 'type' => 'checkbox', 'id' => 'selectall'))
				)
			));
		?>
		
		<?php $i = 0; ?>
		<?php foreach ($unintegratedUsers as $user) : ?>
			<tr <?php echo $i % 2 ? 'class="altrow"' : '' ?>>
				<td><?php echo $user['UnintegratedUser']['first_name'], ' ', $user['UnintegratedUser']['last_name']; ?></td>
				<td><?php echo $user['UnintegratedUser']['affiliation']; ?></td>
				<td>
					<?php echo $form->input("UnintegratedUser.$i.user_id", array('type' => 'hidden', 'value' => $user['UnintegratedUser']['user_id'])); ?>
					<?php echo $form->input("UnintegratedUser.$i.integrate", array('type' => 'checkbox', 'class' => 'selectalltarget')); ?>
				</td>
			</tr>
			<?php $i++; ?>
		<?php endforeach; ?>
	</table>
	<?php echo $form->end('Integrate'); ?>
<?php else : ?>
	<strong>All users have been integrated!</strong>
<?php endif; ?>
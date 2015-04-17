<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'attendee-management'); ?>
<?php $javascript->link(array('select-all'), false); ?>

<h2>Manage Attendees: <?php echo $html->link($workshop['Detail']['name'], $link->viewWorkshop($workshop)), ' ', $timeFormatter->commonDateTime($workshop['Workshop']['date']); ?></h2>

<?php echo $form->create('Attendee', array('action' => 'manage_workshop')); ?>
<?php echo $html->div('', $form->input('Workshop.id', array('value' => $workshop['Workshop']['id'], 'type' => 'hidden'))); ?>
<table style="table-layout: auto">
	<?php echo $this->element('table-headers', array('headers' => array('Last', 'First', 'Affiliation', $form->input('X.selectall', array('type' => 'checkbox', 'id' => 'selectall', 'label' => 'Attended?')), 'Paid', 'Payment Amount', 'Payment Type', 'Check Number', 'Actions'))); ?>
	<?php foreach ($attendees as $i => $a) : ?>
		<tr <?php if ($i % 2 == 0) echo 'class="altrow"'; ?>>
			<td><?php echo $a['User']['last_name']; ?></td>
			<td><?php echo $a['User']['first_name']; ?></td>
			<td><?php echo $a['User']['affiliation']; ?></td>
			<td>
				<?php
					echo $form->input("Attendee.$i.id", array('type' => 'hidden', 'value' => $a['Attendee']['id']));
					echo $form->input("Attendee.$i.attendance", array('type' => 'checkbox', 'checked' => $a['Attendee']['attendance'] > 0, 'label' => false, 'class' => 'selectalltarget'));
				?>
			</td>
			<td>$<?php echo $paid[$a['Attendee']['id']]; ?></td>
			<td><?php echo $html->div('payment-amount', $form->input("PaymentRecord.$i.amount", array('label' => false))); ?></td>
			<td>
				<?php
					echo $form->input("PaymentRecord.$i.attendee_id", array('type' => 'hidden', 'value' => $a['Attendee']['id']));
					echo $form->input("PaymentRecord.$i.payment_opt_id", array('options' => $paymentOptions, 'default' => $defaultPaymentOptionId, 'label' => false, 'class' => 'payment-type'));
				?>
			</td>
			<td><?php echo $html->div('check-number', $form->input("PaymentRecord.$i.check_number", array('class' => 'check-number', 'label' => false))); ?></td>
			<td>
				<div class="actions">
				<?php
					echo $html->link($html->image('attendee-management/print-certificate.png', array('title' => 'Print Workshop Certificate', 'alt' => 'Scroll')), array('controller' => 'workshops', 'action' => 'print_certificates', $workshop['Workshop']['id'], $a['User']['id']), array('escape' => false));
					echo $html->link($html->image('attendee-management/stats.png', array('title' => 'View User Statistics', 'alt' => 'Bar Graph')), array('controller' => 'users', 'action' => 'stats', $a['User']['id']), array('escape' => false));
					echo $html->link($html->image('attendee-management/edit-payment-records.png', array('title' => 'Edit Payment Records', 'alt' => 'Dollar')), array('controller' => 'admin', 'action' => 'payments', $workshop['Workshop']['id'], $a['Attendee']['id']), array('escape' => false));
					echo $html->link($html->image('attendee-management/generate-invoice.png', array('title' => 'Generate Invoice', 'alt' => 'Dollar')), array('controller' => 'attendees', 'action' => 'invoice', $a['Attendee']['id']), array('escape' => false));
					echo $html->link($html->image('attendee-management/email-invoice.png', array('title' => 'Email Invoice', 'alt' => 'Envelope')), array('controller' => 'attendees', 'action' => 'email_invoice', $a['Attendee']['id']), array('escape' => false), 'Really send email invoice? This can\'t be undone.');
					echo $html->link($html->image('attendee-management/edit-user-profile.png', array('title' => 'Edit User Profile', 'alt' => 'Wrench')), array('controller' => 'users', 'action' => 'edit', $a['User']['id']), array('escape' => false));
					echo $html->link($html->image('attendee-management/unenroll.png', array('title' => 'Unenroll', 'alt' => 'X')), array('controller' => 'attendees', 'action' => 'delete', $a['Attendee']['id']), array('class' => 'unenroll', 'escape' => false), sprintf('Really unenroll %s %s from this workshop?', $a['User']['first_name'], $a['User']['last_name']));
				?>
				</div>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
<?php echo $form->end(''); ?>

<?php
	debug ($attendees);
	debug ($workshop);
?>

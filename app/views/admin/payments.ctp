<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $html->css('payments', null, array(), false); ?>

<h2>Payment Records</h2>

<?php if (!$workshop_id) : // select workshop ?>
	<?php echo $this->element('page-numbers'); ?>

	<table id="workshops">
		<?php
			$headers = array(
				'Workshop' => 'Detail.name',
				'City' => 'Workshop.city',
				'Date' => 'Workshop.date',
				'Manage'
			);
			
			echo $this->element('table-headers', array('headers' => $headers));
		?>
		
		<?php $i = 0; ?>
		<?php foreach ($workshops as $workshop): ?>
		<tr
		<?php
			/* apply alt row colors on odd rows */
			if ($i % 2) echo " class='altrow'";
		?>>
			<td><?php echo $workshop['Detail']['name']; ?></td>
			<td><?php echo $workshop['Workshop']['city']; ?></td>
			<td><?php echo $workshop['Workshop']['date']; ?></td>
			<td>
				<?php
					if (!empty($workshop['Attendee']))
						echo $html->link('Manage Attendee Payment Records', '/admin/payments/' . $workshop['Workshop']['id'], array('class' => 'button-link full-width slim'));
					else
						echo 'No Attendees!';
				?>
			</td>
		</tr>
		<?php $i++; ?>
		<?php endforeach; ?>
	</table>

	<?php echo $this->element('page-numbers'); ?>

	<?php debug ($workshops); ?>
	
	
	
<?php elseif (!$attendee_id) : // select attendee ?>
	<h3><?php echo $workshop['Detail']['name']; ?></h3>
	<div>
		<div><strong>Public Cost:</strong> $<?php echo $workshop['Workshop']['public_cost']; ?></div>
		<p><strong>Private Cost:</strong> $<?php echo $workshop['Workshop']['private_cost']; ?></p>
	</div>
	
	<table>
		<tr>
			<th>Attendee</th>
			<th>City</th>
			<th>Affiliation</th>
			<th>Paid</th>
			<th>Manage</th>
		</tr>
		
		<?php $i = 0; ?>
		<?php foreach ($attendees as $attendee): ?>
		<tr class="
		<?php
			/* apply alt row colors on odd rows */
			if ($i % 2)
				echo 'altrow';
		?>">
			<td><?php echo $attendee['User']['full_name']; ?></td>
			<td><?php echo $attendee['User']['city']; ?></td>
			<td><?php echo $attendee['User']['affiliation']; ?></td>
			<td>$<?php echo $paid[$attendee['Attendee']['id']]; ?></td>
			<td><?php echo $html->link('Manage', array($workshop_id, $attendee['Attendee']['id']), array('class' => 'button-link full-width slim')); ?></td>
		</tr>
		<?php $i++; ?>
		<?php endforeach; ?>
	</table>
	<?php debug ($attendees); ?>
	
	
	
<?php else : // manage payment records ?>

	<h3><?php echo $workshop['Detail']['name']; ?></h3>
	<div>
		<div><strong>Public Cost:</strong> $<?php echo $workshop['Workshop']['public_cost']; ?></div>
		<p><strong>Private Cost:</strong> $<?php echo $workshop['Workshop']['private_cost']; ?></p>
	</div>

	<?php echo $html->link('&laquo; Select another attendee', '/admin/payments/' . $workshop_id, array('class' => 'backlink', 'escape' => false)); ?>
	<?php echo $html->link('&laquo; Select another workshop', '/admin/payments/', array('class' => 'backlink', 'escape' => false)); ?>
	
	<p>
		<strong style="font-size: 150%;"><?php echo $attendee['User']['full_name'], ', ', $attendee['Workshop']['Detail']['name'], ', ', $attendee['Workshop']['date']; ?></strong><br />
		<strong>Total Paid: $<?php echo $total; ?></strong>
	</p>

	<?php if (!empty($records)) : ?>
		<h3>Existing Records</h3>
		
		<table>
			<tr>
				<th>Paid Date</th>
				<th>Amount</th>
				<th>Type</th>
				<th>Check Number</th>
				<th>Delete</th>
			</tr>
			
			<?php $i = 0; ?>
			
			<?php foreach ($records as $record) : ?>
				<tr <?php if ($i % 2) echo " class='altrow'";?>>
					<td><?php echo $record['PaymentRecord']['paid_on']; ?></td>
					<td>$<?php echo $record['PaymentRecord']['amount']; ?></td>
					<td><?php echo $record['PaymentOption']['value']; ?></td>
					<td>
						<?php
							if ($record['PaymentOption']['value'] == 'Check')
								echo $record['PaymentRecord']['check_number'];
							else
								echo 'N/A';
						?>
					</td>
					<td><?php echo $html->link('Delete', array('controller' => 'payment_records', 'action' => 'delete', $record['PaymentRecord']['id'])); ?></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
	
	<h3>Add a Record</h3>
	<?php
		echo $form->create('PaymentRecord', array('action' => 'add'));
		echo $form->hidden('PaymentRecord.attendee_id', array('value' => $attendee_id));
		echo $form->input('PaymentRecord.amount');
		echo $form->input('PaymentRecord.payment_opt_id', array('options' => $payment_options, 'label' => 'Payment Type'));
		echo $form->input('PaymentRecord.check_number', array('label' => 'Check Number (optional)'));
		echo $form->end('Add');
	?>
	
	<?php echo $html->link('&laquo; Select another attendee', '/admin/payments/' . $workshop_id, array('class' => 'backlink', 'escape' => false)); ?>
	<?php echo $html->link('&laquo; Select another workshop', '/admin/payments/', array('class' => 'backlink', 'escape' => false)); ?>

	<?php debug ($total); ?>
	<?php debug ($records); ?>
	<?php debug ($attendee); ?>

<?php endif; ?>
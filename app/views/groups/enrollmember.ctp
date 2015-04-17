<?php
	$this->set('subnavcontent', $this->element('orgsubnav'));
	$bodyClass = 'enroll-group-member';
 ?>

<h2>Enroll Members in Workshops</h2>


<?php if (isset($this->params['named']['workshopid'])) : ?>

	<?php $bodyClass .= ' step-2'; ?>

	<h3><?php echo $workshop['Detail']['name'] . ', ' . $workshop['Workshop']['date'] ?></h3>

	<?php echo $html->link('&laquo; Choose another workshop', array('action' => 'enrollmember'), array('escape' => false, 'class' => 'backlink')); ?>

	<h4>Members currently signed up for this workshop</h4>
	<?php if (!empty($signedup)) : ?>
		<table id="signedup">
			<tr>
				<th>Name</th>
				<th>Undo Enrollment</th>
				<th>Generate Invoice</th>
			</tr>
			
			<?php $i = 0; ?>
			<?php foreach ($signedup as $index => $user): ?>
			<tr
			<?php
				/* apply alt row colors on odd rows */
				if ($i % 2) echo " class='altrow'";
			?>>
				<td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
				<td><?php echo $html->link('Undo', '/workshops/remove_attendee/' . $workshopId . '/' . $user['id']); ?></td>
				<td><div class="actions">
				      <?php echo $html->link($html->image('attendee-management/generate-invoice.png', array('title' => 'Generate Invoice', 'alt' => 'Dollar')), array('controller' => 'attendees', 'action' => 'invoice', $attendees[$i]['Attendee']['id']), array('escape' => false))?>
				    </div>
				</td>
			</tr>
			<?php $i++; ?>
			<?php endforeach; ?>
		</table>
	
	<?php else : ?>
		<div class="paragraph"><strong>No members have been signed up!</strong></div>
	<?php endif; ?>


	<h4>Members available to sign up for this workshop</h4>
	<?php if ($workshopFull) : ?>
		<div class="paragraph"><strong>Workshop is full!</strong></div>
	<?php elseif (!empty($notsignedup)) : ?>
		<table id="notsignedup">
			<tr>
				<th>Name</th>
				<th>Enroll Member</th>
			</tr>
			
			<?php $i = 0; ?>
			<?php foreach ($notsignedup as $index => $user): ?>
			<tr
			<?php
				/* apply alt row colors on odd rows */
				if ($i % 2) echo " class='altrow'";
			?>>
				<td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
				<td><?php echo $html->link('Enroll', '/workshops/add_attendee/' . $workshopId . '/' . $user['id']); ?></td>
			</tr>
			<?php $i++; ?>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<div class="paragraph"><strong>No members left to sign up!</strong></div>
	<?php endif; ?>

	<?php echo $html->link('&laquo; Choose another workshop', array('action' => 'enrollmember'), array('escape' => false, 'class' => 'backlink')); ?>

<?php else : ?>
	
	<?php $bodyClass .= ' step-1'; ?>

	<p>Select a workshop to begin.</p>

	<?php echo $this->element('page-numbers'); ?>

	<table id="workshops">
		<?php
			$headers = array(
				'Workshop' => 'Detail.name',
				'City' => 'Workshop.city',
				'Date' => 'Workshop.date',
				'Category',
				'Manage Enrollment'
			);
			
			echo $this->element('table-headers', array('headers' => $headers));
		?>
		
		<?php $i = 0; ?>
		<?php foreach ($workshops as $index => $workshop): ?>
		<tr
		<?php
			/* apply alt row colors on odd rows */
			if ($i % 2) echo " class='altrow'";
		?>>
			<td><?php echo $workshop['Detail']['name']; ?></td>
			<td><?php echo $workshop['Workshop']['city']; ?></td>
			<td><?php echo $timeFormatter->commonDate($workshop['Workshop']['date']); ?></td>
			<td><?php echo $workshop['Detail']['Category']['name']; ?></td>
			<td><?php echo $html->link('Manage', array('workshopid' => $workshop['Workshop']['id'])); ?></td>
		</tr>
		<?php $i++; ?>
		<?php endforeach; ?>
	</table>

	<?php echo $this->element('page-numbers'); ?>

<?php endif; ?>

<?php $this->set('bodyClass', $bodyClass); ?>

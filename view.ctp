<?php $this->set('subnavcontent', $this->element('workshopsubnav',
	array('merge' => array('View Workshop: ' . $workshop['Detail']['name'] => $link->viewWorkshop($workshop)), 'here' => 'View Workshop: ' . $workshop['Detail']['name'], 'position' => 0)));
	
	$this->set('bodyClass', 'two-column view-workshop');
	$javascript->link('jquery.fancybox-1.3.4.pack', false);
	$html->css('fancybox/jquery.fancybox-1.3.4.css', null, array(), false);
?>

<script type="text/javascript" charset="utf-8">
	$(function() {
		$('a.modal-trigger').fancybox({autoDimensions: false, width: 600, overlayColor: '#000', ajax: {data: { workshopId: <?php echo $workshop['Workshop']['id']; ?>}}});
	});
</script>

<h2><?php echo $workshop['Detail']['name']; ?></h2>

<?php if ($workshop['Workshop']['unlisted']) : ?>
	<div class="themoreyouknow">This workshop is unlisted. Only those with the URL can access it.</div>
<?php endif; ?>

<div id="right-column">
<div id="right-column-content">
	<div id="description-wrapper" class="paragraph bottom-separator">
		<h3>Description</h3>
		<div id="description">
			<?php echo $bbCode->bb2html($workshop['Detail']['description']); ?>
		</div>
	</div>
	
	<?php if (!empty($workshop['Workshop']['notes'])) : ?>
		<div id="additional-notes-wrapper" class="paragraph bottom-separator">
			<h3>Additional Notes</h3>
			<div id="additional-notes">
				<?php echo $bbCode->bb2html($workshop['Workshop']['notes']); ?>
			</div>
		</div>
	<?php endif; ?>
	
	<?php if (!empty($workshop['Agenda'])) : ?>
	<h3>Agenda</h3>
	<div id="agenda">
		<?php
			$dayNum = 1;
			for ($i = 0; $i < sizeof($workshop['Agenda']); $i++)
			{
				if ($i == 0)
				{
					echo $html->tag('h4', 'Day ' . $dayNum++ . ': ' . date('F jS, Y', strtotime($workshop['Agenda'][$i]['timestamp'])), array('class' => 'day-header'));
					echo $html->tag('div', null, array('class' => 'day'));
					echo $html->tag('table', null, array('class' => 'agenda-table no-frills'));
				}
				else if (strtotime(date('Y-m-d', strtotime($workshop['Agenda'][$i]['timestamp']))) > strtotime(date('Y-m-d', strtotime($workshop['Agenda'][$i - 1]['timestamp']))))
				{
					echo $html->tag('/table', null);
					echo $html->tag('/div', null);
					echo $html->tag('h4', 'Day ' . $dayNum++ . ': ' . date('F jS, Y', strtotime($workshop['Agenda'][$i]['timestamp'])), array('class' => 'day-header'));
					echo $html->tag('div', null, array('class' => 'day'));
					echo $html->tag('table', null, array('class' => 'agenda-table'));
				}
				
				echo $html->tag('tr', null);
				echo $html->tag('td', $html->image('timestamp-icon.png', array('alt' => 'Time:', 'class' => 'agenda-timestamp')) . date('h:i a', strtotime($workshop['Agenda'][$i]['timestamp'])), array('class' => 'agenda-timestamp'));
				echo $html->tag('td', $bbCode->bb2html($workshop['Agenda'][$i]['description']));
				echo $html->tag('/tr', null);
			}
			
			echo $html->tag('/table', null);
			echo $html->tag('/div', null);
		?>
	</div> <!-- agenda -->
	<?php endif; ?>
</div> <!-- right column content -->
</div> <!-- right column -->

<div id="left-column">
<div id="left-column-content">
	<div id="flyer-preview">
		<?php
			if (!empty($flyerThumbnailUrl))
				echo $html->link(
					$html->image($flyerThumbnailUrl, array('alt' => 'Flyer thumbnail for ' . $workshop['Detail']['name'])),
					array('controller' => 'flyers', 'action' => 'view', 'flyerid' => $workshop['Flyer']['id']),
					array('escape' => false)
				);
			else
				echo $html->image('no-flyer-thumbnail.png', array('class' => 'no-flyer', 'alt' => 'No flyer thumbnail available.'));
		?>
	</div>
	
	<div>
		<?php
			if (!empty($workshop['Workshop']['flyer_id']))
			{
				echo $html->link('View flyer (PDF)',
					array(
						'controller' => 'flyers',
						'action' => 'view',
						'flyerid' => $workshop['Flyer']['id']
					),
					
					array('class' => 'button-link slim full-width', 'id' => 'view-flyer-button')
				);
			}
			else
			{
				echo $html->div('button-link slim full-width disabled', 'No flyer available', array('id' => 'view-flyer-button'));
			}
		?>
	</div>
	
	<div>
		<?php
			echo $progressBar->bar('Enrollment Total: ', $nAttendees, $capacity);
			
			$cutoff = strtotime('-3 days', strtotime($workshop['Workshop']['date']));
			$now = time();
			$registrationClosed = ($now > $cutoff);
			$conclusion = strtotime($workshop['Agenda'][sizeof($workshop['Agenda']) - 1]['timestamp']);
			$workshopConcluded = ($now > $conclusion);
			$locked = $registrationClosed || $workshopConcluded;
			
			if ($workshopConcluded)
				echo $html->div('button-link disabled full-width slim', 'Workshop Concluded', array('id' => 'enroll-button'));
			// if registration is closed, no one gets in or out
			else if ($registrationClosed)
				echo $html->div('button-link disabled full-width slim', 'Registration Closed', array('id' => 'enroll-button'));
			if ($session->check('User.id'))
			{
				$isAttendee = in_array($workshop['Workshop']['id'], $enrolled);
				
				// allow the user to unenroll even if workshop is full
				if ($isAttendee && !$locked)
				{
					echo $html->link(
						'Unenroll From This Workshop &raquo;',
						'remove_attendee/' . $workshop['Workshop']['id'],
						array('class' => 'button-link reject full-width slim', 'id' => 'enroll-button'), null, array('escape' => false));
				}
				else if ($workshopFull)
					echo $html->div('button-link disabled full-width slim', 'Workshop Full', array('id' => 'enroll-button'));
				else if (!$isAttendee && !$workshopFull && !$locked)
				{
					echo $html->link(
						'Attend This Workshop &raquo;',
						'add_attendee/' . $workshop['Workshop']['id'],
						array('class' => 'button-link accept full-width slim', 'id' => 'enroll-button'), null, array('escape' => false));
						
						
				}
				
				if ($session->read('GroupMember.permissions'))
					echo $html->link('Manage Group Attendance &raquo;', array('controller' => 'groups', 'action' => 'enrollmember', 'workshopid' => $workshopLinkId), array('class' => 'button-link full-width slim'), null, array('escape' => false));
				
				if ($workshopEditable)
					echo $html->link('Edit This Workshop &raquo;', array('controller' => 'workshops', 'action' => 'edit', $workshop['Workshop']['id']), array('class' => 'button-link full-width slim'), null, array('escape' => false));
			}
			else
			{
				echo $html->link(
					'Log in to attend this workshop &raquo;',
					$link->loginLocation(),
					array('class' => 'button-link full-width slim', 'id' => 'enroll-button'), null, array('escape' => false));
			}
			
			echo $html->link('Who\'s Attending?', array('action' => 'get_attendees', $workshop['Workshop']['id']), array('class' => 'modal-trigger button-link full-width slim'));
		?>
	</div>
	
	<div class="paragraph" id="iconed-info">
		<p class="icon-left" id="bsr-credits">Worth <strong><?php echo $workshop['Detail']['credits']; ?></strong> Baystate Roads Credits<br/>Worth <strong><?php echo $workshop['Detail']['ceu_credits']; ?></strong> CEU Credits</p>
		<p class="icon-left" id="cost">Costs <strong>$<?php echo $workshop['Workshop']['public_cost']; ?></strong> (<strong>Public Sector</strong>)<br/>Costs <strong>$<?php echo $workshop['Workshop']['private_cost']; ?></strong> (<strong>Private Sector</strong>)</p>
		<p class="icon-left" id="instructor">Instructed by <strong><?php echo $workshop['Workshop']['instructor']; ?></strong></p>
		<p class="icon-left" id="location"><?php echo $link->mapDirections($workshop); ?></p>
	</div>
</div> <!-- left column content -->
</div> <!-- left column -->

<div class="clearfix"></div>

<?php
	debug ($workshop);
?>
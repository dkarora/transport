<?php $this->set('subnavcontent', $this->element('workshopsubnav')); ?>
<?php $this->set('bodyClass', 'browse-workshops'); ?>

<h2>Browse Workshops</h2>

<?php echo $this->element('page-numbers'); ?>

<table id="workshops" style="table-layout: auto">
	<?php
		$headers = array(
			'Workshop' => 'Detail.name',
			'City' => 'Workshop.city',
			'Date' => 'Workshop.date',
			'Registered/Capacity',
			'Credits'
		);
		
		echo $this->element('table-headers', array('headers' => $headers));
	?>
	
	<?php foreach ($workshops as $index => $workshop): ?>
	<tr
		<?php
			/* apply alt row colors on odd rows */
			if ($index % 2)
				echo " class='altrow'";
		?>>
		
		<td>
			<?php
				echo $html->link($workshop['Detail']['name'], $link->viewWorkshop($workshop));
				
				$cutoff = strtotime('-1 days', strtotime($workshop['Workshop']['date']));
				$now = time();
				$registrationClosed = ($now > $cutoff);
				$conclusion = strtotime($workshop['Agenda'][sizeof($workshop['Agenda']) - 1]['timestamp']);
				$workshopConcluded = ($now > $conclusion);
				
				if ($workshopConcluded)
					echo $html->tag('span', 'Workshop Concluded', array('class' => 'workshop-subtext'));
				else if ($registrationClosed)
					echo $html->tag('span', 'Registration Closed', array('class' => 'workshop-subtext'));
			?>
		</td>
		<td><?php echo $workshop['Workshop']['city']; ?></td>
		<td><?php echo date('m/d/Y', strtotime($workshop['Workshop']['date'])); ?></td>
		<td><?php echo $progressBar->bar('', $registered[$workshop['Workshop']['id']], $workshop['Workshop']['capacity'], false); ?></td>
		<td><?php echo $workshop['Detail']['credits']; ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo $this->element('page-numbers'); ?>
<?php debug ($workshops); ?>
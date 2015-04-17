<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Print Workshop Certificates</h2>

<?php echo $this->element('page-numbers'); ?>

<table id="workshops">
	<?php
		$headers = array(
			'Workshop' => 'Detail.name',
			'City' => 'Workshop.city',
			'Date' => 'Workshop.date',
			'Print'
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
		<td><?php echo $timeFormatter->commonDateTime($workshop['Workshop']['date']); ?></td>
		<td>
			<?php
				if (!empty($workshop['Attendee']))
				{
					echo $html->div('', $html->link('Print All', '/workshops/print_certificates/' . $workshop['Workshop']['id'], array('class' => 'button-link full-width slim')));
					echo $html->div('', $html->link('Print Single', '/admin/single_workshop_certificate/' . $workshop['Workshop']['id'], array('class' => 'button-link full-width slim')));
				}
				else
					echo $html->div('button-link full-width disabled slim', 'No Attendees');
			?>
		</td>
	</tr>
	<?php $i++; ?>
	<?php endforeach; ?>
</table>

<?php echo $this->element('page-numbers'); ?>

<?php debug ($workshops); ?>
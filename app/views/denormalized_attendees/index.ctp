<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Export Attendees</h2>

<?php echo $html->link('Export All', array('action' => 'export', 'all'), array('class' => 'button-link full-width')); ?>

<?php if (!empty($workshops)) : ?>
	<?php echo $this->element('page-numbers'); ?>
	<table>
		<?php
			echo $this->element('table-headers', array(
				'headers' => array(
					'Workshop' => 'Detail.name',
					'Town' => 'Workshop.city',
					'Date' => 'Workshop.date',
					'Export CSV'
				)
			));
		?>
		
		<?php foreach ($workshops as $i => $ws) : ?>
			<tr class="<?php echo ($i % 2 == 0 ? 'altrow' : ''); ?>">
				<td><?php echo $ws['Detail']['name']; ?></td>
				<td><?php echo $ws['Workshop']['city']; ?></td>
				<td><?php echo $timeFormatter->commonDateTime($ws['Workshop']['date']); ?></td>
				<td><?php echo $html->link('Export CSV', array('action' => 'export', $ws['Workshop']['id']), array('class' => 'button-link full-width')); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php echo $this->element('page-numbers'); ?>
<?php else : ?>
	<strong>No workshops!</strong>
<?php endif;
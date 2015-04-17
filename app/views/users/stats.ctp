<?php $this->set('bodyClass', 'user-stats'); ?>

<h2><?php echo sprintf('%s (%s)', $user['User']['full_name'], $userTitle); ?></h2>
<p><span class="big-number"><?php echo $creditTotals['CreditTotal']['road_scholar_credits']; ?></span> Road Scholar Credits</p>
<p><span class="big-number"><?php echo $creditTotals['CreditTotal']['ceu_credits']; ?></span> CEU Credits</p>
<p><span class="big-number"><?php echo $numWorkshopsAttended; ?></span> Workshops attended (<?php echo $numNoShows; ?> no-shows)</p>

<h3>Upcoming Workshops</h3>
<?php if (!empty($upcomingWorkshops)) : ?>
	<table>
		<tr>
			<th>Workshop</th>
			<th>Date</th>
			<th>Location</th>
			<th>City/Town</th>
			<th>Credits</th>
		</tr>
		
		<?php $i = 0; ?>
		<?php foreach ($upcomingWorkshops as $key => $ws) : ?>
		<tr<?php if ($i % 2) : echo ' class="altrow"'; endif;?>>
			<td><?php echo $html->link($ws['Detail']['name'], $link->viewWorkshop($ws)); ?></td>
			<td><?php echo $timeFormatter->commonDate($ws['Workshop']['date']); ?></td>
			<td><?php echo $ws['Workshop']['location']; ?></td>
			<td><?php echo $ws['Workshop']['city']; ?></td>
			<td><?php echo $ws['Detail']['credits']; ?></td>
		</tr>
		<?php $i++; ?>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<p>No upcoming workshops!</p>
<?php endif; ?>

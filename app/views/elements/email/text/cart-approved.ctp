Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,



An administrator has approved your cart. This email will serve as your receipt.

<?php if (!empty($videos)) : ?>
	Videos
	------

<?php foreach ($videos as $index => $item) : ?>
	<?php echo $item['Video']['name']; ?> (<?php echo $item['Instance']['format']; ?>)
<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($pubs)) : ?>
	Publications
	------------
	
<?php foreach ($pubs as $index => $item) : ?>
	<?php echo $item['Publication']['name']; ?>
<?php endforeach; ?>
<?php endif; ?>

The check-in date for these items is <?php echo date('F j, Y', strtotime('+3 weeks')); ?>.



Thanks,

The Baystate Roads Team
http://baystateroads.org/
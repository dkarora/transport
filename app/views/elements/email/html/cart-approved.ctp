<p>Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,</p>



<p>An administrator has approved your cart. This email will serve as your receipt.</p>

<?php if (!empty($videos)) : ?>
<p>
	<span style="font-weight: bold; font-size: 1.25em;">Videos</span><br />

<?php foreach ($videos as $index => $item) : ?>
	<?php echo $item['Video']['name']; ?> (<?php echo $item['Instance']['format']; ?>)<br />
<?php endforeach; ?>
</p>
<?php endif; ?>

<?php if (!empty($pubs)) : ?>
<p>
	<span style="font-weight: bold; font-size: 1.25em;">Publications</span><br />
	
<?php foreach ($pubs as $index => $item) : ?>
	<?php echo $item['Publication']['name']; ?> <br />
	
<?php endforeach; ?>
</p>
<?php endif; ?>

<p>The check-in date for these items is <strong><?php echo date('F j, Y', strtotime('+3 weeks')); ?>.</strong></p>



<p>
Thanks, <br />

The Baystate Roads Team<br />
http://baystateroads.org
</p>
<p>Hello <?php echo sprintf('%s %s', $admin['User']['first_name'], $admin['User']['last_name']); ?>,</p>


<p>A user has registered. Here are the credentials:</p>

<p>
Name: <?php echo $user['User']['full_name']; ?><br />

Username: <?php echo $user['User']['username']; ?><br />

Address:<br />
<?php echo $user['User']['address_line1'], (!empty($user['User']['address_line2']) ? ', ' . $user['User']['address_line2'] : ''); ?><br />

<?php echo $user['User']['city'], ', ', $user['User']['state']; ?><br />

<?php echo $user['User']['zip']; ?><br />

Phone: <?php echo $user['User']['phone']; ?><br />

<?php if (!empty($user['User']['admin'])): ?>
	<p><strong>Warning: This user is identified as a site-wide administrator. If this is incorrect, do not approve this user.</strong></p>
<?php endif; ?>
</p>



<p>Go to <?php echo $html->link(Router::url('/admin/pending_registrations/', true), Router::url('/admin/pending_registrations/', true)); ?> to approve or deny this registration.</p>
	


Thanks,<br />

The Baystate Roads Team<br />
http://baystateroads.org/

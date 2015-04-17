Hello <?php echo sprintf('%s %s', $admin['User']['first_name'], $admin['User']['last_name']); ?>,


A user has registered. Here are the credentials:

Name: <?php echo $user['User']['full_name']; ?>
Username: <?php echo $user['User']['username']; ?>
Address:
<?php echo $user['User']['address_line1'], (!empty($user['User']['address_line2']) ? ', ' . $user['User']['address_line2'] : ''); ?>
<?php echo $user['User']['city'], ', ', $user['User']['state']; ?>
<?php echo $user['User']['zip']; ?>
Phone: <?php echo $user['User']['phone']; ?>

<?php if (!empty($user['User']['admin'])): ?>
Warning: This user is identified as a site-wide administrator. If this is incorrect, do not approve this user.
<?php endif; ?>

Go to <?php echo Router::url('/admin/pending_registrations/', true); ?> to approve or deny this registration.


Thanks,

The Baystate Roads Team
http://baystateroads.org/
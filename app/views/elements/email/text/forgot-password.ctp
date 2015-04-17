Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,


Your password has been reset. Here are your new login credentials:

Username: <?php echo $user['User']['username']; ?>
Password: <?php echo $newpass; ?>

Go to <?php echo Router::url('/users/login/', true); ?> to log in with your new password.


Thanks,

The Baystate Roads Team
http://baystateroads.org/
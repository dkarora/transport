<p>Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,</p>


<p>Your password has been reset. Here are your new login credentials:</p>

<p>
Username: <?php echo $user['User']['username']; ?><br />
Password: <?php echo $newpass; ?><br />
</p>

<p>Go to <?php echo $html->link(Router::url('/users/login/', true), Router::url('/users/login/', true)); ?> to log in with your new password.</p>


Thanks,<br />

The Baystate Roads Team<br />
http://baystateroads.org/

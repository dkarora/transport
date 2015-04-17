<p>Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,</p>


<p>
<p>Thank you for registering at Baystate Roads!</p>

<p>Although your account is now active, <strong>you will still need to <?php echo $html->link('log in', Router::url('/users/login/', true)); ?> and <?php echo $html->link('enroll for any workshop/conference', Router::url('/workshops', true)); ?> that you would like to participate in</strong>. Please visit the website, log in, and choose the workshop that you would like to enroll in.  </p>

<p>With your new account, many functions are now available to you, including borrowing from our lending libraries, enrolling in workshops, and more.</p>

<p>Your account is now active. Visit <a href="<?php echo Router::url('/users/login/', true); ?>"><?php echo Router::url('/users/login/', true); ?></a> to log in.</p>
</p>


<p>
Thanks,<br />

The Baystate Roads Team<br />
http://baystateroads.org/
</p>


You are receiving this email because you, or someone in your organizaion, registered an account with the username <?php echo $user['User']['username']; ?> at the Baystate Roads Program website. (<?php echo Router::url('/', true); ?>). If you have any questions or concerns about the registration process or your enrollment in any workshop or event, please contact Dan Montagna at the Baystate Roads Program at 413-545-5403.
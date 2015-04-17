Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,



Thank you for registering at Baystate Roads!

IMPORTANT: Although your account is now active, you will still need to log in and enroll for any workshop/conference that you would like to participate in. Please visit the website, log in, and choose the workshop that you would like to enroll in.

With your new account, many functions are now available to you, including borrowing from our lending libraries, enrolling in workshops, and more.

Your account is now active. Visit <?php echo Router::url('/users/login/', true); ?> to log in.



Thanks,

The Baystate Roads Team
http://baystateroads.org/


You are receiving this email because you, or someone in your organizaion, registered an account with the username <?php echo $user['User']['username']; ?> at the Baystate Roads Program website. (<?php echo Router::url('/', true); ?>).
If you have any questions or concerns about the registration process or your enrollment in any workshop or event, please contact Dan Montagna at the Baystate Roads Program at 413-545-5403.
Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,


A new cart has been submitted by <?php echo sprintf('%s %s', $cartOwner['User']['first_name'], $cartOwner['User']['last_name']); ?>.

Go to <?php echo Router::url('/admin/carts/', true); ?> to approve or reject it.


Thanks,

The Baystate Roads Team
http://baystateroads.org
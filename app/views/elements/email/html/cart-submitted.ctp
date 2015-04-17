<p>Hello <?php echo sprintf('%s %s', $user['User']['first_name'], $user['User']['last_name']); ?>,</p>


<p>A new cart has been submitted by <?php echo sprintf('%s %s', $cartOwner['User']['first_name'], $cartOwner['User']['last_name']); ?>.</p>

<p>Go to <?php echo $html->link(Router::url('/admin/carts/', true), Router::url('/admin/carts/', true)); ?> to approve or reject it.</p>


Thanks,<br />

The Baystate Roads Team<br />
http://baystateroads.org/
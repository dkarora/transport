<?php
	if (empty($cartItems))
		$cartItems = array();
?>

<?php
	$loggedIn = $session->read('User.id');
	
	if ($loggedIn) :
?>

<?php echo $html->link('View Cart (' . sizeof($cartItems) . ' item' . (sizeof($cartItems) == 1 ? '' : 's') . ')', '/cart_items/', array('escape' => false, 'class' => 'button-link slim cart-preview')); ?>

<?php endif; ?>
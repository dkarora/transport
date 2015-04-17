<?php $this->set('subnavcontent', $this->element('libraries-subnav')); ?>
<?php echo $this->element('cart', array('cartItems' => $cartItems)); ?>

<h2>Publications</h2>
<strong>Don't see what you want? New content is added/updated regularly, so please check back often!</strong>

<?php
	$loggedIn = $session->read('User.id');
	
	$headers = array(
		'Name' => 'Publication.name',
		'Category' => 'Category.name',
		'ID' => 'Publication.bsr_assignment',
		'Published' => 'Publication.year_published',
		'Availability',
	);
	
	if ($loggedIn)
		$headers[] = 'Add To Cart';
	
	$data = $publications;
	
	$keying = array(
		array('Publication', 'name'),
		array('Category', 'name'),
		array('Publication', 'bsr_assignment'),
		array('Publication', 'year_published'),
		array('Publication', 'availability'),
	);
	
	if ($loggedIn)
		$keying[] = array('Publication', 'add_to_cart_links');
	
	echo $this->element('page-numbers');
	$paginatorTable->printTable($headers, $data, $keying, array('style' =>'table-layout: auto'));
	echo $this->element('page-numbers');
?>

<?php debug ($publications); ?>
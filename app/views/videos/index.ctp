<?php $this->set('subnavcontent', $this->element('libraries-subnav')); ?>
<?php $this->set('bodyClass', 'library videos'); ?>

<?php echo $this->element('cart', array('cartItems' => $cartItems)); ?>

<h2>Videos</h2>
<strong>Don't see what you want? New content is added/updated regularly, so please check back often!</strong>

<?php
	$loggedIn = $session->read('User.id');
	
	$headers = array(
		'Name' => 'Video.name',
		'Category' => 'Category.name',
		'ID' => 'Video.bsr_assignment',
		'Published' => 'Video.year_published',
		'Availability',
	);
	
	if ($loggedIn)
		$headers[] = 'Add To Cart';
	
	$data = $videos;
	
	$keying = array(
		array('Video', 'name'),
		array('Category', 'name'),
		array('Video', 'bsr_assignment'),
		array('Video', 'year_published'),
		array('Video', 'availability'),
	);
	
	if ($loggedIn)
		$keying[] = array('Video', 'add_to_cart_links');
	
	echo $this->element('page-numbers');
	$paginatorTable->printTable($headers, $data, $keying, array('style' =>'table-layout: auto'));
	echo $this->element('page-numbers');
?>

<?php debug ($videos); ?>
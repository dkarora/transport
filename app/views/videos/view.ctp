<?php
	$this->set('subnavcontent', $this->element('libraries-subnav',
		array('merge' => array('Video: ' . $video['Video']['name'] => array('controller' => 'videos', 'action' => 'view', $video['Video']['id'], $slugGenerator->generate($video['Video']['name']))))));
?>

<?php echo $this->element('cart', array('cartItems' => $cartItems)); ?>

<h2><?php echo $video['Video']['name']; ?></h2>

<p><strong>Category: </strong><?php echo $video['Category']['name']; ?></p>
<p><strong>Video ID: </strong><?php echo $video['Category']['designation'], ' ', $video['Video']['bsr_assignment']; ?></p>
<p><strong>Source: </strong><?php echo $video['Video']['source']; ?></p>
<p><strong>Year Published: </strong><?php echo $video['Video']['year_published']; ?></p>
<p><strong>Quality Rating: </strong><?php echo $html->image(sprintf('quality-%s-star.png', $video['Video']['quality_rating']), array('alt' => sprintf('%s star', $video['Video']['quality_rating']))); ?></p>
<p><strong>Length: </strong><?php echo $video['Video']['length']; ?> minutes</p>
<p><strong>Availability: <br/></strong><?php echo $availability; ?></p>

<?php if ($session->read('User.id')) : ?>
	<p><?php echo $cartlinks; ?></p>
<?php else : ?>
	<p><strong>Please <?php echo $link->login('log in'); ?> or <?php echo $html->link('register', '/users/register'); ?> to request videos.</strong></p>
<?php endif; ?>

<?php debug ($video); ?>
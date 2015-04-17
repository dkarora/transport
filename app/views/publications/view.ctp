<?php
	$this->set('subnavcontent', $this->element('libraries-subnav',
		array('merge' => array('Publication: ' . $pub['Publication']['name'] => array('controller' => 'publications', 'action' => 'view', $pub['Publication']['id'], $slugGenerator->generate($pub['Publication']['name']))))));
?>

<?php echo $this->element('cart', array('cartItems' => $cartItems)); ?>

<h2><?php echo $pub['Publication']['name']; ?></h2>

<p><strong>Category: </strong><?php echo $pub['Category']['name']; ?></p>
<p><strong>Video ID: </strong><?php echo $pub['Category']['designation'], ' ', $pub['Publication']['bsr_assignment']; ?></p>
<p><strong>Source: </strong><?php echo $pub['Publication']['source']; ?></p>
<p><strong>Year Published: </strong><?php echo $pub['Publication']['year_published']; ?></p>
<p><strong>Availability: </strong><?php echo $availability; ?></p>

<?php if ($session->read('User.id')) : ?>
	<p><?php echo $cartlinks; ?></p>
<?php else : ?>
	<p><strong>Please <?php echo $link->login('log in'); ?> or <?php echo $html->link('register', '/users/register'); ?> to request videos.</strong></p>
<?php endif; ?>

<?php debug ($pub); ?>
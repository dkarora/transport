<?php $this->set('subnavcontent', $this->element('libraries-subnav')); ?>

<?php echo $html->link('Empty Cart', '/cart_items/empty_cart/', array('class' => 'button-link slim empty-cart'), 'Are you sure you want to empty your cart?'); ?>

<h2>My Cart</h2>

<?php if (empty($cart)) : ?>
	
	<p><strong>Your cart is empty!</strong></p>

<?php else : ?>

	<?php echo $form->create('CartItem', array('url' => '/cart_items/modify/')); ?>
	
<?php endif; ?>



<?php if (!empty($cart)) : ?>

	<table>
		<tr>
			<th>Title</th>
			<th>Source</th>
			<th>ID</th>
			<th>Type</th>
			<th>Remove?</th>
		</tr>
		
		<?php foreach ($cart as $index => $item) : ?>
			<?php
				if (empty($item['Video']))
					$type = 'Publication';
				else
					$type = 'Video';
			?>
			
			<tr>
				<?php if ($type == 'Video') : ?>
					<td><?php echo $item['Video']['name']; ?></td>
					<td><?php echo $item['Video']['source']; ?></td>
					<td><?php echo $item['Video']['bsr_assignment']; ?></td>
					<td>Video (<?php echo $item['VideoInstance']['format']; ?>)</td>
					<td><?php echo $form->checkbox("CartItem.$index.remove_item"); ?></td>
					
				<?php else : ?>
					<td><?php echo $item['name']; ?></td>
					<td><?php echo $item['source']; ?></td>
					<td><?php echo $item['bsr_assignment']; ?></td>
					<td>Publication</td>
					<td><?php echo $form->checkbox("CartItem.$index.remove_item"); ?></td>
				<?php endif; ?>
			</tr>
			
		<?php endforeach; ?>
	</table>
	
<?php endif; ?>



<?php
	if (!empty($cart))
	{
		echo $form->end('Modify');
		echo $html->link('Checkout', '/cart_items/checkout/', array('class' => 'button-link slim cart-checkout'));
	}
?>

<?php debug ($cart); ?>
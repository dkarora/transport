<?php foreach ($newsletters as $i => $n) : ?>

	<div class="newsletter-preview-container<?php if ($i % 2 == 0) { echo ' left-column'; } ?>">
		<h4 class="newsletter-preview-heading">
			<?php echo $link->markedLink(
				$n['Newsletter']['season'] . ' ' . $n['Newsletter']['year'],
				array(
					'controller' => 'newsletters',
					'action' => 'view',
					$n['Newsletter']['id']
				),
				
				array('class' => 'newsletter-preview-link'),
				false, 'pdf'
				);
			?>
		</h4>
		
		<span class="newsletter-preview-summary">
			<?php echo $n['Newsletter']['summary']; ?>
		</span>
	</div>
	
	<?php if ($i % 2) : ?>
		<br class="clearfix"></br>
	<?php endif; ?>

<?php endforeach; ?>

<br class="clearfix"></br>
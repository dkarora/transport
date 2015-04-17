<?php $this->set('subnavcontent', $this->element('news_posts_subnav')); ?>
<?php $html->css('newsletters', null, array(), false); ?>

<h2>
	<?php
		if (!empty($this->params['named']['year']))
			echo $this->params['named']['year'];
	?>
	Newsletters
</h2>

<?php if (!empty($all_years) && $all_years) : ?>

	<?php
		foreach ($years as $year)
			echo $html->link('' . $year . ' &raquo;', array('year' => $year), array('escape' => false, 'class' => 'newsletter-year-link'));
	?>

<?php else : ?>

	<?php if (empty($this->params['named']['year'])) : ?>

		<h3>Most Recent</h3>
		<?php echo $this->element('newsletter_preview', array('newsletters' => $most_recent)); ?>
		
		<h3>Past Newsletters</h3>
		<?php
			foreach ($years as $year)
				echo $html->link('' . $year . ' &raquo;', array('year' => $year), array('escape' => false, 'class' => 'newsletter-year-link'));
			echo $html->link('All &raquo;', array('all_years'), array('escape' => false, 'class' => 'newsletter-year-link'));
		?>

	<?php else : ?>

		<?php echo $html->link('&laquo; Choose another year', array('year' => null), array('class' => 'backlink', 'escape' => false)); ?>
		
		<?php echo $this->element('newsletter_preview', array('newsletters' => $year_newsletters)); ?>
		
		<?php echo $html->link('&laquo; Choose another year', array('year' => null), array('class' => 'backlink', 'escape' => false)); ?>

	<?php endif; ?>
<?php endif; ?>
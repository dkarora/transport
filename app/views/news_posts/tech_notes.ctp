<?php $this->set('subnavcontent', $this->element('news_posts_subnav')); ?>
<?php $html->css('newsletters', null, array(), false); ?>

<h2>
	<?php
		if ($show_all)
			echo 'All ';
	?>
	Tech Notes
</h2>

<?php
	echo $this->element('tech_note_preview', array('technotes' => $tn, 'model_name' => 'TechNote'));
	
	if (!$show_all)
		echo $html->link('All Tech Notes &raquo;', array('all'), array('escape' => false, 'class' => 'newsletter-year-link'));
	else
		echo $html->link('&laquo; Recent Tech Notes', array(), array('escape' => false, 'class' => 'newsletter-year-link'));
	
	debug ($tn);
?>
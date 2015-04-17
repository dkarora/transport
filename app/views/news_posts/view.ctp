<?php
	$this->set('bodyClass', 'news-post');
	$here = 'View news post: ' . $post['NewsPost']['title'];
	$merge = array($here => $link->viewNewsPost($post));
	
	$this->set('subnavcontent', $this->element('news_posts_subnav', array('here' => $here, 'merge' => $merge)));
	
	function neighbors($html, $link, $neighbors)
	{
		if (!empty($neighbors['prev']))
		{
			echo $html->link(
				'&laquo; ' . $neighbors['prev']['NewsPost']['title'],
				$link->viewNewsPost($neighbors['prev']),
				array('class' => 'post-neighbors-previous'),
				false,
				false
			);
		}
		
		if (!empty($neighbors['next']))
		{
			echo $html->link(
				$neighbors['next']['NewsPost']['title'] . ' &raquo;',
				$link->viewNewsPost($neighbors['next']),
				array('class' => 'post-neighbors-next'),
				false,
				false
			);
		}
	}
?>

<?php if (!empty($here)) : ?>

	<div class="post-neighbors-container post-neighbors-container-top">
		<?php neighbors($html, $link, $neighbors); ?>
	</div>

	<div id="post-container">
		<h2 id="post-title"><?php echo $post['NewsPost']['title']; ?></h2>
		<span id="post-author">Posted by <?php echo $post['Author']['first_name'], ' ', $post['Author']['last_name']; ?></span>
		<span id="post-timestamp"><?php echo $timeFormatter->commonDate($post['NewsPost']['date_posted']); ?></span>
		<div id="post-content"><?php echo $bbCode->bb2html($post['NewsPost']['content']); ?></div>
	</div>
	
	<div id="social-links">
		<?php echo $html->div('facebook-like', $facebook->like()); ?>
	</div>
	
	<div class="post-neighbors-container post-neighbors-container-bottom">
		<?php neighbors($html, $link, $neighbors); ?>
	</div>

<?php else : ?>

	<h2 id="post-title">News Post not found.</h2>
	
	<div id="post-content">
		We're sorry, but we couldn't find the post you requested.
		Please click your browser's back button and choose a different link, or <?php echo $html->link('click here to go back to the home page', '/'); ?>.
	</div>

<?php endif; ?>
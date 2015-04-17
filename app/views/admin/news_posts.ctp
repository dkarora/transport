<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>News Posts</h2>

<div id="write-new-post">
	<h3>Write New Post</h3>

	<?php		
		$rows = array(
			'NewsPost.title' => array(),
			'NewsPost.content' => array('type' => 'textarea'),
			'NewsPost.preview' => array('type' => 'textarea')
		);
		
		if ($linkedToTwitter)
			$rows['NewsPost.tweet'] = array('type' => 'checkbox', 'label' => 'Tweet?', 'checked' => 'checked');
		
		echo $this->element('neat-form',
			array(
				'model' => 'NewsPost',
				'form_opts' => array('action' => 'create'),
				'rows' => $rows,
				'end' => 'Post',
				'css' => array('neat-form', 'admin_news_posts')
			)
		);
	?>
</div>

<div id="preview-pane" style="display: none; background-color: white;">
	<h3>Post Preview</h3>
	<div id="preview-content"></div>
</div>

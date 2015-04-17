<?php $html->css('search', null, array(), false); ?>
<?php
	$script =
<<<EOT
	$(function() {
		$('#BigSearchBox').focus();
	});
EOT;
	$javascript->codeBlock($script, array('inline' => false));
?>
<?php
	$this->set('subnavcontent', $this->element('news_posts_subnav',
		array(
			'merge' => 
				array('Search' => '/search/'),
				'here' => 'Search',
				'position' => 0
			)
		)
	);
?>

<h2>Search Baystate Roads</h2>

<?php
	$select = array(
		'everything' => 'Entire Site',
		'newsletters' => 'Newsletters',
		'tech_notes' => 'Tech Notes',
		'workshops' => 'Workshops'
	);
	
	echo $form->create('Search', array('url' => '/search/'));
	echo $form->input('Search.0.query', array('label' => false, 'id' => 'BigSearchBox'));
	echo $form->select('Search.0.area', $select, null, array(), false);
	echo $form->end('Search');
?>
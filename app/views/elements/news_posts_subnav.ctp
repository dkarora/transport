<?php
	if (!isset($options) || empty($options))
	{
		$options = array(
			'Latest News' => '/news_posts/index',
			'Newsletters' => '/news_posts/newsletters',
			'Tech Notes' => '/news_posts/tech_notes',
			'Links' => '/links/',
		);
	}
	
	$cd = array();
	
	if (empty($attr))
		$attr = $cd;
	else
		$attr = array_merge($attr, $cd);
	
	$opt = array();
	$opt['options'] = $options;
	if (!empty($merge))
		$opt['merge'] = $merge;
	if (!empty($here))
		$opt['here'] = $here;
	if (!empty($position))
		$opt['position'] = $position;
	$opt['attr'] = $attr;
	
	echo $this->element('subnavgeneric', $opt);
?>
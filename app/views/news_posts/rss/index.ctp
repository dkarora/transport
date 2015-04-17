<?php
    $this->set('documentData', array(
        'xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));

    $this->set('channelData', array(
        'title' => __("Baystate Roads - News", true),
        'link' => $html->url('/', true),
        'description' => __("The latest news from the Baystate Roads Program.", true),
        'language' => 'en-us'));
	
	foreach ($posts as $post)
	{
		$postTime = strtotime($post['NewsPost']['date_posted']);
		
		$postLink = array(
			'controller' => 'news_posts',
			'action' => 'view',
			'post_id' => $post['NewsPost']['id']);
		
		$bodyText = $post['NewsPost']['content'];
		
		// apply bbcode
		$bodyText = $bbCode->bb2html($bodyText);
		
		echo  $rss->item(array(), array(
			'title' => $post['NewsPost']['title'],
			'link' => $postLink,
			'guid' => array('url' => $postLink, 'isPermaLink' => 'true'),
			'description' =>  $bodyText,
			'dc:creator' => $post['Author']['first_name'] . ' ' . $post['Author']['last_name'],
			'pubDate' => $post['NewsPost']['date_posted']));
	}

?>
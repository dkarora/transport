<?php
	class LinkHelper extends AppHelper
	{
		var $helpers = array('Html', 'SlugGenerator');
		var $linkComponent = null;
		
		function __construct($options = null)
		{
			parent::__construct($options);
			
			App::import('Component', 'Link');
			$this->linkComponent = new LinkComponent();
		}
		
		function _links_anchors($arr)
		{
			$anch = array();
			foreach ($arr as $id => $text)
				$anch[] = $this->Html->link($text, "#" . $id);
			
			return implode($anch, ' | ');
		}

		function links_categories()
		{
			$cats = array(
				'bsr-links' => "BSR's Links of Interest",
				'web-training' => "Web Based Training",
				'streaming-video' => "Streaming Video",
				'training-opps' => "Training Opportunities",
				'listserv' => "Listservs",
				'transportation' => "Transportation Topics",
				'web-conferences' => "Web Conferences"
			);
			
			return $this->Html->div('categories', $this->_links_anchors($cats));
		}
		
		function links_make_link($title, $description, $url, $image, $alt)
		{
			return $this->Html->div('link', $this->Html->link($this->Html->image($image, array('class' => 'thumbnail', 'alt' => $alt)), $url, array('escape' => false, 'rel' => 'external')) .
				$this->Html->tag('div', $this->markedLink($title, $url, array('rel' => 'external')), array('class' => 'link-title')) . 
				$this->Html->tag('div', $description, array('class' => 'link-description')));
		}
		
		function loginLocation($return = true)
		{
			return '/users/login/' . ($return ? base64_encode('/' . $this->params['url']['url']) : '');
		}
		
		function login($text, $return = true)
		{
			return $this->Html->link($text, $this->loginLocation($return));
		}
		
		function viewWorkshop($workshop)
		{
			if (!$workshop['Workshop']['unlisted'])
				return array('controller' => 'workshops', 'action' => 'view', $workshop['Workshop']['id'], $this->SlugGenerator->generate($workshop['Detail']['name']));
			else
				return array('controller' => 'workshops', 'action' => 'view', $this->linkComponent->_alphaID($workshop['Workshop']['id']), $this->SlugGenerator->generate($workshop['Detail']['name']));
		}
		
		function viewNewsPost($post)
		{
			return array('controller' => 'news_posts', 'action' => 'view', $post['NewsPost']['id'], $this->SlugGenerator->generate($post['NewsPost']['title']));
		}
		
		function _matchFileType($types, $url)
		{
			$pattern = '/\\b(https?:\\/\\/|ftp:\\/\\/)(([a-zA-Z0-9\\-]+)\\.)*[a-zA-Z]+\\/\\S+\\%s(\\?[\\S]+|\\b)/i';
			
			if (is_array($types))
			{
				foreach ($types as $t)
				{
					$pat = sprintf($pattern, $t);
					
					if (preg_match($pat, $url))
						return true;
				}
			}
			else if (is_string($types))
			{
				$pat = sprintf($pattern, $types);
				
				if (preg_match($pat, $url))
					return true;
			}
			
			return false;
		}
		
		function markedLink($title, $url = null, $options = array(), $confirmMessage = false, $forceType = false)
		{
			$ext = '';
			$loc = Router::url($url, true);
			$video = array('.3gp', '.asx', '.avi', '.flv', '.mkv', '.mov', '.mp4', '.mpg', '.mpeg', '.rm', '.rv', '.swf', '.wmv');
			$audio = array('.aac', '.aif', '.aiff', '.au', '.m4a', '.mid', '.midi', '.mp3', '.ogg', '.ra', '.ram', '.wav', '.wave', '.wma', '.wv');
			
			// edge case for email
			if ($forceType === 'email' || strtolower(substr(trim($loc), 0, 6)) == 'mailto')
			{
				$ext = $this->Html->image('email-link.png', array('alt' => '(Email)', 'class' => 'link-mark', 'title' => 'This is an email link.'));
			}
			else if ($forceType === 'video' || $this->_matchFileType($video, $loc))
			{
				$ext = $this->Html->image('video-link.png', array('alt' => '(Video)', 'class' => 'link-mark', 'title' => 'This is a video link.'));
			}
			else if ($forceType === 'image' || $this->_matchFileType($audio, $loc))
			{
				$ext = $this->Html->image('audio-link.png', array('alt' => '(Audio)', 'class' => 'link-mark', 'title' => 'This is an audio link.'));
			}
			else if ($forceType === 'pdf' || $this->_matchFileType('.pdf', $loc))
			{
				$ext = $this->Html->image('pdf-link.png', array('alt' => '(PDF)', 'class' => 'link-mark', 'title' => 'This is an Adobe PDF link.'));
			}
			
			return $ext . $this->Html->link($title, $url, $options, $confirmMessage);
		}
		
		function mapDirections($location, $text = 'Get Directions', $provider = 'google')
		{
			$where = '';
			
			if (is_string($location))
				$where = $location;
			// are all workshops going to be in massachusetts? i hope so...
			else if (is_array($location))
				$where = $location['Workshop']['location'] . ', ' . $location['Workshop']['city'] . ' MA';
			
			// compact multiple spaces into one
			$origloc = $where;
			$where = preg_replace('/\s\s+/', ' ', $where);
			
			// send to provider
			$provider = strtolower($provider);
			
			if ($provider == 'google')
			{
				$format = 'http://maps.google.com/maps?saddr=&daddr=%s';
				$url = sprintf($format, $where);
			}
			// bing, yahoo, mapquest, etc can go here
			else
			{
				return '';
			}
			
			return $where . '<br />' . $this->Html->link(nl2br($text), $url, array('escape' => false, 'rel' => 'external'));
		}
	}
?>
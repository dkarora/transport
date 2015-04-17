<?php
	class VideosController extends AppController
	{
		var $name = 'Videos';
		var $components = array('SessionPlus', 'ErrorListFormatter');
		var $helpers = array('PaginatorTable', 'Html', 'Paginator', 'SlugGenerator', 'Link');
		var $uses = array('CartItem', 'Video');
		
		var $pageTitle = 'Baystate Roads &rsaquo; ';
		
		var $cartItems = array();
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$adminActions = array('add');
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
			
			$this->pageTitle .= 'Videos';
			
			$userid = $this->Session->read('User.id');
			if ($userid)
				$this->cartItems = $this->CartItem->getCart($userid);
			
			$this->set('cartItems', $this->cartItems);
		}
		
		function add()
		{
			if (!empty($this->data))
			{
				if ($this->Video->saveAll($this->data))
					$this->SessionPlus->flashSuccess('Video created.');
				else
				{
					$this->SessionPlus->flashError('Video could not be created. Please fix the errors below.');
					$this->ErrorListFormatter->setOldData($this->data);
				}
			}
			
			$this->redirect($this->referer());
		}
		
		function _generateCartLinks($video, $userid)
		{
			if (!$userid)
				return '';
			
			// add to cart links
			$cartlinks = array();
			App::import('Helper', 'Html');
			$html = new HtmlHelper();
			
			foreach ($video['Instance'] as $inst)
			{
				$this->Video->Instance->id = $inst['id'];
				$checkouts = $this->Video->Instance->getCheckouts();
				$remaining = $inst['quantity'] - sizeof($checkouts);
				
				$avail[] = sprintf('%s: %s/%s', $inst['format'], $remaining, $inst['quantity']);
				
				if ($remaining <= 0)
				{
					$cartlinks[] = $html->tag('span', sprintf('No %ss available', $inst['format']), array('class' => 'button-link slim disabled'));
				}
				else if ($this->CartItem->isVideoInCart($inst['id'], $userid))
				{
					return $html->tag('div', 'In Cart', array('class' => 'button-link slim disabled'));
				}
				
				else if ($this->Video->Instance->isRequested($inst['id'], $userid))
				{
					return $html->tag('div', 'Ordered', array('class' => 'button-link slim disabled'));
				}
				
				else if ($this->Video->Instance->isCheckedOut($inst['id'], $userid))
				{
					return $html->tag('div', 'Checked Out', array('class' => 'button-link slim disabled'));
				}
				else
				{
					$cartlinks[] = $html->link(sprintf('Request %s', $inst['format']), array('controller' => 'cart_items', 'action' => 'add', 'video', $inst['id']), array('class' => 'button-link slim'));
				}
			}
			
			return implode($cartlinks, '<br />');
		}
		
		function _generateAvailability($video)
		{
			$avail = array();
			
			foreach ($video['Instance'] as $inst)
			{
				$this->Video->Instance->id = $inst['id'];
				$checkouts = $this->Video->Instance->getCheckouts();
				$remaining = $inst['quantity'] - sizeof($checkouts);
				
				$avail[] = sprintf('%s: %s/%s', $inst['format'], $remaining, $inst['quantity']);
			}
			
			return implode($avail, '<br />');
		}
		
		function index()
		{
			$this->pageTitle .= '';
			
			App::import('Helper', 'SlugGenerator');
			App::import('Helper', 'Html');
			$html = new HtmlHelper();
			$slugGenerator = new SlugGeneratorHelper();
			
			// paginate videos, send to view
			$videos = $this->paginate('Video');
			
			$userid = $this->Session->read('User.id');
			
			// modify data to conform to table
			for ($i = 0; $i < sizeof($videos); $i++)
			{
				// add designation to bsr id
				$videos[$i]['Video']['bsr_assignment'] = $videos[$i]['Category']['designation'] . ' ' . $videos[$i]['Video']['bsr_assignment'];
				
				$videos[$i]['Video']['availability'] = $this->_generateAvailability($videos[$i]);
				
				// make links for names
				$videos[$i]['Video']['name'] = $html->link($videos[$i]['Video']['name'], array('controller' => 'videos', 'action' => 'view', $videos[$i]['Video']['id'], $slugGenerator->generate($videos[$i]['Video']['name'])));
				
				// make pretty links for cart
				$videos[$i]['Video']['add_to_cart_links'] = $this->_generateCartLinks($videos[$i], $userid);
			}
			
			$this->set('videos', $videos);
		}
		
		function view($video_id = null, $slug = null)
		{
			// if no video id panic
			if (empty($video_id))
			{
				$this->SessionPlus->flashError('Something went wrong!');
				$this->redirect($this->referer());
			}
			
			// get the video
			$this->Video->id = $video_id;
			$video = $this->Video->find('first');
			
			$userid = $this->Session->read('User.id');
			
			// if bad video id panic
			if (empty($video))
			{
				$this->SessionPlus->flashError('Oh dear, something went terribly wrong.');
				$this->redirect($this->referer());
			}
			
			// if no slug redirect
			if (empty($slug))
			{
				App::import('Helper', 'SlugGenerator');
				$slugGenerator = new SlugGeneratorHelper();
				
				$this->redirect(array('controller' => $this->params['controller'], 'action' => $this->params['action'], $video_id, $slugGenerator->generate($video['Video']['name'])));
			}
			
			// make pretty availability
			$avail = $this->_generateAvailability($video);
			
			// make pretty cart links
			$cartlinks = $this->_generateCartLinks($video, $userid);
			
			$this->pageTitle .= ' &rsaquo; ' . $video['Video']['name'];
			$this->set('video', $video);
			$this->set('cartlinks', $cartlinks);
			$this->set('availability', $avail);
		}
	}
?>
<?php
	class PublicationsController extends AppController
	{
		var $name = 'Publications';
		var $components = array('SessionPlus', 'ErrorListFormatter');
		var $helpers = array('PaginatorTable', 'SlugGenerator', 'Link');
		var $uses = array('Publication', 'CartItem');
		var $cartItems = array();
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->pageTitle .= 'Publications';
			
			$adminActions = array('add');
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
			
			$userid = $this->Session->read('User.id');
			if ($userid)
				$this->cartItems = $this->CartItem->getCart($userid);
			
			$this->set('cartItems', $this->cartItems);
		}
		
		function index()
		{
			App::import('Helper', 'SlugGenerator');
			App::import('Helper', 'Html');
			$html = new HtmlHelper();
			$slugGenerator = new SlugGeneratorHelper();
			
			// paginate videos, send to view
			$videos = $this->paginate('Publication');
			
			$userid = $this->Session->read('User.id');
			
			// modify data to conform to table
			for ($i = 0; $i < sizeof($videos); $i++)
			{
				// add designation to bsr id
				$videos[$i]['Publication']['bsr_assignment'] = $videos[$i]['Category']['designation'] . ' ' . $videos[$i]['Publication']['bsr_assignment'];
				
				$videos[$i]['Publication']['availability'] = $this->_generateAvailability($videos[$i]);
				
				// make links for names
				$videos[$i]['Publication']['name'] = $html->link($videos[$i]['Publication']['name'], array('controller' => 'publications', 'action' => 'view', $videos[$i]['Publication']['id'], $slugGenerator->generate($videos[$i]['Publication']['name'])));
				
				// make pretty links for cart
				$videos[$i]['Publication']['add_to_cart_links'] = $this->_generateCartLinks($videos[$i], $userid);
			}
			
			$this->set('publications', $videos);
		}
		
		function _generateCartLinks($pub, $userid)
		{
			if (!$userid)
				return '';
			
			// add to cart links
			$cartlinks = array();
			App::import('Helper', 'Html');
			$html = new HtmlHelper();
			
			$checkouts = $this->Publication->getCheckoutCount();
			$remaining = $pub['Publication']['quantity'] - $checkouts;
			
			// all copies checked out
			if ($remaining <= 0)
				return $html->tag('span', 'None available', array('class' => 'button-link slim disabled'));
			
			// in cart
			else if ($this->CartItem->isPublicationInCart($pub['Publication']['id'], $userid))
				return $html->tag('div', 'In Cart', array('class' => 'button-link slim disabled'));
			
			// requested (submitted cart, not yet approved)
			else if ($this->Publication->isRequested($pub['Publication']['id'], $userid))
				return $html->tag('div', 'Ordered', array('class' => 'button-link slim disabled'));
			
			// checked out (cart approved)
			else if ($this->Publication->isCheckedOut($pub['Publication']['id'], $userid))
				return $html->tag('div', 'Checked Out', array('class' => 'button-link slim disabled'));
			
			// otherwise, available
			else
				return $html->link('Request', array('controller' => 'cart_items', 'action' => 'add', 'publication', $pub['Publication']['id']), array('class' => 'button-link slim'));
		}
		
		function _generateAvailability($pub)
		{
			$this->Publication->id = $pub['Publication']['id'];
			$checkouts = $this->Publication->getCheckoutCount();
			$remaining = $pub['Publication']['quantity'] - $checkouts;
			
			return sprintf('%s/%s', $remaining, $pub['Publication']['quantity']);
		}
		
		function add()
		{
			$this->ErrorListFormatter->deleteOldData();
			
			if (!empty($this->data['Publication']))
			{
				if ($this->Publication->save($this->data['Publication']))
					$this->SessionPlus->flashSuccess('Publication created.');
				else
				{
					$this->SessionPlus->flashError('Publication not saved! Please correct the errors below.');
					$this->ErrorListFormatter->setOldData($this->data);
				}
			}
			
			$this->redirect($this->referer());
		}
		
		function view($pub_id = null, $slug = null)
		{
			// if no video id panic
			if (empty($pub_id))
			{
				$this->SessionPlus->flashError('Something went wrong!');
				$this->redirect($this->referer());
			}
			
			// get the video
			$this->Publication->id = $pub_id;
			$pub = $this->Publication->find('first');
			
			$userid = $this->Session->read('User.id');
			
			// if bad video id panic
			if (empty($pub))
			{
				$this->SessionPlus->flashError('Oh dear, something went terribly wrong.');
				$this->redirect($this->referer());
			}
			
			// if no slug redirect
			if (empty($slug))
			{
				App::import('Helper', 'SlugGenerator');
				$slugGenerator = new SlugGeneratorHelper();
				
				$this->redirect(array('controller' => $this->params['controller'], 'action' => $this->params['action'], $pub_id, $slugGenerator->generate($pub['Publication']['name'])));
			}
			
			// make pretty availability
			$avail = $this->_generateAvailability($pub);
			
			// make pretty cart links
			$cartlinks = $this->_generateCartLinks($pub, $userid);
			
			$this->pageTitle .= ' &rsaquo; ' . $pub['Publication']['name'];
			$this->set('pub', $pub);
			$this->set('cartlinks', $cartlinks);
			$this->set('availability', $avail);
		}
	}
?>
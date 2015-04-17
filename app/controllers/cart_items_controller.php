<?php
	class CartItemsController extends AppController
	{
		var $name = 'CartItems';
		var $components = array('SessionPlus', 'EmailPlus');
		var $uses = array('CartItem', 'VideoRequest', 'VideoCheckout', 'User', 'PublicationCheckout', 'PublicationRequest');
		
		var $pageTitle = 'Baystate Roads &rsaquo; Cart';
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$adminActions = array('check_in_publication', 'check_in_video', 'approve_cart');
			$userActions = array('index', 'add', 'modify', 'empty_cart', 'checkout');
			
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
			$this->SessionPlus->denyAnonymousUsersFrom($userActions);
		}
		
		function check_in_publication($checkout_id = null)
		{
			// check id
			if (empty($checkout_id))
			{
				$this->SessionPlus->flashError('No checkout specified.');
				$this->redirect($this->referer());
			}
			
			// check for valid checkout
			$this->PublicationCheckout->id = $checkout_id;
			$co = $this->PublicationCheckout->find('first');
			if (empty($co))
			{
				$this->SessionPlus->flashError('Invalid publication checkout.');
				$this->redirect($this->referer());
			}
			
			// do the delete
			if ($this->PublicationCheckout->delete())
			{
				$coId = $co['PublicationCheckout']['id'];
				$format = '%s checked in %s (%d) for %s (%d)';
				$message = sprintf($format,
					$this->SessionPlus->loggableUserName(),
					$co['Publication']['name'], $co['Publication']['id'],
					$co['Owner']['full_name'], $co['Owner']['id']
				);
				$this->logAction($this->PublicationCheckout, 'delete', $message, $coId);
				
				$this->SessionPlus->flashSuccess('Publication checked in.');
			}
			else
				$this->SessionPlus->flashError('Publication not checked in! Please try again.');
			
			$this->redirect($this->referer());
		}
		
		function check_in_video($checkout_id = null)
		{
			// check id
			if (empty($checkout_id))
			{
				$this->SessionPlus->flashError('No checkout specified.');
				$this->redirect($this->referer());
			}
			
			// check for valid video
			$this->VideoCheckout->id = $checkout_id;
			$co = $this->VideoCheckout->find('first');
			if (empty($co))
			{
				$this->SessionPlus->flashError('Invalid video checkout.');
				$this->redirect($this->referer());
			}
			
			if ($this->VideoCheckout->delete())
			{
				$this->VideoRequest->Instance->Video->id = $co['Instance']['video_id'];
				$targetVideo = $this->VideoRequest->Instance->Video->find('first', array('recursive' => -1));
				
				$coId = $co['VideoCheckout']['id'];
				$format = '%s checked in %s (%s) (%d) for %s (%d)';
				$message = sprintf($format,
					$this->SessionPlus->loggableUserName(),
					$targetVideo['Video']['name'], $co['Instance']['format'], $co['Instance']['id'],
					$co['Owner']['full_name'], $co['Owner']['id']
				);
				$this->logAction($this->VideoCheckout, 'delete', $message, $coId);
				
				$this->SessionPlus->flashSuccess('Video checked in.');
			}
			else
				$this->SessionPlus->flashError('Video not checked in! Please try again.');
			
			$this->redirect($this->referer());
		}
		
		function approve_cart($user_id = null)
		{
			// no user id, nothing to do
			if (empty($user_id))
				$this->redirect($this->referer());
			
			// get all video requests for this user
			$this->VideoRequest->recursive = -1;
			$vreqs = $this->VideoRequest->find('all', array('conditions' => array('user_id' => $user_id), 'fields' => array('user_id', 'instance_id')));
			$vreqs = Set::combine($vreqs, '{n}.VideoRequest.instance_id', '{n}.VideoRequest');
			
			// get all publication requests for this user
			$this->PublicationRequest->recursive = -1;
			$preqs = $this->PublicationRequest->find('all', array('conditions' => array('user_id' => $user_id), 'fields' => array('user_id', 'publication_id')));
			$preqs = Set::combine($preqs, '{n}.PublicationRequest.publication_id', '{n}.PublicationRequest');
			
			// check if there's anything to save
			if (empty($preqs) && empty($vreqs))
			{
				$this->SessionPlus->flashError('Nothing to approve!');
				$this->redirect($this->referer());
			}
			
			$videos = $this->VideoRequest->getCombinedRequests($user_id);
			$pubs = $this->PublicationRequest->getCombinedRequests($user_id);
			$this->VideoCheckout->query('START TRANSACTION');
			
			// delete/saveAll returns false if data is empty, so check
			if ((empty($vreqs) ? true : $this->VideoRequest->deleteAll(array('VideoRequest.user_id' => $user_id)) && $this->VideoCheckout->saveAll($vreqs)) &&
				(empty($preqs) ? true : $this->PublicationRequest->deleteAll(array('PublicationRequest.user_id' => $user_id)) && $this->PublicationCheckout->saveAll($preqs))
			)
			{
				$this->VideoCheckout->query('COMMIT');
				$this->SessionPlus->flashSuccess('Cart approved.');
				
				$this->User->recursive = 0;
				$this->User->id = $user_id;
				$user = $this->User->find('first');
				
				$this->set('user', $user);
				$this->set('videos', $videos);
				$this->set('pubs', $pubs);
				
				// attempt to send email
				$this->EmailPlus->sendNoReply('cart-approved', $user['User']['email'], 'Your Baystate Roads cart has been approved');
				
				if (Configure::read())
					debug ($this->Session->read('Message.email'));
			}
			else
			{
				$this->VideoCheckout->query('ROLLBACK');
				$this->SessionPlus->flashError('Cart not approved!');
			}
			
			$this->redirect($this->referer());
		}
		
		function index()
		{
			$this->pageTitle .= 'Libraries &rsaquo; My Cart';
			
			$cartItems = array();
			
			$userid = $this->Session->read('User.id');
			
			$cart = $this->CartItem->getCombinedCart($userid);
			$this->set('cart', $cart);
		}
		
		function add($type = null, $id = null)
		{
			// don't do anything if we're missing a parameter
			if (empty($type) || empty($id))
			{
				$this->SessionPlus->flashError('Something went wrong!');
				$this->redirect($this->referer());
			}
			
			if ($type == 'video')
			{
				$this->_addVideo($id);
			}
			else if ($type == 'publication')
			{
				$this->_addPublication($id);
			}
			// if not any of the right types then bail
			else
			{
				$this->SessionPlus->flashError('Something went wrong!');
				$this->redirect($this->referer());
			}
		}
		
		function modify()
		{
			$del = array();
			
			foreach ($this->data['CartItem'] as $i => $item)
			{
				if ($item['remove_item'])
					$del[] = $i;
			}
			
			if ($this->CartItem->deleteAll(array('CartItem.id' => $del)))
				$this->SessionPlus->flashSuccess('Cart modified.');
			else
				$this->SessionPlus->flashError('Cart was not modified! Please try again.');
			
			$this->redirect($this->referer());
		}
		
		function empty_cart()
		{
			$userid = $this->Session->read('User.id');
			
			if ($this->CartItem->deleteAll(array('CartItem.user_id' => $userid)))
				$this->SessionPlus->flashSuccess('Cart emptied.');
			else
				$this->SessionPlus->flashError('Cart not emptied! Please try again.');
			
			$this->redirect($this->referer());
		}
		
		function checkout()
		{
			// login check
			$userid = $this->Session->read('User.id');
			
			// check out all videos
			$videos = $this->CartItem->getCartVideoIds($userid);
			
			// check out all publications
			$publications = $this->CartItem->getCartPublicationIds($userid);
			
			// check for maximum items
			$maxCheckouts = 10;
			
			$cartSize = sizeof($videos) + sizeof($publications);
			$checkoutsSize = $this->VideoCheckout->find('count', array('conditions' => array('user_id' => $userid), 'recursive' => -1));
			$checkoutsSize += $this->PublicationCheckout->find('count', array('conditions' => array('user_id' => $userid), 'recursive' => -1));
			
			if ($cartSize + $checkoutsSize > $maxCheckouts)
			{
				$remove = ($cartSize + $checkoutsSize - $maxCheckouts);
				$this->SessionPlus->flashError('The maximum amount of checkouts is limited to ' . $maxCheckouts . '. Please remove ' . $remove . ' item' . ($remove > 1 ? 's' : '') . ' from your cart.');
				$this->redirect($this->referer());
			}
			
			$vreq = array();
			foreach ($videos as $v)
				$vreq[] = array('instance_id' => $v, 'user_id' => $userid);
			
			$preq = array();
			foreach ($publications as $p)
				$preq[] = array('publication_id' => $p, 'user_id' => $userid);
			
			// do a transaction
			$this->CartItem->begin();
			
			// set del
			$del = array_merge(array_keys($videos), array_keys($publications));
			
			// save videos
			if ((!empty($vreq) ? $this->VideoRequest->saveAll($vreq) : true) && (!empty($preq) ? $this->PublicationRequest->saveAll($preq) : true) && $this->CartItem->deleteAll(array('CartItem.id' => $del)))
			{
				$this->SessionPlus->flashSuccess('Your order has been submitted. An administrator will review your cart and clear it for checkout.');
				$this->CartItem->commit();
				
				// get the cart owner
				$this->User->id = $userid;
				$this->User->recursive = -1;
				$co = $this->User->find('first');
				
				// send email to admins
				$admins = $this->User->getAdmins();
				
				foreach ($admins as $admin)
				{
					$this->EmailPlus->reset();
					$this->set('user', $admin);
					$this->set('cartOwner', $co);
					
					$this->EmailPlus->sendNoReply('cart-submitted', $admin['User']['email'], 'Baystate Roads: Cart Submitted');
					
					if (Configure::read())
						debug ($this->Session->read('Message.email'));
				}
			}
			else
			{
				$this->SessionPlus->flashError('Your order could not be submitted! Please try again.');
				$this->CartItem->rollback();
			}
			
			$this->redirect($this->referer());
		}
		
		function _addVideo($instance_id)
		{
			$user_id = $this->Session->read('User.id');
			
			if ($this->CartItem->addVideo($instance_id, $user_id))
				$this->SessionPlus->flashSuccess('Video added to cart.');
			else
				$this->SessionPlus->flashError('Video could not be added! Please try again.');
			
			$this->redirect($this->referer());
		}
		
		function _addPublication($pub_id)
		{
			$user_id = $this->Session->read('User.id');
			
			if ($this->CartItem->addPublication($pub_id, $user_id))
				$this->SessionPlus->flashSuccess('Publication added to cart.');
			else
				$this->SessionPlus->flashError('Publication could not be added! Please try again.');
			
			$this->redirect($this->referer());
		}
	}
?>
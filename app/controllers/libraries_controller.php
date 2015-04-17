<?php
	class LibrariesController extends AppController
	{
		var $name = 'Libraries';
		var $uses = array('CartItem');
		var $pageTitle = 'Baystate Roads &rsaquo; ';
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->pageTitle .= 'Libraries';
		}
		
		function index()
		{
			$cartItems = array();
			
			$userid = $this->Session->read('User.id');
			if ($userid)
				$cartItems = $this->CartItem->getCart($userid);
			
			$this->set('cartItems', $cartItems);
		}
	}
?>
<?php
	class SessionPlusComponent extends Object
	{
		var $name = 'SessionPlus';
		var $components = array('Session');
		
		function initialize(&$controller, $settings = array())
		{
			// saving the controller reference for later use
			$this->controller =& $controller;
		}
		
		function flashSuccessAndRedirect($text, $to = null, $layout = 'default')
		{
			$this->flashSuccess($text, $layout);
			if ($to === null)
				$to = $this->controller->referer();
			$this->controller->redirect($to);
		}
		
		function flashErrorAndRedirect($text, $to = null, $layout = 'default')
		{
			$this->flashError($text, $layout);
			if ($to === null)
				$to = $this->controller->referer();
			$this->controller->redirect($to);
		}
		
		function flashError($text, $layout = 'default')
		{
			if (empty($text))
				return;
			
			$this->Session->setFlash('<div class="error">' . $text . '</div>', $layout, array('class' => ''));
		}
		
		function flashSuccess($text, $layout = 'default')
		{
			if (empty($text))
				return;
			
			$this->Session->setFlash('<div class="success">' . $text . '</div>', $layout, array('class' => ''));
		}
		
		function flashInfo($text, $layout = 'default')
		{
			$this->Session->setFlash('<div class="info">' . $text . '</div>', $layout, array('class' => ''));
		}
		
		private function _userLoginUrl($return = true)
		{
			return '/users/login/' . ($return ? base64_encode('/' . $this->controller->params['url']['url']) : '');
		}
		
		function redirectToLogin($layout = 'default', $return = true)
		{
			$this->flashError('Please log in.', $layout);
			$this->controller->redirect($this->_userLoginUrl($return));
		}
		
		// checks if the current user is a logged in site administrator and denies access if not.
		function denyNonAdmins($message = 'You do not have sufficient permissions to perform that action.', $referTo = null, $layout = 'default')
		{
			// make it a no-op if the user is an admin
			if ($this->isUserAdmin())
				return;
			
			if ($referTo == null)
				$referTo = $this->_userLoginUrl(true);
			
			$this->flashError($message, $layout);
			$this->controller->redirect($referTo);
		}
		
		// checks if the current user is logged in and denies access if not.
		function denyAnonymousUsers($message = 'Please log in.', $referTo = null, $layout = 'default')
		{
			// if the user is indeed logged in, don't do anything
			if ($this->isUserLoggedIn())
				return;
			
			// default to login page with redirect back to where they were
			if ($referTo == null)
				$referTo = $this->_userLoginUrl(true);
			
			$this->flashError($message, $layout);
			$this->controller->redirect($referTo);
		}
		
		// returns true if the user is logged in, false otherwise.
		function isUserLoggedIn()
		{
			// this could be simplified to return $this->Session->check('User.id'), but we want to make sure we're returning true|false
			if ($this->Session->check('User.id'))
				return true;
			return false;
		}
		
		// returns true if the user is logged in and an admin, false otherwise.
		function isUserAdmin()
		{
			// this check will automatically fail if the user is not logged in, so we don't have to call isUserLoggedIn().
			if ($this->Session->read('User.admin') == 1)
				return true;
			return false;
		}
		
		function denyAnonymousUsersFrom($actions, $message = 'Please log in.', $referTo = null, $layout = 'default')
		{
			if (is_string($actions))
				$actions = array($actions);
			
			$currentAction = $this->controller->params['action'];
			
			if (in_array($currentAction, $actions))
				$this->denyAnonymousUsers($message, $referTo, $layout);
		}
		
		function denyNonAdminsFrom($actions, $message = 'You do not have sufficient permissions to perform that action.', $referTo = null, $layout = 'default')
		{
			if (is_string($actions))
				$actions = array($actions);
			
			$currentAction = $this->controller->params['action'];
			
			if (in_array($currentAction, $actions))
				$this->denyNonAdmins($message, $referTo, $layout);
		}
		
		// shortcut to return the current user id, or a default value if not logged in.
		function userIdCoalesce($id = null, $coalesceTo = 0)
		{
			if ($id === null)
				$id = $this->Session->read('User.id');
			
			if (empty($id))
				return $coalesceTo;
			return $id;
		}
		
		// gets a string describing the current user suitable for logging
		function loggableUserName($start_sentence = true)
		{
			if ($this->IsUserLoggedIn())
				return $this->Session->read('User.full_name') . ' (' . $this->Session->read('User.id') . ')';
				
			$anon = 'an anonymous user';
			if ($start_sentence)
				$anon = ucfirst($anon);
			return $anon;
		}
	}
?>
<?php
	class UsersController extends AppController
	{
		var $name = 'Users';
		var $uses = array('User', 'Group', 'GroupInvite', 'GroupMember', 'Attendee', 'Workshop', 'IntegrationRequest', 'CreditTotal');
		var $helpers = array('Javascript', 'Html', 'Link', 'TimeFormatter');
		var $components = array('SessionPlus', 'EmailPlus', 'Password', 'RequestHandler');
		
		var $pageTitle = 'Baystate Roads &rsaquo; ';
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$userActions = array('legacy', 'index', 'change_password', 'edit');
			$adminActions = array('delete', 'stats', 'edit_user', 'group', 'attendance', 'search_workshops', 'add_attendee');
			
			$this->SessionPlus->denyAnonymousUsersFrom($userActions);
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
		}

		function _getUserNCredits ($user_id)
		{
                  $this->CreditTotal->contain();
		  $this->CreditTotal->order = null;
		  $this->CreditTotal->id = $user_id;
		  return $this->CreditTotal->find ('first');
		}


		function _getUserTitle ($n_road_scholar_credits)
		{
		  // go magic numbers!
		  if ($n_road_scholar_credits >= 22)
		    return 'Master Roads Scholar';
		  else if ($n_road_scholar_credits >= 7)
		    return 'Roads Scholar';
		  else
		    return 'User';
		}

		
		function stats($user_id = null)
		{
			if (empty($user_id))
				$this->redirect($this->referer());
			
			$this->User->id = $user_id;
			$this->User->contain();
			$user = $this->User->find('first');
			
			if (empty($user))
				$this->redirect($this->referer());
			
			$this->pageTitle .= sprintf('%s\'s User Stats', $user['User']['full_name']);
			
			$creditTotals = $this->_getUserNCredits ($user_id);
			
			$this->Attendee->contain();
			$numWorkshopsAttended = $this->Attendee->find('count', array('conditions' => array('Attendee.user_id' => $user_id)));
			$this->Attendee->contain(array('Workshop'));
			$numNoShows = $this->Attendee->find('count', array('conditions' => array('Attendee.user_id' => $user_id, 'Attendee.attendance' => 0, 'Workshop.date <' => date('Y-m-d H:i:s'))));
			
			$upcomingWorkshops = $this->Workshop->getUpcoming ($user_id);
			
			$userTitle = $this->_getUserTitle ($creditTotals['CreditTotal']['road_scholar_credits']);

			$this->set('user', $user);
			$this->set('creditTotals', $creditTotals);
			$this->set('numWorkshopsAttended', $numWorkshopsAttended);
			$this->set('numNoShows', $numNoShows);
			$this->set('userTitle', $userTitle);
			$this->set('upcomingWorkshops', $upcomingWorkshops);
		}
		
		function delete($user_id)
		{
			$this->User->id = $user_id;
			
			if (empty($user_id) || !$this->User->exists())
				$this->redirect($this->referer());
			
			// get the user for logging
			$this->User->contain();
			$user = $this->User->read();
			
			// if they are the last admin in a group that needs to be resolved first
			$group = $this->User->GroupMember->Group->groupOfUser($user_id);
			if (!empty($group) && $this->User->GroupMember->Group->isGroupAdmin($user_id) && $this->User->GroupMember->Group->getAdmins($group['Group']['id'], true) == 1)
				$this->SessionPlus->flashError('That user is the last admin of a group. Set a new admin first.');
			else if ($this->User->deleteWithAssets($user_id))
			{
				$this->SessionPlus->flashSuccess('User deleted successfully!');
				
				$format = '%s deleted user %s (%d) and associated assets';
				$message = sprintf($format,
					$this->SessionPlus->loggableUserName(),
					$user['User']['full_name'], $user['User']['id']
				);
				$this->logAction($this->User, 'delete', $message, $user_id);
			}
			else
				$this->SessionPlus->flashError('User could not be deleted!');
			
			//$this->redirect($this->referer());
		}
		
		function legacy()
		{
			$userId = $this->Session->read('User.id');
			
			if (!empty($this->data))
			{
				if (!empty($this->data['User']['integrate']))
				{
					// okay, let's do it, yo.
					if ($this->IntegrationRequest->makeRequest($userId))
						$this->SessionPlus->flashSuccess('Integration request submitted!');
					else
						$this->SessionPlus->flashError('Integration request could not be submitted!');
				}
				
				$this->redirect($this->referer());
			}
			
			$this->pageTitle .= 'Legacy Integration';
			
			$integration = $this->IntegrationRequest->requested($userId);
			$filled = $this->IntegrationRequest->filled($userId);
			
			$this->set('integrationRequested', $integration);
			$this->set('integrationFilled', $filled);
		}
		
		function index()
		{
			$this->pageTitle .= 'Dashboard';
			
			// find workshops this user is attending after right now
			$now = date("Y-m-d H:i:s");
			$userId = $this->Session->read('User.id');
			
			$upcoming = $this->Attendee->find('all', array('recursive' => -1, 'conditions' => array('user_id =' => $userId)));
			$ws = array();
			
			if (!empty($upcoming))
			{
				foreach ($upcoming as $key => $value)
					$or []= array('Workshop.id =' => $value['Attendee']['workshop_id']);
				
				// this returns a little too much info.
				// ehhh.
				$ws = $this->Workshop->find('all', array('order' => $this->Workshop->alias.'.date', 'conditions' => array('AND' => array('OR' => $or), 'date >=' => $now)));
			}
			
			$isGroupAdmin = $this->Group->isGroupAdmin($userId);
			$this->set('isGroupAdmin', $isGroupAdmin);
			
			if ($isGroupAdmin)
			{
				$group = $this->Group->groupOfUser($userId);
				$members = $this->Group->getMembers($group['Group']['id']);
				$memberIds = Set::classicExtract($members, '{n}.Member.user_id');
				
				$attendees = $this->Attendee->find('all', array('order' => array($this->Attendee->Workshop->alias.'.date' => 'asc', $this->Attendee->User->alias.'.last_name' => 'asc'), 'conditions' => array($this->Attendee->Workshop->alias.'.date >=' => $now, $this->Attendee->alias.'.user_id' => $memberIds)));
				
				// grab the details and merge into the results
				for ($i = 0; $i < sizeof($attendees); $i++)
				{
					$detailId = $attendees[$i]['Workshop']['detail_id'];
					$attendees[$i]['Detail'] = array_shift($this->Attendee->Workshop->Detail->find('first', array('recursive' => -1, 'conditions' => array('id' => $detailId))));
				}
				
				$this->set('groupAttendees', $attendees);
			}
			
			$this->set('upcoming', $ws);
		}
		
		function change_password()
		{
			$this->pageTitle .= 'Change Password';
			
			if (!empty($this->data))
			{
				$this->data['User']['id'] = $this->Session->read('User.id');
				$this->data['User']['password'] = $this->data['User']['new_password'];
				
				if ($this->User->save($this->data))
				{
					// clear data so we don't get dots on success
					$this->data = array();
					$this->SessionPlus->flashSuccess('Password changed.');
					
					$format = '%s changed account password';
					$message = sprintf($format, $this->SessionPlus->loggableUserName());
					$this->logAction($this->User, 'update', $message);
				}
				else
					$this->SessionPlus->flashError('Password was not changed. Please fix the errors below.');
			}
		}
		
		function edit($user_id = null)
		{
			$editingOwn = false;
			
			if (empty($user_id) || (!empty($user_id) && $user_id === $this->Session->read('User.id')))
			{
				$user_id = $this->Session->read('User.id');
				$editingOwn = true;
			}
			else if (!$this->User->canAdminister($this->Session->read('User.id'), $user_id))
				$this->redirect($this->referer());
			$userId = $user_id;
			
			$this->pageTitle .= 'Edit Account Info';
			$this->set('states', $this->User->getStates());
			
			// check if the user is in a group
			$group = $this->Group->groupOfUser($userId);
			$affiliationOptions = array();
			if (!empty($group))
				$affiliationOptions = array('enabled' => 'enabled');
			
			// if no POST data retrieve from database to populate fields
			if (empty($this->data))
			{
				$this->User->recursive = -1;
				$this->User->id = $userId;
				$this->data = $this->User->read();
			}
			else
			{
				$this->data['User']['id'] = $userId;
				
				// if in a group, override the affiliation data
				//if (!empty($group))
					//$this->data['User']['affiliation'] = $group['Group']['name'];
				
				if ($this->User->save($this->data))
				{
					$this->SessionPlus->flashSuccess('Account updated successfully.');
					
					// refresh the session data
					if ($editingOwn)
						foreach ($this->data['User'] as $key => $value)
							$this->Session->write("User.$key", $value);
				}
				else
					$this->SessionPlus->flashError('Account was not updated. Please correct the errors below.');
			}
			
			$this->set('editingOwn', $editingOwn);
			$this->set('userId', $userId);
			$this->set('affiliationOptions', $affiliationOptions);
			
			//new added for person record test
			
			$now = date("Y-m-d H:i:s");
			$userId = $this->Session->read('User.id');
			
			$upcoming = $this->Attendee->find('all', array('recursive' => -1, 'conditions' => array('user_id =' => $userId)));
			$ws = array();
			
			if (!empty($upcoming))
			{
				foreach ($upcoming as $key => $value)
					$or []= array('Workshop.id =' => $value['Attendee']['workshop_id']);
				
				// this returns a little too much info.
				// ehhh.
				$ws = $this->Workshop->find('all', array('order' => $this->Workshop->alias.'.date', 'conditions' => array('AND' => array('OR' => $or), 'date >=' => $now)));
			}
			
			$isGroupAdmin = $this->Group->isGroupAdmin($userId);
			$this->set('isGroupAdmin', $isGroupAdmin);
			
			if ($isGroupAdmin)
			{
				$group = $this->Group->groupOfUser($userId);
				$members = $this->Group->getMembers($group['Group']['id']);
				$memberIds = Set::classicExtract($members, '{n}.Member.user_id');
				
				$attendees = $this->Attendee->find('all', array('order' => array ($this->Attendee->Workshop->alias.'.date' => 'asc',
				                                                                  $this->Attendee->User->alias.'.last_name' => 'asc'),
				                                                'conditions' => array($this->Attendee->Workshop->alias.'.date >=' => $now,
				                                                                      $this->Attendee->alias.'.user_id' => $memberIds)));
				
				// grab the details and merge into the results
				for ($i = 0; $i < sizeof($attendees); $i++)
				{
					$detailId = $attendees[$i]['Workshop']['detail_id'];
					$attendees[$i]['Detail'] = array_shift($this->Attendee->Workshop->Detail->find('first', array('recursive' => -1, 'conditions' => array('id' => $detailId))));
				}
				
				$this->set('groupAttendees', $attendees);
			}
			
			$this->set('upcoming', $ws);
			
			//new added for group information in edit
			
			
		
			$this->pageTitle .= 'Member List';
			
			$this->Group->id = $this->groupMembership['Group']['id'];
			$this->set('admins', $this->Group->getAdmins());
			$this->set('members', $this->Group->getNonAdmins());
			
			if ($this->isGroupAdmin)
				$this->set('groupInvites', $this->Group->getInvites());
		
			
		}


                // TODO : Mostly duplicate of the above, need to merge the two functionalities
		function edit_user ($user_id)
		{
		  if (empty ($user_id))
		  {
		    // Not allowed to edit without a user id
		    $this->redirect ($this->referer ());
		  }

		  if (!empty ($this->data))
		  {
                    $this->data['User']['id'] = $user_id;

		    if ($this->User->save ($this->data))
		    {
		      $this->SessionPlus->flashSuccess('Account updated successfully.');

		      // refresh the session data if self account updated
                      $editingOwn = ($user_id === $this->Session->read ('User.id')) ? true : false;
		      if ($editingOwn)
			foreach ($this->data['User'] as $key => $value)
			  $this->Session->write ("User.$key", $value);
		    }
		    else
		    {
		      $this->SessionPlus->flashError('Account was not updated. Please correct the errors below.');
		    }
		  }
		  else
		  {
                    $this->User->recursive = -1;
                    $this->User->id = $user_id;
  		    $this->data = $this->User->read();
		  }

		  $_states = $this->User->getStates ();
		  $this->set ('user_id', $user_id);
		  $this->set ('states', $_states);
		}


    function group ($user_id)
    {
      if (empty ($user_id))
      {
        $this->SessionPlus->flashError ("Invalid user id");
        $this->redirect ($this->referrer);
      }

      // Get group that user belongs to
      $group = $this->Group->groupOfUser ($user_id);
      $group_admins = array ();
      if (!empty($group))
      {
        $admin_members = $this->Group->getAdmins ($group['Group']['id']);

        foreach ($admin_members as $ga)
        {
          $group_admins []= $this->User->findById ($ga['Member']['user_id']);
        }
      }

      $this->set ('user_id', $user_id);
      $this->set ('user_group', $group);
      $this->set ('group_admins', $group_admins);
    }


    function attendance ($user_id)
    {
      if (empty ($user_id))
      {
        $this->SessionPlus->flashError ("Invalid user id");
        $this->redirect ($this->referrer ());
      }

      $ws = $this->Attendee->userAttendanceRecord ($user_id);
      $n_attended = $this->Attendee->nWSAttended ($ws);
      $n_credits  = $this->_getUserNCredits ($user_id);
      $user_title = $this->_getUserTitle ($n_credits['CreditTotal']['road_scholar_credits']);

      $this->set ('user_id', $user_id);
      $this->set ('user_workshops', $ws);
      $this->set ('n_attended', $n_attended);
      $this->set ('n_credits', $n_credits);
      $this->set ('n_ws_registered', count ($ws));
      $this->set ('user_title', $user_title);
    }

    function search_workshops ($user_id)
    {
      if (empty ($user_id))
      {
        $this->SessionPlus->flashError ("Invalid user id");
        $this->redirect ($this->referrer ());
      }

      $workshop_name = '';
      if (array_key_exists ('workshop_name', $this->params['url']))
      {
        $workshop_name = $this->params['url']['workshop_name'];
      }

      // paginate through workshops
      $this->paginate['recursive'] = -1;

      // Get current attendance records
      $attended = $this->Attendee->find ('all',
                                         array ('contain' => false,
                                                'fields' => 'workshop_id',
                                                'conditions' => array ($this->Attendee->alias . '.user_id' => $user_id)));

      $ws_attended = array ();
      foreach ($attended as $a)
        $ws_attended[] = $a['Attendee']['workshop_id'];

      $conditions = array ();

      // Condition for pruning out workshops already attended
      if (!empty ($ws_attended))
        $conditions = array ("NOT" => array ($this->Workshop->alias . '.id' => $ws_attended));

      if ($workshop_name)
      {
        $conditions = array_merge ($conditions, array ($this->Workshop->Detail->alias . '.name LIKE' => '%'. $workshop_name . '%'));
      }
      $this->paginate[$this->Workshop->alias]['order'] = array ($this->Workshop->alias . '.date' => 'desc');
      $this->paginate[$this->Workshop->alias]['contain'] = 'Detail';
      $this->paginate[$this->Workshop->alias]['conditions'] = $conditions;
      $workshops = $this->paginate ('Workshop');

      $this->set ('workshop_name', $workshop_name);
      $this->set ('workshops', $workshops);
      $this->set ('user_id', $user_id);
    }

    function add_attendee ($user_id, $workshop_id)
    {
      $this->User->id = $user_id;
      if (!empty ($user_id) && !empty ($workshop_id))
      {
        $this->Attendee->overrideCutoff = true;
        if ($this->Attendee->enrollAttendee ($workshop_id, $user_id, 1 /*attended*/))
        {
          $this->SessionPlus->flashSuccess ('Attendee added.');

	  // read the user
	  $attendee = $this->Attendee->read ();

	  $user = $this->User->read();

	  $this->Workshop->id = $workshop_id;
	  $this->Workshop->contain ('Detail');
	  $workshop = $this->Workshop->read();

	  // log it
	  $format = '%s added %s to workshop %s';
	  $message = sprintf($format,
	      $this->SessionPlus->loggableUserName(),
	      $user['User']['full_name'],
	      $workshop['Detail']['name']
	      );

	  $this->logAction ($this->User, 'add_attendee', $message);
        }
        else
        {
          $this->SessionPlus->flashError ('Attendee not added. Check if workshop full.');
        }
      }
      else
      {
        $this->SessionPlus->flashError ('Invalid Arguments');
      }

      $this->redirect ($this->referer ());
    }



		
		function login($return_url = null)
		{
			$this->pageTitle .= 'Log In';
			
			if (!$return_url && !empty($this->data['User']['return_url']))
				$return_url = $this->data['User']['return_url'];
			
			$this->set('return_url', $return_url);
			
			// if they already put the details in
			if (!$this->Session->check('User.id'))
			{
				if (!empty($this->data))
				{
					$result = $this->User->find('first', array(
						'conditions' => array(
							'User.username' => $this->data['User']['username'],
							// convert the password to sha1 hash
							'User.password' => sha1(Configure::read('Security.salt') . $this->data['User']['password'])	
						)
					));
					
					// check if this account is not active
					if (!empty($result) && !$result['User']['active'])
					{
						$this->SessionPlus->flashError('That user account is still pending activation.');
						return;
					}
					
					// if the login was OK
					if (!empty($result))
					{
						// get a new session to prevent hijacking
						$this->Session->renew();
						
						// fill in the session variables and boot them to their user page
						$this->Session->write('User', $result['User']);
						$this->Session->write('GroupMember', $result['GroupMember']);
						
						$this->SessionPlus->flashSuccess('Welcome back, ' . 
							// choose the nickname over the first name if it's available
							(empty($result['User']['nickname']) ? $result['User']['first_name'] : $result['User']['nickname']) .
							'.');
						
						if (!empty($return_url))
							$this->redirect(base64_decode($return_url));
						
						if (!$result['User']['admin'])
							$this->redirect('/users/');
						else
							$this->redirect('/admin/');
					}
					else
					{
						// tell them to try again
						$this->SessionPlus->flashError('Incorrect username and password combination. Check your credentials and try again.');
						
						// clear the password
						$this->data['User']['password'] = '';
					}
				}
			}
			// if they're already logged in then redirect to their user page
			else
			{
				if ($this->Session->read('User.admin') == 0)
					$this->redirect('/users/');
				else
					$this->redirect('/admin/');
			}
		}
		
		function logout()
		{
			$this->Session->destroy();
			
			$this->SessionPlus->flashSuccess('You\'ve been logged out.');
			$this->redirect('/users/login/', null, true);
		}
		
		function register()
		{
			$this->pageTitle .= 'Register New User';
			$this->set('states', $this->User->getStates());
			
			if (!empty($this->data))
			{
				if ($this->User->save($this->data))
				{
					// log action
					$user = $this->User->read();
					$format = '%s (%d) registered a new account from IP %s';
					$message = sprintf($format,
						$user['User']['full_name'], $user['User']['id'],
						$this->RequestHandler->getClientIP()
					);
					$this->logAction($this->User, 'create', $message);
					
					// send email to admins
					$admins = $this->User->find('all', array('recursive' => -1, 'conditions' => array('admin' => 1)));
					$user = $this->User->read();
					
					foreach ($admins as $admin)
					{
						$this->EmailPlus->reset();
						$this->set('user', $user);
						$this->set('admin', $admin);
						
						$this->EmailPlus->sendNoReply('user-registered', $admin['User']['email'], 'New User Registration');
						
						if (Configure::read())
							debug ($this->Session->read('Message.email'));
					}
					
					$this->SessionPlus->flashSuccess('Thank you for registering. An administrator will approve your account for use shortly.');
					$this->data = array();
				}
				else
					$this->SessionPlus->flashError('Account was not created. Please fix the errors below.');
			}
		}
		
		function forgot()
		{
			$this->pageTitle .= 'Username/Password retrieval';
			
			if (!empty($this->data))
			{
				$result = $this->User->find('first', array('conditions' => $this->data['User'], 'recursive' => -1));
				
				if (empty($result))
					$this->SessionPlus->flashError('Invalid username or email address. Please check your credentials.');
				else
				{
					$newpass = $this->Password->generate();
					$this->set('user', $result);
					$this->set('newpass', $newpass);
					
					if ($this->EmailPlus->sendNoReply('forgot-password', $result['User']['email'], 'Your Baystate Roads Account: Password Reset'))
					{
						debug($newpass);
						
						// set the new password
						$this->User->id = $result['User']['id'];
						
						if ($this->User->save(array('password' => $newpass)))
						{
							// clear forms if production mode
							if (!Configure::read())
								$this->data = array();
							
							$user = $this->User->read();
							
							// log it
							$format = '%s reset the password of %s (%d) from IP %s';
							$message = sprintf($format,
								$this->SessionPlus->loggableUserName(),
								$user['User']['full_name'], $user['User']['id'],
								$this->RequestHandler->getClientIP()
							);
							$this->logAction($this->User, 'update', $message);
							
							$this->SessionPlus->flashSuccess('Your password has been reset. Please check your email for your new login credentials.');
						}
						else
							$this->SessionPlus->flashError('Password was not reset! Please disregard any emails associated with this transaction.');
					}
					else
					{
						$this->SessionPlus->flashError('Email could not be sent. Please try again later.');
						debug ($this->EmailPlus->smtpError);
					}
				}
			}
		}
	}
?>

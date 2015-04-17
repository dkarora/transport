<?php
	class GroupsController extends AppController {
		var $name = 'Groups';
		var $uses = array('User', 'Group', 'Workshop', 'Attendee');
		var $helpers = array('Paginator', 'Html', 'Javascript', 'TimeFormatter');
		var $components = array('SessionPlus', 'ErrorListFormatter', 'Link');
		
		var $pageTitle = 'Baystate Roads &rsaquo; ';
		var $isGroupAdmin = false;
		var $isGroupMember = false;
		var $groupMembership = array();
                var $paginate = array ('limit' => 20);
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyAnonymousUsers();
			
			$this->pageTitle .= 'My Organization &rsaquo; ';
			$userId = $this->Session->read('User.id');

			$adminActions = array ('add',
			                       'add_member',
			                       'administer', 
			                       'search');

			$this->SessionPlus->denyNonAdminsFrom ($adminActions);
			
			$this->groupMembership = $this->Group->Member->find('first', array('conditions' => array('Member.user_id' => $userId)));
			
			if (!empty($this->groupMembership))
			{
				$this->isGroupMember = true;
				$this->isGroupAdmin = $this->Group->Member->isAdminPermission($this->groupMembership['Member']['permissions']);
			}
			
			// quick security+sanity check:
			// if the current user is not in a group and the current action is not allowed without group membership
			// redirect to the no_group page
			$nogroup = array('no_group', 'create_new');
			if (!$this->isGroupMember && !in_array($this->params['action'], $nogroup))
				$this->redirect('/groups/no_group/');
			
			// these pages only allow group-level admin access
			$mustbeadmin = array('admin_options', 'edit_member', 'newmember', 'enrollmember');
			if (!$this->isGroupAdmin && in_array($this->params['action'], $mustbeadmin))
			{
				$this->SessionPlus->flashError('You must be a group administrator to do that.');
				$this->redirect($this->referer());
			}
			
			// finally set some view vars
			$this->set('groupMembership', $this->groupMembership);
			$this->set('isGroupAdmin', $this->isGroupAdmin);
			$this->set('isGroupMember', $this->isGroupMember);
		}


		function add_member ($group_id)
		{
		  if (empty ($group_id))
		  {
		    $this->SessionPlus->flashError ('Missing group');
		    $this->redirect ($this->referer ());
		  }
		  else
		  {
		    $this->Group->recursive = -1;
		    $this->Group->id = $group_id;
		    $this->data = $this->Group->read ();
		  }

		  // Get list of all users
		  $part_lastname = '';
		  if (array_key_exists ('part_lastname', $this->params['url']))
		  {
		    $part_lastname = $this->params['url']['part_lastname'];
		  }

		  // paginate through users
		  $this->paginate['recursive'] = -1;

                  // Only find those users that don't belong to a group already
		  $conditions = array ('GroupMember.id is NULL');
		  if ($part_lastname)
		  {
		    $conditions = array_merge($conditions, 
		                              array($this->User->alias.'.last_name LIKE' => $part_lastname . '%'));
		  }

		  $this->paginate[$this->User->alias]['conditions'] = $conditions;
		  $users = $this->paginate('User');
		  debug ($users);


                  $this->set('part_lastname', $part_lastname);
		  $this->set('users', $users);
		}


		function administer ($group_id)
		{
		  if (empty ($group_id))
		  {
		    $this->SessionPlus->flashError ('Missing group');
		    $this->redirect ($this->referer ());
		  }
		  else
		  {
		    $this->Group->recursive = -1;
		    $this->Group->id = $group_id;
		    $this->data = $this->Group->read ();
		  }

		  // Show admins and non-admins
		  $this->pageTitle .= 'Member List';

		  $this->set('admins', $this->Group->getAdmins());
		  $this->set('members', $this->Group->getNonAdmins());
		}


		function search ()
		{
		  $part_groupname = '';

		  if (array_key_exists ('part_groupname', $this->params['url']))
		  {
		    $part_groupname = $this->params['url']['part_groupname'];
		  }

		  $this->pageTitle .= 'Search Groups';

		  // paginate through users
		  $this->paginate['recursive'] = -1;

		  // set a filter if needed
		  $conditions = array();
		  if ($part_groupname)
		  {
		    $conditions = array_merge($conditions, array($this->Group->alias.'.name LIKE' => '%'. $part_groupname . '%'));
		  }

		  $this->paginate[$this->Group->alias]['conditions'] = $conditions;
		  $groups = $this->paginate ('Group');

		  $this->set('part_groupname', $part_groupname);
		  $this->set('groups', $groups);
		}
		

		function admin_options()
		{			
			$this->pageTitle .= 'Admin Options';
			
			$this->Group->id = $this->groupMembership['Group']['id'];
			$this->set('memberList', $this->Group->getNonAdmins());
			$this->set('groupInvites', $this->Group->getInvites());
		}
		
		function member_options()
		{
			$this->pageTitle .= 'Member Options';
			$lastAdmin = false;
			
			// check if we're the last of our kind
			if ($this->isGroupAdmin)
			{
				$admin_count = $this->Group->getAdmins($this->groupMembership['Group']['id'], true);
				
				if ($admin_count == 1)
					$lastAdmin = true;
			}
			
			$this->set('lastAdmin', $lastAdmin);
		}
		
		function create_new()
		{
			$userId = $this->Session->read('User.id');
			
			if ($this->isGroupMember || $this->Group->Invite->userHasPendingInvite($userId))
			{
				$this->SessionPlus->flashError('No go!');
				$this->redirect($this->referer());
			}
			
			$this->ErrorListFormatter->deleteOldData();
			
			if (!empty($this->data))
			{
				// add in the GroupMember data
				$this->data[$this->Group->Member->alias] = array(array('user_id' => $userId, 'permissions' => 1));
				
				if ($this->Group->saveAll($this->data))
				{
					// update the user's affiliation
					$this->User->resetAffiliation($userId, $this->data[$this->Group->alias]['name']);
					
					// refresh the session so the org hidden subnav will work
					$newinfo = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
					$this->Session->write('User', $newinfo['User']);
					$this->Session->write('GroupMember', $newinfo['GroupMember']);
					
					// log it
					$group = $this->Group->read();
					$format = '%s created group %s (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$group[$this->Group->alias]['name'], $group[$this->Group->alias]['id']
					);
					$this->logAction($this->Group, 'create', $message);
					
					$this->SessionPlus->flashSuccess('Group created.');
					$this->redirect('/groups/');
				}
				else
				{
					$this->ErrorListFormatter->setOldData($this->data);
					
					$this->SessionPlus->flashError('Group was not created. Please fix the errors below.');
					$this->redirect($this->referer());
				}
			}
		}


                // Both add and edit functionality, ignore id
		function add (/* $id = null */)
		{
		  if (!empty ($this->data))
		  {
		    if ($this->Group->save ($this->data))
		    {
		      $this->SessionPlus->flashSuccess ("Groups updated");

                      $format = '%s Group created/updated with id: %s';
                      $message = sprintf ($format, $this->Group->name, $this->Group->id);
                      $this->logAction($this->Group, 'add', $message);
		    }
		    else
		    {
		      $this->SessionPlus->flashError ('Groups was not updated. Please correct the errors below.');
		    }
		  }

		  $_all_groups = $this->Group->find ('list', array ('recursive' => -1));
		  $this->set ('groups', $_all_groups);
		}

		
		function no_group()
		{
			$this->pageTitle .= 'No Organization';
			$groups = $this->Group->find('all', array('order' => $this->Group->alias.'.name'));
			$group_ids = Set::combine($groups, '{n}.Group.id', '{n}.Group.name');
			
			// bring it back now y'all
			$this->ErrorListFormatter->restoreErrorMessages();
			
			$this->set('group_options', $group_ids);
			$this->set('invitePending', $this->Group->Invite->userHasPendingInvite($this->Session->read('User.id')));
		}
		
		function edit_member()
		{
			$this->pageTitle .= 'Edit Member Information';
			
			$userId = $this->params['named']['userid'];
			$user = $this->User->find('first', array('conditions' => array('User.id' => $userId)));
			$this->set('states', $this->User->getStates());
			
			if (empty($user))
			{
				$this->SessionPlus->flashError('User does not exist!');
				$this->redirect($this->referer());
			}
			
			// if we're not an admin in the first place then quit
			if (!$this->User->canAdminister($this->Session->read('User.id'), $userId))
			{
				$this->SessionPlus->flashError('Credential mismatch.');
				$this->redirect($this->referer());
			}
			
			$this->set('user', $user['User']);
			
			if (!empty($this->data))
			{
				// quick override of the affiliation - never trust user input!
				$this->data['User']['affiliation'] = $this->groupMembership['Group']['name'];
				
				if ($this->User->save($this->data))
				{
					$message = '';
					$targetUser = $this->User->read();
					
					if ($targetUser[$this->User->alias]['id'] == $this->SessionPlus->userIdCoalesce())
					{
						$format = '%s updated their own user profile';
						$message = sprintf($format,
							$this->SessionPlus->loggableUserName()
						);
					}
					else
					{
						$format = '%s updated %s\'s (%d) user profile';
						$message = sprintf($format,
							$this->SessionPlus->loggableUserName(),
							$targetUser[$this->User->alias]['full_name'], $targetUser[$this->User->alias]['id']
						);
					}
					
					$this->logAction($this->User, 'update', $message);
					
					$this->SessionPlus->flashSuccess('Account information updated.');
				}
				else
					$this->SessionPlus->flashError('Account information was not updated. Please fix the errors below.');
				
				$this->User->data = $this->data;
			}
			else
			{
				$this->data = $user;
				$this->set('user', $user['User']);
			}
		}
		
		function index()
		{
			$this->pageTitle .= 'Member List';
			
			$this->Group->id = $this->groupMembership['Group']['id'];
			$this->set('admins', $this->Group->getAdmins());
			$this->set('members', $this->Group->getNonAdmins());
			
			if ($this->isGroupAdmin)
				$this->set('groupInvites', $this->Group->getInvites());
		}
		
		function newmember()
		{
			$this->pageTitle .= 'Register Member';
			$this->set('states', $this->User->getStates());
			
			if (!empty($this->data))
			{
				$gid = $this->groupMembership['Group']['id'];
				$this->data['GroupMember'] = array('group_id' => $gid);
				
				// set this user as active
				$this->data['User']['active'] = 1;
				
				// rule #1 of network applications: don't trust user input!
				// override the affiliation
				$this->data['User']['affiliation'] = $this->groupMembership['Group']['name'];
				
				// if applicable add a group admin
				if ($this->data['Group']['set_admin'])
					$this->data['GroupMember']['permissions'] = 1;
				else
					$this->data['GroupMember']['permissions'] = 0;
				
				unset($this->data['Group']);
				
				// valid data?
				if ($this->User->saveAll($this->data))
				{
					$this->Group->id = $gid;
					$targetUser = $this->User->read();
					$targetGroup = $this->Group->find('first', array('recursive' => -1));
					
					$format = '%s created user %s (%d) for group %s (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$targetUser[$this->User->alias]['full_name'], $targetUser[$this->User->alias]['id'],
						$targetGroup[$this->Group->alias]['name'], $targetGroup[$this->Group->alias]['id']	
					);
					$this->logAction($this->User, 'create', $message);
					
					$this->SessionPlus->flashSuccess('User successfully created.');
					
					// clear the fields for the user
					$this->data = array();
				}
				else
					$this->SessionPlus->flashError('User not created. Please fix the errors below.');
			}
		}
		
		function enrollmember()
		{
			$this->pageTitle .= 'Enroll/Unenroll Members in Workshops';
			
			// if the workshop hasn't been picked yet
			if (empty($this->params['named']['workshopid']))
			{
				// grab all the workshops available to join: after right now and public
				$conditions = array('date >=' => date('Y-m-d H:i:s', strtotime('+3 days')), 'unlisted' => 0);
				$this->set('workshops', $this->paginate('Workshop', $conditions));
			}
			// otherwise we're at the management screen thing
			else
			{
				// get all members for this group
				$groupId = $this->groupMembership['Group']['id'];
				$this->Group->id = $groupId;
				$members = $this->Group->getMembers();
				
				$attending = array();
				$not_attending = array();
				$attendees = array ();
				
				$workshopId = $this->params['named']['workshopid'];
				$idHashed = false;
				
				if (!is_numeric($workshopId))
				{
					$workshopId = $this->Link->_alphaID($workshopId, true);
					$idHashed = true;
				}
				
				$this->Workshop->id = $workshopId;
				$workshop = $this->Workshop->find('first');
				$workshopFull = $this->Workshop->isFull();
				
				debug ($workshopId);
				debug ($workshopFull);
				
				if (empty($workshop))
					$this->cakeError('error404');
				
				// if we get a hashed id and a public workshop, don't give them any hints
				if ($idHashed && !$workshop['Workshop']['unlisted'])
					$this->cakeError('error404');
				// if we get an unhashed id and an unlisted workshop, don't allow it
				else if (!$idHashed && $workshop['Workshop']['unlisted'])
					$this->cakeError('error404');
				
				// check if each user is currently attending the workshop
				foreach ($members as $member)
				{
					// check if this user is signed up
					$att = $this->Attendee->find('first', array('recursive'  => -1,
					                                            'conditions' => array('user_id'     => $member['User']['id'],
					                                                                  'workshop_id' => $workshopId)));

					if (!empty ($att)) {
				          $attending []= $member['User'];
				          $attendees []= $att;
					}
					else {
					  $not_attending []= $member['User'];
					}
				}

				$this->set('signedup', $attending);
				$this->set('attendees', $attendees);
				$this->set('notsignedup', $not_attending);
				$this->set('workshop', $workshop);
				$this->set('workshopId', $workshopId);
				$this->set('workshopFull', $workshopFull);
			}
		}
		
		function removemember($id = null)
		{
			$user_id = $this->Session->read('User.id');
			$target_gm_id = null;
			
			// check for being part of a group
			if (!$this->isGroupMember)
			{
				$this->SessionPlus->flashError("You're not part of an organization.");
				$this->redirect($this->referer());
			}
			
			// if $id is supplied then we're removing another user
			// otherwise we're removing ourselves
			if ($id)
			{
				// get the user in question
				$this->Group->Member->id = $id;
				$target_gm = $this->Group->Member->read();
				
				// removing other user, check if we're a group admin for the right group and we're not adminstering another admin
				if ($this->isGroupAdmin &&
					$this->groupMembership['Member']['group_id'] == $target_gm['Member']['group_id'] &&
					!$target_gm['Member']['permissions'])
				{
					$target_gm_id = $id;
				}
				else
				{
					$this->SessionPlus->flashError('Credential mismatch.');
					$this->redirect($this->referer());
				}
			}
			else
				$target_gm_id = $this->groupMembership['Member']['id'];
			
			$this->Group->Member->id = $target_gm_id;
			$targetGroupMember = $this->Group->Member->find('first');
			$this->User->id = $targetGroupMember['Member']['user_id'];
			$targetUser = $this->User->read();
			$targetGroup = $this->Group->groupOfUser($targetUser['User']['id']);
			
			if ($this->Group->removeMember($target_gm_id))
			{
				$message = null;
				
				// if we removed ourselves, update the session
				if ($this->groupMembership['Member']['id'] == $target_gm_id)
				{
					$format = '%s removed self from group "%s" (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$this->groupMembership['Group']['name'], $this->groupMembership['Group']['id']
					);
					$this->Session->write('GroupMember', array());
				}
				else
				{
					$format = '%s removed %s (%d) from group "%s" (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$targetUser['User']['full_name'], $targetUser['User']['id'],
						$targetGroup['Group']['name'], $targetGroup['Group']['id']
					);
				}
				
				$this->logAction($this->Group->Member, 'delete', $message, $target_gm_id);
				
				$this->SessionPlus->flashSuccess('Removed from group.');
			}
			else
				$this->SessionPlus->flashError('Could not remove user from group! Please try again.');
			
			$this->redirect($this->referer());
		}
	}
?>

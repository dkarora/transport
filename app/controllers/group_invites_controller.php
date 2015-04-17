<?php
	class GroupInvitesController extends AppController
	{
		var $name = 'GroupInvites';
		var $components = array('SessionPlus');
		var $uses = array('GroupInvite', 'GroupMember');
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyAnonymousUsers();
		}
		
		function respond()
		{
			// check for permissions
			if (!$this->Session->read('GroupMember.permissions'))
				return;
			
			// $this->data is expected to be in 
			if (!empty($this->data))
			{				
				// the delList is a list of invites that will be deleted from the
				// database. the addList is a list of invites that will be converted
				// to rows in the group_members table.
				
				// unless the action is none, then a request will be added to the
				// delList. if the action is accept, then it will get added to the
				// addList as well.
				$delList = array();
				$addList = array();
				
				foreach ($this->data['GroupInvite'] as $req)
				{
					if ($req['action'] === 'none')
						continue;
					
					// get the row in the GroupInvite table
					$invite = $this->GroupInvite->find('first', array('conditions' => array('id' => $req['id']), 'recursive' => -1));
					
					// make sure we have a match first
					if ($invite['GroupInvite']['group_id'] !== $this->Session->read('GroupMember.group_id'))
						continue;
					
					$delList[] = $invite['GroupInvite'];
					
					if ($req['action'] === 'accept')
					{
						$member = $invite['GroupInvite'];
						$member['permissions'] = 0;
						$addList[] = $member;
					}
				}
				
				if (!empty($delList))
				{
					// log and delete each one
					foreach ($delList as $item)
					{
						$this->GroupInvite->id = $item['id'];
						$this->GroupInvite->delete();
						
						$this->GroupInvite->User->id = $item['user_id'];
						$targetUser = $this->GroupInvite->User->find('first', array('recursive' => -1));
						
						$format = '%s (%d) deleted group request (%d) created by %s (%d)';
						$message = sprintf($format,
							$this->Session->read('User.full_name'), $this->Session->read('User.id'),
							$item['id'],
							$targetUser[$this->GroupInvite->User->alias]['full_name'], $targetUser[$this->GroupInvite->User->alias]['id']
						);
						$this->logAction($this->GroupInvite, 'delete', $message, $item['id']);
					}
				}
				
				if (!empty($addList))
				{
					$group = $this->GroupInvite->Group->groupOfUser($this->Session->read('User.id'));
					$groupName = $group[$this->GroupInvite->Group->alias]['name'];
					
					foreach ($addList as $item)
					{
						debug ($item);
						
						$this->GroupMember->create();
						$this->GroupMember->save($item);
						
						$this->GroupMember->User->id = $item['user_id'];
						$targetUser = $this->GroupMember->User->find('first', array('recursive' => -1));
						$uAlias = $this->GroupMember->User->alias;
						
						// log it
						$message = sprintf(
							'%s added %s (%d) to group %s (%d) via request (%d)',
							$this->SessionPlus->loggableUserName(),
							$targetUser[$uAlias]['full_name'], $targetUser[$uAlias]['id'],
							$groupName, $group[$this->GroupInvite->Group->alias]['id'],
							$item['id']
						);
						$this->logAction($this->GroupMember, 'create', $message);
						
						debug ($message);
						debug ($this->GroupMember->ActivityLog->validationErrors);
						die;
						
						// set each user's affiliation to the group name
						$this->GroupMember->User->create();
						$this->GroupMember->User->id = $item['user_id'];
						$this->GroupMember->User->set('affiliation', $groupName);
						$this->GroupMember->User->save();
					}
				}
				
				$this->SessionPlus->flashSuccess('Requests updated.');
				$this->redirect($this->referer());
			}
		}
		
		function request()
		{
			if (!empty($this->data))
			{
				$userId = $this->Session->read('User.id');
				
				// check if a request is already pending
				if ($this->GroupInvite->userHasPendingInvite($userId))
				{
					$this->SessionPlus->flashError('You already have a pending request.');
					$this->redirect($this->referer());
				}
				
				// check if user is already in a group
				if ($this->GroupInvite->Group->isGroupMember($userId))
				{
					$this->SessionPlus->flashError('You are already in a group!');
					$this->redirect($this->referer());
				}
				
				debug ($this->data);
				
				if ($this->GroupInvite->save(array('user_id' => $userId, 'group_id' => $this->data['GroupInvite']['group_id'])))
				{
					$this->GroupInvite->Group->id = $this->data['GroupInvite']['group_id'];
					$group = $this->GroupInvite->Group->read();
					
					// log the action
					$format = '%s (%d) created request (%d) to join group %s (%d)';
					$message = sprintf($format,
						$this->Session->read('User.full_name'), $this->Session->read('User.id'),
						$this->GroupInvite->id,
						$group[$this->GroupInvite->Group->alias]['name'], $group[$this->GroupInvite->Group->alias]['id']
					);
					$this->logAction($this->GroupInvite, 'create', $message);
					
					$this->SessionPlus->flashSuccess('Request sent!');
				}
				else
					$this->SessionPlus->flashError('Request was not sent. Please try again.');
			}
			
			$this->redirect($this->referer());
		}
		
		function cancel_request()
		{
			$userId = $this->Session->read('User.id');
			
			if (!$this->GroupInvite->userHasPendingInvite($userId))
			{
				$this->SessionPlus->flashError('You have no requests to cancel!');
				$this->redirect($this->referer());
			}
			
			$req = $this->GroupInvite->find('first', array('recursive' => -1, 'conditions' => array('user_id' => $userId)));
			
			if ($this->GroupInvite->delete($req['GroupInvite']['id']))
			{
				$this->GroupInvite->Group->id = $req['GroupInvite']['group_id'];
				$group = $this->GroupInvite->Group->find('first', array('recursive' => -1));
				
				$format = '%s (%d) deleted request (%d) to join group %s (%d)';
				$message = sprintf($format,
					$this->Session->read('User.full_name'), $this->Session->read('User.id'),
					$req['GroupInvite']['id'],
					$group[$this->GroupInvite->Group->alias]['name'], $group[$this->GroupInvite->Group->alias]['id']
				);
				$this->logAction($this->GroupInvite, 'delete', $message, $req['GroupInvite']['id']);
				
				$this->SessionPlus->flashSuccess('Your request has been cancelled.');
			}
			else
				$this->SessionPlus->flashError('You request could not be cancelled. Please try again.');
			
			$this->redirect($this->referer());
		}
	}
?>

<?php
	class Group extends AppModel {
		var $name = 'Group';
		var $displayField = 'name';
		var $order = 'name';
		
		var $validate = array(
			'name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a name.'
				),
				
				'unique' => array(
					'rule' => 'isUnique',
					'message' => 'That group name is already taken! Please choose another.'
				)
			)
		);
		
		var $hasMany = array(
			'Member' => array(
				'className' => 'GroupMember',
				'foreignKey' => 'group_id',
				'dependent' => true
			),
			
			'Invite' => array(
				'className' => 'GroupInvite',
				'foreignKey' => 'group_id',
				'dependent' => true
			)
		);
		
		function getMembers($group_id = null, $mode = 'all', $conditions = array())
		{
			if (!$group_id)
			{
				if (!$this->id)
					return array();
				else
					$group_id = $this->id;
			}
			
			return $this->Member->find($mode, array('conditions' => array_merge(array($this->Member->alias.'.group_id' => $group_id), $conditions)));
		}
		
		function getAdmins($group_id = null, $count = false)
		{
		  if (!$group_id)
		  {
		    if (!$this->id)
		      return array();
		    else
		      $group_id = $this->id;
		  }

		  $mode = 'all';
		  if ($count)
		    $mode = 'count';

		  return $this->Member->find ($mode, array('conditions' => array ($this->Member->alias.'.permissions' => 1,
		                                                                  $this->Member->alias.'.group_id' => $group_id)));
		}

		function getNonAdmins($group_id = null, $count = false)
		{
			if (!$group_id)
			{
				if (!$this->id)
					return array();
				else
					$group_id = $this->id;
			}
			
			$mode = 'all';
			if ($count)
				$mode = 'count';
			
			return $this->Member->find($mode, array('conditions' => array($this->Member->alias.'.permissions' => 0, $this->Member->alias.'.group_id' => $group_id)));
		}
		
		function getInvites($group_id = null)
		{
			if (!$group_id)
			{
				if (!$this->id)
					return array();
				else
					$group_id = $this->id;
			}
			
			return $this->Invite->find('all', array('conditions' => array($this->Invite->alias.'.group_id' => $group_id)));
		}
		
		// if group_id is not provided, this method will check if the user is
		// a member of any group. if group_id is provided and not empty() then
		// this method will only check the provided group.
		function isGroupMember($user_id, $group_id = null)
		{
			$count = 0;
			
			if (!$group_id)
				$count = $this->Member->find('count', array('conditions' => array($this->Member->alias.'.user_id' => $user_id)));
			else
				$count = $this->Member->find('count', array('conditions' => array($this->Member->alias.'.user_id' => $user_id, $this->Member->alias.'.group_id' => $group_id)));
			
			if (!empty($count))
				return true;
			return false;
		}
		
		function removeMember($gm_id = null)
		{
			if (!$gm_id)
				return;
			
			$success = false;
			
			// get the group member to do checks later
			$this->Member->id = $gm_id;
			$member = $this->Member->read();
			
			if ($success = $this->Member->delete())
			{
				$group_id = $member[$this->Member->alias]['group_id'];
				
				// check for remaining admins
				$admins = $this->getAdmins($group_id, true);
				
				// if no one is left to administer the group, disband it
				if (empty($admins))
					$this->delete($group_id);
			}
			
			return $success;
		}
		
		// gets the Group of the specified user.
		function groupOfUser($user_id = null, $recursive = -1)
		{
			if (empty($user_id))
				return array();
			
			$gm = $this->Member->find('first', array('recursive' => -1, 'conditions' => array('Member.user_id' => $user_id)));
			if (empty($gm))
				return array();
			
			$this->id = $gm['Member']['group_id'];
			return $this->find('first', array('recursive' => $recursive));
		}
		
		function isGroupAdmin($user_id, $group_id = null)
		{
			if (!$this->isGroupMember($user_id, $group_id))
				return false;
			
			if (!$group_id)
				$count = $this->Member->find('count', array('conditions' => array($this->Member->alias.'.permissions' => 1, $this->Member->alias.'.user_id' => $user_id)));
			else
				$count = $this->Member->find('count', array('conditions' => array($this->Member->alias.'.permissions' => 1, $this->Member->alias.'.user_id' => $user_id, $this->Member->alias.'.group_id' => $group_id)));
			
			if (!empty($count))
				return true;
			return false;
		}
	}
?>

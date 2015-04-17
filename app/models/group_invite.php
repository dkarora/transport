<?php
	class GroupInvite extends AppModel
	{
		var $name = 'GroupInvite';
		
		var $validate = array(
			'new_or_existing' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please select whether to join a new or existing group.',
					'on' => 'create'
				)
			),
			
			'group_id' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'The group ID must be provided.'
				),
				
				'exists' => array(
					'rule' => '_groupExists',
					'message' => 'That group does not exist in the database.',
					'last' => true
				)
			),
			
			'user_id' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'The user ID must be provided.'
				),
				
				'oneInvitePerUser' => array(
					'rule' => 'isUnique',
					'message' => 'Each user cannot have more than one group invite pending at a time.',
				),
				
				'userExists' => array(
					'rule' => '_userExists',
					'message' => 'That user does not exist!'
				)
			)
		);
		
		var $belongsTo = array(
			'Group' => array(
				'className' => 'Group',
				'foreignKey' => 'group_id'
			),
			
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
			)
		);
		
		function userHasPendingInvite($user_id = null)
		{
			if ($user_id)
			{
				$req = $this->find('count', array('recursive' => -1, 'conditions' => array('user_id' => $user_id)));
				
				if (!empty($req))
					return true;
			}
			
			return false;
		}
		
		function _userExists($check)
		{
			$user = $this->User->find('count', array('conditions' => array('id' => $check['user_id']), 'recursive' => -1));
			
			if (empty($user))
				return false;
			
			return true;
		}
		
		function _groupExists($check)
		{
			$group = $this->Group->find('count', array('conditions' => array('id' => $check['group_id']), 'recursive' => -1));
			if (empty($group))
				return false;
			
			return true;
		}
	}
?>
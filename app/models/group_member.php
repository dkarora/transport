<?php
	class GroupMember extends AppModel
	{
		var $name = 'GroupMember';
		
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
		
		var $validate = array(
			'permissions' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Permissions cannot be blank.'
				),
				
				'validPermissions' => array(
					'rule' => '_validPermissions',
					'message' => 'Invalid permissions.',
					'last' => true
				)
			),
			
			'group_id' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Missing group ID.',
					'last' => true
				),
				
				'validGroup' => array(
					'rule' => '_validGroup',
					'message' => 'Invalid group.',
					'last' => true
				)
			),
			
			'user_id' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Missing user ID.',
					'last' => true
				),
				
				'validUser' => array(
					'rule' => '_validUser',
					'message' => 'Invalid user.',
					'last' => true
				),
				
				'singleGroup' => array(
					'rule' => '_singleGroup',
					'message' => 'This user is already in a group!',
					'last' => true,
					'on' => 'create',
				),
			)
		);
		
		function _singleGroup($check)
		{
			// find records that match up to this user
			$uid = $check['user_id'];
			if ($this->find('count', array('recursive' => -1, 'conditions' => array('user_id' => $uid))))
				return false;
			return true;
		}
		
		function _validPermissions($check)
		{
			// valid permission flags: 0 for member, 1 for admin
			// hey guys hard coding is a great idea right lolololol
			$valid = array(0, 1);
			$permission = $check['permissions'];
			
			return in_array($permission, $valid);
		}
		
		function _validGroup($check)
		{
			$this->Group->id = $check['group_id'];
			$count = $this->Group->find('count');
			
			if (!empty($count))
				return true;
			return false;
		}
		
		function _validUser($check)
		{
			$this->User->id = $check['user_id'];
			$count = $this->User->find('count');
			
			if (!empty($count))
				return true;
			return false;
		}
		
		function isAdminPermission($permission)
		{
			return $permission > 0;
		}
		
		function afterSave($created)
		{
			parent::afterSave($created);
			
			// update the user's affiliation to match
			$userId = $this->data[$this->alias]['user_id'];
			$groupId = $this->data[$this->alias]['group_id'];
			$groupName = null;
			
			if (!empty($this->data[$this->Group->alias]['name']))
				$groupName = $this->data[$this->Group->alias]['name'];
			else
			{
				$this->Group->id = $groupId;
				$this->Group->recursive = -1;
				$group = $this->Group->read();
				$groupName = $group[$this->Group->alias]['name'];
			}
			
			$this->User->resetAffiliation($userId, $groupName);
		}
		
		function afterDelete()
		{
			parent::afterDelete();
			
			// reset the user's affiliation
			$this->User->resetAffiliation($this->data[$this->alias]['user_id']);
		}
	}
?>
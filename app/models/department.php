<?php
	class Department extends AppModel
	{
		var $name = 'Department';
		
		var $recursive = 2;
		
		var $belongsTo = array(
			'Group' => array(
				'className' => 'Group',
				'foreignKey' => 'group_id'
			)
		);
		
		var $hasMany = array(
			'Member' => array(
				'className' => 'GroupMember',
				'foreignKey' => 'department_id',
				'conditions' => array('Member.permissions = 0'),
				'dependent' => true
			),
			
			'Admin' => array(
				'className' => 'GroupMember',
				'foreignKey' => 'department_id',
				'conditions' => array('permissions' => 1),
				'dependent' => true
			)
		);
		
		var $validate = array(
			'name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a name for the department.'
				)/*,
				
				'unique' => array(
					'rule' => 'uniqueDepartmentInGroup',
					'message' => 'That department name is currently in use. Please choose another.'
				)*/
			),
			
			'group_id' => array(
				'exists' => array(
					'rule' => 'groupExists',
					'message' => 'The group you have selected does not seem to exist!'
				)
			)
		);
		
		function isUserInAnyDepartment($user_id = null)
		{
			if ($user_id)
			{
				$member = $this->Member->find('first', array('recursive' => -1, 'conditions' => array('Member.user_id' => $user_id)));
				$admin = $this->Admin->find('first', array('recursive' => -1, 'conditions' => array('Admin.user_id' => $user_id)));
				
				if (!empty($member) || !empty($admin))
					return true;
			}
			
			return false;
		}
		
		function uniqueDepartmentInGroup($check)
		{
			// todo: fill this in
			return false;
		}
		
		function groupExists($check)
		{
			$result = $this->Group->find('first', array('conditions' => array('id' => $check['group_id']), 'recursive' => -1));
			
			if (!empty($result))
				return true;
			return false;
		}
		
		function nonAdminMembers($dept_id = null)
		{
			if (empty($dept_id))
			{
				if (empty($this->id))
					return array();
				$dept_id = $this->id;
			}
			
			return $this->Member->find('all', array('conditions' => array('Member.department_id' => $dept_id, 'Member.permissions' => 0)));
		}
	}
?>
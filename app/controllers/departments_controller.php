<?php
	class DepartmentsController extends AppController
	{
		var $name = 'Departments';
		var $components = array('SessionPlus', 'ErrorListFormatter');
		var $uses = array('Department', 'GroupInvite');
		
		function remove_member($gm_id = null)
		{
			// no id, no go
			if (empty($gm_id))
				$this->redirect($this->referer());
			
			// check for login
			$userid = $this->Session->read('User.id');
			if (!$userid)
				$this->SessionPlus->redirectToLogin();
			
			// get current user
			$user = $this->Department->Member->find('first', array('recursive' => -1, 'conditions' => array('Member.user_id' => $userid)));
			
			// check for admin
			if (!$user['Member']['permissions'])
			{
				$this->SessionPlus->flashError('You do not have sufficient privileges to perform that action.');
				$this->redirect($this->referer());
			}
			
			// get the user to be removed
			$rem = $this->Department->Member->find('first', array('recursive' => -1, 'conditions' => array('Member.id' => $gm_id)));
			
			// check for user
			if (empty($rem))
			{
				$this->SessionPlus->flashError('Invalid user.');
				$this->redirect($this->referer());
			}
			
			// check for non-admin
			if ($rem['Member']['permissions'])
			{
				$this->SessionPlus->flashError('You do not have sufficient privileges to perform that action.');
				$this->redirect($this->referer());
			}
			
			// check for same department
			if ($rem['Member']['department_id'] != $user['Member']['department_id'])
			{
				$this->SessionPlus->flashError('Invalid user.');
				$this->redirect($this->referer());
			}
			
			// finally, delete it
			if ($this->Department->Member->delete($gm_id))
				$this->SessionPlus->flashSuccess('User removed from department.');
			else
				$this->SessionPlus->flashError('User could not be removed! Please try again.');
			
			$this->redirect($this->referer());
		}
		
		function add()
		{
			if (empty($this->data))
				$this->redirect($this->referer());
			
			$this->ErrorListFormatter->deleteOldData();
			
			// check for login
			$userid = $this->Session->read('User.id');
			if (!$userid)
				$this->SessionPlus->redirectToLogin();
			
			// check for group
			if ($this->Department->isUserInAnyDepartment($userid))
			{
				$this->SessionPlus->flashError('You are already in a group!');
				$this->redirect($this->referer());
			}
			
			// check for group invite
			if ($this->GroupInvite->find('count', array('recursive' => -1, 'conditions' => array('GroupInvite.user_id' => $userid))))
			{
				$this->SessionPlus->flashError("You can't do that while an invite is pending.");
				$this->redirect($this->referer());
			}
			
			// add in admin array
			$this->data['Admin'] = array(0 => array('user_id' => $userid, 'permissions' => 1));
			
			if ($this->Department->saveAll($this->data))
			{
				// refresh session vars
				$newinfo = $this->Department->Admin->read();
				$this->Session->write('GroupMember', $newinfo['Admin']);
				
				$this->SessionPlus->flashSuccess('Department created.');
				$this->redirect($this->referer());
			}
			else
			{
				$this->ErrorListFormatter->setOldData($this->data);
				
				$this->SessionPlus->flashError('Department not created! Please fix the errors below.');
				$this->redirect($this->referer());
			}
		}
	}
?>
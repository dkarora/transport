<?php
	class GroupMembersController extends AppController
	{
		var $name = 'GroupMembers';
		
		function beforeFilter()
		{
		  parent::beforeFilter();
		  $this->SessionPlus->denyNonAdmins();
		}


		function delete ($member_id)
		{
		  if (!empty ($member_id))
		  {
		    $this->GroupMember->id = $member_id;
		    $gm = $this->GroupMember->read ();

		    if ($this->GroupMember->delete($member_id))
		    {
		      $this->SessionPlus->flashSuccess('Group member removed.');

		      $format = '%s removed %s (%d) from group %s (%d)';
		      $message = sprintf($format,
		         $this->SessionPlus->loggableUserName(),
			 $gm['User']['full_name'], $gm['User']['id'],
			 $gm['Group']['name'], $gm['Group']['id']);
		      $this->logAction($this->GroupMember, 'delete', $message, $member_id);
		    }
		    else
		    {
		      $this->SessionPlus->flashError('Group member could not be deleted!');
		    }
		  }

		  $this->redirect ($this->referer ());
		}


		function _set_permission ($member_id, $perm_bit = 0)
		{
		  if (empty ($member_id))
		  {
		    // Sanity check, this shouldn't happen ever
		    return;
		  }

		  $this->GroupMember->id = $member_id;
		  $gm = $this->GroupMember->read ();
		  $old_perm_bit = $gm['GroupMember']['permissions'];
		  $gm['GroupMember']['permissions'] = $perm_bit;

		  if ($this->GroupMember->save ($gm))
		  {
		    // Log message
		    $format = '%s changed permissions for %s (%d) for group %s (%d) from %d to %d';
		    $message = sprintf($format,
		       $this->SessionPlus->loggableUserName(),
		       $gm['User']['full_name'], $gm['User']['id'],
		       $gm['Group']['name'], $gm['Group']['id'],
		       $old_perm_bit, $perm_bit);
		       $this->logAction($this->GroupMember, 'delete', $message, $member_id);
		  }
		}


		function set_admin ($member_id)
		{
		  $this->_set_permission ($member_id, 1);
		  $this->redirect ($this->referer ());
		}

		function remove_admin ($member_id)
		{
		  $this->_set_permission ($member_id, 0);
		  $this->redirect ($this->referer ());
		}


                /* apparently the first parameter is also set as the model id
                 * which is not required, since this is an add (not an edit)
                 */
		function add ($group_id, $user_id, $is_admin)
		{
		  $this->GroupMember->id = null;
		  if (!empty ($group_id) && !empty ($user_id) && !empty ($is_admin))
		  {
		    $gm = array ();
		    $gm['GroupMember']['user_id'] = $user_id;
		    $gm['GroupMember']['group_id'] = $group_id;
		    $gm['GroupMember']['permissions'] = ($is_admin == 'yes')? 1 : 0;

		    if ($this->GroupMember->save($gm))
		    {
		      $this->GroupMember->User->id = $user_id;
		      $this->GroupMember->Group->id = $group_id;

		      $user = $this->GroupMember->User->read();
		      $group = $this->GroupMember->Group->read();
		      $permissions = ($is_admin == 'yes') ? 1 : 0;

		      $this->SessionPlus->flashSuccess('Group member saved.');

		      $format = '%s added %s (%d) to group %s (%d) as %s (permissions %d)';
		      $message = sprintf($format,
			  $this->SessionPlus->loggableUserName(),
			  $user['User']['full_name'], $user['User']['id'],
			  $group['Group']['name'], $group['Group']['id'],
			  ($permissions ? 'administrator' : 'member'), $permissions
			  );
		      $this->logAction($this->GroupMember, 'add', $message);
		    }
		    else
		      $this->SessionPlus->flashError('Group member not saved! Please correct the errors below.');
		  }
		  else
		  {
		    $this->SessionPlus->flashError('Group member not saved! Information missing');
		  }

		  $this->redirect ($this->referer ());
		}
	}
?>

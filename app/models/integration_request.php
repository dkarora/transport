<?php
	class IntegrationRequest extends AppModel
	{
		var $order = 'User.last_name';
		
		var $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
			)
		);
		
		var $hasMany = array(
			'LegacyRecord' => array(
				'className' => 'LegacyRecord',
				// there's no relational connection between the models
				// query for nothing to suppress errors from cake
				'finderQuery' => 'SELECT * FROM legacy_records WHERE 0=1'
			)
		);
		
		var $validate = array(
			'user_id' => array(
				'validUser' => array(
					'rule' => '_validUser',
					'message' => 'Invalid user ID?! Are you messing around with something?',
					'last' => true
				),
				
				'onePerUser' => array(
					'rule' => 'isUnique',
					'message' => 'Only one request per user!',
				)
			)
		);
		
		function _validUser($check)
		{
			// do a quick count check
			$result = $this->User->find('count', array('recursive' => -1, 'conditions' => array('id' => $check['user_id'])));
			
			if (!empty($result))
				return true;
			return false;
		}
		
		function requested($user_id = null)
		{
			if (empty($user_id))
				return false;
			
			// check for count
			$result = $this->find('count', array('recursive' => -1, 'conditions' => array('user_id' => $user_id)));
			
			if (!empty($result))
				return true;
			return false;
		}
		
		function filled($user_id = null)
		{
			if (empty($user_id))
				return false;
			
			// check for count
			$result = $this->find('count', array('recursive' => -1, 'conditions' => array('user_id' => $user_id, 'filled' => 1)));
			
			if (!empty($result))
				return true;
			return false;
		}
		
		function makeRequest($user_id = null)
		{
			if (empty($user_id))
				return false;
			
			$data = array('user_id' => $user_id, 'filled' => 0);
			
			return $this->save($data);
		}
		
		function countPending()
		{
			$result = $this->find('count', array('recursive' => -1, 'conditions' => array('filled' => 0)));
			
			return $result;
		}
		
		function getPending()
		{
			$results = $this->find('all', array('conditions' => array('filled' => 0)));
			
			return $results;
		}
		
		function getFilled()
		{
			$results = $this->find('all', array('conditions' => array('filled' => 1)));
			
			return $results;
		}
		
		function getRequest($id, $findMatches = false)
		{
			$this->id = $id;
			$result = $this->find('first');
			
			if ($findMatches && !empty($result))
			{
				// query the legacy_records table for matches
				$arr = array();
				$recs = $this->LegacyRecord->find('all', array('conditions' => array('last_name' => $result['User']['last_name'], 'first_name' => $result['User']['first_name'], 'filled' => 0)));
				
				// put results into typical hasMany form
				foreach ($recs as $k => $v)
					$arr[$k] = $v[$this->LegacyRecord->alias];
				
				$result[$this->LegacyRecord->alias] = $arr;
			}
			
			return $result;
		}
	}
?>
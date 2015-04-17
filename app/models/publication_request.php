<?php	
	class PublicationRequest extends AppModel
	{
		var $name = 'PublicationRequest';
		
		var $belongsTo = array(
			'Publication' => array(
				'className' => 'Publication',
				'foreignKey' => 'publication_id'
			),
			
			'Owner' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
			)
		);
		
		var $validate = array(
			'publication_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Publication ID not provided.',
					'last' => true
				),
				
				'validPublication' => array(
					'rule' => '_validPublication',
					'message' => 'Invalid publication.',
					'last' => true
				),
				
				'notRequested' => array(
					'rule' => '_notRequested',
					'message' => 'Publication already requested!'
				)
			),
			
			'user_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Owner ID not provided.',
					'last' => true
				),
				
				'validUser' => array(
					'rule' => '_validUser',
					'message' => 'Invalid user.',
				)
			)
		);
		
		function _notRequested($check)
		{
			$result = $this->find('count', array('recursive' => -1, 'conditions' => array('publication_id' => $check['publication_id'], 'user_id' => $this->data[$this->name]['user_id'])));
			
			// if any records returned, then this is already requested
			// duh
			if ($result)
				return false;
			return true;
		}
		
		function _validUser($check)
		{
			$this->Owner->id = $check['user_id'];
			$result = $this->Owner->find('count', array('recursive' => -1));
			
			if ($result)
				return true;
			return false;
		}
		
		function _validPublication($check)
		{
			$this->Publication->id = $check['publication_id'];
			$result = $this->Publication->find('count', array('recursive' => -1));
			
			if ($result)
				return true;
			return false;
		}
		
		function getCombinedRequests($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			$pubs = $this->find('all', array('conditions' => array('user_id' => $user_id), 'recursive' => 1));
			for ($i = 0; $i < sizeof ($pubs); $i++)
			{
				$this->Publication->Category->id = $pubs[$i]['Publication']['category_id'];
				$this->Publication->Category->recursive = -1;
				$pubs[$i] = array_merge($pubs[$i], $this->Publication->Category->find('first'));
			}
			
			return $pubs;
		}
	}
?>
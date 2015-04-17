<?php
	class VideoRequest extends AppModel
	{
		var $name = 'VideoRequest';
		
		var $belongsTo = array(
			'Instance' => array(
				'className' => 'VideoInstance',
				'foreignKey' => 'instance_id'
			),
			
			'Owner' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
			)
		);
		
		var $validate = array(
			'instance_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Instance ID not provided.',
					'last' => true
				),
				
				'validInstance' => array(
					'rule' => '_validVideoInstance',
					'message' => 'Invalid video instance.',
					'last' => true
				),
				
				'notRequested' => array(
					'rule' => '_notRequested',
					'message' => 'Video already requested!'
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
			$result = $this->find('count', array('recursive' => -1, 'conditions' => array('instance_id' => $check['instance_id'], 'user_id' => $this->data[$this->name]['user_id'])));
			
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
		
		function _validVideoInstance($check)
		{
			$this->Instance->id = $check['instance_id'];
			$result = $this->Instance->find('count', array('recursive' => -1));
			
			if ($result)
				return true;
			return false;
		}
		
		function getCombinedRequests($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			// get all relevant videorequests
			$this->recursive = 1;
			$vreq = $this->find('all', array('conditions' => array('user_id' => $user_id)));
			$this->Instance->Video->recursive = 0;
			
			for ($i = 0; $i < sizeof($vreq); $i++)
			{
				$this->Instance->Video->id = $vreq[$i]['Instance']['video_id'];
				$vreq[$i] = array_merge($this->Instance->Video->find('first'), $vreq[$i]);
			}
			
			return $vreq;
		}
	}
?>
<?php
	class VideoCheckout extends AppModel
	{
		var $name = 'VideoCheckout';
		var $order = 'checkout_time DESC';
		
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
			'checkout_time' => array(
				'empty' => array(
					'rule' => 'blank',
					'message' => 'Checkout time must be empty.',
					'last' => true
				)
			),
			
			'instance_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Video instance ID not provided.',
					'last' => true
				),
				
				'validVideoInstance' => array(
					'rule' => '_validVideoInstance',
					'message' => 'Invalid video instance.',
					'last' => true
				),
				
				'notCheckedOut' => array(
					'rule' => '_notCheckedOut',
					'message' => 'Instance already checked out by this user.',
					'last' => true
				),
			),
			
			'user_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'User ID not provided.',
					'last' => true
				),
				
				'validUser' => array(
					'rule' => '_validUser',
					'message' => 'Invalid user.',
					'last' => true
				)
			)
		);
		
		function _validVideoInstance($check)
		{
			$this->Instance->id = $check['instance_id'];
			$result = $this->Instance->find('count', array('recursive' => -1));
			
			if ($result > 0)
				return true;
			return false;
		}
		
		function _notCheckedOut($check)
		{
			$uid = $this->data[$this->name]['user_id'];
			$iid = $check['instance_id'];
			
			$result = $this->find('count', array('recursive' => -1, 'conditions' => array('user_id' => $uid, 'instance_id' => $iid)));
			if ($result > 0)
				return false;
			return true;
		}
		
		function _validUser($check)
		{
			$this->Owner->id = $check['user_id'];
			
			$result = $this->Owner->find('count');
			if ($result > 0)
				return true;
			return false;
		}
		
		function getAllCombinedCheckouts()
		{
			$this->recursive = 1;
			$co = $this->find('all');
			
			for ($i = 0; $i < sizeof($co); $i++)
			{
				$this->Instance->Video->recursive = 0;
				$this->Instance->Video->id = $co[$i]['Instance']['video_id'];
				$video = $this->Instance->Video->find('first');
				$co[$i] = array_merge($co[$i], $video);
			}
			
			return $co;
		}
	}
?>
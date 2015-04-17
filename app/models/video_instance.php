<?php
	class VideoInstance extends AppModel
	{
		var $name = 'VideoInstance';
		
		var $belongsTo = array(
			'Video' => array(
				'className' => 'Video',
				'foreignKey' => 'video_id'
			)
		);
		
		var $hasMany = array(
			'Checkout' => array(
				'className' => 'VideoCheckout',
				'foreignKey' => 'instance_id'
			),
			
			'Request' => array(
				'className' => 'VideoRequest',
				'foreignKey' => 'instance_id'
			)
		);
		
		var $validate = array(
			'video_id' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'No video ID provided!',
					'last' => true
				),
				
				'validVideo' => array(
					'rule' => '_isValidVideo',
					'message' => 'Invalid video ID.',
				),
			),
			
			'quantity' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Quantity not provided.',
					'last' => true
				),
				
				'number' => array(
					'rule' => 'numeric',
					'message' => 'Quantity was not a number.',
					'last' => true
				),
				
				'positive' => array(
					'rule' => array('comparison', '>', 0),
					'message' => 'Quantity must be greater than zero.',
				)
			),
			
			'format' => array(
				'valid' => array(
					'rule' => '_isValidFormat',
					'message' => 'Invalid format.',
				)
			)
		);
		
		function _isValidFormat($check)
		{
			return in_array($check['format'], $this->getEnumValues('format'));
		}
		
		function _isValidVideo($check)
		{
			$result = $this->Video->find('first', array('recursive' => -1, 'conditions' => array('Video.id' => $check['video_id'])));
			if (empty($result))
				return false;
			return true;
		}
		
		function getCheckouts($id = null, $recursive = -1)
		{
			if (empty($id))
				$id = $this->id;
			
			return $this->Checkout->find('all', array('recursive' => $recursive, 'conditions' => array('instance_id' => $id)));
		}
		
		function isRequested($instance_id = null, $user_id = null)
		{
			if (empty($instance_id) || empty($user_id))
				return false;
			
			$result = $this->Request->find('count', array('conditions' => array('instance_id' => $instance_id, 'user_id' => $user_id), 'recursive' => -1));
			
			if ($result == 0)
				return false;
			
			return true;
		}
		
		function isCheckedOut($instance_id = null, $user_id = null)
		{
			if (empty($instance_id) || empty($user_id))
				return false;
			
			$result = $this->Checkout->find('count', array('conditions' => array('instance_id' => $instance_id, 'user_id' => $user_id), 'recursive' => -1));
			
			if ($result == 0)
				return false;
			
			return true;
		}
	}
?>
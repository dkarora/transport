<?php
	class CartItem extends AppModel
	{
		var $belongsTo = array(
			'Owner' => array(
				'className' => 'User',
				'foreignKey' => 'user_id'
			),
			
			'VideoInstance' => array(
				'className' => 'VideoInstance',
				'foreignKey' => 'reference_id',
				'conditions' => array('CartItem.type' => 'Video')
			),
			
			'Publication' => array(
				'className' => 'Publication',
				'foreignKey' => 'reference_id',
				'conditions' => array('CartItem.type' => 'Publication')
			),
		);
		
		var $validate = array(
			'type' => array(
				'validType' => array(
					'rule' => '_validType',
					'message' => 'Bad cart item type.',
					'last' => true
				)
			),
			
			'user_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'No user ID provided.',
					'last' => true
				),
				
				'validUser' => array(
					'rule' => '_validUser',
					'message' => 'Invalid user ID.'
				)
			),
			
			'reference_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'No cart reference ID provided.',
					'last' => true
				),
				
				'number' => array(
					'rule' => 'numeric',
					'message' => 'Reference ID is not a number.',
				),
				
				'notInCart' => array(
					'rule' => '_notInCart',
					'message' => 'This item is already in your cart!',
				)
			),
		);
		
		function _notInCart($check)
		{
			$result = $this->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'reference_id' => $check['reference_id'],
					'user_id' => $this->data[$this->name]['user_id'],
					'type' => $this->data[$this->name]['type']
				)));
			
			if (empty($result))
				return true;
			
			return false;
		}
		
		function addVideo($instance_id = null, $user_id = null)
		{
			$vid = array('user_id' => $user_id, 'reference_id' => $instance_id, 'type' => 'Video');
			return $this->save($vid);
		}
		
		function addPublication($pub_id = null, $user_id = null)
		{
			$pub = array('user_id' => $user_id, 'reference_id' => $pub_id, 'type' => 'Publication');
			return $this->save($pub);
		}
		
		function getCart($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			return $this->find('all', array('conditions' => array('CartItem.user_id' => $user_id)));
		}
		
		function getCartVideoIds($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			// find videos in user cart
			$result = $this->find('all', array('conditions' => array('CartItem.user_id' => $user_id, 'CartItem.type' => 'Video'), 'recursive' => -1, 'order' => 'CartItem.reference_id ASC'));
			
			// if nothing in the cart don't bother with other queries
			if (empty($result))
				return array();
			
			// convert to just relevant ids
			return Set::combine($result, '{n}.CartItem.id', '{n}.CartItem.reference_id');
		}
		
		function getCartVideos($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			// convert to just relevant ids
			$raw = $this->getCartVideoIds($user_id);
			
			if (empty($raw))
				return array();
			
			// convert to set up array as CartItem.id => VideoInstance
			$rt = array_combine(array_keys($raw), array_values($this->VideoInstance->find('all', array('conditions' => array('VideoInstance.id' => $raw), 'order' => 'VideoInstance.id ASC'))));
			
			return $rt;
		}
		
		function getCartPublicationIds($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			return Set::combine($this->find('all', array('recursive' => -1, 'conditions' => array('CartItem.type' => 'Publication', 'CartItem.user_id' => $user_id))), '{n}.CartItem.id', '{n}.CartItem.reference_id');
		}
		
		function getCartPublications($user_id = null)
		{
			if (empty($user_id))
				return array();
			
			// get ids
			$ids = $this->find('all', array('recursive' => 0, 'conditions' => array('CartItem.user_id' => $user_id, 'CartItem.type' => 'Publication')));
			$ids = Set::combine($ids, '{n}.CartItem.id', '{n}.Publication');
			
			return $ids;
		}
		
		function getCombinedCart($user_id = null)
		{
			$pubs = $this->getCartPublications($user_id);
			$vids = $this->getCartVideos($user_id);
			
			return $pubs + $vids;
		}
		
		function isVideoInCart($instance_id = null, $user_id = null)
		{
			if (!$instance_id || !$user_id)
				return false;
			
			$result = $this->find('count', array('conditions' => array('reference_id' => $instance_id, 'user_id' => $user_id, 'type' => 'Video'), 'recursive' => -1));
			
			if ($result)
				return true;
			return false;
		}
		
		function isPublicationInCart($pub_id = null, $user_id = null)
		{
			if (!$pub_id || !$user_id)
				return false;
			
			$result = $this->find('count', array('conditions' => array('reference_id' => $pub_id, 'user_id' => $user_id, 'type' => 'Publication'), 'recursive' => -1));
			
			if ($result)
				return true;
			return false;
		}
		
		function _validUser($check)
		{
			$this->Owner->id = $check['user_id'];
			$owner = $this->Owner->read();
			
			if (empty($owner))
				return false;
			return true;
		}
		
		function _validType($check)
		{
			return in_array($check['type'], $this->getEnumValues('type'));
		}
	}
?>
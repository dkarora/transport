<?php
	class Publication extends AppModel
	{
		var $name = 'Publication';
		
		var $order = array(
			'Publication.name' => 'ASC'
		);
		
		var $belongsTo = array(
			'Category' => array(
				'className' => 'PublicationCategory',
				'foreignKey' => 'category_id',
			)
		);
		
		var $hasMany = array(
			'Checkout' => array(
				'className' => 'PublicationCheckout',
				'foreignKey' => 'publication_id'
			),
			
			'Request' => array(
				'className' => 'PublicationRequest',
				'foreignKey' => 'publication_id'
			)
		);
		
		var $validate = array(
			'category_id' => array(
				'categoryExists' => array(
					'rule' => 'categoryExists',
					'message' => 'Invalid category!',
				)
			),
			
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Name must not be empty.',
				),
				
				'unique' => array(
					'rule' => 'isUnique',
					'message' => 'Name must be unique.'
				)
			),
			
			'source' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Source cannot be empty.'
				)
			),
			
			'bsr_assignment' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Video ID cannot be empty.'
				)
			),
			
			'year_published' => array(
				'isNumber' => array(
					'rule' => 'numeric',
					'message' => 'Year must be a number.',
					'last' => true
				),
			),
			
			'quantity' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Quantity cannot be empty.',
					'last' => true
				),
				
				'number' => array(
					'rule' => 'numeric',
					'message' => 'Quantity must be a number.',
					'last' => true
				),
				
				'positive' => array(
					'rule' => array('comparison', '>', 0),
					'message' => 'Quantity must be greater than zero.'
				)
			)
		);
		
		function isRequested($pub_id = null, $user_id = null)
		{
			if (!$pub_id || !$user_id)
				return false;
			
			$result = $this->Request->find('count', array('recursive' => -1, 'conditions' => array('publication_id' => $pub_id, 'user_id' => $user_id)));
			
			if ($result)
				return true;
			return false;
		}
		
		function isCheckedOut($pub_id = null, $user_id = null)
		{
			if (!$pub_id || !$user_id)
				return false;
			
			$result = $this->Checkout->find('count', array('recursive' => -1, 'conditions' => array('publication_id' => $pub_id, 'user_id' => $user_id)));
			
			if ($result)
				return true;
			return false;
		}
		
		function beforeValidate()
		{
			if (is_array($this->data[$this->name]['year_published']))
				$this->data[$this->name]['year_published'] = $this->data[$this->name]['year_published']['year'];
			
			return true;
		}
		
		function categoryExists($check)
		{
			$result = $this->Category->find('first', array('recursive' => -1, 'conditions' => array('id' => $check['category_id'])));
			if (!empty($result))
				return true;
			return false;
		}
		
		function getCheckoutCount($pub_id = null)
		{
			if (empty($pub_id))
				$pub_id = $this->id;
			
			return $this->Checkout->find('count', array('recursive' => -1, 'conditions' => array('publication_id' => $pub_id)));
		}
	}
?>
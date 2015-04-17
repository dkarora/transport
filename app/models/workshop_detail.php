<?php
	class WorkshopDetail extends AppModel
	{
		var $name = 'WorkshopDetail';
		var $actsAs = array('Legacy');
		var $belongsTo = array(
			'Category' => array(
				'className' => 'WorkshopCategory',
				'foreignKey' => 'category_id'
			)
		);
		
		var $validate = array(
			'name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a name.',
					'last' => true
				)
			),
			
			'credits' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter an amount of credits.',
					'last' => true
				),
				
				'number' => array(
					'rule' => 'numeric',
					'message' => 'Please enter a valid number of credits.',
					'last' => true
				),
				
				'positivenumber' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'Please enter a positive number of credits.',
					'last' => true
				)
			),
			
			'description' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter in the description.',
					'last' => true
				)
			),
			
			'ceu_credits' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a number of credits.',
				),
				
				'isNumber' => array(
					'allowEmpty' => true,
					'rule' => 'numeric',
					'message' => 'Please provide a valid number of credits.',
					'last' => true
				)
			),
			
			'category_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Please select a category.',
					'last' => true
				),
				
				'validCategory' => array(
					'rule' => '_validCategory',
					'message' => 'Invalid category!',
				)
			)
		);
		
		function useLegacy($legacy, $cascade = true)
		{
			parent::useLegacy($legacy);
			
			// cascade down
			if ($cascade)
				$this->Category->useLegacy($legacy);
		}
		
		function _validCategory($check)
		{
			// find the category, yo
			// check legacy too in case we're adding a legacy workshop
			$this->Category->useLegacy(true);
			
			$result = $this->Category->find('count', 
				array('recursive' => -1, 'conditions' => array('Category.id' => $check['category_id'])));
			
			if ($result)
				return true;
			return false;
		}
	}
?>
<?php
	class Video extends AppModel
	{
		var $name = 'Video';
		
		var $order = array(
			'Video.name' => 'ASC'
		);
		
		var $belongsTo = array(
			'Category' => array(
				'className' => 'VideoCategory',
				'foreignKey' => 'category_id',
			)
		);
		
		var $hasMany = array(
			'Instance' => array(
				'className' => 'VideoInstance',
				'foreignKey' => 'video_id',
				'dependent' => true
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
			
			'quality_rating' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Quality rating cannot be empty.',
				),
				
				'withinRange' => array(
					'rule' => array('between', 1, 5),
					'message' => 'Quality rating must be between 1 and 5 inclusive.'
				),
				
				'isNumber' => array(
					'rule' => 'numeric',
					'message' => 'Quality rating must be a number.'
				)
			),
			
			'length' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Length cannot be empty.',
				),
				
				'isNumber' => array(
					'rule' => 'numeric',
					'message' => 'Length must be a number.'
				),
				
				'withinRange' => array(
					'rule' => array('comparison', '>', 0),
					'message' => 'Length must be greater than zero.'
				)
			),
			
			'year_published' => array(
				'isNumber' => array(
					'rule' => 'numeric',
					'message' => 'Year must be a number.',
					'last' => true
				),
			)
		);
		
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
	}
?>
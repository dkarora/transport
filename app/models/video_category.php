<?php
	class VideoCategory extends AppModel
	{
		var $name = 'VideoCategory';
		var $order = 'name ASC';
		
		var $validate = array(
			'designation' => array(
				'length' => array(
					'rule' => array('maxLength', 3),
					'message' => 'Designation must be no longer than 3 characters.'
				),
				
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Designation cannot be empty.'
				),
				
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Designations must be unique.'
				)
			),
			
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Name cannot be empty.'
				),
				
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Names must be unique.'
				)
			)
		);
	}
?>
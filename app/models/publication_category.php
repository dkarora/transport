<?php
	class PublicationCategory extends AppModel
	{
		var $name = 'PublicationCategory';
		var $order = 'designation ASC';
		
		var $hasMany = array(
			'Publication' => array(
				'className' => 'Publication',
				'foreignKey' => 'category_id'
			)
		);
		
		var $validate = array(
			'name' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Name cannot be empty.',
					'last' => true
				),
			),
			
			'designation' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Designation cannot be empty.',
					'last' => true
				)
			)
		);
	}
?>
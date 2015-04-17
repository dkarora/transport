<?php
	class WorkshopCategory extends AppModel
	{
		var $name = 'WorkshopCategory';
		
		var $validate = array(
			'name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a name.',
					'last' => true
				)
			)
		);
		
		var $actsAs = array('Legacy');
	}
?>
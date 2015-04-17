<?php
	class TechNote extends AppModel
	{
		var $name = 'TechNote';
		
		var $validate = array(
			'summary' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the summary.',
					'last' => true
				)
			),
			
			'name' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the name.',
					'last' => true
				)
			),
			
			'title' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Title must be filled in.',
					'last' => true
				)
			)
			
			// etc
		);
		
		function mostRecent($howMany = 4)
		{
			return $this->find('all', array('limit' => $howMany, 'order' => 'TechNote.id DESC'));
		}
	}
?>
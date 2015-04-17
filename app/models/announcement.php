<?php
	class Announcement extends AppModel
	{
		var $name = 'Announcement';
		var $order = 'id DESC';
		
		var $validate = array(
			'text' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Text must not be empty.'
				)
			)
		);
		
		function mostRecent()
		{
			$result = $this->find('first', array('recursive' => -1));
			return $result[$this->name]['text'];
		}
	}
?>
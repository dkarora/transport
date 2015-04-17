<?php
	App::import('Sanitize');
	
	class AgendaItem extends AppModel
	{
		var $name = 'AgendaItem';
		var $order = 'timestamp';
		var $escapeFields = array('description');
		
		var $validate = array(
			'workshop_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Workshop ID not specified.',
					'last' => true
				),
				
				'validWorkshop' => array(
					'rule' => '_validWorkshop',
					'message' => 'Invalid workshop.',
					'last' => true
				)
			),
			
			'timestamp' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'messsage' => 'Timestamp not specified.',
					'last' => true
				)
			),
			
			'description' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Description not specified.',
					'last' => true
				)
			)
		);
		
		var $belongsTo = array(
			'Workshop' => array(
				'className' => 'Workshop',
				'foreignKey' => 'workshop_id'
			),
		);
		
		function _validWorkshop($check)
		{
			$result = $this->Workshop->find('count', array('recursive' => -1, 'conditions' => array('id' => $check['workshop_id'])));
			
			if ($result)
				return true;
			return false;
		}
		
		function afterFind($results, $primary)
		{
			return $this->_htmlEscape($results, $this->escapeFields);
		}
	}
?>
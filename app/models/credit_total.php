<?php
	class CreditTotal extends AppModel
	{
		var $name = 'CreditTotal';
		var $actsAs = array('Immutable');
		var $primaryKey = 'user_id';
		var $order = 'User.last_name';
		
		var $belongsTo = array(
			'User' => array(
				'primaryKey' => 'user_id'
			)
		);
	}
?>
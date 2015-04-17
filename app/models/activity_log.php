<?php
	class ActivityLog extends AppModel
	{
		var $name = 'ActivityLog';
		var $order = array(
			'ActivityLog.created' => 'desc'
		);
		var $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
			),
		);
		
		var $validate = array(
			'id' => array(
				'on' => 'update',
				'rule' => array('_alwaysFail'),
				'message' => 'Activity logs are immutable.',
				'last' => true,
			),
			
			// this field can be empty to represent an anonymous user
			'user_id' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'validUser' => array(
					'rule' => array('_validUser'),
					'message' => 'Invalid user id.',
				)
			),
			
			'created' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'blank' => array(
					'rule' => 'blank',
					'message' => 'This field must be left blank.',
				),
			),
			
			'model_name' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'The model name must be provided.',
				),
				
				'resolvable' => array(
					'rule' => array('_resolvableTableName'),
					'message' => 'Table name must be resolvable.'
				),
			),
			
			'object_id' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'The object id must be provided.',
				),
			),
			
			'action' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'validenum' => array(
					'rule' => array('_validEnum'),
					'message' => 'That action type is invalid.',
				),
			),
			
			'message' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a message for the log.',
				),
			),
			
			'url' => array(
				'immutable' => array(
					'on' => 'update',
					'rule' => array('_alwaysFail'),
					'message' => 'Activity logs are immutable.',
					'last' => true,
				),
				
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a URL for the log.',
				),
			),
		);
		
		function _resolvableTableName($check)
		{
			$name = $check['model_name'];
			
			// check the class registry and see if we can get a model out of it
			$exists = ClassRegistry::init($name);
			
			if (!empty($exists))
				return true;
			return false;
		}
		
		function _validEnum($check)
		{
			$enum = $check['action'];
			$values = $this->getEnumValues('action');
			
			if (array_key_exists($enum, $values))
				return true;
			return false;
		}
		
		function _validUser($check)
		{
			// allow user_id = 0 for anonymous users
			$userId = $check['user_id'];
			if ($userId === 0)
				return true;
			
			$this->User->id = $userId;
			if ($this->User->exists())
				return true;
			
			return false;
		}
		
		function _alwaysFail($check)
		{
			return false;
		}
		
		function logAction($model, $type, $message, $url, $user_id, $object_id)
		{
			// create the data array with what we have
			$data = array(
				'model_name' => $model,
				'action' => $type,
				'message' => $message,
				'url' => $url,
				'user_id' => $user_id,
				'object_id' => $object_id,
				'created' => ''
			);
			
			$this->create();
			return $this->save($data);
		}
	}
?>
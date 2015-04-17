<?php
	class LoggableBehavior extends ModelBehavior
	{
		function setup(&$Model, $settings)
		{
			if (!isset($this->settings[$Model->alias]))
				$this->settings[$Model->alias] = array('legacy' => false);
			
			$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array)$settings);
		}
		
		// shortcut to ActivityLog::logAction()
		function logAction(&$Model, $type, $message, $url, $user_id = 0, $object_id = null)
		{
			// set the model to have a hasMany association with ActivityLog
			if (!array_key_exists('ActivityLog', $Model->hasMany))
			{
				$Model->bindModel(array(
					'hasMany' => array(
						'ActivityLog' => array(
							'className' => 'ActivityLog',
							'foreignKey' => 'object_id',
							'conditions' => array('ActivityLog.model_name' => $Model->name),
							'dependent' => false,
						)
					)
				));
			}
			
			// name is constant and resolvable; alias is not.
			$modelName = $Model->name;
			$objectId = $object_id;
			if (!$objectId)
				$objectId = $Model->id;
			
			return $Model->ActivityLog->logAction($modelName, $type, $message, $url, $user_id, $objectId);
		}
	}
?>
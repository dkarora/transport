<?php
	class ImmutableBehavior extends ModelBehavior
	{
		function setup(&$Model, $settings)
		{
			if (!isset($this->settings[$Model->alias]))
			{
				$this->settings[$Model->alias] = array(
					'restrict' => array('create', 'update', 'delete'),
					'invalidate' => null,
					'failureMessage' => 'This model is read only.'
				);
			}
			$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array)$settings);
		}
		
		private function getFieldToInvalidate($Model)
		{
			$inv = $this->settings[$Model->alias]['invalidate'];
			if (!empty($inv))
				return $inv;
			
			$schema = array_keys($Model->schema());
			return $schema[0];
		}
		
		function beforeSave(&$Model)
		{
			// if we want to restrict create check if the model will create
			$willCreate = false;
			
			if ($Model->find('count') == 0)
				$willCreate = true;
			
			if ((!$willCreate && in_array('update', $this->settings[$Model->alias]['restrict'])) || 
				($willCreate && in_array('create', $this->settings[$Model->alias]['restrict'])))
			{
				$Model->invalidate($this->getFieldToInvalidate(), $this->settings[$Model->alias]['failureMessage']);
				return false;
			}
			
			return true;
		}
		
		function beforeDelete(&$Model, $cascade = true)
		{
			if (in_array('delete', $this->settings[$Model->alias]['restrict']))
			{
				$Model->invalidate($this->getFieldToInvalidate(), $this->settings[$Model->alias]['failureMessage']);
				return false;
			}
			
			return true;
		}
	}
?>
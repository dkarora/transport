<?php
	class ErrorListFormatterComponent extends Object {
		var $name = 'ErrorListFormatter';
		var $components = array('Session');
		
		function initialize(&$controller, $settings = array())
		{
			// saving the controller reference for later use
			$this->controller =& $controller;
		}
		
		function format($errors)
		{
			// this is a really hacky way to get this done
			// but cake refuses to allow $this->set() to work
			$rt = '<ul class="errorlist">';
			foreach($errors as $key => $value)
				$rt .= "<li>$value</li>";
			$rt .= '</ul>';
			
			$this->Session->setFlash($rt, 'default', array(), 'valErrors');
		}
		
		function setOldData($data)
		{
			// this should allow the _Token key from the Security component
			// to live happily in $this->data without trying to create a model
			// out of it
			if (isset($data['_Token']))
				unset($data['_Token']);
			
			$this->Session->write('OldData', $data);
		}
		
		function deleteOldData()
		{
			$this->Session->delete('OldData');
		}
		
		function getOldData()
		{
			$d = $this->Session->read('OldData');
			return $d;
		}
		
		function restoreErrorMessages()
		{
			$odata = $this->getOldData();
			
			// if no data, nothing to be done
			if (empty($odata))
				return;
			
			$mkey = array_keys($odata);
			
			if (!empty($odata))
			{
				foreach ($mkey as $k => $v)
				{
					// grab each model for the error messages
					$model = & ClassRegistry::init($v);
					$model->set($odata);
					$model->validates();
				}
			}
			
			$this->controller->data = $odata;
			
			// don't hang on to stale data
			$this->deleteOldData();
		}
	}
?>
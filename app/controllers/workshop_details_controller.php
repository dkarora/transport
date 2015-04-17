<?php
	class WorkshopDetailsController extends AppController
	{
		var $name = 'WorkshopDetails';
		var $components = array('ErrorListFormatter', 'SessionPlus');
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
			
			if ($this->params['action'] == 'add')
				$this->Security->enabled = false;
		}
		
		function add()
		{
			$this->ErrorListFormatter->deleteOldData();
			
			if (!empty($this->data))
			{
				if ($this->WorkshopDetail->save($this->data))
				{
					$detail = $this->WorkshopDetail->read();
					$format = '%s created workshop %s (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$detail['WorkshopDetail']['name'], $detail['WorkshopDetail']['id']
					);
					$this->logAction($this->WorkshopDetail, 'create', $message);
					
					$this->SessionPlus->flashSuccess('Your workshop has been added.');
				}
				else
				{
					$this->SessionPlus->flashError('Your workshop has not been added. Please correct the errors below.');
					$this->ErrorListFormatter->setOldData($this->data);
				}
			}
			
			$this->redirect($this->referer());
		}
	}
?>
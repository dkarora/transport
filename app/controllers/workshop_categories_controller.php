<?php
	class WorkshopCategoriesController extends AppController
	{
		var $name = 'WorkshopCategories';
		var $components = array('ErrorListFormatter', 'SessionPlus');
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
		}
		
		function add()
		{
			$this->ErrorListFormatter->deleteOldData();
			
			if ($this->WorkshopCategory->save($this->data))
			{
				$cat = $this->WorkshopCategory->read();
				$format = '%s created workshop category %s (%d)';
				$message = sprintf($format,
					$this->SessionPlus->loggableUserName(),
					$cat['WorkshopCategory']['name'], $cat['WorkshopCategory']['id']
				);
				$this->logAction($this->WorkshopCategory, 'create', $message);
				
				$this->SessionPlus->flashSuccess('Workshop category added.');
			}
			else
			{
				$this->ErrorListFormatter->setOldData($this->data);
				$this->SessionPlus->flashError('Workshop category was not added. Please correct the errors below.');
			}
			
			$this->redirect($this->referer());
		}
	}
?>
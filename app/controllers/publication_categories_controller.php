<?php
	class PublicationCategoriesController extends AppController
	{
		var $name = 'PublicationCategories';
		var $components = array('SessionPlus', 'ErrorListFormatter');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
		}
		
		function add()
		{
			$this->ErrorListFormatter->deleteOldData();
			
			// data check
			if (!empty($this->data))
			{
				if ($this->PublicationCategory->save($this->data['PublicationCategory']))
					$this->SessionPlus->flashSuccess('Category saved.');
				else
				{
					$this->SessionPlus->flashError('Category not saved! Please fix the errors below.');
					$this->ErrorListFormatter->setOldData($this->data);
				}
			}
			
			$this->redirect($this->referer());
		}
	}
?>
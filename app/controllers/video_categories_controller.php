<?php
	class VideoCategoriesController extends AppController
	{
		var $name = 'VideoCategories';
		var $components = array('SessionPlus', 'ErrorListFormatter');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
		}
		
		function add()
		{
			$this->ErrorListFormatter->deleteOldData();
			
			if (!empty($this->data))
			{
				if ($this->VideoCategory->save($this->data))
					$this->SessionPlus->flashSuccess('Video category created.');
				else
				{
					$this->SessionPlus->flashError('Video category was not created. Please fix the errors below.');
					$this->ErrorListFormatter->setOldData($this->data);
				}
			}
			
			$this->redirect($this->referer());
		}
	}
?>
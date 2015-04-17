<?php
	class AnnouncementsController extends AppController
	{
		var $components = array('SessionPlus', 'ErrorListFormatter');
		
		function add()
		{
			$this->SessionPlus->denyNonAdmins();
			
			if ($this->Announcement->save($this->data))
				$this->SessionPlus->flashSuccess('Announcement created.');
			else
			{
				$this->SessionPlus->flashError('Announcement not created. Please correct the errors below.');
				$this->ErrorListFormatter->setOldData($this->data);
			}
			
			$this->redirect($this->referer());
		}
	}
?>
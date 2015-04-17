<?php
	class LegacyRecordsController extends AppController
	{
		var $components = array('SessionPlus');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
		}
		
		function add()
		{
			if (!empty($this->data))
			{
				if ($this->data['LegacyRecord']['file']['error'] == 0 && is_uploaded_file($this->data['LegacyRecord']['file']['tmp_name']))
				{
					if ($this->LegacyRecord->importFromCsv($this->data['LegacyRecord']['file']['tmp_name']))
						$this->SessionPlus->flashSuccess('Legacy records imported successfully.');
					else
						$this->SessionPlus->flashError('Legacy records were not imported!');
				}
				else
					$this->SessionPlus->flashError('File error.');
			}
			
			$this->redirect($this->referer());
		}
	}
?>
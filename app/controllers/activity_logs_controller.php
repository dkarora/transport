<?php
	class ActivityLogsController extends AppController
	{
		var $name = 'ActivityLogs';
		var $paginate = array(
			'ActivityLog' => array(
				'limit' => 50
			)
		);
		var $helpers = array('TimeFormatter');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
			$this->pageTitle .= 'Activity Logs';
		}
		
		function index()
		{
			$logs = $this->paginate('ActivityLog');
			$this->set('logs', $logs);
		}
		
		function view($id)
		{
			$this->pageTitle .= ' &rsaquo; View';
		}
	}
?>
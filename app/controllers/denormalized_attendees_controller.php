<?php
	class DenormalizedAttendeesController extends AppController
	{
		var $helpers = array('Csv', 'TimeFormatter');
		var $uses = array('DenormalizedAttendee', 'Workshop');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
		}
		
		function index()
		{
			$this->pageTitle .= 'Export Attendees';
			
			// get all workshops
			$this->Workshop->recursive = 0;
			$workshops = $this->paginate('Workshop');
			$this->set('workshops', $workshops);
		}
		
		function export($workshop_id = null)
		{
			if (!$workshop_id)
			{
				$this->SessionPlus->flashError('No workshop ID provided.');
				$this->redirect($this->referer());
			}
			
			if ($workshop_id != 'all')
			{
				$this->Workshop->id = $workshop_id;
				$workshop = $this->Workshop->find('first', array('recursive' => 0));
			}
			$headers = array_keys($this->DenormalizedAttendee->schema());
			// strike workshop_id from the final data
			$wsIdIdx = array_search('workshop_id', $headers);
			if ($wsIdIdx !== false)
				unset($headers[$wsIdIdx]);
			$this->set('headers', $headers);
			
			if ($workshop_id != 'all')
			{
				$this->set('data', $this->DenormalizedAttendee->find('all', array('fields' => $headers, 'conditions' => array('workshop_id' => $workshop_id), 'recursive' => -1)));
				$this->set('workshop', $workshop);
				$this->header("Content-disposition: attachment;filename=".Inflector::slug($workshop['Detail']['name']).'-'.Inflector::slug(date('Y-m-d', strtotime($workshop['Workshop']['date']))).'.csv');
			}
			else
			{
				$this->set('data', $this->DenormalizedAttendee->find('all', array('fields' => $headers, 'recursive' => -1, 'order' => array('workshop_id', 'last_name'))));
				$this->header('Content-disposition: attachment;filename=all-workshops.csv');
			}
			
			$this->header("Content-type: text/csv");
			$this->header("Cache-Control: public");
			$this->header("Content-Description: File Transfer");
			
			$this->layout = 'csv';
		}
	}
?>
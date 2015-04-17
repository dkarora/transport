<?php

class RoadScholarsController extends AppController {
	var $name = 'RoadScholars';
	var $uses = array('Attendee', 'Group', 'CreditTotal');
	var $pageTitle = 'Baystate Roads &rsaquo; ';
	var $helpers = array('Javascript');
	var $components = array('SessionPlus');
	
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->SessionPlus->denyNonAdminsFrom(array('print_certificates'));
		
		$this->pageTitle .= 'Road Scholars &rsaquo; ';
	}
	
	function index()
	{
		$this->pageTitle .= 'FAQ';
	}
	
	function print_certificates()
	{
		if (!empty($this->data))
		{
			$results = array();
			
			foreach ($this->data['RoadScholar'] as $scholar)
			{
				// make sure we actually want to print this one
				if (empty($scholar['selected']))
					continue;
				
				$this->CreditTotal->id = $scholar['user_id'];
				$creditTotal = $this->CreditTotal->find('first');
				$credits = $creditTotal[$this->CreditTotal->alias]['road_scholar_credits'];
				
				$results[] = $creditTotal;
			}
			
			$this->layout = 'pdf';
			Configure::write('debug', 0);
			
			$this->set('scholars', $results);
		}
		else
			$this->redirect($this->referer());
	}
	
	// note that $min is inclusive; $max is exclusive
	function _getScholarsList($min = 0, $max = 0, $conditions = array(), $creditsCountIfAtLeast = 1)
	{
		// only get attendees that showed up
		$conditions = array_merge($conditions, array($this->Attendee->alias.'.attendance' => 1, 'Detail.credits >=' => $creditsCountIfAtLeast));
		$this->Attendee->bindModel(array('belongsTo' => array('Detail' => array('className' => 'WorkshopDetail', 'foreignKey' => false, 'conditions' => array('Detail.id = Workshop.detail_id')))));
		$this->Attendee->unbindModel(array('hasMany' => array('PaymentRecord')));
		
		$groupCond = 'User.id HAVING';
		if (empty($min) && empty($max))
			$groupCond .= ' total_credits > 0';
		else
		{
			if (!empty($min))
				$groupCond .= ' total_credits >= ' . $min;
			if (!empty($max))
			{
				if (!empty($min))
					$groupCond .= ' AND ';
				$groupCond .= ' total_credits < ' . $max;
			}
		}
		
		debug ($groupCond);
		$attendees = $this->Attendee->find('all', array('conditions' => $conditions, 'order' => 'User.last_name', 'group' => $groupCond, 'fields' => array('SUM(Detail.credits) AS `total_credits`', 'User.affiliation', 'User.first_name', 'User.last_name', 'User.id')));
		
		// do a bit of data massaging
		foreach ($attendees as $k => $a)
		{
			$attendees[$k]['total_credits'] = array_shift(array_shift($attendees[$k]));
			
			$group = $this->Group->groupOfUser($a['User']['id']);
			if (!empty($group))
				$attendees[$k]['Group'] = array_shift($group);
			else if (!empty($a['User']['affiliation']))
				$attendees[$k]['Group'] = array('name' => $a['User']['affiliation']);
		}
		
		return $attendees;
	}
	
	function scholars()
	{
		$this->pageTitle .= 'List of Road Scholars';
		
		$this->set('scholars', $this->_getScholarsList(7, 22));
	}
	
	function masterscholars()
	{
		$this->pageTitle .= 'List of Master Road Scholars';
		
		$this->set('scholars', $this->_getScholarsList(22));
	}
	
	function checkprogress()
	{
		$this->pageTitle .= 'Check Your Progress';
		
		// get all
		$scholars = $this->_getScholarsList();
		$this->set('scholars', $scholars);
		
		// if we're logged in then find self
		if ($this->SessionPlus->isUserLoggedIn())
			$this->set('selfscholar', $this->_getScholarsList(0, 0, array($this->Attendee->alias.'.user_id' => $this->Session->read('User.id'))));
		
		if (isset($this->data['RoadScholars']['input']))
		{
			// search for username, first name, or last name
			// current limitation: only single word searches work
			$or = array();
			$or['User.username LIKE'] = $this->data['RoadScholars']['input'] . '%';
			$or['User.first_name LIKE'] = $this->data['RoadScholars']['input'] . '%';
			$or['User.last_name LIKE'] = $this->data['RoadScholars']['input'] . '%';
			$conditions = array('OR' => $or);
			
			$this->set('results', $this->_getScholarsList(0, 0, $conditions));
			$this->set('search_query', $this->data['RoadScholars']['input']);
		}
	}
}

?>
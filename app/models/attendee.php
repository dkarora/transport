<?php
	class Attendee extends AppModel
	{
		var $overrideCutoff = false;
		var $beforeSaveCredits = 0;
		var $afterSaveCredits  = 0;
		var $name = 'Attendee';
		var $belongsTo = array(
			'User' => array(
				'foreignKey' => 'user_id',
				'className' => 'User'
			),
			
			'Workshop' => array(
				'foreignKey' => 'workshop_id',
				'className' => 'Workshop'
			)
		);
		
		var $hasMany = array(
			'PaymentRecord' => array(
				'foreignKey' => 'attendee_id',
				'className' => 'PaymentRecord',
				'dependent' => true
			)
			
		);
		
		var $validate = array(
			'workshop_id' => array(
				'workshopNotFull' => array(
					'rule' => '_workshopNotFull',
					'message' => 'That workshop has filled to capacity.',
					'last' => true
				)
			),
			
			'user_id' => array(
				'notAlreadyEnrolled' => array(
					'rule' => '_notAlreadyEnrolled',
					'message' => 'You have already been enrolled in that workshop.',
					'last' => true
				)
			)
		);
		
		function _notAlreadyEnrolled($check)
		{
			$attendee = $this->find('first', array('recursive' => -1, 'conditions' => array('workshop_id' => $this->data['Attendee']['workshop_id'], 'user_id' => $this->data['Attendee']['user_id'])));
			
			if (empty($attendee))
				return true;
			
			return false;
		}
		
		function _workshopNotFull($check)
		{
			$capacity = $this->Workshop->find('first', array('conditions' => array('Workshop.id' => $check['workshop_id']), 'recursive' => 0));
			$capacity = $capacity['Workshop']['capacity'];
			$nAttendees = $this->find('count', array('conditions' => $check));
			if ($nAttendees >= $capacity)
				return false;
			
			return true;
		}
		
		function _dateDiff($startDate, $endDate)
		{
			// Parse dates for conversion
			$startArry = date_parse($startDate);
			$endArry = date_parse($endDate);
			
			// Convert dates to Julian Days
			$start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
			$end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);
			
			// Return difference
			return round(($end_date - $start_date), 0);
		}
		
		// tests if the insertion will validate the cutoff point
		// (currently 3 days before the workshop begins)
		// returns false if cutoff date is past, true otherwise
		function _applyCutoff()
		{
			// get the attendee so we can get the workshop
			$att = array();
			
			// unenrolling
			if ($this->id)
			{
				$att = $this->find('first', array('conditions' => array('id =' => $this->id), 'recursive' => -1));
			}
			// enrolling
			else if ($this->data['Attendee'])
			{
				$att = $this->data;
			}
			$workshop = $this->Workshop->findById($att['Attendee']['workshop_id']);
			
			// apply the three-day cutoff
			if ($this->_dateDiff(date('Y-m-d H:i:s', strtotime('+1 days')), $workshop['Workshop']['date']) <= 0)
				return false;
			
			return true;
		}
		
		function enrollAttendee($workshop_id = null, $user_id = null, $attendance = 0)
		{
			if ($workshop_id && $user_id)
			{
				$attendee = array(
					'Attendee' => array(
						'user_id' => $user_id,
						'workshop_id' => $workshop_id,
						'attendance' => $attendance
					),
				);

				return $this->save ($attendee);
			}
			
			return false;
		}
		
		function removeAttendee($workshop_id = null, $user_id = null)
		{
			if ($workshop_id && $user_id)
			{
				$params = array(
					$this->alias.'.user_id' => $user_id,
					$this->alias.'.workshop_id' => $workshop_id
				);
				
				$attendee = $this->find('first', array('recursive' => -1, 'conditions' => $params));
				$id = $attendee[$this->alias]['id'];
				
				if ($this->delete($id))
					return $id;
				else
					return false;
			}
			
			return false;
		}

                // XXX: Things are getting out of hand
		function _getUserNCredits ($user_id)
		{
		  $credits = ClassRegistry::init('CreditTotal');
		  $credits->id = $user_id;
		  $rec = $credits->find('first');
		  return $rec['CreditTotal']['road_scholar_credits'];
		}

                // XXX: Things are getting out of hand
		function _sendEmail ($email_to, $template, $subject)
		{
		  App::import ('Core', 'Controller');
		  App::import ('Component', 'EmailPlus');
		  $Controller = new Controller();
		  $email_plus = new EmailPlusComponent(null);
		  $email_plus->initialize($Controller);
		  foreach ($email_to as $to) {
		    $email_plus->reset ();
		    $email_plus->sendNoReply($template, $to, $subject);
		  }
		  unset($email_plus);
		  unset($Controller);
		}

		function beforeSave()
		{
		        $this->beforeSaveCredits = $this->_getUserNCredits($this->data['Attendee']['user_id']);
			if ($this->overrideCutoff)
				return true;
			
			return $this->_applyCutoff();
		}

		function afterSave($created)
		{
		  /* Notifications enabled when we are not in 'debug' mode and we want
		   * notifcation emails to come
		   */
		  $notifications_enabled = Configure::read('SiteSettings.credits_notify') &&
		                           !Configure::read('debug');
		  $email_to = Configure::read('SiteSettings.credits_notify_to');

		  if ($notifications_enabled && $created)
		  {
		    $inserted = $this->read();
		    $full_name = $inserted['User']['full_name'];
		    $this->afterSaveCredits = $this->_getUserNCredits($inserted['Attendee']['user_id']);
		    if ($this->beforeSaveCredits < 7 && $this->afterSaveCredits >= 7)
		    {
		      // New Road Scholar
		      // Take debug status into account
		      $template = 'road-scholar';
		      $subject = 'User "' . $full_name . '" is now a Road Scholar';
		      $this->_sendEmail ($email_to, $content, $subject);
		    }
		    else if ($this->beforeSaveCredits < 22 && $this->afterSaveCredits >=  22)
		    {
		      // New Master Road Scholar
		      // Take debug status into account
		      $template = 'master-road-scholar';
		      $subject = 'User "' . $full_name . '" is now a Master Road Scholar';
		      $this->_sendEmail ($email_to, $template, $subject);
		    }
		  }

		  return true;
		}
		
		function beforeDelete($cascade)
		{
			if ($this->overrideCutoff)
				return true;
			
			return $this->_applyCutoff();
		}
		
		// gets users that are not enrolled in the given workshop
		function availableFor($workshop_id = null)
		{
			if (empty($workshop_id))
				return array();
			
			// todo: make this work properly later
			$this->User->contain();
			$users = $this->User->find('all');
			return Set::combine($users, '{n}.User.id', array('{1}, {0} - {2}', '{n}.User.first_name', '{n}.User.last_name', '{n}.User.affiliation'));
		}


		function userAttendanceRecord ($user_id)
		{
		  if (empty ($user_id))
		  {
		    return array ();
		  }

                  $ws = $this->find ('all', 
                      array ('contain' => array ('Workshop' => array ('Detail'),
                                                 'PaymentRecord'), 
                             'conditions' => array ('user_id =' => $user_id),
                             'order' => array ($this->Workshop->alias.'.date' => 'desc')));

                  foreach ($ws as &$w)
                  {
                    $w['Workshop']['total_payments'] = $this->PaymentRecord->totalPaid ($w['Attendee']['id']);                            
                  } 

                  return $ws;
                }


                function nWSAttended ($attendances)
                {
                  $n_shows = 0;
                  foreach ($attendances as $a)
                  {
                    if ($a['Attendee']['attendance'] == 1)
                    {   
                      ++$n_shows;
                    }
                  }

                  return $n_shows;
                }
	}
?>

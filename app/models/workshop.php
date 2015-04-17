<?php
	class Workshop extends AppModel
	{
		var $name = 'Workshop';
		// set this to 2 so we can access categories as well
		var $recursive = 2;
		var $order = array(
			'Workshop.date' => 'desc',
			'Detail.name' => 'asc',
		);
		var $skipDateOverride = false;
		var $actsAs = array('Legacy');
		var $validateAgenda = true;
		
		var $validate = array(
			'location' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please provide a location.',
					'last' => true
				)
			),
			
			'city' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please provide the city.',
					'last' => true
				)
			),
			
			'detail_id' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'Please select a workshop detail.',
					'last' => true
				),
				
				'validDetail' => array(
					'rule' => '_validDetail',
					'message' => 'Invalid workshop detail.',
					'last' => true
				)
			),
			
			'instructor' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter an instructor.',
					'last' => true
				),
			),
			
			'date' => array(
				'full' => array(
					'rule' => 'notEmpty',
					'message' => 'No date!',
					'last' => true
				)
			),
			
			'capacity' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the workshop\'s capacity.',
					'last' => true
				),
				
				'number' => array(
					'rule' => 'numeric',
					'message' => 'Capacity must be a number.'
				),
				
				'positive' => array(
					'rule' => array('comparison', '>', 0),
					'message' => 'Capacity must be positive.',
				),
			),
			
			'public_cost' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the public sector cost of the workshop.',
					'last' => true
				),
				
				'money' => array(
					'rule' => 'money',
					'message' => 'Please enter a valid amount of money.'
				),
				
				'nonnegative' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'Workshop cost cannot be negative.'
				),
			),
			
			'private_cost' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter the private sector cost of the workshop.',
					'last' => true
				),
				
				'money' => array(
					'rule' => 'money',
					'message' => 'Please enter a valid amount of money.'
				),
				
				'nonnegative' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'Workshop cost cannot be negative.'
				),
			),
		);
		
		var $belongsTo = array(
			'Detail' => array(
				'className' => 'WorkshopDetail',
				'foreignKey' => 'detail_id'
			),
			
			'Flyer' => array(
				'className' => 'Flyer',
				'foreignKey' => 'flyer_id'
			)
		);
		
		var $hasMany = array(
			'Attendee' => array(
				'className' => 'Attendee',
				'foreignKey' => 'workshop_id'
			),
			
			'Agenda' => array(
				'className' => 'AgendaItem',
				'foreignKey' => 'workshop_id',
				'order' => 'Agenda.timestamp'
			)
		);
		
		function afterFind($results)
		{
			parent::afterFind($results);
			
			foreach ($results as $key => $value)
			{
				// check to see if we're returning rows
				if (!is_array($value) || !isset($value[$this->alias]))
					continue;
				
				if (isset($results[$key][$this->Attendee->alias]))
					$attendeeCount = sizeof($results[$key][$this->Attendee->alias]);
				else
				{
					$this->Attendee->contain();
					$attendeeCount = $this->Attendee->find('count', array('conditions' => array($this->Attendee->alias.'.workshop_id' => $value[$this->alias]['id'])));
				}
				
				$results[$key][$this->alias]['attendee_count'] = $attendeeCount;
				$results[$key][$this->alias]['is_full'] = ($attendeeCount >= $value[$this->alias]['capacity']);
			}
			
			return $results;
		}
		
		function getUpcoming($user_id = null, $after = null)
		{
			if (empty($user_id))
				return array();
			
			$dateFormat = 'Y-m-d H:i:s';
			if (empty($after))
				$after = date($dateFormat);
			else if (is_string($after))
				$after = date($dateFormat, strtotime($after));
			else if (is_numeric($after))
				$after = date($dateFormat, $after);
			
			// convert the user id into workshop ids via Attendee
			$this->Attendee->contain('Workshop');
			$attendees = $this->Attendee->find('all', array('conditions' => array('Attendee.user_id' => $user_id, 'Workshop.date >' => $after), 'fields' => array('Attendee.workshop_id')));
			$workshopIds = Set::extract('/Attendee/workshop_id', $attendees);
			$this->contain('Detail');
			return $this->find('all', array('conditions' => array('Workshop.id' => $workshopIds), 'order' => 'Workshop.date'));
		}
		
		function useLegacy($legacy, $cascade = true)
		{
			parent::useLegacy($legacy);
			
			// cascade down
			if ($cascade)
				$this->Detail->useLegacy($legacy);
		}
		
		function _validDetail($check)
		{
			$this->Detail->useLegacy(true);
			$result = $this->Detail->find('count', array('recursive' => -1, 'conditions' => array('id' => $check['detail_id'])));
			
			if (!empty($result))
				return true;
			return false;
		}
		
		function isFull($id = null)
		{
			if (empty($id) && empty($this->id))
				return false;
			
			if (empty($id) && !empty($this->id))
				$id = $this->id;
			
			$workshop = $this->find('first', array('conditions' => array($this->alias.'.id' => $id)));
			if (empty($workshop))
				return false;
			
			if (sizeof($workshop['Attendee']) == $workshop[$this->alias]['capacity'])
				return true;
			
			return false;
		}
		
		function beforeValidate()
		{
			if ($this->validateAgenda)
			{
				if (!function_exists('timeIt'))
				{
					function timeIt($a)
					{
						$time = strtotime(sprintf('%s-%s-%s %s:%s %s', $a['year'], $a['month'], $a['day'], $a['hour'], str_pad($a['min'], 2, '0', STR_PAD_LEFT), $a['meridian']));
						return $time;
					}
				}
				
				if (!function_exists('_datesort'))
				{
					function _datesort($a, $b)
					{
						if ($a === $b)
							return 0;
						
						$a = $a['timestamp'];
						$b = $b['timestamp'];
						
						$at = timeIt($a);
						$bt = timeIt($b);
						
						return $at > $bt ? 1 : -1;
					}
				}
				
				// check for agenda
				if (empty($this->data['Agenda']))
				{
					$this->invalidate('description', "Where's the agenda at, y'all?");
					return false;
				}
				
				if (!$this->skipDateOverride)
				{
					usort(&$this->data['Agenda'], '_datesort');
					$this->data[$this->name]['date'] = array();
					$this->data[$this->name]['date'] = date('Y-m-d H:i:s', timeIt($this->data['Agenda'][0]['timestamp']));
				}
			}
			
			return true;
		}
	}
?>
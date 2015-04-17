<?php
	class PaymentRecord extends AppModel {
		var $name = 'PaymentRecord';
		
		var $belongsTo = array(
			'PaymentOption' => array(
				'className' => 'PaymentOption',
				'foreignKey' => 'payment_opt_id'
			),
			
			'Attendee' => array(
				'className' => 'Attendee',
				'foreignKey' => 'attendee_id'
			)
		);
		
		var $validate = array(
			'amount' => array(
				'not_empty' => array(
					'rule' => 'notEmpty',
					'message' => 'Amount is required.',
					'last' => true
				),
				
				'is_money' => array(
					'rule' => 'money',
					'message' => 'Amount must represent a valid amount of money.',
					'last' => true
				),
				
				'non_negative' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'Amount cannot be negative.',
					'last' => true
				),
			),
			
			'attendee_id' => array(
				'not_empty' => array(
					'rule' => 'notEmpty',
					'message' => 'Attendee ID is required. Wait, what?',
				),
				
				'validAttendee' => array(
					'rule' => '_validAttendee',
					'message' => 'Invalid attendee ID.',
					'last' => true
				),
			),
			
			'payment_opt_id' => array(
				'not_empty' => array(
					'rule' => 'notEmpty',
					'message' => 'Payment type is required. Again, what?',
					'last' => true
				),
				
				'validPaymentOption' => array(
					'rule' => '_validPaymentOption',
					'message' => 'Invalid payment option.',
					'last' => true
				)
			),
			
			'paid_on' => array(
				'empty' => array(
					'rule' => 'blank',
					'message' => 'Paid on date must be empty. Knock off your form tampering, yo.',
					'last' => true
				)
			)
		);
		
		function _validPaymentOption($check)
		{
			if (!isset($check['payment_opt_id']))
				return false;
			
			$this->PaymentOption->id = $check['payment_opt_id'];
			return $this->PaymentOption->exists();
		}
		
		function _validAttendee($check)
		{
			if (!isset($check['attendee_id']))
				return false;
			
			$this->Attendee->id = $check['attendee_id'];
			return $this->Attendee->exists();
		}
		
		function beforeSave()
		{
			// tell the database server to use CURRENT_TIMESTAMP
			$this->data['PaymentRecord']['paid_on'] = null;
			
			return true;
		}
		
		function totalPaid($attendee_id = null)
		{
			$rt = 0;
			
			if ($attendee_id)
			{
				$result = $this->find('all',
					array(
						'recursive' => -1,
						'fields' => array('amount'),
						'conditions' => array('PaymentRecord.attendee_id' => $attendee_id)
					)
				);
				
				foreach ($result as $attendee)
					$rt += $attendee['PaymentRecord']['amount'];
			}
			
			return $rt;
		}
	}
?>
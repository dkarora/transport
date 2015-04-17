<?php
	class AttendeesController extends AppController
	{
		var $name = 'Attendees';
		var $helpers = array('Javascript', 'TimeFormatter');
		var $components = array('SessionPlus', 'EmailPlus');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdminsFrom(array('manage_workshop', 'delete', 'create', 'invoice', 'email_invoice'));
			$this->SessionPlus->denyAnonymousUsersFrom('edit');
		}
		
		function email_invoice($attendee_id = null)
		{
			
			if (empty($attendee_id))
				$this->redirect($this->referer());
			
			$this->Attendee->id = $attendee_id;
			$this->Attendee->contain(array('User', 'PaymentRecord', 'PaymentRecord.PaymentOption'));
			$attendee = $this->Attendee->read();
			
			$this->Attendee->Workshop->id = $attendee['Attendee']['workshop_id'];
			$this->Attendee->Workshop->contain('Detail');
			$workshop = $this->Attendee->Workshop->read();
			
			$this->set(compact('attendee', 'workshop'));

//Configure::write('debug',2);debug($attendee);debug($workshop);die;
			
			// generate an invoice
			App::import('Vendor', 'IndividualInvoice');
			$invoicePath = IndividualInvoice::Generate($attendee, $workshop, 'F');
			
			// and send it away
			$this->EmailPlus->reset();
			$this->EmailPlus->attachFile($invoicePath);
			$result = $this->EmailPlus->sendFromDan('individual-invoice', $attendee['User']['email'], 'Invoice for workshop: ' . $workshop['Detail']['name']);
			// delete the file before finishing
//			unlink($invoicePath);
			
				
			
			if ($result)
				$this->SessionPlus->flashSuccessAndRedirect('Email sent!');
			else
				$this->SessionPlus->flashErrorAndRedirect('Email not sent!');
		}
		
		function invoice($attendee_id = null)
		{
			if (empty($attendee_id))
				$this->redirect($this->referer());
			
			$this->Attendee->id = $attendee_id;
			$this->Attendee->contain(array('User', 'PaymentRecord', 'PaymentRecord.PaymentOption'));
			$attendee = $this->Attendee->read();
			
			$this->Attendee->Workshop->id = $attendee['Attendee']['workshop_id'];
			$this->Attendee->Workshop->contain('Detail');
			$workshop = $this->Attendee->Workshop->read();
			
			$this->set(compact('attendee', 'workshop'));
			
			Configure::write('debug', 0);
			$this->layout = 'pdf';
		}
		
		function add($workshop_id = null)
		{
			$this->pageTitle .= 'Edit Attendees';
			
			if (!empty($this->data))
			{
				if ($this->SessionPlus->isUserAdmin())
					$this->Attendee->overrideCutoff = true;
				
				if ($this->Attendee->save($this->data))
				{
					$this->SessionPlus->flashSuccess('Attendee added.');
					
					// read the user
					$attendee = $this->Attendee->read();
					$this->Attendee->User->id = $attendee['Attendee']['user_id'];
					$user = $this->Attendee->User->read();
					$this->Attendee->Workshop->id = $attendee['Attendee']['workshop_id'];
					$this->Attendee->Workshop->contain('Detail');
					$workshop = $this->Attendee->Workshop->read();
					
					// log it
					$format = '%s added %s to workshop %s';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$user['User']['full_name'],
						$workshop['Detail']['name']
					);
					$this->logAction($this->Attendee, 'create', $message);
				}
				else
				{
					$this->SessionPlus->flashError('Attendee not added.');
					debug ($this->Attendee->data);
					debug ($this->Attendee->validationErrors);
					die;
				}
				
				$this->redirect($this->referer());
			}
			
			if (empty($workshop_id))
				$this->redirect($this->referer());
			
			$this->Attendee->Workshop->id = $workshop_id;
			$this->Attendee->Workshop->contain('Detail');
			$workshop = $this->Attendee->Workshop->read();
			
			if (empty($workshop))
			{
				$this->SessionPlus->flashError('Invalid workshop.');
				$this->redirect($this->referer());
			}
			
			// get attendees that are not enrolled
			$this->set('availableAttendees', $this->Attendee->availableFor($workshop_id));
			$this->set('workshop', $workshop);
		}
		
		function delete($attendee_id = null)
		{
			if (empty($attendee_id))
				$this->redirect($this->referer());
			
			// get the data for logging
			$this->Attendee->id = $attendee_id;
			$this->Attendee->contain(array('User'));
			$attendee = $this->Attendee->read();
			
			$this->Attendee->Workshop->id = $attendee['Attendee']['workshop_id'];
			$this->Attendee->Workshop->contain('Detail');
			$workshop = $this->Attendee->Workshop->read();
			
			$this->Attendee->overrideCutoff = true;
			if ($this->Attendee->delete())
			{
				// log it
				$format = '%s unenrolled %s (%d) from %s (%d)';
				$message = sprintf($format,
					$this->SessionPlus->loggableUserName(),
					$attendee['User']['full_name'], $attendee['User']['id'],
					$workshop['Detail']['name']
				);
				$this->logAction($this->Attendee, 'delete', $message, $attendee['Attendee']['id']);
				
				$this->SessionPlus->flashSuccess('Attendee deleted.');
			}
			else
				$this->SessionPlus->flashError('Attendee could not be deleted!');
			
			$this->redirect($this->referer());
		}
		
		function edit()
		{
			if (!empty($this->data))
			{
				foreach ($this->data['Attendee'] as $index => $record)
				{
					$this->Attendee->create();
					// don't validate on these calls.
					// this is a direct result of bad requirements gathering...
					$this->Attendee->overrideCutoff = true;
					$this->Attendee->save($record, false);
					$this->Attendee->User->id = $record['user_id'];
					$targetUser = $this->Attendee->User->find('first', array('recursive' => -1));
					
					$format = '%s updated attendance record (%d) of user %s (%d)';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$this->Attendee->id,
						$targetUser[$this->Attendee->User->alias]['full_name'], $targetUser[$this->Attendee->User->alias]['id']
					);
					$this->logAction($this->Attendee, 'update', $message);
				}
				
				$this->SessionPlus->flashSuccess('Attendance records updated successfully.');
				$this->redirect($this->referer(), null, true);
			}
		}
		
		function manage_workshop($workshop_id = null)
		{
			$this->pageTitle .= 'Manage Attendees';
			
			if (!empty($this->data))
			{
				$this->Attendee->overrideCutoff = true;
				
				// try to save payment records
				$this->Attendee->PaymentRecord->saveAll($this->data['PaymentRecord'], array('atomic' => false));
				
				// save all attendee records
				if ($this->Attendee->saveAll($this->data['Attendee']))
					$this->SessionPlus->flashSuccess('Attendees saved.');
				else
					$this->SessionPlus->flashError('Attendees not saved!');
				
				$this->redirect($this->referer());
			}
			
			if (empty($workshop_id))
			{
				$this->SessionPlus->flashError('Invalid workshop ID.');
				$this->redirect($this->referer());
			}
			
			// get the workshop we want
			$this->Attendee->Workshop->id = $workshop_id;
			$this->Attendee->Workshop->contain('Detail');
			$workshop = $this->Attendee->Workshop->find('first');
			
			// get the attendees we want
			$this->Attendee->contain('User');
			$attendees = $this->Attendee->find('all', array('order' => 'User.last_name', 'conditions' => array('Attendee.workshop_id' => $workshop_id)));

			// get payment for users
			$paid = array ();
			foreach ($attendees as $attendee)
			{
			  $paid[$attendee['Attendee']['id']] = 
			       $this->Attendee->PaymentRecord->totalPaid($attendee['Attendee']['id']);
			}
			
			// get the payment options
			$paymentOptions = $this->Attendee->PaymentRecord->PaymentOption->find('list');
			
			// get the id of the Check option
			$defaultPaymentOptionId = $this->Attendee->PaymentRecord->PaymentOption->field('PaymentOption.id', array('PaymentOption.value' => 'Check'));
			
			$this->set(compact('attendees', 'workshop', 'paid', 'paymentOptions', 'defaultPaymentOptionId'));
		}
	}
?>

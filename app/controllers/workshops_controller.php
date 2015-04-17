<?php
	class WorkshopsController extends AppController
	{
		var $name = 'Workshops';
		var $uses = array('Workshop', 'Attendee', 'WorkshopCategory', 'PaymentRecord');
		var $components = array('ErrorListFormatter', 'SessionPlus', 'Link', 'RequestHandler', 'Bitly', 'EmailPlus');
		var $helpers = array('Paginator', 'TimeFormatter', 'Javascript', 'BbCode', 'Link', 'ProgressBar');
		var $paginate = array(
			'Workshop' => array(
				'limit' => 25,
			),
		);
		
		var $pageTitle = 'Baystate Roads &rsaquo; Workshops &rsaquo; ';
		
		function ups()
		{
			Configure::write('debug', 2);
			
			debug ($this->Workshop->Flyer->query('SELECT id from flyers order by id desc limit 1'));
		}
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$adminActions = array('print_certificates', 'add', 'edit');
			$userActions = array('add_attendee', 'remove_attendee', 'email_invoice');
			
			$this->SessionPlus->denyAnonymousUsersFrom($userActions);
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
			
			if ($this->params['action'] == 'add')
				$this->Security->enabled = false;
		}
		
		function edit($workshop_id = null)
		{
			if (!empty($this->data) && empty($workshop_id))
				$workshop_id = $this->data['Workshop']['id'];
			else if (empty($workshop_id))
				$this->redirect($this->referer());
			
			$this->Workshop->id = $workshop_id;
			$this->Workshop->contain(array('Detail', 'Flyer', 'Agenda'));
			$workshop = $this->Workshop->read();
			
			if (!empty($this->data))
			{
				if (empty($workshop_id))
					$workshop_id = $this->data['Workshop']['id'];
				
				$this->Workshop->validateAgenda = false;
				if ($this->Workshop->save($this->data))
					$this->SessionPlus->flashSuccess('Workshop saved.');
				else
					$this->SessionPlus->flashError('Workshop not saved!');
				$workshop['Workshop'] = array_merge($workshop['Workshop'], $this->data['Workshop']);
			}
			$this->data = $workshop;
			
			if (empty($workshop))
				$this->SessionPlus->flashErrorAndRedirect('Bad workshop ID.');
			
			$this->Workshop->Flyer->contain();
			$flyerList = $this->Workshop->Flyer->find('list');
			// give them a blank option
			$flyerList = array('(No Flyer)') + $flyerList;
			
			$this->pageTitle .= 'Editing ' . $workshop['Detail']['name'];
			$this->set('workshop', $workshop);
			$this->set('flyerList', $flyerList);
		}
		
		function get_attendees($workshopId)
		{
			if ($this->RequestHandler->isAjax())
			{
				$this->disableCache();
				$this->layout = 'ajax';
			}
			else
			{
				$this->pageTitle .= 'Attendee List';
			}
			
			$this->set('is_ajax', $this->RequestHandler->isAjax());
			$this->set('attendees', $this->Workshop->Attendee->find('all', array('order' => 'User.last_name', 'conditions' => array('workshop_id' => $workshopId))));
		}
		
		function _timeToMinutes($time)
		{
			// if we're not in 24 hour time add 12 hours worth of minutes if needed
			$rt = $time['hour'] * 60 + $time['min'];
			if (!empty($time['meridian']) && $time['meridian'] == 'pm')
				$rt += 12 * 60;
			
			return $rt;
		}
		
		function view($id = null, $slug = null)
		{
			// if we didn't get an id then we can't continue
			if (empty($id))
				$this->cakeError('error404');
			
			$idHashed = false;
			$this->set('workshopLinkId', $id);
			
			// check for hashed id, unlisted flag
			if (!is_numeric($id))
			{
				$id = $this->Link->_alphaID($id, true);
				$idHashed = true;
			}
			
			// if the workshop 404'd
			$workshop = $this->Workshop->find('first', array('conditions' => array('Workshop.id' => $id), 'recursive' => 1));
			if (empty($workshop))
				$this->cakeError('error404');
			
			// if we get a hashed id and a public workshop, don't give them any hints
			if ($idHashed && !$workshop['Workshop']['unlisted'])
				$this->cakeError('error404');
			// if we get an unhashed id and an unlisted workshop, don't allow it
			else if (!$idHashed && $workshop['Workshop']['unlisted'])
				$this->cakeError('error404');
			
			// if we didn't get a slug redirect
			if (empty($slug))
				$this->redirect($this->Link->viewWorkshop($workshop));
			
			if ($this->SessionPlus->isUserLoggedIn())
			{
				// find all the workshops the user is enrolled in
				$enrolled = array();
				$raw = $this->Attendee->find('all', array('fields' => 'workshop_id', 'conditions' => array('Attendee.user_id' => $this->Session->read('User.id')), 'recursive' => -1));
				
				foreach($raw as $key => $value)
					$enrolled[] = $value['Attendee']['workshop_id'];
				$this->set('enrolled', $enrolled);
			}
			
			// determine if the workshop is full
			$nAttendees = $workshop['Workshop']['attendee_count'];
			$capacity = $workshop['Workshop']['capacity'];
			
			$this->pageTitle .= $workshop['Detail']['name'];
			
			// get flyer thumbnail if any
			$flyerThumb = null;
			if (!empty($workshop['Workshop']['flyer_id']))
				$flyerThumb = Router::url('/wsflyers/thumbnails/' . $workshop['Workshop']['flyer_id'] . '.png', true);
			
			$this->set('workshopEditable', $this->SessionPlus->isUserAdmin());
			$this->set('flyerThumbnailUrl', $flyerThumb);
			$this->set('workshop', $workshop);
			$this->set('workshopFull', $nAttendees >= $capacity);
			$this->set('nAttendees', $nAttendees);
			$this->set('capacity', $capacity);
		}
		
		function print_certificates($workshop_id = null, $user_id = null)
		{
			// id check
			if (empty($workshop_id))
			{
				$this->SessionPlus->flashError('No workshop ID.');
				$this->redirect($this->referer());
			}
			
			// get the workshop..
			$this->Workshop->id = $workshop_id;
			$this->Workshop->unbindModel(array('belongsTo' => array('Flyer')));
			$workshop = $this->Workshop->find('first', array('recursive' => 0));
			$this->set('workshop', $workshop);
			
			// get the attendees..
			$conditions = array('Attendee.workshop_id' => $workshop_id);
			if (!empty($user_id))
				$conditions = array_merge($conditions, array('Attendee.user_id' => $user_id));
			
			$this->Attendee->unbindModel(array('belongsTo' => array('Workshop'), 'hasMany' => array('PaymentRecord')));
			$attendees = $this->Attendee->find('all', array('conditions' => $conditions, 'order' => $this->Attendee->User->alias.'.last_name ASC'));
			$this->set('attendees', $attendees);
			
			// set to production mode or tcpdf will panic
			Configure::write('debug', 0);
			$this->layout = 'pdf';
		}
		
		function add()
		{
			$this->ErrorListFormatter->deleteOldData();
			
			if (!empty($this->data))
			{
				if ($this->Workshop->saveAll($this->data))
				{
					$ws = $this->Workshop->read();
					$format = '%s scheduled%s workshop instance %s - %s (%d)';
					$publicStatus = $ws['Workshop']['unlisted'] ? ' unlisted' : '';
					$message = sprintf($format,
						$this->SessionPlus->loggableUserName(),
						$publicStatus,
						$ws['Detail']['name'], $ws['Workshop']['date'], $ws['Workshop']['id']
					);
					$this->logAction($this->Workshop, 'create', $message);
					
					$message = 'Workshop successfully scheduled.';
					if ($this->data['Workshop']['unlisted'])
						$message .= ' Your workshop\'s URL is:<br />' . Router::url($this->Link->viewWorkshop($this->Workshop->data), true);
					
					// tweet it too
					if (!empty($this->data['Social']['tweet']) && $this->data['Social']['tweet'] || true)
					{
						$workshop = $this->Workshop->read();
						
						// make a bit.ly url
						$url = $this->Bitly->shorten(Router::url($this->Link->viewWorkshop($workshop), true));
						
						// create the tweet
						$status = 'New Workshop: ' . $workshop['Detail']['name'];
						if (strlen($status) >= 140 - (strlen($url) + 1))
							$status = substr($status, 0, 140 - (strlen($url) + 4)) . '...';
						$status .= ' ' . $url;
						
						$access = $this->OauthAccessToken->getToken('Twitter');
						$this->OauthConsumer->post('Twitter', $access['OauthAccessToken']['key'], $access['OauthAccessToken']['secret'], 'http://api.twitter.com/1/statuses/update.json', array('status' => $status));
					}
					
					$this->SessionPlus->flashSuccess($message);
				}
				else
				{
					$this->ErrorListFormatter->setOldData($this->data);
					debug ($this->data);
					
					$this->SessionPlus->flashError('Workshop was not scheduled. Please correct the errors below.');
				}
			}
			
			$this->redirect(array('controller' => 'admin', 'action' => 'workshops'), null, true);
		}
		
		
		function add_attendee($workshop_id = null, $user_id = null)
		{
			$proxy = false;
			if (!empty($user_id))
				$proxy = true;
			
			// check if the user is allowed to enroll this member
			if (!empty($user_id))
			{
				$current_user_id = $this->Session->read('User.id');
				
				if (!$this->Attendee->User->canAdminister($current_user_id, $user_id))
				{
					$this->SessionPlus->flashError('You don\'t have the privileges to do that.');
					$this->redirect($this->referer());
				}
			}
			
			// by default use the current user's id
			if (empty($user_id))
				$user_id = $this->Session->read('User.id');
			$targetUser = $this->Attendee->User->find('first', array('conditions' => array('User.id' => $user_id), 'recursive' => -1));
			$targetWorkshop = $this->Workshop->find('first', array('conditions' => array('Workshop.id' => $workshop_id), 'recursive' => 0));
			
			if ($this->Attendee->enrollAttendee($workshop_id, $user_id))
			{
				// log it
				$format = '%s (%d) added attendance record (%d): %s (%d) at workshop %s (%d)';
				$message = sprintf($format,
					$this->Session->read('User.full_name'), $this->Session->read('User.id'),
					$this->Attendee->id,
					$targetUser['User']['full_name'], $this->SessionPlus->userIdCoalesce($targetUser['User']['id']),
					$targetWorkshop['Detail']['name'], $targetWorkshop['Workshop']['id']
				);
				$this->logAction($this->Attendee, 'create', $message);
				
				if ($proxy)
					$this->SessionPlus->flashSuccess('User enrolled successfully.');
				else
					$this->SessionPlus->flashSuccess('You have been successfully enrolled.');
			}
			else
			{
				if ($proxy)
					$this->SessionPlus->flashError('User was not enrolled! Please try again.');
				else
					$this->SessionPlus->flashError('You could not be enrolled in the workshop! Please try again.');
			}
			
			$this->redirect($this->referer());
		}
		
		function remove_attendee($workshop_id = null, $user_id = null)
		{
			$proxyRemove = false;
			if (!empty($user_id))
				$proxyRemove = true;
			
			// check if the user is allowed to administer this member
			if (!empty($user_id))
			{
				$current_user_id = $this->Session->read('User.id');
				
				if (!$this->Attendee->User->canAdminister($current_user_id, $user_id))
				{
					$this->SessionPlus->flashError('You don\'t have the privileges to do that.');
					$this->redirect($this->referer());
				}
			}
			
			// by default use the current user's id
			if (empty($user_id))
				$user_id = $this->Session->read('User.id');
			$targetUser = $this->Attendee->User->find('first', array('conditions' => array('User.id' => $user_id), 'recursive' => -1));
			$targetWorkshop = $this->Workshop->find('first', array('conditions' => array('Workshop.id' => $workshop_id), 'recursive' => 0));
			if ($this->SessionPlus->isUserAdmin())
				$this->Attendee->overrideCutoff = true;
			$removedId = $this->Attendee->removeAttendee($workshop_id, $user_id);
			
			if (!empty($removedId))
			{
				// log it
				$format = '%s (%d) deleted attendance record (%d): %s (%d) at workshop %s (%d)';
				$message = sprintf($format,
					$this->Session->read('User.full_name'), $this->Session->read('User.id'),
					$removedId,
					$targetUser['User']['full_name'], $this->SessionPlus->userIdCoalesce($targetUser['User']['id']),
					$targetWorkshop['Detail']['name'], $targetWorkshop['Workshop']['id']
				);
				$this->logAction($this->Attendee, 'delete', $message, $removedId);
				
				if ($proxyRemove)
					$this->SessionPlus->flashSuccess('User has been removed from the workshop.');
				else
					$this->SessionPlus->flashSuccess('You have been removed from the workshop.');
			}
			else
			{
				if ($proxyRemove)
					$this->SessionPlus->flashError('User could not be removed from the workshop. Please try again.');
				else
					$this->SessionPlus->flashError('You could not be removed from the workshop. Please try again.');
			}
			
			$this->redirect($this->referer());
		}
		
		function index()
		{
			$this->pageTitle .= 'Workshops';
			$conditions = array('unlisted' => 0);
			$workshops = $this->paginate('Workshop', $conditions);
			
			$this->set('workshops', $workshops);
			// find all the workshops the user is enrolled in
			$enrolled = array();
			
			if ($this->SessionPlus->isUserLoggedIn())
			{
				$raw = $this->Attendee->find('all', array('conditions' => array('user_id' => $this->Session->read('User.id')), 'recursive' => -1));
				
				foreach($raw as $key => $value)
					$enrolled[] = $value['Attendee']['workshop_id'];
			}
			
			$registered = array();
			foreach($workshops as $ws)
			{
				$registered[$ws['Workshop']['id']] = 
					$this->Attendee->find('count', 
						array('conditions' => array('Attendee.workshop_id =' => $ws['Workshop']['id']), 'recursive' => -1));
			}
			
			$this->set('registered', $registered);
			$this->set('enrolled', $enrolled);
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
		
		
	}
?>

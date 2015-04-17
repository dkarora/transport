<?php
	class AdminController extends AppController
	{
		var $uses = array('User', 'Group', 'WorkshopCategory', 'WorkshopDetail', 'Workshop', 'Attendee', 'PaymentRecord', 'PaymentOption', 'NewsPost', 'Video', 'VideoCategory', 'Publication', 'PublicationCategory', 'OauthAccessToken', 'LegacyRecord', 'IntegrationRequest', 'CreditTotal');
		var $name = 'Admin';
		var $helpers = array('Paginator', 'Javascript', 'SlugGenerator', 'TimeFormatter', 'PaginatorTable', 'Link');
		var $components = array('ErrorListFormatter', 'SessionPlus', 'OauthConsumer', 'Bitly', 'Password', 'EmailPlus');
		var $paginate = array(
			'limit' => 20,
			'recursive' => 3
		);
		
		var $pageTitle = 'Baystate Roads &rsaquo; ';
		

		function search_users ()
		{
		  $part_lastname = '';

		  if (array_key_exists ('part_lastname', $this->params['url']))
		  {
		    $part_lastname = $this->params['url']['part_lastname'];
		  }

		  $this->pageTitle .= 'Search Users';

		  // paginate through users
		  $this->paginate['recursive'] = -1;
		  $this->User->asset_summary_format = 'string';

		  // set a filter if needed
		  $conditions = array();
		  if ($part_lastname)
		  {
		    $conditions = array_merge($conditions, array($this->User->alias.'.last_name LIKE' => $part_lastname . '%'));
		  }

		  $this->paginate[$this->User->alias]['conditions'] = $conditions;
		  $users = $this->paginate('User');

		  $this->set('part_lastname', $part_lastname);
		  $this->set('users', $users);
		}
		

		function single_workshop_certificate($workshop_id)
		{
			$this->pageTitle .= 'Print Single Workshop Certificate';
			
			// get the workshop..
			$this->Workshop->id = $workshop_id;
			$this->Workshop->unbindModel(array('belongsTo' => array('Flyer')));
			$workshop = $this->Workshop->find('first', array('recursive' => 0));
			$this->set('workshop', $workshop);
			
			// get the attendees..
			$conditions = array('Attendee.workshop_id' => $workshop_id);
			$this->Attendee->unbindModel(array('belongsTo' => array('Workshop'), 'hasMany' => array('PaymentRecord')));
			$attendees = $this->Attendee->find('all', array('conditions' => $conditions));
			$this->set('attendees', $attendees);
		}
		
		function fill_integration($req_id = null)
		{
			if (!$req_id)
			{
				$this->SessionPlus->flashError('No request id!');
				$this->redirect($this->referer());
			}
			
			$req = $this->IntegrationRequest->getRequest($req_id);
			
			if (empty($req))
			{
				$this->SessionPlus->flashError('Bad request id.');
				$this->redirect($this->referer());
			}
			
			$req[$this->IntegrationRequest->alias]['filled'] = 1;
			if ($this->IntegrationRequest->save($req))
			{
				$format = '%s (%d) filled integration request (%d) for user %s (%d)';
				$message = sprintf($format,
					$this->Session->read('User.full_name'), $this->Session->read('User.id'),
					$req['IntegrationRequest']['id'],
					$req['User']['full_name'], $req['User']['id']
				);
				$this->logAction($this->IntegrationRequest, 'update', $message);
				
				$this->SessionPlus->flashSuccess('Request filled.');
			}
			else
				$this->SessionPlus->flashError('Request could not filled!');
			
			$this->redirect($this->referer());
		}
		
		function browse_legacy($req_id = null, $browse_from_letter = null)
		{
			$this->pageTitle .= 'Browse Legacy Records';
			
			// quick sanity checks: check id, check request
			if (!$req_id)
			{
				$this->SessionPlus->flashError('No request id!');
				$this->redirect($this->referer());
			}
			
			$req = $this->IntegrationRequest->getRequest($req_id);
			
			if (empty($req))
			{
				$this->SessionPlus->flashError('Bad request id.');
				$this->redirect($this->referer());
			}
			
			// paginate through the records... cause there's a lot!
			$conditions = array('LegacyRecord.filled' => 0);
			if ($browse_from_letter)
			{
				if ($browse_from_letter != 'misc')
					$conditions = array_merge($conditions, array('LegacyRecord.last_name LIKE' => $browse_from_letter . '%'));
				else
					$conditions = array_merge($conditions, array('LegacyRecord.last_name NOT REGEXP' => '^[a-z]'));
				
				$this->LegacyRecord->order = 'last_name';
			}
			
			$lrecs = $this->paginate('LegacyRecord', $conditions);
			
			$this->set('request', $req);
			$this->set('records', $lrecs);
			$this->set('from_letter', $browse_from_letter);
		}
		
		function new_user()
		{
			$this->pageTitle .= 'Create New User';
			$this->set('states', $this->User->getStates());
			
			if (!empty($this->data))
			{
				//debug ($this->data);
				
				// check the generate password thingy
				if (!empty($this->data['User']['generate_password']))
				{
					$password = $this->Password->generate();
					
					$this->data['User']['password'] = $password;
					$this->data['User']['password_confirm'] = $password;
				}
				else
					$this->data['User']['password_confirm'] = $this->data['User']['password'];
				
				if ($this->User->save($this->data))
				{
					$newUser = $this->User->read();
					
					$format = '%s (%d) created user %s (%d)';
					$message = sprintf($format,
						$this->Session->read('User.full_name'), $this->Session->read('User.id'),
						$newUser['User']['full_name'], $newUser['User']['id']
					);
					$this->logAction($this->User, 'create', $message);
					
					$this->SessionPlus->flashSuccess('User created.');
					$this->set('created_user', $this->data['User']);
					
					$this->data = array();
				}
				else
					$this->SessionPlus->flashError('User not created!');
			}
		}
		
		function legacy_finish()
		{
			$this->Workshop->useLegacy(true);
			$this->WorkshopDetail->useLegacy(true);
			$this->WorkshopDetail->Category->useLegacy(true);
			
			// light sanity check: no data, mo' problems
			if (empty($this->data['Admin']['req_id']) || empty($this->data['Records']))
			{
				$this->SessionPlus->flashError('OH GEEZ. That wasn\'t supposed to happen!');
				$this->redirect('/admin/');
			}
			
			debug ($this->data);
			
			$this->pageTitle .= 'Legacy Integration';
			
			$this->set('steps', array('Choose Records', 'Integrate Records', 'Confirm', 'Complete'));
			$this->set('step', 4);
			
			// get the request
			$request = $this->IntegrationRequest->getRequest($this->data['Admin']['req_id']);
			$this->set('request', $request);
			
			// step through all the records and process them
			foreach ($this->data['Records'] as $r)
			{
				// get the record
				$this->LegacyRecord->id = $r['record_id'];
				$record = $this->LegacyRecord->find('first');
				
				// if we didn't get a record then there's a problem we can't fix here
				if (empty($record))
					continue;
				
				debug ($record);
				debug ($r);
				
				if ($r['insert_type'] == 'new')
				{
					// build a workshop detail + workshop array skeleton
					$w = array();
					$w['WorkshopDetail'] = array(
						'name' => empty($r['new_workshop_name']) ? (empty($record['LegacyRecord']['workshop_name']) ? '(Unknown Workshop)' : $record['LegacyRecord']['workshop_name']) : ($r['new_workshop_name']),
						'credits' => empty($record['LegacyRecord']['road_scholar_credits']) ? 0 : $record['LegacyRecord']['road_scholar_credits'],
						'description' => 'Created with legacy record importer',
						'ceu_credits' => empty($record['LegacyRecord']['ceu_credits']) ? 0 : $record['LegacyRecord']['ceu_credits'],
						'legacy' => 1
					);
					
					$w['Category'] = array(
						'name' => empty($record['LegacyRecord']['workshop_category_name']) ? '(Unknown Workshop Category)' : $record['LegacyRecord']['workshop_category_name'],
						'legacy' => 1
					);
					
					debug ($record);
					
					$this->WorkshopDetail->create();
					$this->WorkshopDetail->saveAll($w);
					
					debug ($this->WorkshopDetail->validationErrors);
					debug ($this->WorkshopDetail->Category->validationErrors);
					
					// save this seperately because there's no link between workshop details and workshops
					// (and because you can't saveAll down past depth 1)
					$ws = array(
						'Workshop' => array(
							'capacity' => 999999,
							'city' => (empty($record['LegacyRecord']['workshop_city']) ? '(Unknown City)' : $record['LegacyRecord']['workshop_city']),
							'location' => (empty($record['LegacyRecord']['workshop_location']) ? '(Unknown Location)' : $record['LegacyRecord']['workshop_location']),
							'instructor' => '(Unknown Instructor)',
							'date' => empty($record['LegacyRecord']['workshop_date']) ? strftime('%F %T') : $record['LegacyRecord']['workshop_date'],
							'detail_id' => $this->WorkshopDetail->getLastInsertID(),
							'legacy' => 1,
							'public_cost' => empty($record['LegacyRecord']['workshop_cost']) ? 0 : $record['LegacyRecord']['workshop_cost'],
						),
						
						'Attendee' => array(
							array(
								'user_id' => $request['User']['id'],
								'attendance' => empty($record['LegacyRecord']['attended']) ? 0 : $record['LegacyRecord']['attended']
							)
						),
						
						'Agenda' => array(
							array(
								'timestamp' => empty($record['LegacyRecord']['workshop_date']) ? strftime('%F %T') : $record['LegacyRecord']['workshop_date'],
								'description' => 'Generated by legacy record importer'
							)
						)
					);
					
					$this->Workshop->create();
					$this->Workshop->skipDateOverride = true;
					$this->Workshop->Attendee->overrideCutoff = true;
					$this->Workshop->saveAll($ws);
					
					debug ($this->Workshop->data);
					debug ($this->Workshop->Attendee->data);
					debug ($this->Workshop->validationErrors);
					debug ($this->Workshop->Attendee->validationErrors);
				}
				else if ($r['insert_type'] == 'existing')
				{
					$ws = array(
						'Workshop' => array(
							'city' => (empty($record['LegacyRecord']['workshop_city']) ? '(Unknown City)' : $record['LegacyRecord']['workshop_city']),
							'location' => (empty($record['LegacyRecord']['workshop_location']) ? '(Unknown Location)' : $record['LegacyRecord']['workshop_location']),
							'instructor' => '(Unknown Instructor)',
							'date' => empty($record['LegacyRecord']['workshop_date']) ? strftime('%F %T') : $record['LegacyRecord']['workshop_date'],
							'detail_id' => $r['existing_workshop_detail'],
							'legacy' => 1
						),
						
						'Attendee' => array(
							array(
								'user_id' => $request['User']['id'],
								'attendance' => empty($record['LegacyRecord']['attended']) ? 0 : $record['LegacyRecord']['attended']
							)
						),
						
						'Agenda' => array(
							array(
								'timestamp' => empty($record['LegacyRecord']['workshop_date']) ? strftime('%F %T') : $record['LegacyRecord']['workshop_date'],
								'description' => 'Generated by legacy record importer'
							)
						)
					);
					
					$this->Workshop->create();
					$this->Workshop->skipDateOverride = true;
					$this->Workshop->Attendee->overrideCutoff = true;
					$this->Workshop->saveAll($ws);
				}
				
				// and (soft) delete the record on the way out
				$record['LegacyRecord']['filled'] = 1;
				$this->LegacyRecord->save($record);
			}
			
			// finally, set the request as filled
			$request['IntegrationRequest']['filled'] = 1;
			$this->IntegrationRequest->save($request);
			
			$format = '%s (%d) filled integration request (%d) for user %s (%d)';
			$message = sprintf($format,
				$this->Session->read('User.full_name'), $this->Session->read('User.id'),
				$request['IntegrationRequest']['id'],
				$request['User']['full_name'], $request['User']['id']
			);
			$this->logAction($this->IntegrationRequest, 'update', $message);
		}
		
		function legacy_integrate()
		{
			$this->Workshop->useLegacy(true);
			$this->WorkshopDetail->useLegacy(true);
			$this->set('steps', array('Choose Records', 'Integrate Records', 'Confirm', 'Complete'));
			$step = 2;
			
			if (!empty($this->data['Records']))
			{
				// check the data
				$ok = true;
				
				foreach ($this->data['Records'] as $r)
				{
					if (!empty($r['ignore']))
						continue;
					
					if (empty($r['selection_type']) || ($r['selection_type'] == 'new' && empty($r['new_workshop_name'])))
					{
						$ok = false;
						break;
					}
				}
				
				if ($ok)
				{
					$step = 3;
					
					// strip out ignored records
					$recs = array();
					foreach ($this->data['Records'] as $k => $r)
						if ($r['ignore'] == 0)
							$recs[] = $k;
				}
				else
					$this->SessionPlus->flashError('Uh oh! Something went wrong. Make sure that all the forms are filled out correctly.');
			}
			
			$this->pageTitle .= 'Legacy Integration';
			
			$req_id = $this->data['Admin']['req_id'];
			
			if ($step == 2)
			{
				// if we're coming from legacy_associate
				if (!empty($this->data['Admin']['Records']))
				{
					// step through the data and filter out the unchecked records
					$recs = array();
					foreach ($this->data['Admin']['Records'] as $k => $v)
					{
						if ($v['select'] == 1)
							$recs[] = $k;
					}
				}
				// if we're coming from legacy_integrate (here)
				else if (empty($this->data['Admin']['from']))
				{
					$recs = array_keys($this->data['Records']);
				}
				
				// check if we just browsed some records and have records to add
				if (!empty($this->data['BrowsedRecords']))
				{
					$selected = array();
					
					// get all the records we selected
					foreach ($this->data['BrowsedRecords'] as $r)
					{
						if (!empty($r['select']))
							$recs[] = $r['select'];
					}
				}
			}
			
			// get the records and workshops to match
			$records = $this->LegacyRecord->find('all', array('conditions' => array('LegacyRecord.id' => $recs, 'LegacyRecord.filled' => 0)));
			$this->set('records', $records);
			
			if ($step == 2)
			{
				$this->Workshop->contain('Detail');
				$workshops = $this->Workshop->find('all');
				$workshopsMapped = $workshops;
				
				// get just the years
				for ($i = 0; $i < sizeof($workshopsMapped); $i++)
				{
					$date = $workshopsMapped[$i]['Workshop']['date'];
					$date = date('Y', strtotime($date));
					$workshopsMapped[$i]['Workshop']['date'] = $date;
				}
				
				$workshops = Set::combine($workshopsMapped, '{n}.Workshop.id', array('[{0}] {1}', '{n}.Workshop.date', '{n}.Detail.name'));
				debug ($workshops);
				
				$this->set('workshops', $workshops);
			}
			
			if ($step == 3)
			{
				// get the associated workshops for those that call for it
				foreach ($this->data['Records'] as $k => $v)
				{
					if ($v['selection_type'] == 'existing')
					{
						$this->WorkshopDetail->id = $v['existing_workshop_detail'];
						$workshop = $this->WorkshopDetail->find('first');
						$this->data['Records'][$k]['Workshop'] = $workshop;
					}
				}
				
				$this->set('reqs', $this->data['Records']);
			}
			
			$this->set('step', $step);
			$this->set('request', $this->IntegrationRequest->getRequest($req_id));
		}
		
		function legacy_associate($req_id = null)
		{
			if (!$req_id)
			{
				$this->SessionPlus->flashError('No request ID specified!');
				$this->redirect($this->referer());
			}
			
			$this->pageTitle .= 'Legacy Associations';
			
			// get the request
			$request = $this->IntegrationRequest->getRequest($req_id, true);
			
			$this->set('request', $request);
			$this->set('steps', array('Choose Records', 'Integrate Records', 'Confirm', 'Complete'));
			$this->set('step', 1);
			$this->set('req_id', $req_id);
		}
		
		function legacy_records()
		{
			$this->pageTitle .= 'Legacy Records';
			
			// get requests
			$requests = $this->IntegrationRequest->getPending();
			$this->set('requests', $requests);
			
			// get filled requests
			$filled = $this->IntegrationRequest->getFilled();
			$this->set('filled', $filled);
		}
		
		function twitter_auth()
		{
			$requestToken = $this->OauthConsumer->getRequestToken('Twitter', 'http://twitter.com/oauth/request_token', Router::url('/admin/twitter_callback', true));
			$this->Session->write('twitter_request_token', $requestToken);
			$this->redirect('http://twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
		}
		
		function twitter_callback()
		{
			$requestToken = $this->Session->read('twitter_request_token');
			$accessToken = $this->OauthConsumer->getAccessToken('Twitter', 'http://twitter.com/oauth/access_token', $requestToken);
			
			// check if this is an update or a create for the logs
			$isCreate = !$this->OauthAccessToken->tokenExists('Twitter');
			$actionType = $isCreate ? 'create' : 'update';
			
			// save access token
			if ($this->OauthAccessToken->setToken('Twitter', $accessToken))
			{
				$format = '';
				if ($isCreate)
					$format = '%s associated Twitter account';
				else
					$format = '%s updated Twitter account association';
				$message = sprintf($format, $this->SessionPlus->loggableUserName());
				$this->logAction($this->OauthAccessToken, $actionType, $message);
				
				$this->SessionPlus->flashSuccess('Twitter account successfully associated.');
			}
			else
				$this->SessionPlus->flashError('Twitter account was not associated!');
			
			// redirect to social connections
			$this->redirect('/admin/social/');
		}
		
		function twitter_deauth()
		{
			$deletedId = $this->OauthAccessToken->deleteToken('Twitter');
			if ($deletedId)
			{
				$format = '%s deleted Twitter account association';
				$message = sprintf($format, $this->SessionPlus->loggableUserName());
				$this->logAction($this->OauthAccessToken, 'delete', $message, $deletedId);
				
				$this->SessionPlus->flashSuccess('Twitter access token removed.');
			}
			else
				$this->SessionPlus->flashError('Twitter access token could not be removed.');
			
			$this->redirect($this->referer());
		}
		
		function social()
		{
			$this->pageTitle .= 'Social Connections';
			
			// get twitter connections
			$twitterUsername = '';
			$twitterToken = $this->OauthAccessToken->getToken('Twitter');
			if (!empty($twitterToken))
			{
				$key = $twitterToken['OauthAccessToken']['key'];
				$secret = $twitterToken['OauthAccessToken']['secret'];
				
				$json = json_decode($this->OauthConsumer->get('Twitter', $key, $secret, 'http://api.twitter.com/1/account/verify_credentials.json'));
				$twitterUsername = $json->screen_name;
			}
			
			$this->set('twitterUsername', $twitterUsername);
		}
		
		function enroll_attendee()
		{
			if (!empty($this->data))
			{
				$user_id = $this->data['Attendee']['user_id'];
				$workshop_id = $this->data['Attendee']['workshop_id'];
				$attended = $this->data['Attendee']['attendance'];
				
				$this->Attendee->overrideCutoff = true;
				if ($this->Attendee->enrollAttendee($workshop_id, $user_id, $attended))
				{
					$this->SessionPlus->flashSuccess('Attendee successfully enrolled.');
				}
				else
				{
					$this->SessionPlus->flashError('Attendee was not enrolled!');
					
					$this->ErrorListFormatter->setOldData($this->data);
				}
			}
			
			$this->redirect($this->referer());
		}
		
		function get_agenda_item()
		{
			$this->layout = 'ajax';
			$this->set('agendaIndex', $_POST['agendaIndex']);
		}
		
		function announcements()
		{
			$this->pageTitle .= 'Announcements';
		}
		
		function news_posts()
		{
			$this->pageTitle .= 'News Posts';
			
			// check for twitter links
			if ($this->OauthAccessToken->tokenExists('Twitter'))
				$this->set('linkedToTwitter', true);
			else
				$this->set('linkedToTwitter', false);
		}
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
			
			$this->pageTitle .= 'Admin &rsaquo; ';
			$this->ErrorListFormatter->restoreErrorMessages();
			
			// disable security so punching in long agendas won't time out
			if ($this->params['action'] == 'workshops')
				$this->Security->enabled = false;
			//Configure::write('debug', 2);
		}
		
		function index()
		{
			$this->pageTitle .= 'Index';
			
			// check for any pending registrations
			$reg = $this->User->getPendingRegistrations();
			$this->set('pending', $reg);
			
			// check for pending integration requests
			$ireq = $this->IntegrationRequest->countPending();
			$this->set('integrationRequests', $ireq);
		}
		
		function workshops()
		{
			$this->pageTitle .= 'Workshops';
			
			$cats = $this->WorkshopCategory->find('all', array('recursive' => -1));
			$this->set('addworkshop_categories', Set::combine(Set::sort($cats, '{n}.WorkshopCategory.name', 'desc'), '{n}.WorkshopCategory.id','{n}.WorkshopCategory.name'));
			
			$details = $this->WorkshopDetail->find('all', array('fields' => array('WorkshopDetail.id', 'WorkshopDetail.name'), 'recursive' => -1, 'order' => 'WorkshopDetail.name ASC'));
			$this->set('workshopnames', Set::combine($details, '{n}.WorkshopDetail.id', '{n}.WorkshopDetail.name'));
			
			$flyers = $this->Workshop->Flyer->find('all', array('recursive' => -1, 'fields' => array('Flyer.id', 'Flyer.friendly_name')));
			$this->set('flyers', Set::combine($flyers, '{n}.Flyer.id', '{n}.Flyer.friendly_name'));
			
			// check for agenda data
			$agendaRows = 0;
			if (!empty($this->data['Agenda']))
			{
				$agendaRows = sizeof($this->data['Agenda']) - 1;
			}
			
			// check for twitter links
			if ($this->OauthAccessToken->tokenExists('Twitter'))
				$this->set('linkedToTwitter', true);
			else
				$this->set('linkedToTwitter', false);
			
			$this->set('numAgendaRows', $agendaRows);
		}
		
		function attendance()
		{
			$this->pageTitle .= 'Attendance';
			
			if (!isset($this->params['named']['workshopid']))
			{
				// get all the workshops currently in the system and paginate them
				$this->paginate['recursive'] = 2;
				$workshops = $this->paginate('Workshop');
				$this->set('workshops', $workshops);
			}
			else
			{
				$workshop = $this->Workshop->find('first', 
					array(
						'conditions' => array('Workshop.id' => $this->params['named']['workshopid']),
						'recursive' => 0,
						'fields' => array('Workshop.date', 'Detail.name', 'Workshop.capacity')
					));
				$this->set('workshop', $workshop);
				
				$attendance = $this->Attendee->find('all',
					array(
						'conditions' => array('Workshop.id' => $this->params['named']['workshopid']),
						'recursive' => 0,
						'fields' => array('Attendee.attendance', 'Attendee.user_id', 'Workshop.id', 'User.first_name', 'User.last_name', 'User.id'),
						'order' => 'User.last_name',
					));
				
				$this->set('attendance', $attendance);
				
				// check if workshop is full
				$workshopFull = false;
				$workshopEmpty = false;
				if (sizeof($attendance) >= $workshop['Workshop']['capacity'])
					$workshopFull = true;
				if (sizeof($attendance) == 0)
					$workshopEmpty = true;
				
				$this->set('workshopFull', $workshopFull);
				$this->set('workshopEmpty', $workshopEmpty);
				
				if (!$workshopFull)
				{
					// get all users not in the workshop
					$attended = array_keys(Set::combine($attendance, '{n}.User.id', '{n}.User.id'));
					
					$conditions = array();
					if (!empty($attended))
						$conditions = array("User.id NOT IN (" . implode(', ', $attended) . ')');
					$users = $this->User->find('all', array('conditions' => $conditions, 'recursive' => -1, 'fields' => array('User.id', 'User.first_name', 'User.last_name', 'User.affiliation')));
					$this->set('users', Set::combine($users, '{n}.User.id', array('{1}, {0} -- {2}', '{n}.User.first_name', '{n}.User.last_name', '{n}.User.affiliation')));
				}
			}
		}
		
		function reports()
		{
			$this->pageTitle .= 'Reports';
		}
		
		function namebadges()
		{
			$this->pageTitle .= 'Print Name Badges';
			
			if (!isset($this->params['named']['workshopid']))
			{
				// get workshops.
				$this->Workshop->contain('Detail');
				$this->paginate['recursive'] = 2;
				$this->set('workshops', $this->paginate('Workshop'));
			}
			else
			{
				$workshopId = $this->params['named']['workshopid'];
				
				// get the workshop..
				$this->Workshop->id = $workshopId;
				$workshop = $this->Workshop->find('first', array('recursive' => 0));
				$this->set('workshop', $workshop);
				
				// get the attendees..
				$this->Attendee->unbindModel(array('belongsTo' => array('Workshop'), 'hasMany' => array('PaymentRecord')));
				$attendees = $this->Attendee->find('all', array('conditions' => array('Attendee.workshop_id' => $workshopId), 'order' => $this->Attendee->User->alias.'.last_name ASC'));
				
				// and get their groups. not very elegant, eh?
				foreach ($attendees as $k => $att)
					$attendees[$k]['Group'] = array_shift($this->Group->groupOfUser($att['User']['id']));
				$this->set('attendees', $attendees);
				
				// set to production mode or tcpdf will panic
				Configure::write('debug', 0);
				$this->layout = 'pdf';
			}
		}
		
		function newsletters()
		{
			$this->pageTitle .= 'Newsletters';
		}
		
		function payments($workshop_id = null, $attendee_id = null)
		{
			$this->pageTitle .= 'Payments';
			
			$this->set('workshop_id', $workshop_id);
			$this->set('attendee_id', $attendee_id);
			
			// select workshop
			if (!$workshop_id)
			{
				// paginate through the workshops
				$this->paginate['recursive'] = 1;
				$this->set('workshops', $this->paginate('Workshop'));
			}
			// select attendee
			else if (!$attendee_id)
			{
				$this->Attendee->recursive = 2;
				$attendees = $this->Attendee->find('all', array('order' => 'User.last_name', 'conditions' => array('Attendee.workshop_id' => $workshop_id)));
				$paid = array();
				foreach ($attendees as $attendee)
					$paid[$attendee['Attendee']['id']] = $this->PaymentRecord->totalPaid($attendee['Attendee']['id']);
				$this->Workshop->id = $workshop_id;
				
				$this->set('paid', $paid);
				$this->set('attendees', $attendees);
				$this->set('workshop', $this->Workshop->read());
			}
			// make changes to payment record
			else
			{
				$this->Workshop->id = $workshop_id;
				$this->Attendee->recursive = 2;
				$this->set('attendee', $this->Attendee->find('first', array('conditions' => 'Attendee.id = ' . $attendee_id)));
				$this->Attendee->recursive = 1;
				$this->set('total', $this->PaymentRecord->totalPaid($attendee_id));
				$this->set('records', $this->PaymentRecord->find('all', array('recursive' => 0, 'conditions' => array('PaymentRecord.attendee_id' => $attendee_id))));
				$this->set('payment_options', Set::combine(
					$this->PaymentOption->find('all', array('recursive' => -1)),
					'{n}.PaymentOption.id','{n}.PaymentOption.value'
				));
				$this->set('workshop', $this->Workshop->read());
			}
		}
		
		function workshop_certificates()
		{
			$this->pageTitle .= 'Print Workshop Certificates';
			
			$this->paginate['recursive'] = 1;
			$this->set('workshops', $this->paginate('Workshop'));
		}
		
		function road_scholar_certificates()
		{
			$this->pageTitle .= 'Print Road Scholar Certificates';
			$conditions = array();
			
			if (!empty($this->params['named']['filter']))
			{
				// road scholars only
				if ($this->params['named']['filter'] == 'rs')
					$conditions = array('AND' => array('CreditTotal.road_scholar_credits >=' => 7, 'CreditTotal.road_scholar_credits <' => 22));
				// master road scholars only
				else if ($this->params['named']['filter'] == 'mrs')
					$conditions = array('CreditTotal.road_scholar_credits >=' => 22);
			}
			
			$scholars = $this->CreditTotal->find('all', array('conditions' => $conditions));
			$this->set('scholars', $scholars);
		}
		
		function tech_notes()
		{
			$this->pageTitle .= 'Tech Notes';
		}
		
		function pending_registrations()
		{
			$this->pageTitle .= 'User Admin';
			
			// get any pending registrations
			$reg = $this->User->getPendingRegistrations(array('username', 'first_name', 'last_name', 'email', 'id'));
			$this->set('pending', $reg);
			
			// handle changes made
			if (!empty($this->data))
			{
				// clear mode from data to prevent possible data issues/php freakout
				$mode = $this->data['Admin']['mode'];
				unset($this->data['Admin']);
				
				// remove any users that weren't checked
				foreach ($this->data['User'] as $key => $user)
				{
					if (!$user['active'])
						unset($this->data['User'][$key]);
				}
				
				// if nothing was checked, then quit
				if (empty($this->data['User']))
					return;
				
				// delete users if in reject mode
				if ($mode == 'reject')
				{
					$this->User->recursive = -1;
					
					$ids = array();
					foreach ($this->data['User'] as $user)
						$ids[] = $user['id'];
					
					if ($this->User->delete($ids))
						$this->SessionPlus->flashSuccess('Users successfully rejected.');
					else
						$this->SessionPlus->flashError('Users could not be rejected.');
					
					$this->redirect('/admin/pending_registrations/');
				}
				// otherwise activate users
				else if ($mode == 'accept')
				{
					$user_ids = Set::combine($this->data['User'], '{n}.id', '{n}.id');
					
					if ($this->User->saveAll($this->data['User']))
					{
						// send an email to the new user!
						foreach ($user_ids as $id)
						{
							// set up an integration request
							$this->IntegrationRequest->makeRequest($id);
							
							$user = $this->User->read(null, $id);
							$this->set('user', $user);
							
							$this->EmailPlus->sendNoReply('account-activated', $user['User']['email'], 'Your Baystate Roads Account Has Been Activated');
							
							if (Configure::read())
								debug ($this->Session->read('Message.email'));
						}
						
						$this->SessionPlus->flashSuccess('Users successfully approved.');
					}
					else
						$this->SessionPlus->flashError('Users could not be approved.');
					
					$this->redirect('/admin/pending_registrations/');
				}
			}
		}
	}
?>

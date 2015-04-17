<?php
	class IntegrationRequestsController extends AppController
	{
		var $name = 'IntegrationRequests';
		var $uses = array('IntegrationRequest', 'UnintegratedUser');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$this->SessionPlus->denyNonAdmins();
		}
		
		function integrate()
		{
			// get all the users who don't have integration requests already
			$unUsers = $this->UnintegratedUser->find('all');
			$this->set('unintegratedUsers', $unUsers);
			$this->pageTitle .= 'Unintegrated Users';
			
			if (!empty($this->data))
			{
				foreach ($this->data['UnintegratedUser'] as $unUser)
				{
					if (empty($unUser['integrate']))
						continue;
					
					$userId = $unUser['user_id'];
					$this->IntegrationRequest->User->id = $userId;
					$user = $this->IntegrationRequest->User->read();
					
					if ($this->IntegrationRequest->makeRequest($userId))
					{
						$format = '%s created integration request (%d) for user %s (%d)';
						$message = sprintf($format,
							$this->SessionPlus->loggableUserName(),
							$this->IntegrationRequest->id,
							$user['User']['full_name'], $userId
						);
						$this->logAction($this->IntegrationRequest, 'create', $message);
					}
				}
				
				$this->SessionPlus->flashSuccess('Integration requests created.');
				$this->redirect($this->referer());
			}
		}
	}
?>
<?php
	class NewslettersController extends AppController
	{
		var $name = 'Newsletters';
		var $components = array('FileUpload', 'SessionPlus', 'ErrorListFormatter');
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			if ($this->params['action'] == 'upload')
			{
				// plug security hole for people who fiddle with POST data
				$this->SessionPlus->denyNonAdmins();
				
				if (!empty($this->data))
				{
					$this->FileUpload->fileModel = 'Newsletter';
					$this->FileUpload->uploadDir = 'newsletters';
					$this->FileUpload->allowedTypes = array('application/pdf');
					$this->FileUpload->fields['friendly_name'] = 'friendly_name';
					$this->FileUpload->fields['summary'] = 'summary';
					$this->FileUpload->fields['season'] = 'season';
					$this->FileUpload->fields['year'] = 'year';
					
					// not exactly elegant but it stops the FileUpload component
					// from exploding on a bad file type
					//if (!in_array($this->data['Newsletter']['file']['type'], $this->FileUpload->allowedTypes))
					//{
						//$this->SessionPlus->flashError(sprintf('The file type %s is not allowed. Allowed file types are: %s.', $this->data['Newsletter']['file']['type'], implode(', ', $this->FileUpload->allowedTypes)));
						//$this->redirect($this->referer());
					//}
					//else
					//{
						$this->FileUpload->data['Newsletter']['year'] = $this->FileUpload->data['Newsletter']['year']['year'];
						$this->FileUpload->data['Newsletter']['file']['friendly_name'] = urldecode($this->data['Newsletter']['file']['name']);
						$this->FileUpload->data['Newsletter']['file']['name'] = md5($this->data['Newsletter']['file']['name']) . '.pdf';
						$temp = $this->FileUpload->data['Newsletter'];
						unset($temp['file']);
						$this->FileUpload->data['Newsletter']['file'] = array_merge($this->FileUpload->data['Newsletter']['file'], $temp);
					//}
				}
			}
		}
		
		function upload()
		{
			if (empty($this->data))
				$this->redirect($this->referer());
			
			if ($this->FileUpload->success)
			{
				$this->SessionPlus->flashSuccess('Newsletter uploaded.');
				
				$format = '%s uploaded newsletter %s %d (%d)';
				$message = sprintf($format,
					$this->SessionPlus->loggableUserName(),
					$this->FileUpload->data['Newsletter']['season'], $this->FileUpload->data['Newsletter']['year'], $this->FileUpload->modelUsed->id
				);
				$this->logAction($this->FileUpload->modelUsed, 'create', $message);
			}
			else
			{
				$this->SessionPlus->flashError('Newsletter was not uploaded. Please correct the errors below.');
				$this->ErrorListFormatter->format($this->FileUpload->modelUsed->validationErrors);
			}
			
			$this->redirect($this->referer());
		}
		
		function view($id = null)
		{
			if (!$id)
			{
				$this->redirect($this->referer());
				return;
			}
			
			$newsletter =
				$this->Newsletter->find(
					'first',
					array(
						'conditions' => array('Newsletter.id' => $id),
						'recursive' => -1
					)
				);
			
			if (empty($newsletter))
			{
				$this->SessionPlus->flashError('There was a problem finding that newsletter.');
				$this->redirect($this->referer());
				
				return;
			}
			
			$this->set('newsletter', $newsletter);
			
			$this->autoRender = false;
			$this->redirect(Router::url('/newsletters/' . $newsletter['Newsletter']['name'], true));
		}
	}
?>
<?php
	class FlyersController extends AppController
	{
		var $name = 'Flyers';
		var $components = array('FileUpload', 'SessionPlus');
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			if ($this->params['action'] == 'upload')
			{
				$this->Security->enabled = false;
				
				// plug security hole for people who fiddle with POST data
				$this->SessionPlus->denyNonAdmins();
				
				if (!empty($this->data))
				{
					$this->FileUpload->fileModel = 'Flyer';
					$this->FileUpload->uploadDir = 'wsflyers';
					$this->FileUpload->allowedTypes = array('application/pdf');
					$this->FileUpload->fields['friendly_name'] = 'friendly_name';
					
					// not exactly elegant but it stops the FileUpload component
					// from exploding on a bad file type
					/*if (!in_array($this->data['Flyer']['file']['type'], $this->FileUpload->allowedTypes))
					{
						$this->SessionPlus->flashError(sprintf('The file type %s is not allowed. Allowed file types are: %s.', $this->data['Flyer']['file']['type'], implode(', ', $this->FileUpload->allowedTypes)));
						$this->redirect($this->referer());
					}*/
					//else
					//{
						$this->FileUpload->data['Flyer']['file']['friendly_name'] = urldecode($this->data['Flyer']['file']['name']);
						$this->FileUpload->data['Flyer']['file']['name'] = md5($this->data['Flyer']['file']['name']) . '.pdf';
					//}
				}
			}
		}
		
		function view()
		{
			if (empty($this->params['named']['flyerid']))
			{
				$this->redirect($this->referer());
				return;
			}
			
			$flyer =
				$this->Flyer->find(
					'first',
					array(
						'conditions' => array('Flyer.id' => $this->params['named']['flyerid']),
						'recursive' => -1
					)
				);
			
			if (empty($flyer))
			{
				$this->SessionPlus->flashError('There was a problem finding that flyer.');
				$this->redirect($this->referer());
				
				return;
			}
			
			$this->set('flyer', $flyer);
			
			$this->autoRender = false;
			$this->redirect(Router::url('/wsflyers/' . $flyer['Flyer']['name'], true));
		}
		
		function upload()
		{
			// this automatically puts the file into the flyers folder
			// and also inserts its metadata into the database
			// how convienient for us :)
			if (!empty($this->data))
			{				
				if ($this->FileUpload->success)
				{
					$flyer = $this->Flyer->read();
					
					// log it
					$format = '%s (%d) uploaded flyer %s (%d)';
					$message = sprintf($format,
						$this->Session->read('User.full_name'), $this->Session->read('User.id'),
						$flyer['Flyer']['friendly_name'], $flyer['Flyer']['id']
					);
					$this->logAction($this->Flyer, 'create', $message);
					
					$this->SessionPlus->flashSuccess('Upload successful.');
				}
				else
					$this->SessionPlus->flashError($this->FileUpload->showErrors());
			}
			
			$this->redirect($this->referer());
		}
		
		function thumbnail($flyer_id = null)
		{
			// check if we have a flyer
			$thumbPath = WWW_ROOT . 'wsflyers' . DS . 'thumbnails' . DS . "$flyer_id.png";
			$this->Flyer->id = $flyer_id;
			if (!$this->Flyer->exists() || !is_readable($thumbPath))
				$thumbPath = IMAGES . 'no-flyer-thumbnail.png';
			
			$this->layout = 'flyer_thumbnail';
			$this->set('thumbnailPath', $thumbPath);
		}
	}
?>
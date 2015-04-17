<?php
	class TechNotesController extends AppController
	{
		var $name = 'TechNotes';
		var $components = array('FileUpload', 'SlugGenerator', 'SessionPlus');
		var $uploadDir = 'tech_note_uploads';
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			$adminActions = array('upload');
			$this->SessionPlus->denyNonAdminsFrom($adminActions);
			
			if ($this->params['action'] == 'upload')
			{
				$this->FileUpload->fileModel = 'TechNote';
				$this->FileUpload->uploadDir = $this->uploadDir;
				$this->FileUpload->allowedTypes = array('application/pdf');
				$this->FileUpload->fields['friendly_name'] = 'friendly_name';
				$this->FileUpload->fields['title'] = 'title';
				$this->FileUpload->fields['summary'] = 'summary';
				
				// not exactly elegant but it stops the FileUpload component
				// from exploding on a bad file type
				//if (!empty($this->data) &&
					//!in_array($this->data['TechNote']['file']['type'], $this->FileUpload->allowedTypes))
				//{
					//$this->SessionPlus->flashError(sprintf('The file type %s is not allowed. Allowed file types are: %s.', $this->data['TechNote']['file']['type'], implode(', ', $this->FileUpload->allowedTypes)));
					//$this->redirect($this->referer());
				//}
				//else
				//{
					// fiddle with the tech note title to make a post slug
					$slug = $this->SlugGenerator->generate($this->data['TechNote']['title']);
					
					$this->FileUpload->data['TechNote']['file']['friendly_name'] = urldecode($this->data['TechNote']['file']['name']);
					$this->FileUpload->data['TechNote']['file']['name'] = md5($this->data['TechNote']['file']['name']) . '.pdf';
					$this->FileUpload->data['TechNote']['file']['title'] = $this->data['TechNote']['title'];
					$this->FileUpload->data['TechNote']['file']['summary'] = $this->data['TechNote']['summary'];
				//}
			}
		}
		
		function view($id = null)
		{
			if (!$id || !is_numeric($id))
			{
				$this->redirect($this->referer());
				return;
			}
			
			$tn = $this->TechNote->find('first', array('conditions' => array('TechNote.id' => $id), 'recursive' => -1));
			
			if (empty($tn))
			{
				$this->SessionPlus->flashError('There was a problem finding that Tech Note.');
				$this->redirect($this->referer());
				
				return;
			}
			
			$this->set('technote', $tn);
			
			$this->autoRender = false;
			$this->redirect(Router::url('/' . $this->uploadDir . '/' . $tn['TechNote']['name'], true));
		}
		
		function upload()
		{
			// this automatically puts the file into the flyers folder
			// and also inserts its metadata into the database
			// how convenient for us :)
			if (!empty($this->data))
			{				
				if (!$this->FileUpload->success)
				{
					$this->SessionPlus->flashError($this->FileUpload->showErrors());
				}
				else
				{
					$this->SessionPlus->flashSuccess('Upload successful.');
				}
			}
			
			$this->redirect($this->referer());
		}
	}
?>
<?php
	class PaymentRecordsController extends AppController {
		var $name = 'PaymentRecord';
		var $components = array('SessionPlus', 'ErrorListFormatter');
		var $helpers = array('Javascript');
		
		function beforeFilter()
		{
			parent::beforeFilter();
			
			// this could probably be expanded to use $this->User->canAdminister() but right now the only people
			// who can mess with payment records are site-wide admins
			$this->SessionPlus->denyNonAdmins();
		}
		
		function add()
		{
			if (!empty($this->data))
			{				
				if ($this->PaymentRecord->save($this->data))
					$this->SessionPlus->flashSuccess('Payment record saved.');
				else
					$this->SessionPlus->flashError('Payment record not saved!');
				
				$this->ErrorListFormatter->format($this->PaymentRecord->validationErrors);
			}
			
			$this->redirect($this->referer());
		}
		
		function delete($id)
		{
			if ($this->PaymentRecord->delete($id))
				$this->SessionPlus->flashSuccess('Payment record deleted.');
			else
				$this->SessionPlus->flashError('Payment record not deleted!');
		}
	}
?>
<?php
	App::import('Component', 'Email');
	class EmailPlusComponent extends EmailComponent
	{
		private function setDefaultSmtp()
		{
			$this->xMailer =	sprintf('Baystate Roads Emailer (PHP %s)', phpversion());
			$this->delivery =	'smtp';
			$this->sendAs =		'both';
			
			// gee, i hope they don't mind, hurr hurr hurr
			$this->smtpOptions = array(
				'port' => 25,
				'timeout' => 30,
				'host' => 'mail-auth.oit.umass.edu'
			);
			
			if (Configure::read())
				$this->delivery = 'debug';
		}
		
		private function setNoReplyOptions()
		{
			$this->setDefaultSmtp();
			
			$this->from =		'BaystateRoadsBot <noreply@baystateroads.org>';
			$this->replyTo =	'noreply@baystateroads.org';
		}
		
		function sendNoReply($template, $to, $subject = 'Email From Baystate Roads')
		{
			$this->setNoReplyOptions();
			
			$this->template =	$template;
			$this->to =			$to;
			$this->subject =	$subject;
			
			return $this->send();
		}
		
		function sendFromDan($template, $to, $subject = 'Email From Baystate Roads')
		{
			$this->setDefaultSmtp();
			
			$this->from = 		'Cindy Schaedig <cindy@baystateroads.org>';
			$this->replyTo = 	'Cindy Schaedig <cindy@baystateroads.org>';
			
			$this->template =	$template;
			$this->to =			$to;
			$this->subject = 	$subject;
			
			return $this->send();
		}
		
		function attachFile($path)
		{
			// assume that this is an absolute path. blah.
			$this->attachments[] = $path;
		}
	}
?>

<?php
class AppController extends Controller
{
	var $helpers = array('Html', 'Form', 'Javascript', 'Facebook.Facebook', 'Link', 'TimeFormatter');
	var $components = array('Security', 'SessionPlus');
	var $openGraphTags = array();
	var $useOpenGraph = true;
 
	function beforeFilter()
	{
		parent::beforeFilter();
		
		// version number: increase this at every tag
		Configure::write('App.version_number', '1.1');
		
		// redirect to the *actual* baystate roads site
		if (Configure::read('SiteSettings.host_redirect_hack') === true)
		{
			$here = Router::url('/', true);
			$matches = array();
			preg_match('@^(?:http://)?([^/]+)@i', $here, $matches);
			$host = strtolower($matches[1]);
			
			if ($host != 'baystateroads.eot.state.ma.us')
			{
				$url = $this->params['url']['url'];
				if (!empty($url) && $url[0] == '/')
					$url = substr($url, 1);
				$this->redirect('http://baystateroads.eot.state.ma.us/' . $url, 301);
			}
		}
		
		$this->Security->enabled = false;
		
		// set the default for the whole website here. overwrite in individual controllers
		$this->openGraphTags = array(
			// required stuff
			'og:title' => '',
			'og:type' => 'government',
			'og:image' => Router::url('/img/baystate-logo.png', true),
			'og:url' => '',
			
			'fb:admins' => '1841585219',
			
			// additional stuff
			'og:site_name' => 'Baystate Roads Program',
			'og:description' => 'The Baystate Roads Program provides technology transfer assistance to all communities in the Commonwealth of Massachusetts. The program is a cooperative effort of the Federal Highway Administration, Massachusetts Executive Office of Transportation, and the University of Massachusetts at Amherst.',
			
			// real-world stuff
			'og:latitude' => '42.39512890273371',
			'og:longitude' => '-72.52933502197266',
			'og:street-address' => '130 Natural Resources Road',
			'og:locality' => 'Amherst',
			'og:region' => 'MA',
			'og:postal-code' => '01003',
			'og:country-name' => 'USA',
			
			'og:email' => 'info@baystateroads.org',
			'og:phone_number' => '413-545-2604',
			'og:fax_number' => '413-545-6471',
		);
		
		// write the base title
		$this->pageTitle = 'Baystate Roads &rsaquo; ';
		
		if (Configure::read('SiteSettings.site_status') == 'Offline' && Router::url($this->here) != Router::url(Configure::read('SiteSettings.site_offline_url')))
			$this->redirect(Configure::read('SiteSettings.site_offline_url'));
	}
	
	function beforeRender()
	{
		parent::beforeRender();
		
		if ($this->useOpenGraph)
		{
			// check for required fields
			if (empty($this->openGraphTags['og:title']))
				$this->openGraphTags['og:title'] = $this->pageTitle;
			if (empty($this->openGraphTags['og:url']))
				$this->openGraphTags['og:url'] = Router::url(null, true);
			
			$this->set('fb_ogTags', $this->openGraphTags);
		}
	}
	
	// shortcut method to Model::logAction()
	protected function logAction($loggableModel, $type, $message, $object_id = null)
	{
		if ($loggableModel->Behaviors->attached('Loggable'))
			return $loggableModel->logAction($type, $message, Router::normalize($this->here), $this->SessionPlus->userIdCoalesce(), $object_id);
		
		return false;
	}
	
	// like django, except with less terrible
	protected function getObjectOrError($Model, $id, $error = 'error404', $messages = array())
	{
		// can't get an object, gotta 404
		if (empty($Model) || empty($id))
		{
			$this->cakeError($error, $messages);
			return false;
		}
		
		$Model->id = $id;
		$obj = $Model->read();
		if (empty($obj))
		{
			$this->cakeError($error, $messages);
			return false;
		}
		
		return $obj;
	}
}
?>
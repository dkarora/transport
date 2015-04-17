<?php
class SearchController extends AppController
{
	var $name = 'Search';
	// since we're going to be using google, there's no need
	// to access our own database
	var $uses = array();
	var $pageTitle = 'Baystate Roads &rsaquo; ';
	
	function beforeFilter()
	{
		parent::beforeFilter();
		
		if (!empty($this->Security))
			$this->Security->enabled = false;
	}
	
	function index($q = null, $area = null)
	{		
		// if no query given in url but query was posted, use that
		if (empty($q) && !empty($this->data['Search']['query']))
		{
			$q = $this->data['Search']['query'];
		}
		
		// if no area given in url but posted, use that
		if (empty($area) && !empty($this->data['Search']['area']))
		{
			$area = $this->data['Search']['area'];
		}
		
		// check for indexed searches
		if (empty($q) && !empty($this->data['Search'][0]))
		{
			foreach ($this->data['Search'] as $s)
			{
				if (!empty($s['query']))
				{
					$q = $s['query'];
					if (empty($area) && !empty($s['area']))
						$area = $s['area'];
					
					break;
				}
			}
		}
		
		// do the search if a query was found
		if (!empty($q))
		{
			// convert the controller if needed
			switch ($area)
			{
				case 'tech_notes':
					$area = 'news_posts/tech_notes';
					break;
				
				case 'newsletters':
					$area = 'news_posts/newsletters';
					break;
				
				case 'everything':
					$area = '';
					break;
			}
			
			// use google's search
			// todo: create custom search and funnel into that
			$this->redirect(sprintf('http://www.google.com/search?q=%s', urlencode('site:' . Router::url('/' . $area, true)) . ' ' . $q));
		}
		
		$this->pageTitle .= 'Search';
		
		// if no query is given, allow for page to render with big search bar
	}
}
?>
<?php
	function _sortNewsletters($a, $b)
	{
		// check for year before season
		$ay = $a['Newsletter']['year'];
		$by = $b['Newsletter']['year'];
		
		if ($ay != $by)
			return ($ay < $by ? -1 : 1);
		
		$as = $a['Newsletter']['season'];
		$bs = $b['Newsletter']['season'];
		
		$sorted = array('Spring', 'Summer', 'Fall', 'Winter');
		$ask = array_search($as, $sorted);
		$bsk = array_search($bs, $sorted);
		
		if ($ask == $bsk)
			return 0;
		
		return ($ask < $bsk ? -1 : 1);
	}
	
	class Newsletter extends AppModel
	{
		var $name = 'Newsletter';
		
		var $validate = array(
			'summary' => array(
				'notempty' => array(
					'rule' => 'notEmpty',
					'message' => 'Please enter a summary for the newsletter.',
					'last' => true
				)
			)
		);
		
		function mostRecent($goBack = 4)
		{
			$year = array();
			
			if ($goBack >= 1)
			{
				// find the highest year first
				$year = $this->find('all', array('order' => 'year DESC', 'limit' => $goBack));
				usort($year, "_sortNewsletters");
				$year = array_reverse($year);
			}
			
			return $year;
		}
		
		function findYear($year = null)
		{
			$news = array();
			
			if ($year)
			{
				$news = $this->find('all', array('conditions' => array('Newsletter.year' => $year)));
				usort($news, "_sortNewsletters");
				$news = array_reverse($news);
			}
			
			return $news;
		}
		
		function recordedYears($limit = null)
		{
			$raw = $this->findAll(null, 'DISTINCT Newsletter.year');
			$years = array();
			
			if ($limit && $limit > 0)
				$raw = array_slice($raw, 0, $limit);
			
			foreach ($raw as $r)
				$years[] = $r['Newsletter']['year'];
			
			sort(&$years);
			
			return $years;
		}
	}
?>
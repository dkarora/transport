<?php
	class TimeFormatterHelper extends AppHelper
	{
		var $helpers = array('Time');
		
		function writtenWord($date)
		{
			return date('F j, Y', strtotime($date));
		}
		
		function duration($dur)
		{
			$rt = '';
			
			// check days
			if ($dur >= 60 * 24)
			{
				$rt .= (empty($rt) ? '' : ', ') . floor($dur / (60 * 24)) . ' days';
				
				$dur %= (60 * 24);
			}
			
			// check hours
			if ($dur >= 60)
			{
				$rt .= (empty($rt) ? '' : ', ') . floor($dur / 60) . ' hours';
				
				$dur %= 60;
			}
			
			// check minutes
			if ($dur > 0)
			{
				$rt .= (empty($rt) ? '' : ', ') . $dur . ' minutes';
			}
			
			return $rt;
		}
		
		function commonDate($ts)
		{	
			return date('m/d/Y', strtotime($ts));
		}
		
		function commonDateTime($ts)
		{
			return $this->commonDate($ts) . ' ' . $this->commonTime($ts);
		}
		
		function commonTime($ts)
		{
			return date('g:i A', strtotime($ts));
		}
		
		function ago($date)
		{
			$ago = $this->Time->timeAgoInWords($date, array('end' => '+25 years'));
			// check for future
			if (substr($ago, strlen($ago) - 3) != 'ago')
				$ago .= ' from now';
			
			return $ago;
		}
	}
?>
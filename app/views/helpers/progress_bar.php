<?php
	class ProgressBarHelper extends AppHelper
	{
		var $helpers = array('Html');
		
		function bar($before, $filled, $total, $showPercent = true)
		{
			$format = '';
			$args = array();
			$percent = ($filled / $total) * 100;
			
			if ($showPercent)
			{
				$format = '%s%s/%s (%s%%)';
				$args = array($before, $filled, $total, number_format($percent));
			}
			else
			{
				$format = '%s%s/%s';
				$args = array($before, $filled, $total);
			}
			
			$text = vsprintf($format, $args);
			$fullnessTag = 'progress-bar-';
			
			if ($percent < 50)
				$fullnessTag .= 'low-percent';
			else if ($percent >= 50 && $percent < 75)
				$fullnessTag .= 'mid-percent';
			else if ($percent >= 75 && $percent < 100)
				$fullnessTag .= 'high-percent';
			else
				$fullnessTag .= 'max-percent';
			
			$rt = $this->Html->div('progress-bar',
				$this->Html->div('progress-bar-text', $text) . $this->Html->div('progress-bar-fill ' . $fullnessTag, '', array('style' => sprintf('width: %s%%', $percent))));
			
			return $rt;
		}
	}
?>
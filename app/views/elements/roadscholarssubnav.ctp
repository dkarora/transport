<?php
	if (!isset($options) || empty($options))
	{
		$options = array(
			'FAQ' => '/road_scholars/index',
			'List of Road Scholars' => '/road_scholars/scholars',
			'List of Master Road Scholars' => '/road_scholars/masterscholars',
			'Check Your Progress' => '/road_scholars/checkprogress'
		);
	}
	
	$cd = array();
	
	if (empty($attr))
		$attr = $cd;
	else
		$attr = array_merge($attr, $cd);
	
	echo $this->element('subnavgeneric', array('options' => $options, 'attr' => $attr));
?>
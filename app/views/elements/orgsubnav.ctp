<?php
	if (!isset($options) || empty($options))
	{
		$options = array(
			'Member List' => '/groups/index',
			'Member Options' => '/groups/member_options'
		);
	}
	
	if ($isGroupAdmin)
	{
		$m = array(
			'Register Member' => '/groups/newmember',
			'Enroll/Unenroll Members in Workshops' => '/groups/enrollmember',
			'Admin Options' => '/groups/admin_options'
		);
		
		if (!empty($merge))
			$merge = array_merge($m, $merge);
		else
			$merge = $m;
	}
	
	$cd = array();
	
	if (empty($attr))
		$attr = $cd;
	else
		$attr = array_merge($attr, $cd);
	
	$opt = array();
	$opt['options'] = $options;
	if (!empty($merge))
		$opt['merge'] = $merge;
	if (!empty($here))
		$opt['here'] = $here;
	if (!empty($position))
		$opt['position'] = $position;
	$opt['attr'] = $attr;
	
	echo $this->element('subnavgeneric', $opt);
?>
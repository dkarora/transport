<?php
	if (!isset($options) || empty($options))
	{
		$options = array(
			'Index' => '/admin/index',
			'News Posts' => '/admin/news_posts',
			'Workshops' => '/admin/workshops',
			'Attendance' => '/admin/attendance',
			'Print Name Badges' => '/admin/namebadges',
			'Payments' => '/admin/payments',
			'Workshop Certificates' => '/admin/workshop_certificates',
			'Road Scholar Certificates' => '/admin/road_scholar_certificates',
			'Newsletters' => '/admin/newsletters',
			'Tech Notes' => '/admin/tech_notes',
			'Pending Registrations' => '/admin/pending_registrations/',
			'Announcements' => '/admin/announcements/',
			'Social Connections' => '/admin/social/',
			'Legacy Records' => '/admin/legacy_records/',
			'Create New User' => '/admin/new_user/',
			'Search Users' => '/admin/search_users/',
			'Activity Logs' => '/activity_logs/',
			'Add/Edit Group' => '/groups/add/',
			'Manage Groups' => '/groups/search',
			'Unintegrated Users' => '/integration_requests/integrate',
			'Export Attendees' => '/denormalized_attendees',
		);
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

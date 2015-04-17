<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Admin Dashboard</h2>

<h3>Alerts</h3>
<?php
	if (sizeof($pending) > 0)
		echo $html->div('notification pending-registrations-notification', $html->link(sprintf('You have %s pending registrations.', sizeof($pending)), '/admin/pending_registrations/'));
	
	if ($integrationRequests > 0)
		echo $html->div('notification pending-integration-requests-notification', $html->link(sprintf('You have %s pending integration requests.', $integrationRequests), '/admin/legacy_records/#requests'));
	
	if (sizeof($pending) == 0 && $integrationRequests == 0)
		echo $html->div('', 'No new alerts.');
?>

<?php debug($session->read()); ?>
<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2>Legacy Records</h2>

<h3>Import CSV</h3>
<p style="color: red; font-weight: bold;">This can take a while; be patient and only hit submit once!</p>

<?php
	echo $form->create('LegacyRecord', array('type' => 'file'));
	echo $form->input('LegacyRecord.file', array('type' => 'file'));
	echo $form->end('');
?>

<h3 id="requests">Pending Requests</h3>

<?php
	if (empty($requests))
		echo $html->div('', 'No pending requests!');
	else
		foreach ($requests as $req)
			echo $html->div('', $html->link(sprintf('%s %s', $req['User']['first_name'], $req['User']['last_name']), sprintf('/admin/legacy_associate/%s', $req['IntegrationRequest']['id'])));
?>

<h3>Filled Requests</h3>

<?php
	if (empty($filled))
		echo $html->div('', 'No filled requests!');
	else
		foreach ($filled as $req)
			echo $html->div('', $html->link($req['User']['full_name'], sprintf('/admin/legacy_associate/%s', $req['IntegrationRequest']['id'])));
?>
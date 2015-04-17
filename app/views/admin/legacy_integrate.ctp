<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'legacy-associate'); ?>

<h2>Legacy Records</h2>

<?php echo $this->element('steps', array('steps' => $steps, 'step' => $step)); ?>

<h3>User Info</h3>
<?php
	echo sprintf('%s %s <br />', $request['User']['first_name'], $request['User']['last_name']);
	echo sprintf('%s <br />%s <br />', $request['User']['address_line1'], $request['User']['address_line2']);
	echo sprintf('%s, %s %s <br />', $request['User']['city'], $request['User']['state'], $request['User']['zip']);
	echo sprintf('%s', $request['User']['phone']);
?>

<?php if ($step == 2): ?>
	<h3>Records</h3>

	<?php
		echo $form->create('Admin', array('url' => '/admin/legacy_integrate/'));
		echo $form->hidden('Admin.req_id', array('value' => $request['IntegrationRequest']['id']));
		echo $html->tag('ol', null, 'record-list');
		foreach ($records as $r)
		{
			$k = $r['LegacyRecord']['id'];
			$n = $this->element('legacy-record-display', array('record' => $r['LegacyRecord']));
			
			$n .= $form->input("Records.$k.selection_type", array('legend' => 'Selection Type', 'options' => array('new' => 'New', 'existing' => 'Existing'), 'type' => 'radio'));
			$n .= $form->input("Records.$k.existing_workshop_detail", array('options' => $workshops, 'label' => 'Existing Workshop'));
			$n .= $form->input("Records.$k.new_workshop_name", array('value' => $r['LegacyRecord']['workshop_name']));
			$n .= $form->input("Records.$k.ignore", array('type' => 'checkbox'));
			
			echo $html->tag('li', $n);
		}
		echo $html->tag('/ol', null);
		echo $form->end('Submit');
	?>
<?php else : ?>
	<h3>Pending Changes</h3>
	<?php
		if (empty($records))
			echo $html->div('', 'Uh, no records selected! Go back and pick some.');
		else
		{
			echo $form->create('Admin', array('url' => '/admin/legacy_finish/'));
			echo $form->hidden('Admin.req_id', array('value' => $request['IntegrationRequest']['id']));
			echo $html->tag('ol', null);
			foreach ($records as $k => $r)
			{
				debug ($r);
				
				$n = $this->element('legacy-record-display', array('record' => $r['LegacyRecord']));
				$n .= $html->tag('div', null, array('class' => 'pending-changes'));
				$n .= $html->div('', $html->tag('strong', 'For this entry, the following actions will be taken:'));
				
				$n .= $form->hidden("Records.$k.record_id", array('value' => $r['LegacyRecord']['id']));
				$n .= $form->hidden("Records.$k.insert_type", array('value' => $reqs[$r['LegacyRecord']['id']]['selection_type']));
				$n .= $form->hidden("Records.$k.new_workshop_name", array('value' => $reqs[$r['LegacyRecord']['id']]['new_workshop_name']));
				
				if ($reqs[$r['LegacyRecord']['id']]['selection_type'] == 'new')
				{
					$n .= $html->tag('ul', null);
					$n .= $html->tag('li', sprintf('A workshop category named %s will be created and flagged as legacy.', $html->tag('strong', $r['LegacyRecord']['workshop_category_name'])));
					$n .= $html->tag('li', sprintf('A workshop named %s will be created and flagged as legacy.', $html->tag('strong', $reqs[$r['LegacyRecord']['id']]['new_workshop_name'])));
					$n .= $html->tag('li', sprintf('A workshop with the %s details will be scheduled at %s.', $html->tag('strong', $reqs[$r['LegacyRecord']['id']]['new_workshop_name']), $html->tag('strong', $r['LegacyRecord']['workshop_date'])));
					$n .= $html->tag('li', sprintf('The user\'s attendance for that workshop will be set to %s.', $html->tag('strong', (!empty($r['LegacyRecord']['attended']) ? 'attended' : 'not attended'))));
					$n .= $html->tag('/ul', null);
					
					$n .= $form->hidden("Records.$k.new_workshop_name", array('value' => $reqs[$r['LegacyRecord']['id']]['new_workshop_name']));
				}
				else if ($reqs[$r['LegacyRecord']['id']]['selection_type'] == 'existing')
				{
					$n .= $html->tag('ul', null);
					$n .= $html->tag('li', sprintf('A workshop with the %s details will be scheduled at %s and flagged as legacy.', $html->tag('strong', $reqs[$r['LegacyRecord']['id']]['Workshop']['WorkshopDetail']['name']), $html->tag('strong', $r['LegacyRecord']['workshop_date'])));
					$n .= $html->tag('li', sprintf('The user\'s attendance for that workshop will be set to %s.', $html->tag('strong', (!empty($r['LegacyRecord']['attended']) ? 'attended' : 'not attended'))));
					$n .= $html->tag('/ul', null);
					
					$n .= $form->hidden("Records.$k.existing_workshop_detail", array('value' => $reqs[$r['LegacyRecord']['id']]['existing_workshop_detail']));
				}
				$n .= $html->tag('/div', null);
				
				echo $html->tag('li', $n);
			}
			echo $html->tag('/ol', null);
			
			echo $html->div('', sprintf('If this looks correct, press the button below %s', $html->tag('span', 'once and only once!', array('class' => 'submit-warning'))));
			echo $form->end('Submit');
		}
	?>
<?php endif; ?>
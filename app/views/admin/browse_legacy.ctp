<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'legacy-records'); ?>

<?php
	echo $form->create('BrowsedRecords', array('url' => '/admin/legacy_integrate'));
	// tell the paginator what the base url is
	$here = $request['IntegrationRequest']['id'];
	if ($from_letter)
		$here .= '/' . $from_letter;
	$paginator->options = array('url' => $here);
	echo $form->input('Admin.req_id', array('value' => $request['IntegrationRequest']['id'], 'type' => 'hidden'));
	echo $form->input('Admin.from', array('value' => 'browse_legacy', 'type' => 'hidden'));
?>

<div class="paginator">
	<?php
		// common options
		echo $html->tag('span', null, array('class' => 'page-list letter-list'));
		
		// add a clearing tag
		if ($from_letter)
			echo $html->tag('span', $html->link('All', '/admin/browse_legacy/' . $request['IntegrationRequest']['id']));
		else
			echo $html->tag('span', 'All', array('class' => 'current'));
		
		$letter = 'A';
		for ($i = 0; $i <= 25; $i++)
		{
			if ($letter != $from_letter)
				echo $html->tag('span', $html->link($letter . " ", '/admin/browse_legacy/' . $request['IntegrationRequest']['id'] . '/' . $letter));
			else
				echo $html->tag('span', $letter, array('class' => 'current'));
			$letter++;
		}
		
		// non-letters
		if ($from_letter != 'misc')
			echo $html->tag('span', $html->link('Misc', '/admin/browse_legacy/' . $request['IntegrationRequest']['id'] . '/' . 'misc'));
		else
			echo $html->tag('span', 'Misc', array('class' => 'current'));
		
		echo $html->tag('/span', null);
	?>
</div>

<?php echo $this->element('page-numbers'); ?>

<?php if (!empty($records)) : ?>
<table>
	<?php
		$headers = array(
			'Name' => 'LegacyRecord.last_name',
			'Address' => 'LegacyRecord.address_line1',
			'Affiliation' => 'LegacyRecord.affiliation',
			'Workshop' => 'LegacyRecord.workshop_name',
			'Select'
		);
		
		echo $this->element('table-headers', array('headers' => $headers));
	?>
	
	<?php foreach ($records as $index => $r): $req = $r['LegacyRecord']; ?>
	<tr
		<?php
			/* apply alt row colors on odd rows */
			if ($index % 2)
				echo " class='altrow'";
		?>>
		
		<td>
			<?php
				if (empty($req['middle_name']))
					$n = sprintf('%s %s', $req['first_name'], $req['last_name']);
				else
					$n = sprintf('%s %s %s', $req['first_name'], $req['middle_name'], $req['last_name']);
				
				if (!empty($req['suffix']))
					$n .= sprintf(', %s', $req['suffix']);
				
				echo $n;
			?>
		</td>
		<td>
			<?php
				if (!empty($req['address_line1']) || !empty($req['address_line2']))
					echo sprintf('%s <br />%s', $req['address_line1'], $req['address_line2']);
				else
					echo $html->div('info-missing', '(No street address)');
				
				echo '<br />';
				
				if (!empty($req['city']))
					echo $req['city'];
				else
					echo $html->div('info-missing', '(No city)');
				
				if (!empty($req['state']))
					echo sprintf(', %s', $req['state']);
				else
					echo $html->div('info-missing', ' (No state)');
				
				echo '<br />';
				
				if (!empty($req['zip']))
					echo $req['zip'];
				else
					echo $html->div('info-missing', '(No ZIP code)');
			?>
		</td>
		<td>
			<?php
				if (!empty($req['affiliation']))
					echo $req['affiliation'];
				else
					echo $html->div('info-missing', '(No affiliation)');
				
				echo '<br />';
				
				if (!empty($req['department_name']))
					echo $req['department_name'];
				else
					echo $html->div('info-missing', '(No department)');
			?>
		</td>
		<td>
			<?php
				if (!empty($req['workshop_name']))
					echo $req['workshop_name'];
				else
					echo $html->div('info-missing', '(No workshop name)');
				
				echo '<br />';
				
				if (!empty($req['workshop_location']))
					echo $req['workshop_location'];
				else
					echo $html->div('info-missing', '(No workshop location)');
				
				if (!empty($req['workshop_city']))
					echo sprintf(', %s', $req['workshop_city']);
				else
					echo $html->div('info-missing', ' (No workshop city)');
				
				echo '<br />';
				
				if (!empty($req['workshop_date']))
					echo $timeFormatter->commonDate($req['workshop_date']);
				else
					echo $html->div('info-missing', '(No workshop date)');
				
				echo '<br />';
				
				if (!empty($req['workshop_category_name']))
					echo $req['workshop_category_name'];
				else
					echo $html->div('info-missing', '(No workshop category)');
				
				echo '<br />';
				
				echo sprintf('Attended: %s', (!empty($req['attended']) ? 'Yes' : 'No'));
			?>
		</td>
		<td><?php echo $form->input("BrowsedRecords.$index.select", array('type' => 'checkbox', 'class' => 'selectalltarget', 'checked' => false, 'value' => $req['id'])); ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="paginator">
	<?php
		// common options
		echo $html->tag('span', null, array('class' => 'page-list letter-list'));
		
		// add a clearing tag
		if ($from_letter)
			echo $html->tag('span', $html->link('All', '/admin/browse_legacy/' . $request['IntegrationRequest']['id']));
		else
			echo $html->tag('span', 'All', array('class' => 'current'));
		
		$letter = 'A';
		for ($i = 0; $i <= 25; $i++)
		{
			if ($letter != $from_letter)
				echo $html->tag('span', $html->link($letter . " ", '/admin/browse_legacy/' . $request['IntegrationRequest']['id'] . '/' . $letter));
			else
				echo $html->tag('span', $letter, array('class' => 'current'));
			$letter++;
		}
		
		// non-letters
		if ($from_letter != 'misc')
			echo $html->tag('span', $html->link('Misc', '/admin/browse_legacy/' . $request['IntegrationRequest']['id'] . '/' . 'misc'));
		else
			echo $html->tag('span', 'Misc', array('class' => 'current'));
		
		echo $html->tag('/span', null);
	?>
</div>

<?php
	echo $this->element('page-numbers');
	
	echo $form->submit();
	echo $form->end();
?>
<?php else : ?>
	<strong>No records!</strong>
<?php endif;
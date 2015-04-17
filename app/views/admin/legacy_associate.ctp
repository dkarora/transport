<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $this->set('bodyClass', 'legacy-records legacy-associate'); ?>
<?php $javascript->link(array('select-all'), false); ?>

<h2>Legacy Associations</h2>

<?php echo $this->element('steps', array('steps' => $steps, 'step' => $step)); ?>

<h3>User Info</h3>

<?php
	echo sprintf('%s %s <br />', $request['User']['first_name'], $request['User']['last_name']);
	echo sprintf('%s <br />%s <br />', $request['User']['address_line1'], $request['User']['address_line2']);
	echo sprintf('%s, %s %s <br />', $request['User']['city'], $request['User']['state'], $request['User']['zip']);
	echo sprintf('%s', $request['User']['phone']);
?>

<h3>Records</h3>

<?php
	if (empty($request['LegacyRecord']))
	{
		echo $html->div('', sprintf('No records found! %s', $html->link('Browse the records to find more?', sprintf('/admin/browse_legacy/%s', $req_id))));
		echo $html->div('', $html->link('Mark as filled?', sprintf('/admin/fill_integration/%s', $req_id)));
	}
	
	if (!empty($request['LegacyRecord'])) :
		// send the request_id with it so we know to add the matches at the top
		echo $html->div('paragraph', sprintf('Not the records you\'re looking for? %s', $html->link('Browse the records to find more.', sprintf('/admin/browse_legacy/%s', $req_id))));
		echo $form->create('Admin', array('url' => '/admin/legacy_integrate/'));
		echo $form->input('req_id', array('value' => $req_id, 'type' => 'hidden'));
?>
		<table>
			<tr>
				<th>Name</th>
				<th>Address</th>
				<th>Affiliation</th>
				<th>Workshop</th>
				<th><?php echo $form->checkbox('Opt.selectall', array('id' => 'selectall')); ?> Select</th>
			</tr>
			
			<?php foreach ($request['LegacyRecord'] as $req) : $k = $req['id'] ?>
			<tr>
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
				
				<td><?php echo $form->input("Admin.Records.$k.select", array('type' => 'checkbox', 'class' => 'selectalltarget', 'checked' => false)); ?></td>
			</tr>
			
			<?php endforeach; ?>
		</table>

<?php
		// cakephp is refusing to show a submit button so print it manually
		echo $form->submit();
		echo $form->end();
		endif;
?>


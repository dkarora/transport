<?php
	if (!empty($record))
	{
		$n = 'Name: ';
		$req = $record;
		
		if (empty($req['middle_name']))
			$n = sprintf('%s %s', $req['first_name'], $req['last_name']);
		else
			$n = sprintf('%s %s %s', $req['first_name'], $req['middle_name'], $req['last_name']);
		
		if (!empty($req['suffix']))
			$n .= sprintf(', %s', $req['suffix']);
		
		$n .= '<br />';
		
		if (!empty($req['address_line1']) || !empty($req['address_line2']))
			$n .= sprintf('%s <br />%s', $req['address_line1'], $req['address_line2']);
		else
			$n .= $html->div('info-missing', '(No street address)');
		
		$n .= '<br />';
		
		if (!empty($req['city']))
			$n .= $req['city'];
		else
			$n .= $html->div('info-missing', '(No city)');
		
		if (!empty($req['state']))
			$n .= sprintf(', %s', $req['state']);
		else
			$n .= $html->div('info-missing', ' (No state)');
		
		$n .= '<br />';
		
		if (!empty($req['zip']))
			$n .= $req['zip'];
		else
			$n .= $html->div('info-missing', '(No ZIP code)');
		
		$n .= '<br /><br />';
		
		if (!empty($req['affiliation']))
			$n .= $req['affiliation'];
		else
			$n .= $html->div('info-missing', '(No affiliation)');
		
		$n .= '<br />';
		
		if (!empty($req['department_name']))
			$n .= $req['department_name'];
		else
			$n .= $html->div('info-missing', '(No department)');
		
		$n .= '<br /><br />';
		
		if (!empty($req['workshop_name']))
			$n .= $req['workshop_name'];
		else
			$n .= $html->div('info-missing', '(No workshop name)');
		
		$n .= '<br />';
		
		if (!empty($req['workshop_location']))
			$n .= $req['workshop_location'];
		else
			$n .= $html->div('info-missing', '(No workshop location)');
		
		if (!empty($req['workshop_city']))
			$n .= sprintf(', %s', $req['workshop_city']);
		else
			$n .= $html->div('info-missing', ' (No workshop city)');
		
		$n .= '<br />';
		
		if (!empty($req['workshop_date']))
			$n .= $timeFormatter->commonDate($req['workshop_date']);
		else
			$n .= $html->div('info-missing', '(No workshop date)');
		
		$n .= '<br />';
		
		if (!empty($req['workshop_category_name']))
			$n .= $req['workshop_category_name'];
		else
			$n .= $html->div('info-missing', '(No workshop category)');
		
		$n .= '<br />';
		$n .= sprintf('Attended: %s', (!empty($req['attended']) ? 'Yes' : 'No'));
		$n .= '<br /><br />';
	}
	
	echo $n;
?>
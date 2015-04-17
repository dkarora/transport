<?php
	$rows = array(
		"Agenda.$agendaIndex.timestamp" => array(),
		"Agenda.$agendaIndex.description" => array('type' => 'textarea')
	);
	
	echo $this->element('neat-form', array(
		'rows' => $rows,
		'isAjax' => true
	));
?>
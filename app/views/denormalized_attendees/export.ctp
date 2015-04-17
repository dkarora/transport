<?php
	if (!empty($headers))
		$csv->addRow($headers);
	
	foreach ($data as $row)
	{
		$values = array_values($row['DenormalizedAttendee']);
		$csv->addRow($values);
	}
	
	echo $csv->render(false);
?>
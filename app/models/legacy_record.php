<?php
	class LegacyRecord extends AppModel
	{
		var $name = 'LegacyRecord';
		
		function importFromCsv($filename)
		{
			// start praying.
			set_time_limit(600);
			
			if (($file = fopen($filename, "r")) === false)
				return false;
			
			$headers = fgetcsv($file);
			
			// transform old field names into new ones
			$field_mappings = array(
				'first_name' => 'first_name',
				'middle_name' => 'middle_name',
				'last_name' => 'last_name',
				'nickname' => 'nickname',
				'Suffix' => 'suffix',
				'title' => 'job_title',
				'address_line1' => 'address_line1',
				'address_line2' => 'address_line2',
				'city' => 'city',
				'state' => 'state',
				'zip' => 'zip',
				'phone' => 'phone',
				'fax' => 'fax',
				'email' => 'email',
				'company/agency/affiliation' => 'affiliation_name',
				'Dept' => 'department_name',
				'attended?' => 'attended',
				'workshop' => 'workshop_name',
				'workshop city' => 'workshop_city',
				'workshop location' => 'workshop_location',
				'workshop date' => 'workshop_date',
				' Cost ' => 'workshop_cost',
				'payment_type' => 'payment_type_name',
				'check number' => 'check_number',
				' cash_amnt ' => 'amount_paid',
				'rs_credits' => 'road_scholar_credits',
				'ceus' => 'ceu_credits',
				'par_cat' => 'workshop_category_name'
			);
			
			$arr = array();
			
			while (($data = fgetcsv($file)) !== false)
			{
				$row = array();
				
				for ($i = 0; $i < sizeof ($data); $i++)
				{
					if (!array_key_exists($headers[$i], $field_mappings))
						continue;
					
					$colname = $field_mappings[$headers[$i]];
					
					// do some data manipulation
					$data[$i] = trim($data[$i]);
					if ($colname == 'attended')
					{
						if (strtolower($data[$i]) == 'yes')
							$data[$i] = 1;
						else
							$data[$i] = 0;
					}
					else if ($colname == 'workshop_date')
					{
						// change date to iso 8601 format
						if (!empty($data[$i]))
							$data[$i] = preg_replace("/([0-9]{1,2})\/([0-9]{2})\/([0-9]{4})/i", "$3-$1-$2 00:00:00", $data[$i]);
						// blank = unknown date
						else
							$data[$i] = null;
					}
					else if ($colname == 'ceu_credits' || $colname == 'road_scholar_credits')
					{
						if (empty($data[$i]))
							$data[$i] = 0;
					}
					
					$row[$colname] = $data[$i];
				}
				
				$arr[] = $row;
			}
			
			//$arr = array($this->alias => $arr);
			
			fclose($file);
			
			return $this->saveAll($arr);
		}
	}
?>
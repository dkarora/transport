<?php
	class PaginatorTableHelper extends AppHelper
	{
		var $helpers = array('Html', 'Paginator');
		
		function _enl($str, $numlines = 1)
		{
			echo $str;
			for ($i = 0; $i < $numlines; $i++)
				echo "\n";
		}
		
		function printTable($headers, $data, $dataKeying, $opts = array())
		{
			$this->_enl($this->Html->tag('table', null, $opts));
			$this->_enl($this->Html->tag('tr', null));
			
			// print each header (sort)
			foreach ($headers as $key => $value)
				$this->_enl($this->Html->tag('th', is_numeric($key) ? $value : $this->Paginator->sort($key, $value)));
			
			$this->_enl($this->Html->tag('/tr', null), 2);
			
			for ($i = 0; $i < sizeof($data); $i++)
			{
				$this->_enl($this->Html->tag('tr', null, array('class' => $i % 2 ? 'altrow' : '')));
				foreach ($dataKeying as $keying)
				{
					// dataKeying is an array of keys in data
					if (is_array($keying))
					{
						$d = $data[$i];
						while (!empty($keying))
						{
							$k = array_shift($keying);
							$d = $d[$k];
						}
						
						$this->_enl($this->Html->tag('td', $d));
					}
				}
				$this->_enl($this->Html->tag('/tr', null), 2);
			}
			
			$this->_enl($this->Html->tag('/table', null));
		}
	}
?>
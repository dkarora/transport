<?php
	$columns = array();
	
	if (isset($headers))
	{
		foreach ($headers as $key => $value)
		{
			if (!is_numeric($key) && isset($paginator))
			{
				$c = $paginator->sort($key, $value);
				$options = array();
				if ($paginator->sortKey() == $value)
				{
					$dir = $paginator->sortDir();
					$options['class'] = "sorted-column-header $dir";
				}
				
				$columns []= $html->tag('th', $c, $options);
			}
			else
				$columns []= $html->tag('th', $value);
		}
	}
	
	echo $html->tag('tr', implode($columns));
?>
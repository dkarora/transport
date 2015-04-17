<?php
	class LegacyBehavior extends ModelBehavior
	{
		function setup(&$Model, $settings)
		{
			if (!isset($this->settings[$Model->alias]))
				$this->settings[$Model->alias] = array('legacy' => false);
			
			$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array)$settings);
		}
		
		function legacy(&$Model)
		{
			return $this->settings[$Model->alias]['legacy'];
		}
		
		function useLegacy(&$Model, $legacy, $cascade = true)
		{
			
			
			if (is_bool($legacy))
			{
				$this->settings[$Model->alias]['legacy'] = $legacy;
			}
		}
		
		function beforeFind(&$Model, $queryData)
		{
			// if useLegacy is set then leave it alone (don't restrict on legacy one way or another)
			if ($this->legacy($Model))
				return true;
			
			// no conditions, include the legacy condition
			if (empty($queryData['conditions']))
				$queryData['conditions'] = array($Model->alias.'.legacy' => 0);
			else
			{
				$conds = $queryData['conditions'];
				
				if (is_string($conds))
				{
					// check if $conds does not reference the legacy field
					if (strstr($conds, $Model->alias.'.legacy') === FALSE)
						$conds .= ' AND ' . $Model->alias.'.legacy = 0';
				}
				else if (is_array($conds))
				{
					$cond_keys = array_keys($conds);
					$found_legacy = false;
					
					foreach ($cond_keys as $key => $value)
					{
						if (strstr($key, $Model->alias.'.legacy') !== FALSE || strstr($key, 'legacy') !== FALSE)
							$found_legacy = true;
					}
					
					if (!$found_legacy)
						$conds[$Model->alias.'.legacy'] = 0;
				}
				
				$queryData['conditions'] = $conds;
			}
			
			return $queryData;
		}
	}
?>
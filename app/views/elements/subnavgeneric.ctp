<?php
	$id = 'id="subnav"';
	$class = '';
	
	if (!empty($attr))
	{
		if (!empty($attr['container_id']))
			$id = 'id="' . $attr['container_id'] . '"';
		
		if (!empty($attr['container_class']))
			$class = 'class="' . $attr['container_class'] . '"';
	}
?>

<div <?php echo $id, ' ', $class; ?>>
	<ul>
		<?php
			/*
				here's some options that can be funneled in:
				$options:	array with keys as the link text and value as either
							an array or string that can be formatted into a
							CakePHP url.
							
				$merge:		[optional] array of the same format as $options that
							can be inserted into the output list.
							
				$position:	[optional] string or integer that dictates where the
							$merge list is inserted into the output. if $position
							is an integer, it is interpreted as an index of where to
							insert. otherwise, $position can be 'after' or 'before'.
							$position is defaulted to 'after' if not supplied.
							
				$here:		[optional] a string that corresponds to a key in $options
							or $merge. the link corresponding to this key will be
							set to the current location (class => here). if not
							provided or not in the merged options list, the menu
							falls back to the default behaviour.
							
				$attr:		[optional] an array that may contain the following elements:
								$container_id:
									[optional] a string containing the id of the container.
									if null or empty, the id will not be set. if unset,
									the id defaults to 'subnav'.
											
								$container_class:
									[optional] a string containing the class of the container.
									if null, empty, or unset, the class will not be set.
				
			*/
			
			// merge more options into the list?
			if (!empty($merge))
			{
				// default behaviour: tack on the options to the end
				// alternatively if $position is 'after' then do this too
				if (empty($position) || (is_string($position) && 'after' == strtolower($position)))
				{
					$options = array_merge($options, $merge);
				}
				// $position is 'before' prepend $merge to $options
				else if(!empty($position) && is_string($position) || (isset($position) && $position == 0))
				{
					if ('before' == strtolower($position))
						$options = array_merge($merge, $options);
				}
				// otherwise if $position is an integer then insert $merge into $options at that index
				else if (!empty($position) && is_int($position))
				{
					$options = array_merge(
						array_slice($options, 0, $position, true),
						$merge,
						array_slice($options, $position, sizeof($options) - $position, true)
					);
				}
			}
			
			// check if $here is in the array
			$herefound = false;
			if (!empty($here))
				$herefound = array_key_exists($here, $options);
			
			// echo out the options
			foreach ($options as $name => $loc)
			{
				// check if we sent an array
				if (is_array($loc))
				{
					// mmmm, kludges.
					$loc = '/' . substr(Router::url($loc), strlen(Router::url('/')));
				}
				
				// determine if we are here
				$parsedloc = Router::parse($loc);
				
				echo '<li>';
				if ((empty($here) && $parsedloc['controller'] == $this->params['controller'] && $parsedloc['action'] == $this->params['action']) || ($herefound && !empty($here) && $name == $here))
				{
					// special case for pages controller
					if ($this->params['controller'] == 'pages')
					{
						if (empty($here) && $parsedloc['controller'] == 'pages' && $parsedloc['action'] == 'display' && !empty($this->params['pass']) && !empty($parsedloc['pass']) && $this->params['pass'][0] == $parsedloc['pass'][0])
							echo $html->link ($name, $loc, array('class' => 'here'));
						else
							echo $html->link ($name, $loc);
					}
					else
						echo $html->link ($name, $loc, array('class' => 'here'));
				}
				else
					echo $html->link ($name, $loc);
				echo '</li>';
			}
		?>
	</ul>
</div>
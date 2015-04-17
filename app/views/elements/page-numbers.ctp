<?php
	if (empty($id))
		$id = '';
?>

<div <?php if ($id) echo "id='$id'"; ?> class="paginator">
	<?php
		// common options
		$opts = array('escape' => false);
		
		// grab link text to ensure we're not passing null for $html->tag()
		$first = $paginator->first('&laquo;', $opts);
		$prev = $paginator->prev('&lsaquo;', $opts);
		$next = $paginator->next('&rsaquo;', $opts);
		$last = $paginator->last('&raquo;', $opts);
		$numbers = $paginator->numbers(array('separator' => null));
		
		if (empty($first))
			$first = '';
		if (empty($prev))
			$prev = '';
		if (empty($next))
			$next = '';
		if (empty($last))
			$last = '';
		if (empty($numbers))
			$numbers = $html->tag('span', $paginator->current(), array('class' => 'current'));
		
		echo $html->tag('span', $first, array('class' => 'first-page navigate-link backwards'));
		echo $html->tag('span', $prev, array('class' => 'prev-page navigate-link backwards'));
		echo $html->tag('span', $numbers, array('class' => 'page-list'));
		echo $html->tag('span', $next, array('class' => 'next-page navigate-link forwards'));
		echo $html->tag('span', $last, array('class' => 'last-page navigate-link forwards'));
	?>
</div> <!-- page listing -->

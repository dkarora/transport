<?php $this->set('subnavcontent', $this->element('roadscholarssubnav')); ?>

<?php if (!$session->check('User.id')) : ?>

<p><strong>Please note: These records reflect workshops taken past February 1st, 2011. To request submission of workshops before that date, <?php echo $html->link('log in', '/users/login/' . base64_encode('/' . $this->params['url']['url'])); ?> or <?php echo $html->link('register', '/users/register'); ?> and request legacy record integration.</strong></p>
	
<?php elseif (isset($selfscholar)) : ?>

	<h2>Your Progress</h2>
	<?php echo $this->element('scholarstable', array('scholars' => $selfscholar)); ?>

<?php else : ?>
	
	<h2>Your Progress</h2>
	<p>It appears as if you haven't taken any workshops yet!</p>

<?php endif; ?>

<?php if (isset($results)) : ?>

	<h2>Search Results for '<?php echo $search_query; ?>'</h2>
	<?php echo $this->element('scholarstable', array('scholars' => $results)); ?>

<?php endif; ?>

<h2>Search All Users</h2>
<?php
	$rows = array(
		'input' => array('label' => 'Username or Name')
	);
	
	echo $this->element('neat-form', array(
		'rows' => $rows,
		'model' => 'RoadScholars',
		'form_opts' => array('action' => 'checkprogress'),
		'end' => 'Search'
	));
?>

<h2>All Users' Progress</h2>

<?php echo $this->element('scholarstable', array('scholars' => $scholars)); ?>

<?php debug ($this->params); ?>
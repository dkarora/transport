<?php
	if (empty($steps))
		$steps = array();
	
	if (empty($step))
		$step = 1;
?>

<div id="steps-wrapper">
	<?php foreach ($steps as $k => $v) : ?>
		<div class="steps-step<?php if ($k == $step - 1) echo ' steps-current-step'; ?>" style="width: <?php echo (100 / sizeof($steps)) ?>%">
			<div class="steps-step-number">Step <?php echo $k + 1; ?></div>
			<div class="steps-step-text"><?php echo $v; ?></div>
		</div>
	<?php endforeach; ?>
	<div class="clearfix" style="height: 0;"></div>
</div>
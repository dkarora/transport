<?php
	$this->set(
		'subnavcontent',
		$this->element(
			'adminsubnav',
			array(
				'merge' => array(
					'Edit Workshop: ' . $workshop['Detail']['name'] => '/' . $this->params['url']['url']
				),
				
				'here' => 'Edit Workshop: ' . $workshop['Detail']['name'], 'position' => 0
			)
		)
	);
	$this->set('bodyClass', 'two-column edit-workshop');
	$baseUrl = Router::url('/flyers/thumbnail/', true);
	$javascript->codeBlock("$(function() { $('#WorkshopFlyerId').change(function() { var base = '$baseUrl'; $('#FlyerThumbnail').attr('src', base + $(this).val()); }) });", array('inline' => false));
?>

<h2>Editing <?php printf('%s (%s)', $html->link($workshop['Detail']['name'], $link->viewWorkshop($workshop)), $timeFormatter->commonDateTime($workshop['Workshop']['date'])); ?></h2>

<div id="left-column">
<div id="left-column-content">
	<h3>Instance Data</h3>
	<?php
		$rows = array(
			'Workshop.id' => array('type' => 'hidden'),
			'Workshop.city' => array(),
			'Workshop.private_cost' => array(),
			'Workshop.public_cost' => array(),
			'Workshop.capacity' => array(),
			'Workshop.location' => array(),
			'Workshop.instructor' => array(),
			'Workshop.notes' => array('type' => 'textarea'),
			'Workshop.unlisted' => array('type' => 'checkbox'),
		);
		
		echo $this->element(
			'neat-form',
			array(
				'model' => 'Workshop',
				'form_opts' => array(
					'url' => '/' . $this->params['url']['url'],
				),
				'rows' => $rows,
				'end' => 'Save',
			)
		);
	?>
</div>
</div>

<div id="right-column">
<div id="right-column-content">
	<h3>Flyer</h3>
	<div id="flyer-preview">
		<?php echo $html->image('/flyers/thumbnail/' . $workshop['Workshop']['flyer_id'], array('id' => 'FlyerThumbnail', 'alt' => 'Flyer thumbnail')); ?>
	</div>
	
	<?php
		$rows = array(
			'Workshop.id' => array('type' => 'hidden', 'id' => 'FlyerWorkshopId'),
			'Workshop.flyer_id' => array('options' => $flyerList, 'id' => 'WorkshopFlyerId'),
		);
		
		echo $this->element(
			'neat-form',
			array(
				'model' => 'Workshop',
				'form_opts' => array(
					'url' => '/' . $this->params['url']['url'],
				),
				'rows' => $rows,
				'end' => 'Save',
			)
		);
	?>
</div>
</div>

<div class="clearfix"></div>
<?php debug ($workshop); ?>
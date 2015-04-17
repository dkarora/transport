<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $html->css('admin-workshops', null, array(), false); ?>
<?php $javascript->link('admin-workshops', false); ?>

<h2>Add Workshop Category</h2>
<?php
	$rows = array('WorkshopCategory.name' => array());
	
	echo $this->element('neat-form',
		array(
			'model' => 'WorkshopCategory',
			'form_opts' => array('action' => 'add'),
			'rows' => $rows,
			'end' => 'Create'
		)
	);
?>

<h2>Upload Workshop Flyer</h2>
<?php
	$rows = array('Flyer.file' => array('type' => 'file'));
	
	echo $this->element('neat-form',
		array(
			'model' => 'Flyer',
			'form_opts' => array('action' => 'upload', 'type' => 'file'),
			'rows' => $rows,
			'end' => 'Upload'
		)
	);
?>

<h2>Add Workshop</h2>
<?php
	if (!empty($addworkshop_categories))
	{
		$rows = array(
			'WorkshopDetail.name' => array(),
			'WorkshopDetail.category_id' => array('options' => $addworkshop_categories, 'empty' => '--'),
			'WorkshopDetail.credits' => array(),
			'WorkshopDetail.ceu_credits' => array(),
			'WorkshopDetail.description' => array('type' => 'textarea')
		);
		
		echo $this->element('neat-form',
			array(
				'model' => 'WorkshopDetail',
				'form_opts' => array('action' => 'add'),
				'rows' => $rows,
				'end' => 'Add'
			)
		);
	}
	else
	{
		echo $html->div('bottom-separator', $html->tag('strong', 'Workshops must be filed into categories. Please create a category above.'));
	}
?>

<h2>Schedule Workshop</h2>
<?php
	if (!empty($workshopnames))
	{
		$rows = array(
			'Workshop Info',
			'Workshop.detail_id' => array('options' => $workshopnames, 'empty' => '--'),
			'Workshop.capacity' => array(),
			'Workshop.public_cost' => array('label' => 'Public Sector Cost (USD)'),
			'Workshop.private_cost' => array('label' => 'Private Sector Cost (USD)'),
			'Workshop.location' => array('type' => 'textarea'),
			'Workshop.city' => array(),
			'Workshop.instructor' => array(),
			'Workshop.flyer_id' => array('options' => $flyers, 'empty' => array(0 => '--'), 'label' => 'Flyer (Optional)'),
			'Workshop.notes' => array('type' => 'textarea', 'label' => 'Notes (optional)'),
			'Workshop.unlisted' => array('type' => 'checkbox', 'label' => 'Unlisted?'),
		);
		
		if ($linkedToTwitter)
			$rows['Social.tweet'] = array('type' => 'checkbox', 'label' => 'Tweet?', 'checked' => 'checked');
			
		$rows = array_merge($rows, array(
			'Agenda',
			"Agenda.0.timestamp" => array(),
			"Agenda.0.description" => array('type' => 'textarea')
		));
		
		$agendaRows = array();
		for ($i = 1; $i <= $numAgendaRows; $i++)
		{
			$agendaRows["Agenda.$i.timestamp"] = array();
			$agendaRows["Agenda.$i.description"] = array('type' => 'textarea');
		}
		
		echo $this->element('neat-form',
			array(
				'model' => 'Workshop',
				'form_opts' => array('action' => 'add'),
				'rows' => array_merge($rows, $agendaRows),
				'end' => 'Schedule'
			)
		);
		
		echo $html->link('[Add Agenda Item]', 'javascript:;', array('id' => 'add-agenda-item-link'));
	}
	else
	{
		echo $html->div('bottom-separator', $html->tag('strong', 'Workshops must be created before one can be scheduled. Please create a workshop above.'));
	}
?>

//<h2>Cancel Workshop</h2>
//<p>Do this at some point!</p>
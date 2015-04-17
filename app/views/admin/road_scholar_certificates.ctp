<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>
<?php $html->css('admin', null, array(), false); ?>
<?php $javascript->link(array('http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js', 'select-all'), false); ?>

<h2>Print Road Scholar Certificates</h2>

<p id="road-scholars-filter-links">
	Filter:
	<?php
		if (!empty($this->params['named']['filter']))
			echo $html->link('No Filter', array('filter' => null));
		else
			echo '<strong>No Filter</strong>';
			
		if (!empty($this->params['named']['filter']) && $this->params['named']['filter'] == 'rs')
			echo '<strong>Road Scholars</strong>';
		else
			echo $html->link('Road Scholars', array('filter' => 'rs'));
		
		if (!empty($this->params['named']['filter']) && $this->params['named']['filter'] == 'mrs')
			echo '<strong>Master Road Scholars</strong>';
		else
			echo $html->link('Master Road Scholars', array('filter' => 'mrs'));
	?>
</p>

<?php if (empty($scholars)) : ?>
	<p>No Road Scholars!</p>
<?php else : ?>

	<?php echo $form->create('RoadScholar', array('action' => 'print_certificates')); ?>
		<table>
			<tr>
				<th>Name</th>
				<th><?php echo $form->checkbox('Opt.selectall', array('id' => 'selectall')); ?> Select</th>
			</tr>
			
			<?php foreach ($scholars as $key => $scholar) : ?>
			<tr>
				<td><?php echo $scholar['User']['full_name']; ?></td>
				<td>
					<?php echo $form->checkbox("RoadScholar.$key.selected", array('class' => 'selectalltarget', 'checked' => false)); ?>
					<?php echo $form->hidden("RoadScholar.$key.user_id", array('value' => $scholar['User']['id'])); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	<?php echo $form->end('Submit'); ?>
<?php endif; ?>

<?php debug ($scholars); ?>

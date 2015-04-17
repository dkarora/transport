<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<?php
	// tell the paginator what the base url is
	$here = '';
	if ($from_letter)
		$here .= $from_letter;
	$paginator->options = array('url' => $here);
?>

<h2>Delete Users</h2>

<div class="paginator">
	<?php
		// common options
		echo $html->tag('span', null, array('class' => 'page-list letter-list'));
		
		// add a clearing tag
		if ($from_letter)
			echo $html->tag('span', $html->link('All', '/admin/delete_users/'));
		else
			echo $html->tag('span', 'All', array('class' => 'current'));
		
		$letter = 'A';
		for ($i = 0; $i <= 25; $i++)
		{
			if ($letter != $from_letter)
				echo $html->tag('span', $html->link($letter . " ", '/admin/delete_users/' . $letter));
			else
				echo $html->tag('span', $letter, array('class' => 'current'));
			$letter++;
		}
		
		// non-letters
		if ($from_letter != 'misc')
			echo $html->tag('span', $html->link('Misc', '/admin/delete_users/' . 'misc'));
		else
			echo $html->tag('span', 'Misc', array('class' => 'current'));
		
		echo $html->tag('/span', null);
	?>
</div>

<?php echo $this->element('page-numbers'); ?>

<table>
	<?php
		$headers = array(
			'Name' => 'User.last_name',
			'Username' => 'User.username',
			'Affiliation' => 'User.affiliation',
			'Assets',
			'Delete',
		);
		
		echo $this->element('table-headers', array('headers' => $headers));
	?>
	
	<?php foreach ($users as $index => $user) : ?>
	<tr <?php if ($index % 2) echo " class='altrow'";?>>
		<td><?php echo $html->link ($user['User']['full_name'], '/users/edit_user/'. $user['User']['id']); ?></td>
		<td><?php echo $user['User']['username']; ?></td>
		<td><?php echo $user['User']['affiliation']; ?></td>
		<td><?php echo $user['User']['asset_summary']; ?></td>
		<td><?php echo $html->link('Delete', '/users/delete/' . $user['User']['id'], array('class' => 'button-link full-width'), 'Are you sure you want to delete ' . $user['User']['full_name'] . ' and all associated assets? This action CANNOT BE UNDONE!'); ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php debug ($users); ?>

<div class="paginator">
	<?php
		// common options
		echo $html->tag('span', null, array('class' => 'page-list letter-list'));
		
		// add a clearing tag
		if ($from_letter)
			echo $html->tag('span', $html->link('All', '/admin/delete_users/'));
		else
			echo $html->tag('span', 'All', array('class' => 'current'));
		
		$letter = 'A';
		for ($i = 0; $i <= 25; $i++)
		{
			if ($letter != $from_letter)
				echo $html->tag('span', $html->link($letter . " ", '/admin/delete_users/' . $letter));
			else
				echo $html->tag('span', $letter, array('class' => 'current'));
			$letter++;
		}
		
		// non-letters
		if ($from_letter != 'misc')
			echo $html->tag('span', $html->link('Misc', '/admin/delete_users/' . 'misc'));
		else
			echo $html->tag('span', 'Misc', array('class' => 'current'));
		
		echo $html->tag('/span', null);
	?>
</div>

<?php echo $this->element('page-numbers'); ?>

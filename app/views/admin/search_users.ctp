<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<?php
  // tell the paginator what the base url is
  $queryString = explode('?', $_SERVER['REQUEST_URI']);
  if (array_key_exists (1, $queryString))
    $paginator->options['url']['?'] = $queryString[1];
?>

<h2>Search Users</h2>
<br/>

<div>
<?php 
echo $form->create (null, array ('type'   => 'get',
                                 'action' => 'search_users/'));
echo $form->input ('part_lastname', array ('label' => 'Partial Last Name: ',
                                           'value' => $part_lastname,
                                           'size'  => '30',
                                           'div'   => false));
echo $form->submit ('Search', array ('div' => false));
?>
</div>
<br/>



<div> <?php echo $this->element('page-numbers'); ?> </div>
<br/>

<div>
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
</div>
<br/>

<div>
<?php echo $this->element('page-numbers'); ?>
</div>

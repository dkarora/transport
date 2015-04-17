<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<?php
  // tell the paginator what the base url is
  $queryString = explode('?', $_SERVER['REQUEST_URI']);
  if (array_key_exists (1, $queryString))
    $paginator->options['url']['?'] = $queryString[1];
?>

<h2>Search Groups</h2>
<br/>

<div>
<?php 
  echo $form->create (null, array ('type'   => 'get',
                                   'action' => 'search/'));
  echo $form->input ('part_groupname', array ('label' => 'Partial Group Name: ',
                                              'value' => $part_groupname,
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
    'Name', 
    'Add Member',
    'Administer',
    );

echo $this->element('table-headers', array('headers' => $headers));
?>

<?php foreach ($groups as $index => $group) : ?>
<tr <?php if ($index % 2) echo " class='altrow'";?>>
<td><?php echo $group['Group']['name']; ?></td>
<td><?php echo $html->link('Add Member', '/groups/add_member/' . $group['Group']['id'], array('class' => 'button-link full-width')); ?></td>
<td><?php echo $html->link('Administer', '/groups/administer/' . $group['Group']['id'], array('class' => 'button-link full-width')); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<br/>

<div>
<?php echo $this->element('page-numbers'); ?>
</div>

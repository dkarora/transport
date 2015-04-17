<?php
  $this->set('subnavcontent', $this->element('personsubnav', array ('user_id', $user_id)));
?>

<?php
// tell the paginator what the base url is
$queryString = explode('?', $_SERVER['REQUEST_URI']);
if (array_key_exists (1, $queryString))
  $paginator->options['url']['?'] = $queryString[1];
?>


<h2> Search Workshop Attended by User </h2>
<br/>

<div>
<?php 
echo $form->create (null, array ('type'   => 'get',
                                 'action' => 'search_workshops/' . $user_id));
echo $form->input ('workshop_name', array ('label' => 'Partial Workshop Name: ',
                                           'value' => $workshop_name,
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
    '#Credits',
    '#CEU Credits',
    'Location',
    'City',
    'Held On',
    'Add',
    );

echo $this->element('table-headers', array('headers' => $headers));
?>

<?php foreach ($workshops as $index => $ws) : ?>
<tr <?php if ($index % 2) echo " class='altrow'";?>>
    <td> <?php echo $ws['Detail']['name']; ?> </td>
    <td> <?php echo $ws['Detail']['credits']; ?> </td>
    <td> <?php echo $ws['Detail']['ceu_credits']; ?> </td>
    <td> <?php echo $ws['Workshop']['location']; ?> </td>
    <td> <?php echo $ws['Workshop']['city']; ?> </td>
    <td> <?php echo $timeFormatter->commonDate ($ws['Workshop']['date']); ?> </td>
    <td><?php echo $html->link('Add', '/users/add_attendee/' . $user_id . '/' . $ws['Workshop']['id'], array('class' => 'button-link full-width')); ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<br/>

<div>
<?php echo $this->element('page-numbers'); ?>
</div>

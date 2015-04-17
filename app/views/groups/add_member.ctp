<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2><?php echo $this->data['Group']['name']; ?></h2>
<br/> <br/>

<h3>Search User </h2>
<br/>


<div>
<?php 
echo $form->create (null, array ('type'   => 'get',
                                 'action' => 'add_member/' . $this->data['Group']['id']));
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
		'Full Name',
		'User name',
		'Email',
		'Add as admin?',
		'Add as member?'
               );
    echo $this->element('table-headers', array('headers' => $headers));

    $rows = array ();
    foreach ($users as $user)
    {
      $rows[] = array ($user['User']['full_name'],
                       $user['User']['username'],
                       $user['User']['email'],
                       $html->link ('Add admin', 
	                            array ('controller' => 'group_members',
	                                   'action'     => 'add',
	                                   $this->data['Group']['id'],
	                                   $user['User']['id'], 'yes' /*is_admin*/)),
                       $html->link ('Add member', 
	                            array ('controller' => 'group_members',
	                                   'action'     => 'add',
	                                   $this->data['Group']['id'],
	                                   $user['User']['id'], 'no' /*is_admin*/)));
    }

    echo $html->tableCells ($rows, null, array ('class' => 'altrow'));
  ?>
</table>
</div>
<br/>

<div>
<?php echo $this->element('page-numbers'); ?>
</div>

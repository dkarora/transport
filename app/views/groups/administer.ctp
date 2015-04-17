<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>


<h2><?php echo $this->data['Group']['name']; ?></h2>

<br/> <br/>

<h3>Group Administrators</h3>
<?php echo $this->element ('memberstable', array ('users' => $admins, 'tableId' => 'admins', 'tableType' => 'admin')); ?>

<br/> <br/> <br/>

<h3>Group Members</h3>
<?php echo $this->element('memberstable', array('users' => $members, 'tableId' => 'users', 'tableType' => 'user')); ?>

<br/> <br/>

<?php $this->set('subnavcontent', $this->element('adminsubnav')); ?>

<h2> Add/Edit Group Information </h2>

<?php 
$rows = array ('Choose a group to rename it or Choose \'Add New Group\' to add a new group',
               'Group.id' => array ('label'   => 'Group',
                                    'empty'   => '-- Add New Group --',
                                    'options' => $groups),
               'Group.name' => array ('type' => 'text',
                                      'size' => '52'));

$fOpts = array ('controller' => 'groups', 'action' => 'add');

echo $this->element ('neat-form',
                     array ('model'    => 'Group',
                            'form_pts' => $fOpts,
                            'rows'     => $rows,
                            'end'      => 'Save'
                            ));
?>

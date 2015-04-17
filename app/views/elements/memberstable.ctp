<?php
  if (empty($users))
  {
    echo $html->tag('strong', 'No users here!');
    return;
  }

  if (!isset($tableType))
  {
    $tableType = 'user';
  }
?>

<table<?php if(isset($tableId)) { echo " id='$tableId'"; } ?>>

  <th>Full name</th>                                                                           
  <th>User name</th>                                                                           
  <th>Email</th>                                                                               
  <th>
    <?php 
      $text = '';
      $action = '';
      if ($tableType == 'admin')
      {
        echo 'Remove Admin';
        $text = 'Remove';
        $action = 'remove_admin';
      }
      else
      {
        echo 'Set Admin';
        $text = 'Set';
        $action = 'set_admin';
      }
    ?>
  </th>
  <th>Remove From Group</th>

<?php
  $rows = array ();
  foreach ($users as $user)
  {
    $rows[] = array ($user['User']['full_name'],
                     $user['User']['username'],
                     $user['User']['email'],
                     $html->link ($text, array ('controller' => 'group_members',
                                                'action'     => $action,
                                                $user['Member']['id'])),
                     $html->link ('Remove', array ('controller' => 'group_members',
                                                   'action'     => 'delete',
                                                   $user['Member']['id'])));
  }
  echo $html->tableCells ($rows, null, array ('class' => 'altrow'));
?>
</table>

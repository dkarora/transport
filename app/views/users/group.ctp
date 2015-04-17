<?php
  $this->set('subnavcontent', $this->element('personsubnav', array ('user_id', $user_id)));
?>

<h2> Group Information </h2>
<h4>
<?php 
  if (!empty($user_group))
  {
    echo '<b>User Group:</b> ' . $user_group['Group']['name'] . '<br/>';
    echo '<b>Group Admins:</b> <br/>';
    foreach ($group_admins as $ga)
      echo $ga['User']['full_name'] . '<br/>';
  }
  else
    echo '<b>User does not belong to any group yet</b>';
?>
</h4>

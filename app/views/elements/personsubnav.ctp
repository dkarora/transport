<?php
  if (!isset($options) || empty($options))
  {
    $options = array(
      'Edit Info'             => '/users/edit/'       . $user_id,
      'Group Info'            => '/users/group/'      . $user_id,
      'Attendance Info'       => '/users/attendance/' . $user_id,
      'Add Attendance Record' => '/users/search_workshops/'  . $user_id,
      );
  }

  $cd = array();

  if (empty($attr))
    $attr = $cd;
  else
    $attr = array_merge ($attr, $cd);

  echo $this->element('subnavgeneric', array('options' => $options, 'attr' => $attr));
?>

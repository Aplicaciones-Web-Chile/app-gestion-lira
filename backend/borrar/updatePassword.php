<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';

//Update password
$db->execute(
  'update apoderado
      set password=password(?)
          where id=?
      ',
  array(
    $_POST['password'], $_POST['id']
  )
);


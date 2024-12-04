<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';


//validamos que exista el rut
$res = $db->getRow("select rut from apoderado where id=?", $_POST['id']);
if ($res['rut']) {
  //ahora a guardar los datos del apoderado
  $db->execute(
    'update apoderado
      set nombre=?, apellidos=?
          where id=?
      ',
    array(
      $_POST['nombre'], $_POST['apellidos'], $_POST['id']
    )
  );
  $resp = 1;

} else {
  //Comprobar que el rut a agregar no esté utilizado por otro apoderado
  $existe = $db->getRow("select rut from apoderado where rut=?", $_POST['rut']);

  if (isset($existe['rut'])) {
    $resp = 0;
  } else {
    //ahora a guardar los datos del apoderado
    $db->execute(
      'update apoderado
          set nombre=?, apellidos=?, rut=?
              where id=?
          ',
      array(
        $_POST['nombre'], $_POST['apellidos'], $_POST['rut'], $_POST['id']
      )
    );
    $resp = 1;

  }
}
echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);

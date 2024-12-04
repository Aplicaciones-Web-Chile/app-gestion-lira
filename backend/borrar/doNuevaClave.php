<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/commonDB.php';

$url = urldecode($_POST['email']);

// $cr = openssl_decrypt( $url, $CRYPT_CYP, $CRYPT_PWD, 0, $CRYPT_IV ) ;

$url = str_replace(' ', '+', $url);

$sqlID = "SELECT * FROM apoderado_cambiar_pass WHERE codigo=?";
$res = $db->GetRow($sqlID, $url);

if ($res && $res['usado'] == 0) {
  $hour =  date("h") + 1;
  $today = date("Y-m-d") . $hour . date(":i:s");
  $fecha_actual = strtotime($today);
  $fecha_entrada = strtotime($res['fecha_limite']);
  
  if ($fecha_actual > $fecha_entrada) {
    exit('Este enlace ya caducó.');
  } else {
    $db->execute(
      'update apoderado
                     set password=password(?)
                   where correo=?',
      array($_POST['password'], $res['correo'])
    );
    if ($db->errorMsg()) {
      exit('Ocurrió un error, por favor vuelte a intentarlo.');
    }
    $campos = array(1, $res['id']);
    $sqlInsert = 'UPDATE apoderado_cambiar_pass 
    set usado=? where id=?';
    $res = $db->execute($sqlInsert, $campos);
    echo 'OK';
  }
} else {
  exit('Este enlace ya fue utilizado.');
}

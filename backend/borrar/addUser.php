<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';


//validamos que no exista el correo...
$rescorreo	=	$db->getAll("select correo from apoderado where correo=?", $_POST['correo']);

if (count($rescorreo))
	exit('{"id" : -1 ,"message" : "Correo ' . $rescorreo[0]['correo'] . ' ya asignado "}');

//ahora a guardar los datos del apoderado
$res = $db->execute(
	'insert into apoderado
							set correo=?, nombre=?, apellidos=?, password=password(?)
							',
	array(
		$_POST['correo'], $_POST['nombre'], $_POST['apellidos'], $_POST['password']
	)
);
if (!$res) {
	exit('{"id" : -1 ,"message" : "No se pudieron guardar los datos. Por favor vuelve a intentarlo"}');
} else {
	exit('{"id" : ' . $db->insert_id() . '}');
}

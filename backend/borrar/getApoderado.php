<?php

	require_once __DIR__.'/common.php';

	if( !isset($_POST['kven']) )
		die();

	require_once __DIR__.'/commonDB.php';
	echo json_encode( $db->getAll("select id, nombre, apellidos, correo, rut, profesion from apoderado where id=?",$_POST),JSON_INVALID_UTF8_IGNORE);
?>

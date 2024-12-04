<?php

	// require_once __DIR__.'/common.php';

	// if( !isset($_POST['kven']) )
	// 	die();

	// require_once __DIR__.'/commonDB.php';
	// $sql = "select
    // hijo.rut rut
    // ,hijo.nombre nombre
    // ,hijo.apellidos apellidos
    // ,hijo.fecha_nacimiento fecha_nacimiento
    // ,hijo.sexo sexo
	// ,hijo.peso peso
	// ,hijo.altura altura
	// ,hijo.alergias alergias
	// from apoderado 
	// join hijo
    // on hijo.apoderado_id = apoderado.id
	// where apoderado.correo=?
	// order by hijo.nombre";
	// echo json_encode( $db->getAll($sql,$_POST),JSON_INVALID_UTF8_IGNORE);




	// NUEVO:

	
	require_once __DIR__.'/common.php';

	if( !isset($_POST['kven']) )
		die();

	require_once __DIR__.'/commonDB.php';
	$sql = "select
    hijo.rut rut
    ,hijo.nombre nombre
    ,hijo.apellidos apellidos
    ,hijo.fecha_nacimiento fecha_nacimiento
    ,hijo.sexo sexo
	,hijo.peso peso
	,hijo.altura altura
	,hijo.alergias alergias
	,hijo.id

	,hijo_imagenes.url

	from apoderado 
	join hijo
    on hijo.apoderado_id = apoderado.id

	left join hijo_imagenes
	on hijo_imagenes.hijo_id=hijo.id

	where apoderado.id=?
	order by hijo.nombre";

	
	echo json_encode( $db->getAll($sql,$_POST),JSON_INVALID_UTF8_IGNORE);

<?php

	require_once __DIR__.'/common.php';

	if( !isset($_POST['kven']) )
		die();

	require_once __DIR__.'/commonDB.php';
	
	$sql = "select
	notas.ID id
    ,notas.comentario comentario
    ,notas.create_time create_time
	from notas 
	, apoderado
    where notas.apoderado_ID = apoderado.id
	AND apoderado.id=?
	order by notas.create_time";

	echo json_encode( $db->getAll($sql,$_POST),JSON_INVALID_UTF8_IGNORE);
?>

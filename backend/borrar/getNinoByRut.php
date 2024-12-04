<?php
	require_once __DIR__.'/common.php';

	$_REQUEST = (isset($_POST) && is_array($_POST)) ) ? $_POST : $_GET;

	if( !isset($_REQUEST['rut']) )
		die();

	require_once __DIR__.'/commonDB.php';
	echo json_encode( $db->getAll("select * from hijo where rut=?",$_REQUEST),JSON_INVALID_UTF8_IGNORE);
?>

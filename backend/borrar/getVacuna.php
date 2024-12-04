<?php

	require_once __DIR__.'/common.php';
	$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

	if( !isset($_REQUEST['kven']) )
		die();

	require_once __DIR__.'/commonDB.php';
	
	// $idHijo = $_REQUEST['kven'];
		$sql = "SELECT
		*
		FROM vacuna 
		WHERE ID_hijo=?
		ORDER BY fecha_inoculacion DESC";

	$res = $db->getAll($sql,array('ID_hijo'=>$_REQUEST['kven']));
	echo json_encode( array_merge( $res ) ,JSON_INVALID_UTF8_IGNORE);
	// echo json_encode( array_merge( $res , array('sql'=> $sql) ) ,JSON_INVALID_UTF8_IGNORE);
	// Borre el sql de momento, porque me estaba dando un error, ya que se mostraba en la tabla junto a los demás datos
<?php
require_once __DIR__.'/common.php';
require_once __DIR__.'/commonDB.php';

// $_REQUEST = (isset($_POST) && is_array($_POST)) ) ? $_POST : $_GET;
$arrayReturn = array();

// die("hola");
$sqlInsert = "INSERT INTO dientes 
				(posicion, ID_hijo, fecha) 
				values 
				('".$_POST['posicion']."','".abs($_POST['ID_hijo'])."','".$_POST['fecha']."') ";
$res = $db->execute($sqlInsert);

if( !$res ){

	exit( '{"id" : -1 ,"message" : "Ocurrió un error, por favor vuelte a intentarlo."}' );

}else{

	$lastId					=	$db->insert_Id();
	$arrayReturn['lastId']	=	$lastId;
	echo json_encode( $arrayReturn,JSON_INVALID_UTF8_IGNORE);

}

?>
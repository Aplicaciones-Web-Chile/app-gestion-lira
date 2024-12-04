<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';

// $_REQUEST = (isset($_REQUEST) && is_array($_REQUEST)) ) ? $_REQUEST : $_GET;
$arrayReturn = array();
$flag			=	true;


/*
actividad	varchar(255)	
fecha	timestamp NULL [0000-00-00 00:00:00]	
animo	varchar(255)	
ID_hijo*/

// file_put_contents('addMomento.php.json', json_encode($_REQUEST));
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

// primero validamos que se pueda crear el directorio para almacenar imagenes
$target_dir = __DIR__ . '/adjuntos/' . 'Momentos/' . $_REQUEST['ID_hijo'];

if( !is_dir($target_dir) && !mkdir( $target_dir ,0777,true ) )
	exit('ERROR: No se puede crear directorio para imagenes !');


$sqlInsert = "INSERT INTO momentos 
				(actividad, tipo, fecha, hasta, animo, ID_hijo ) 
				values 
				('" . $_REQUEST['actividad'] . "','" . $_REQUEST['tipo'] . "','" . $_REQUEST['fecha'] . "','" . $_REQUEST['hasta'] . "','" . $_REQUEST['animo'] . "','" . abs($_REQUEST['ID_hijo']) . "') ";
$res = $db->execute($sqlInsert);

if (!$res) {

	exit('{"id" : -1 ,"message" : "Ocurrió un error, por favor vuelte a intentarlo."}');
} else {

	$lastId					=	$db->insert_Id();
	$arrayReturn['lastId']	=	$lastId;


	if ($flag === true) {
		$fechatemp 	= 	explode(" ",$_REQUEST['fecha']);
		$fecha		=	$fechatemp[0] . '_' . $fechatemp[1];
		$momentos_id =$arrayReturn['lastId'];

		//limpiamos el directorio y almacenamos las nuevas imagenes
		array_map('unlink', glob($target_dir . '/' .$momentos_id .'-' . $fecha));
		foreach ($_REQUEST['userImages'] as $base64) {
			$ext = explode('/', explode(';', $base64)[0])[1];
			$decoded_file = base64_decode(explode(',', explode(';', $base64)[1])[1]);
			if (file_put_contents($target_dir . '/'.$momentos_id .'-' . $fecha . '.' . $ext, $decoded_file) == 0) {
				$arrayReturn['id'] = -1;
				$arrayReturn['message'] = 'ERROR: Guardando imagen';
				$flag = false;
			} else {
				
				$url = 'Momentos/' .$_REQUEST['ID_hijo'] . '/'. $momentos_id .'-' . $fecha . '.' . $ext;
				$sqlInsert = "INSERT INTO momento_imagenes 
					(momentos_id, url ) 
					values 
					('" . $momentos_id . "','" . $url . "') ";
				$res = $db->execute($sqlInsert);
			}
		}
	}

	echo json_encode($arrayReturn, JSON_INVALID_UTF8_IGNORE);
}

<?php
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';

$arrayReturn	=	array();
$flag			=	true;



//apoderado_id
$rutapoderado = $db->getAll("select rut from apoderado where id=?", $_POST['apoderado_id']);
$rutapoderado = implode("", $rutapoderado[0]);

file_put_contents('addHijo.php.json', json_encode($_POST));
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

// primero validamos que se pueda crear el directorio para almacenar imagenes
$target_dir = __DIR__ . '/adjuntos/' . $rutapoderado;


if( !is_dir($target_dir) && !mkdir( $target_dir ,0777,true ) ){
	//exit('ERROR: No se puede crear directorio para imagenes !');
	$arrayReturn['id'] = -1;
	$arrayReturn['message'] = 'ERROR: No se puede crear el directorio para imagenes (problemas de permiso de escritura)';
	$flag = false;
}

if ($flag === true) {
	$res = $db->getAll("select rut from hijo where rut='" . $_REQUEST['rut'] . "'");
	// $id_apoderado = $db->get("select id from apoderado where correo=?",$_POST['correo']);
	if (count($res)) {

		$arrayReturn['id'] = -1;
		$arrayReturn['message'] = 'El RUT ingresado ya existe';
		$flag = false;
	} else {


		/*
					rut	varchar(20)	
					nombre	varchar(100)	
					apellidos	varchar(100)	
					fecha_nacimiento	date	
					sexo	varchar(20)	
					peso	varchar(20) NULL	
					altura	varchar(20) NULL	
					alergias	text NULL	
					apoderado_id	bigint(20)
				*/

		/* Hack mientras desde la APP se envia el apoderado_id */
		/*if(empty($_REQUEST['apoderado_id'])){
					$_REQUEST['apoderado_id'] = 21;
				}*/

		$sqlInsert = "INSERT INTO `hijo` (`rut`, `nombre`, `apellidos`, `fecha_nacimiento`, `sexo`, `peso`, `altura`, `alergias`, `apoderado_id`)
						VALUES ('" . $_REQUEST['rut'] . "', '" . $_REQUEST['nombre'] . "','" . $_REQUEST['apellidos'] . "','" . $_REQUEST['fecha_nacimiento'] . "','" . $_REQUEST['sexo'] . "','" . $_REQUEST['peso'] . "','" . $_REQUEST['altura'] . "','" . $_REQUEST['alergias'] . "','" . abs($_REQUEST['apoderado_id']) . "');";

		$res = $db->execute($sqlInsert);

		if (!$res) {

			exit(json_encode(
				array(
					'id'		=>	-1,
					'message'	=> 	"Ocurrió un error, por favor vuelte a intentarlo.",
					'res'		=>	$res,
					'sql'		=>	$sqlInsert
				)
			));
		} else {

			$lastId					=	$db->insert_Id();
			$arrayReturn['lastId']	=	$lastId;

			//limpiamos el directorio y almacenamos las nuevas imagenes
			array_map('unlink', glob($target_dir . '/' . $_REQUEST['rut']));
			foreach ($_POST['userImages'] as $base64) {
				$ext = explode('/', explode(';', $base64)[0])[1];
				$decoded_file = base64_decode(explode(',', explode(';', $base64)[1])[1]);
				if (file_put_contents($target_dir . '/' . $_REQUEST['rut'] . '.' . $ext, $decoded_file) == 0) {
					$arrayReturn['id'] = -1;
					$arrayReturn['message'] = 'ERROR: Guardando imagen';
					$flag = false;
				} else {
					$hijo_id = $db->insert_id();
					$url = $rutapoderado . '/' . $_POST['rut'] .  '.' . $ext;
					$sqlInsert = "INSERT INTO hijo_imagenes 
						(hijo_id, url ) 
						values 
						('" . $hijo_id . "','" . $url . "') ";
					$res = $db->execute($sqlInsert);
				}
			}

			#echo json_encode( $arrayReturn,JSON_INVALID_UTF8_IGNORE);

		}
	}
}



echo json_encode($arrayReturn, JSON_INVALID_UTF8_IGNORE);

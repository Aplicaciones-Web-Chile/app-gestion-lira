<?php
header('Access-Control-Allow-Origin: *');

	require_once __DIR__.'/common.php';
	require_once __DIR__.'/commonDB.php';

	$arrayReturn	=	array();
	$flag			=	true;

	$hour =  date("h") + 1;
	$today = date("Y-m-d") . $hour . date(":i:s");
	$fecha_actual = strtotime($today);

//apoderado_id
	$rutapoderado = $db->getAll("select rut from apoderado where id=?",$_POST['apoderado_id'] );
	$rutapoderado = implode("",$rutapoderado[0]);
	
	$urlBorrar = $db->getRow("select url from hijo");
    // file_put_contents('addHijo.php.json',json_encode( $_POST ) );
	// $_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

	// primero validamos que se pueda crear el directorio para almacenar imagenes
	$target_dir = __DIR__.'/adjuntos/' . $rutapoderado;
	$borrar 	= __DIR__.'/adjuntos/';
	
	$existeIMG = $db->getRow("select * from hijo_imagenes where hijo_id=?",$_POST['id'] );

	
	if( $flag === true ){

		//limpiamos el directorio y almacenamos las nuevas imagenes
		foreach( $_POST['userImages'] as $base64 ){

			$ext = explode('/', explode(';', $base64 )[0] )[1];
			if($existeIMG){
				array_map( 'unlink', glob( $borrar.'/'.$existeIMG['url']));
			}

			$decoded_file = base64_decode( explode(',', explode(';', $base64 )[1] )[1] );
			if( file_put_contents( $target_dir.'/'. $_POST['rut'] .'_' . $fecha_actual .'.'.$ext , $decoded_file) == 0 ){
				$arrayReturn['id'] = -1;
				$arrayReturn['message'] = 'ERROR: Guardando imagen';
				$flag = false;
			}else {
				
				$url = $rutapoderado . '/' . $_POST['rut'] .'_' . $fecha_actual . '.' . $ext;
				$campos = array($url,$_POST['id']);

				if($existeIMG){
					$sqlInsert = 'UPDATE hijo_imagenes 
					set url=? where hijo_id=?';
					$res = $db->execute($sqlInsert, $campos);
				}
				else{
					$sqlInsert = "INSERT INTO hijo_imagenes 
					(url,hijo_id) 
					values 
					('" . $url . "','" . $_POST['id'] . "') ";
						$res = $db->execute($sqlInsert);
				}
			}
		}
    }

echo json_encode( $arrayReturn,JSON_INVALID_UTF8_IGNORE);
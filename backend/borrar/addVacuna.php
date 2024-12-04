<?php
require_once __DIR__.'/common.php';
require_once __DIR__.'/commonDB.php';

$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

$arrayReturn = array();

/*
	nombre_vacuna	varchar(255)	
	contra_que	varchar(255)	
	edad	int(11)	
	curso	varchar(50) NULL	
	fecha_inoculacion	date	
	ID_hijo
*/
#$sqlInsert = "INSERT INTO vacuna ('ID','nombre_vacuna','contra_que','edad','curso','fecha_inoculacion','ID_hijo') values (NULL, '".$_REQUEST['nombre_vacuna']."','".$_REQUEST['contra_que']."','".abs($_REQUEST['edad'])."','".$_REQUEST['curso']."','".$_REQUEST['fecha_inoculacion']."','".abs($_REQUEST['ID_hijo'])."') ";
$sqlInsert = "INSERT INTO `vacuna` (`nombre_vacuna`, `contra_que`, `observacion`, `fecha_inoculacion`, `ID_hijo`)
VALUES ('".$_REQUEST['nombre_vacuna']."', '".$_REQUEST['contra_que']."','".$_REQUEST['observacion']."','".$_REQUEST['fecha_inoculacion']."','".abs($_REQUEST['ID_hijo'])."');";
$res = $db->execute($sqlInsert);

if( !$res ){

	exit( json_encode(	array(
								'id'		=>	-1,
								'message'	=>	"Ocurrió un error, por favor vuelte a intentarlo.",
								'sql'		=>	$sqlInsert
							 )
					)
		);

}else{

	$lastId					=	$db->insert_Id();
	$arrayReturn['lastId']	=	$lastId;
	echo json_encode( $arrayReturn,JSON_INVALID_UTF8_IGNORE);

}
// https://appediatra.aplicacionesweb.dev/backend/addVacuna.php?nombre_vacuna=VacunaTest&contra_que=ResfrioComun&edad=5&curso=Kinder&fecha_inoculacion=2021-06-18&ID_hijo=6
?>
<?php
require_once __DIR__.'/common.php';
require_once __DIR__.'/commonDB.php';

$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

$arrayReturn = array();

$sqlInsert = "INSERT INTO `vacuna_minsal` (`id_vacuna`, `id_hijo`, `tipo`)
VALUES ('".abs($_REQUEST['id_vacuna'])."','".abs($_REQUEST['id_hijo'])."','".$_REQUEST['tipo_vacuna']."');";
$res = $db->execute($sqlInsert);

if( !$res ){

	exit( json_encode(	array(
								'id'		=>	-1,
								'message'	=>	"Ocurrió un error al intentar registrar esta vacuna, por favor vuelte a intentarlo.",
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
<?php
require_once __DIR__.'/common.php';
require_once __DIR__.'/commonDB.php';

$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

$arrayReturn = array();

/*
	que_mediste	varchar(255)	
	medida	varchar(255)	
	edad	int
	mes		int	
	ID_hijo	bigint(20)
*/

$sqlInsert = "INSERT INTO `crecimiento` (`que_mediste`, `medida`, `edad`, `mes`, `ID_hijo`)
VALUES ('".$_REQUEST['que_mediste']."', '".$_REQUEST['medida']."','".$_REQUEST['edad']."', '".$_REQUEST['mes']."','".abs($_REQUEST['ID_hijo'])."');";
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
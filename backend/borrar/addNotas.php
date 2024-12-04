<?php
require_once __DIR__.'/common.php';
require_once __DIR__.'/commonDB.php';


#file_put_contents('filename.txt', print_r($_POST, true));

$arrayReturn = array();


$table	=	'notas';

$SQL 	=	"SELECT ID FROM $table ORDER BY ID DESC LIMIT 1";
$lastID	=	$db->getOne($SQL);

$record = array();
$record['ID']			=	$lastID+1;
$record['comentario']	=	$_POST['comentario'];
$record['create_time']	=	$_POST['create_time'];
$record['creado_por']	=	$_POST['creado_por'];
$record['ID_creador']	=	$_POST['ID_creador'];
$record['apoderado_ID']	=	$_POST['apoderado_ID'];

$res = $db->autoExecute($table,$record,'INSERT');

$arrayReturn = array('res' => $res, 'data' => $record);



// https://appediatra.aplicacionesweb.dev/backend/addNotas.php?comentario=prueba+I&create_time=2021-06-18&creado_por=Apoderado&ID_creador=16&apoderado_ID=16

echo json_encode( $arrayReturn,JSON_INVALID_UTF8_IGNORE);
?>
<?php
/*

Filtros: 
- Por fecha
- id hijo 
- tipo (comida baño sueño)
*/
require_once __DIR__.'/common.php';
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

if( !isset($_REQUEST['kven']) )
	die();

require_once __DIR__.'/commonDB.php';

$sql = "SELECT momentos.*,momento_imagenes.url
		  FROM momentos
		  LEFT JOIN momento_imagenes
			on momento_imagenes.momentos_id=momentos.id
		 WHERE momentos.ID_hijo=?
		   AND momentos.tipo=?
		   AND DATE_FORMAT(momentos.fecha, \"%Y-%m-%d\")=?
		 ORDER BY momentos.fecha DESC";
#die($sql);

$res = $db->getAll($sql,$_REQUEST);

echo json_encode( $res , JSON_INVALID_UTF8_IGNORE );
#echo json_encode( array_merge( $res , array('sql'=> $sql) ) ,JSON_INVALID_UTF8_IGNORE);
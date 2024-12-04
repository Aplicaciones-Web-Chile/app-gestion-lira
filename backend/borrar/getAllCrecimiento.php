<?php

require_once __DIR__.'/common.php';
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

if( !isset($_REQUEST['kven']) )
	die();

require_once __DIR__.'/commonDB.php';

$sql = "SELECT
medida, MONTH(fecha) as fecha_mes
FROM crecimiento 
WHERE ID_hijo=?
AND DATE_FORMAT(fecha, \"%Y\") = '2021'
ORDER BY fecha ASC";
$res = $db->getAll($sql,$_REQUEST);

$arrayMes = array();
for($i=1; $i<=12; $i++){ 
	$arrayMes[$i] = 0;
	foreach($res as $r){
		if($r['fecha_mes'] == $i){
			$arrayMes[$i] = $r['medida'];
		}
	}
}
echo json_encode( array_merge( $arrayMes ) ,JSON_INVALID_UTF8_IGNORE);
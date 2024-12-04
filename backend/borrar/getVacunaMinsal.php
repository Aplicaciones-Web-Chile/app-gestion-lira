<?php

require_once __DIR__ . '/common.php';
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

if (!isset($_REQUEST['kven']))
	die();

require_once __DIR__ . '/commonDB.php';

// $idHijo = $_REQUEST['kven'];
$sql = "SELECT
		*
		FROM vacuna_minsal
		WHERE id_hijo=?
		ORDER BY id_vacuna ASC";

$res = $db->getAll($sql, array('id_hijo' => $_REQUEST['kven']));


$arrayVacunas = array();
for ($i = 1; $i <= 19; $i++) {
	$arrayVacunas[$i] = 0;
	foreach ($res as $r) {
		if ($r['id_vacuna'] == $i) {
			$arrayVacunas[$i] = $r;
		}
	}
}

echo json_encode(array_merge($arrayVacunas), JSON_INVALID_UTF8_IGNORE);
	// echo json_encode( array_merge( $res , array('sql'=> $sql) ) ,JSON_INVALID_UTF8_IGNORE);
	// Borre el sql de momento, porque me estaba dando un error, ya que se mostraba en la tabla junto a los demás datos
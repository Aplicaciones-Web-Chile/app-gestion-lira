<?php

require_once __DIR__ . '/common.php';

if (!isset($_POST['kven']))
	die();

require_once __DIR__ . '/commonDB.php';

$sqlID = "SELECT id FROM hijo WHERE rut=?";
$id_hijo = $db->GetRow($sqlID, $_POST);

$campos = array(
	"id" =>  $id_hijo['id'],
	"rut" =>  $_POST['kven']
);

$sql =
	"SELECT hijo.*, hijo_imagenes.url
	FROM hijo 
	LEFT JOIN hijo_imagenes
	ON hijo_imagenes.hijo_id=?
	WHERE rut=?";

$res = $db->getAll($sql, $campos);

echo json_encode($res, JSON_INVALID_UTF8_IGNORE);

<?php

require_once __DIR__.'/common.php';
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

if( !isset($_REQUEST['kven']) )
	die();

require_once __DIR__.'/commonDB.php';

$sql = "SELECT
url
FROM momento_imagenes 
WHERE momentos_id=?";
$res = $db->getAll($sql,$_REQUEST);

echo json_encode( array_merge( $res ) ,JSON_INVALID_UTF8_IGNORE);
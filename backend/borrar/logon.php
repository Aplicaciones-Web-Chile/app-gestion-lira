<?php

require_once __DIR__ . '/common.php';

isset($_POST['kusr']) || die();
require_once __DIR__ . '/commonDB.php';

$data = $db->getAll("select id from apoderado where correo=? and password=password(?)", array_values($_POST));
if (count($data) == 0) {
	$data = $db->getAll("select id from apoderado where rut=? and password=password(?)", array_values($_POST));
	if(count($data) == 0){
		$resp[0]['resp'] = 'NO';
	}
	else{
		$resp[0]['resp'] = 'OK';
		$resp[0]['id'] = $data[0]['id'];
	}
}else{
	$resp[0]['resp'] = 'OK';
	$resp[0]['id']	 =	$data[0]['id'];
}
	echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);

<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';


#file_put_contents('filename.txt', print_r($_POST, true));
$flag            =   true;
$arrayReturn    =   array();


// file_put_contents('addMomento.php.json', json_encode($_REQUEST));
$_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;


// primero validamos que se pueda crear el directorio para almacenar imagenes
$target_dir = __DIR__ . '/adjuntos/' . 'Recuerdos/' . $_REQUEST['id_hijo'];

$fechatemp     =     explode(" ", $_REQUEST['fecha']);
$fecha        =    $fechatemp[0] . '_' . $fechatemp[1];


if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true))
    exit('ERROR: No se puede crear directorio para imagenes !');

if ($flag === true) {

    //limpiamos el directorio y almacenamos las nuevas imagenes 
    // array_map('unlink', glob($target_dir . '/110_.png')); PARA BORRAR ARCHIVOS

    foreach ($_REQUEST['userImages'] as $base64) {
        $ext = explode('/', explode(';', $base64)[0])[1];
        $decoded_file = base64_decode(explode(',', explode(';', $base64)[1])[1]);
        if (file_put_contents($target_dir . '/' . $_REQUEST['id_hijo'] . '_' . $fecha . '.' . $ext, $decoded_file) == 0) {
            $arrayReturn['id'] = -1;
            $arrayReturn['message'] = 'ERROR: Guardando imagen';
            $flag = false;
        } else {

            $url = 'Recuerdos/' . $_REQUEST['id_hijo'] . '/' . $_REQUEST['id_hijo'] . '_' . $fecha . '.' . $ext;
            $sqlInsert = "INSERT INTO fotos_recuerdos 
        (url,fecha,id_hijo) 
        values 
        ('" . $url . "','" . $_REQUEST['fecha'] . "','" . $_REQUEST['id_hijo'] . "') ";
            $res = $db->execute($sqlInsert);


            if (!$res) {
                exit('{"id" : -1 ,"message" : "Ocurrió un error, por favor vuelte a intentarlo."}');
            } else {
                $lastId                    =    $db->insert_Id();
                $arrayReturn['lastId']    =    $lastId;
            }
        }
    }
}


// $arrayReturn = array('res' => $res);


echo json_encode($arrayReturn, JSON_INVALID_UTF8_IGNORE);

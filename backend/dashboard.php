<?php
// Configurar headers CORS
header('Access-Control-Allow-Origin: http://localhost:8080');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400'); // 24 horas

// Si es una petición OPTIONS, terminar aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include_once(__DIR__.'/common.php');
include_once(__DIR__.'/api.php');

if (!isset($_POST['peticion'])) {
    die( json_encode(['error' => 'No se especificó la petición.']) );
}
$response = requestApi([
    'metodo' => $_POST['peticion'],
    /*
    'body'   => [ 
        'Distribuidor' => '001',
    ],
    */
    'body'=>$_POST
]);
echo json_encode($response, JSON_INVALID_UTF8_IGNORE);

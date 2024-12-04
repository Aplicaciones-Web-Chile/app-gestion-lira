
<?php

include_once(__DIR__.'/common.php');
include_once(__DIR__.'/api.php');

// Verificar que la petición contiene los datos necesarios
if (!isset($_POST['peticion'])) {
    echo json_encode(['error' => 'No se especificó la petición.']);
    exit;
}

// Obtener el tipo de petición
$peticion = $_POST['peticion'];

// Configurar el cuerpo de la solicitud al API
$requestBody = [
    'Distribuidor' => '001',
    'peticion' => $peticion,
];

// Llamar a la función para consumir el API
$response = requestApi([
    'metodo' => $peticion,
    'body' => $requestBody,
]);

// Validar la respuesta y devolverla al cliente
if ($response) {
    echo json_encode($response, JSON_INVALID_UTF8_IGNORE);
} else {
    echo json_encode(['error' => 'No se pudo obtener los datos.']);
}

?>

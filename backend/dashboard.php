<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/api.php';

$body = read_json_body();

if (!isset($body['peticion'])) {
    echo json_encode(['error' => 'No se especificó la petición.']);
    exit;
}

$response = requestApi([
    'metodo' => $body['peticion'],
    'body' => $body,
]);

echo json_encode($response, JSON_INVALID_UTF8_IGNORE);

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('session.use_strict_mode', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// --- CORS ---
$allowed_origins = [
    'https://appgestion.distribuidoralira.cl',
    'https://backend-lira-app-gestion.aplicacionesweb.cl',
    'http://localhost:8080',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Vary: Origin');
} else {
    // Si quieres permitir sin credenciales para cualquier origen:
    // header('Access-Control-Allow-Origin: *');
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
$reqHeaders = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? 'Content-Type, Authorization';
header("Access-Control-Allow-Headers: $reqHeaders");
header('Access-Control-Max-Age: 86400');
// Si vas a usar cookies/sesión entre dominios, descomenta:
// header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// --- Cache ---
header('Expires: Wed, 16 Aug 1972 04:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// --- Body helper (NO pisar $_POST global)
function read_json_body(): array
{
    $ctype = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    if (stripos($ctype, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
    return $_POST; // compatibilidad si llega como form
}

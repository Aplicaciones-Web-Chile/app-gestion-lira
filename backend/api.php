<?php
/**
 * Manejador de Solicitudes API
 * 
 * Punto de entrada principal para las solicitudes API. Maneja autenticación,
 * límite de tasa y reenvía solicitudes al servidor API principal.
 * 
 * @package    AppGestion
 * @subpackage API
 * @author     Equipo de Desarrollo
 * @version    1.0.0
 */

require_once __DIR__ . '/src/ErrorHandler.php';
require_once __DIR__ . '/src/RateLimiter.php';

use AppGestion\ErrorHandler;
use AppGestion\RateLimiter;

// Cargar configuración
$config = require_once __DIR__ . '/config/config.php';

// Inicializar manejador de errores
$errorHandler = ErrorHandler::getInstance($config);

// Inicializar limitador de tasa
$rateLimiter = new RateLimiter($config);

/**
 * Realizar solicitud a servicio API externo
 * 
 * @param array $request Parámetros de la solicitud
 * @return mixed Respuesta de la API
 * @throws Exception Si la solicitud falla
 */
function requestApi($request) {
    global $config, $rateLimiter;
    
    // Obtener identificador del cliente (dirección IP o clave API)
    $clientId = $_SERVER['REMOTE_ADDR'];
    
    // Verificar límite de tasa
    if (!$rateLimiter->shouldAllowRequest($clientId)) {
        http_response_code(429);
        $rateLimitInfo = $rateLimiter->getRateLimitInfo($clientId);
        die(json_encode([
            'error' => 'Límite de solicitudes excedido',
            'limite_tasa' => $rateLimitInfo
        ]));
    }
    
    // Construir URL de solicitud
    $url = $config['api']['server'] . $config['api']['base_path'] . $request['metodo'];
    
    // Inicializar cURL
    $ch = curl_init($url);
    if ($ch === false) {
        throw new Exception('Error al inicializar cURL');
    }
    
    // Configurar opciones de cURL
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $config['api']['timeout'],
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => true,  // Habilitar verificación SSL
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($request['body'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ]);
    
    // Configurar headers
    $headers = [
        'Authorization: ' . $config['api']['auth_token'],
        'Distribuidor: ' . $config['api']['distribuidor'],
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Ejecutar solicitud
    $response = curl_exec($ch);
    
    // Verificar errores de cURL
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Error de cURL: $error");
    }
    
    // Obtener código de estado HTTP
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Agregar headers de límite de tasa a la respuesta
    $rateLimitInfo = $rateLimiter->getRateLimitInfo($clientId);
    header('X-RateLimit-Limit: ' . $rateLimitInfo['limite']);
    header('X-RateLimit-Remaining: ' . $rateLimitInfo['restante']);
    header('X-RateLimit-Reset: ' . $rateLimitInfo['reinicio']);
    
    // Manejar respuesta
    if ($statusCode === 200) {
        $data = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Respuesta JSON inválida de la API');
        }
        return $data;
    } else {
        throw new Exception("Error de API: HTTP $statusCode");
    }
}

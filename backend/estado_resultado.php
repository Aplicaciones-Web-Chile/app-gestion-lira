<?php
/**
 * Script para consumir y devolver un archivo JSON.
 * 
 * Este archivo PHP está diseñado para consumir un archivo JSON almacenado localmente y devolver su contenido en formato JSON.
 * 
 * @author [Tu nombre]
 * @version 1.0
 */

// Establecer el header para indicar que se devolverá contenido JSON
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Configuración de la ruta del archivo JSON
define('JSON_FILE_PATH', __DIR__ . '~public/saldo_output_fixed.json'); // Cambia esta ruta según la ubicación correcta

// Verificar si el archivo JSON existe
if (file_exists(JSON_FILE_PATH)) {
    // Leer el contenido del archivo JSON y devolverlo como respuesta
    echo file_get_contents(JSON_FILE_PATH);
} else {
    // En caso de error, devolver un mensaje JSON con el error
    echo json_encode([
        'status' => 'error',
        'message' => 'Archivo JSON no encontrado'
    ]);
}

?>

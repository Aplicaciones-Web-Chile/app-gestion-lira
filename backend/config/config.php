<?php
/**
 * Archivo de Configuración
 * 
 * Este archivo contiene todas las configuraciones para la API
 * 
 * @package    AppGestionLira
 * @subpackage Configuracion
 * @author     AplicacionesWeb
 * @version    1.0.0
 */

return [
    'app' => [
        'name' => 'App de gestión Distribuidora Lira',
        'version' => '1.0.0'
    ],
    'api' => [
        'server' => 'https://api2.aplicacionesweb.cl',
        'base_path' => '/apilira-gestion/dashboard/',
        'timeout' => 60,
        'auth_token' => '94ec33d0d75949c298f47adaa78928c2',
        'distribuidor' => '001'
    ],
    'rate_limit' => [
        'enabled' => true,
        'max_requests' => 100,        // Máximo de solicitudes por ventana de tiempo
        'time_window' => 3600,        // Ventana de tiempo en segundos (1 hora)
        'storage_path' => __DIR__ . '/../storage/rate_limit/'
    ],
    'error_reporting' => [
        'display_errors' => true,     // Establecer en false en producción
        'log_errors' => true,
        'error_log_path' => __DIR__ . '/../storage/logs/error.log'
    ]
];

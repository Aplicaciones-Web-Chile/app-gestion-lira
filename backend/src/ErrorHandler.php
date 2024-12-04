<?php
/**
 * Clase Manejadora de Errores
 * 
 * Proporciona funcionalidad centralizada para el manejo de errores en la API
 * 
 * @package    AppGestion
 * @subpackage ManejoErrores
 * @author     Equipo de Desarrollo
 * @version    1.0.0
 */

namespace AppGestion;

class ErrorHandler {
    private static $instance = null;
    private $config;
    
    /**
     * Constructor
     * 
     * @param array $config Arreglo de configuración
     */
    private function __construct($config) {
        $this->config = $config;
        $this->initializeErrorHandling();
    }
    
    /**
     * Obtener instancia singleton
     * 
     * @param array $config Arreglo de configuración
     * @return ErrorHandler
     */
    public static function getInstance($config = null) {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    
    /**
     * Inicializar manejo de errores
     */
    private function initializeErrorHandling() {
        if ($this->config['error_reporting']['log_errors']) {
            ini_set('log_errors', 1);
            ini_set('error_log', $this->config['error_reporting']['error_log_path']);
        }
        
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }
    
    /**
     * Manejar errores de PHP
     * 
     * @param int    $errno   Número de error
     * @param string $errstr  Mensaje de error
     * @param string $errfile Archivo donde ocurrió el error
     * @param int    $errline Línea donde ocurrió el error
     */
    public function handleError($errno, $errstr, $errfile, $errline) {
        $error = [
            'tipo' => 'Error',
            'mensaje' => $errstr,
            'archivo' => $errfile,
            'linea' => $errline,
            'fecha' => date('Y-m-d H:i:s')
        ];
        
        $this->logError($error);
        
        if ($this->config['error_reporting']['display_errors']) {
            $this->outputError($error);
        } else {
            $this->outputError(['mensaje' => 'Ocurrió un error interno']);
        }
    }
    
    /**
     * Manejar excepciones no capturadas
     * 
     * @param \Throwable $exception El objeto de la excepción
     */
    public function handleException($exception) {
        $error = [
            'tipo' => 'Excepción',
            'mensaje' => $exception->getMessage(),
            'archivo' => $exception->getFile(),
            'linea' => $exception->getLine(),
            'traza' => $exception->getTraceAsString(),
            'fecha' => date('Y-m-d H:i:s')
        ];
        
        $this->logError($error);
        
        if ($this->config['error_reporting']['display_errors']) {
            $this->outputError($error);
        } else {
            $this->outputError(['mensaje' => 'Ocurrió un error interno']);
        }
    }
    
    /**
     * Registrar error en archivo
     * 
     * @param array $error Información del error
     */
    private function logError($error) {
        if ($this->config['error_reporting']['log_errors']) {
            $logMessage = sprintf(
                "[%s] %s: %s en %s en la línea %d\n",
                $error['fecha'],
                $error['tipo'],
                $error['mensaje'],
                $error['archivo'],
                $error['linea']
            );
            
            error_log($logMessage);
        }
    }
    
    /**
     * Enviar error como respuesta JSON
     * 
     * @param array $error Información del error
     */
    private function outputError($error) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'estado' => 'error',
            'error' => $error
        ]);
        exit;
    }
}

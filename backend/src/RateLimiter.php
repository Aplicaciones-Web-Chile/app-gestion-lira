<?php
/**
 * Clase Limitadora de Tasa
 * 
 * Implementa la funcionalidad de límite de tasa para proteger la API contra abusos
 * 
 * @package    AppGestion
 * @subpackage Seguridad
 * @author     Equipo de Desarrollo
 * @version    1.0.0
 */

namespace AppGestion;

class RateLimiter {
    private $config;
    private $storage_path;
    
    /**
     * Constructor
     * 
     * @param array $config Arreglo de configuración
     */
    public function __construct($config) {
        $this->config = $config['rate_limit'];
        $this->storage_path = $this->config['storage_path'];
        
        if (!file_exists($this->storage_path)) {
            mkdir($this->storage_path, 0755, true);
        }
    }
    
    /**
     * Verificar si se debe permitir la solicitud
     * 
     * @param string $identifier Identificador único para el cliente (dirección IP o clave API)
     * @return bool Verdadero si se debe permitir la solicitud, falso si se debe bloquear
     */
    public function shouldAllowRequest($identifier) {
        if (!$this->config['enabled']) {
            return true;
        }
        
        $file = $this->storage_path . md5($identifier) . '.json';
        $current_time = time();
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            
            // Limpiar solicitudes antiguas
            $data['requests'] = array_filter($data['requests'], function($timestamp) use ($current_time) {
                return $timestamp > ($current_time - $this->config['time_window']);
            });
            
            if (count($data['requests']) >= $this->config['max_requests']) {
                return false;
            }
            
            $data['requests'][] = $current_time;
        } else {
            $data = ['requests' => [$current_time]];
        }
        
        file_put_contents($file, json_encode($data));
        return true;
    }
    
    /**
     * Obtener información del límite de tasa para un cliente
     * 
     * @param string $identifier Identificador único para el cliente
     * @return array Información sobre las solicitudes restantes
     */
    public function getRateLimitInfo($identifier) {
        $file = $this->storage_path . md5($identifier) . '.json';
        $current_time = time();
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            $data['requests'] = array_filter($data['requests'], function($timestamp) use ($current_time) {
                return $timestamp > ($current_time - $this->config['time_window']);
            });
            
            $remaining = $this->config['max_requests'] - count($data['requests']);
            $reset = $current_time + $this->config['time_window'];
        } else {
            $remaining = $this->config['max_requests'];
            $reset = $current_time + $this->config['time_window'];
        }
        
        return [
            'limite' => $this->config['max_requests'],
            'restante' => $remaining,
            'reinicio' => $reset
        ];
    }
}

/**
 * Servicio de caché para almacenar datos en localStorage
 * Incluye manejo de expiración y validación de datos
 */

const CACHE_PREFIX = 'lira_cache_';
const DEFAULT_EXPIRY = 24 * 60 * 60 * 1000; // 24 horas en milisegundos

export default {
  /**
   * Guarda datos en el caché con tiempo de expiración
   * @param {string} key - Clave para identificar los datos
   * @param {any} data - Datos a almacenar
   * @param {number} [expiryMs] - Tiempo de expiración en milisegundos
   */
  set(key, data, expiryMs = DEFAULT_EXPIRY) {
    const item = {
      data,
      timestamp: Date.now(),
      expiry: expiryMs
    };
    localStorage.setItem(CACHE_PREFIX + key, JSON.stringify(item));
  },

  /**
   * Obtiene datos del caché si son válidos
   * @param {string} key - Clave de los datos
   * @returns {any|null} Datos almacenados o null si no existen o expiraron
   */
  get(key) {
    const item = localStorage.getItem(CACHE_PREFIX + key);
    if (!item) return null;

    const { data, timestamp, expiry } = JSON.parse(item);
    if (Date.now() - timestamp > expiry) {
      this.remove(key);
      return null;
    }

    return data;
  },

  /**
   * Elimina datos del caché
   * @param {string} key - Clave a eliminar
   */
  remove(key) {
    localStorage.removeItem(CACHE_PREFIX + key);
  },

  /**
   * Genera una clave única para el caché basada en los parámetros
   * @param {string} prefix - Prefijo para la clave
   * @param {Object} params - Parámetros para generar la clave
   * @returns {string} Clave única
   */
  generateKey(prefix, params) {
    const sortedParams = Object.keys(params)
      .sort()
      .map(key => `${key}:${params[key]}`)
      .join('|');
    return `${prefix}_${sortedParams}`;
  }
};

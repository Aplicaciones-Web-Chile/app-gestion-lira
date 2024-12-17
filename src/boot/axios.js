import Vue from 'vue'
import axios from 'axios'

// Crear una instancia de axios con la configuración personalizada
const axiosInstance = axios.create({
  withCredentials: false,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Configurar el interceptor de solicitud
axiosInstance.interceptors.request.use(
  config => {
    // No enviar petición OPTIONS duplicada
    if (config.method === 'options') {
      return Promise.reject('Cancelando petición OPTIONS duplicada');
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Asignar la instancia personalizada a Vue
Vue.prototype.$axios = axiosInstance
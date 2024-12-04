<!-- 
  Error404.vue
  Página de error 404 (No encontrado)
  
  Este componente muestra una página de error amigable cuando se intenta acceder
  a una ruta que no existe en la aplicación. Incluye:
  - Número de error grande y visible
  - Mensaje explicativo
  - Botón para volver al inicio
-->
<template>
  <!-- Contenedor principal a pantalla completa -->
  <div class="error-page fullscreen flex flex-center">
    <div class="text-center">
      <!-- Número de error -->
      <div class="error-code">404</div>

      <!-- Mensaje de error -->
      <div class="error-message text-h2">
        Página no encontrada
      </div>

      <!-- Botón de retorno -->
      <q-btn
        class="q-mt-xl home-button"
        color="primary"
        to="/"
        label="Volver al Inicio"
        no-caps
        unelevated
      />
    </div>
  </div>
</template>

<script>
/**
 * Componente Error404
 * Maneja la visualización de la página de error 404
 */
export default {
  name: 'Error404'
}
</script>

<style lang="scss">
.error-page {
  background: linear-gradient(145deg, #0288d1 0%, #26c6da 100%);
  color: white;

  .error-code {
    font-size: 30vh;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
  }

  .error-message {
    opacity: 0.8;
    margin: 1rem 0;
  }

  .home-button {
    background: white;
    color: #0288d1;
    font-weight: 500;
    padding: 8px 24px;
    
    &:hover {
      background: #f5f5f5;
    }
  }
}
</style>

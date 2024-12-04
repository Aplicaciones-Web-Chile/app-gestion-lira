<!-- 
  logout.vue
  Componente de cierre de sesión
  
  Este componente maneja el proceso de cierre de sesión del usuario.
  Realiza las siguientes acciones:
  1. Limpia el token de autenticación del localStorage
  2. Redirige al usuario a la página de login
  3. Muestra una animación de carga mientras se procesa el logout
-->
<template>
  <div class="logout-page fullscreen flex flex-center">
    <div class="text-center">
      <q-spinner
        color="primary"
        size="3em"
        :thickness="5"
      />
      <div class="text-h6 q-mt-md">
        Cerrando sesión...
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Componente Logout
 * Gestiona el proceso de cierre de sesión de la aplicación
 */
export default {
  name: 'LogoutPage',

  /**
   * Al montar el componente, ejecuta automáticamente el proceso de logout
   */
  mounted() {
    this.onLogout();
  },

  methods: {
    /**
     * Realiza el proceso de cierre de sesión
     * - Elimina el token de autenticación
     * - Redirige al usuario a la página de login
     */
    onLogout() {
      // Limpiar datos de autenticación
      localStorage.removeItem("authToken");
      
      // Pequeño delay para mostrar la animación
      setTimeout(() => {
        this.$router.push("/login");
      }, 1000);
    },
  },
};
</script>

<style lang="scss">
.logout-page {
  background: #f5f6fa;

  .text-h6 {
    color: #666;
    margin-top: 1rem;
  }
}
</style>

<!--
  login.vue
  Página de inicio de sesión

  Este componente maneja la autenticación de usuarios en la aplicación.
  Características principales:
  - Formulario de login con validación
  - Manejo de errores con mensajes al usuario
  - Almacenamiento del token de autenticación
  - Redirección al dashboard después del login exitoso
-->
<template>
  <div class="login-container">
    <q-card class="login-card">
      <!-- Logo de la empresa -->
      <div class="text-center q-mb-lg">
        <img src="~assets/logo-lira.png" width="150" alt="Logo Lira" class="logo" />
      </div>

      <!-- Formulario de login -->
      <q-form @submit.prevent="onLogin" class="q-gutter-md">
        <q-input
          v-model="email"
          label="Usuario"
          :rules="[val => !!val || 'El usuario es requerido']"
          outlined
          class="q-mb-md"
        >
          <template v-slot:prepend>
            <q-icon name="person" />
          </template>
        </q-input>

        <q-input
          v-model="password"
          type="password"
          label="Contraseña"
          :rules="[val => !!val || 'La contraseña es requerida']"
          outlined
          class="q-mb-lg"
        >
          <template v-slot:prepend>
            <q-icon name="lock" />
          </template>
        </q-input>

        <!-- Botón de login -->
        <q-btn
          type="submit"
          label="Iniciar Sesión"
          color="primary"
          class="full-width"
          :loading="loading"
        />
      </q-form>
    </q-card>

    <!-- Diálogo de notificaciones -->
    <q-dialog v-model="showDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">{{ alertMessage }}</div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Cerrar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import Const from '../assets/const.js'

/**
 * Componente Login
 * Maneja la autenticación de usuarios en la aplicación
 */
export default {
  name: 'LoginPage',

  data() {
    return {
      email: '',
      password: '',
      alertMessage: '',
      showDialog: false,
      loading: false,
      // Lista de usuarios autorizados
      users: [
        { email: 'javier', password: '123123' },
        { email: 'Lira', password: 'Lira2024' }
      ]
    }
  },

  methods: {
    /**
     * Maneja el proceso de inicio de sesión
     * Valida las credenciales y redirige al dashboard si son correctas
     */
    onLogin() {
      this.loading = true;

      // Buscar usuario con las credenciales proporcionadas
      const user = this.users.find(
        user => user.email === this.email && user.password === this.password
      );

      if (user) {
        // Credenciales válidas
        const authToken = this.generateAuthToken();
        localStorage.setItem('authToken', authToken);

        this.alertMessage = 'Iniciando sesión...';
        this.showDialog = true;

        // Redirigir al dashboard después de un breve delay
        setTimeout(() => {
          this.loading = false;
          this.$router.push('/dashboard');
        }, 1000);
      } else {
        // Credenciales inválidas
        this.loading = false;
        this.alertMessage = 'Usuario o contraseña incorrectos';
        this.showDialog = true;
      }
    },

    /**
     * Genera un token de autenticación simple
     * TODO: Implementar generación de token más segura
     */
    generateAuthToken() {
      return Math.random().toString(36).substring(2) + Date.now().toString(36);
    }
  }
}
</script>

<style lang="scss">
.login-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #0288d1 0%, #26c6da 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  padding: 2rem;
  border-radius: 12px;

  .logo {
    max-width: 150px;
    height: auto;
    margin-bottom: 2rem;
  }

  .q-input {
    .q-field__control {
      height: 56px;
    }

    .q-field__marginal {
      height: 56px;
    }
  }

  .q-btn {
    height: 44px;
    font-size: 16px;
  }
}
</style>

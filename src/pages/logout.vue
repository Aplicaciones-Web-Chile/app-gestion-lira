<script>
export default {
  data() {
    return {};
  },
  methods: {
    onLogout() {
      localStorage.removeItem("authToken"); // Limpiar el token de autenticación
      this.$router.push("/login"); // Redirigir al login
    },
  },
};
</script>

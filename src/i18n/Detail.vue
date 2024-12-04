<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <div class="text-h6">Detalles para {{ id }}</div>
      </q-card-section>

      <!-- Mostrar los datos detallados -->
      <q-list v-if="detalles.length">
        <q-item v-for="(detalle, index) in detalles" :key="index">
          <q-item-section>{{ detalle.label }}</q-item-section>
          <q-item-section>{{ detalle.value }}</q-item-section>
        </q-item>
      </q-list>

      <!-- Mostrar mensaje si no hay datos -->
      <div v-else class="q-mt-md text-center">
        <q-icon name="info" size="md" color="grey-7" />
        <div>No hay datos para mostrar</div>
      </div>

      <!-- Botón para descargar los datos en CSV -->
      <q-card-actions align="right">
        <q-btn
          label="Descargar CSV"
          color="primary"
          :disable="!detalles.length"
          @click="downloadCSV"
        />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script>

import Vue from "vue";
export default {
  data() {
    return {
      id: null,
      date: null,
      detalles: [], // Aquí se almacenarán los datos detallados
    };
  },
  mounted() {
    // Cargar datos detallados al montar el componente
    if (this.$store.state.cards.id === null) {
      console.log('El id es null');
      this.$router.push("/Dashboard");
    }

    // Obtener los valores del Store
    this.id = this.$store.state.cards.id;
    this.date = this.$store.state.cards.date;

    // Mostrar los datos en la consola para depuración
    console.log("ID desde el store:", this.id);
    console.log("Fecha desde el store:", this.date);

    this.cargarDetalles();
  },
  methods: {
    async cargarDetalles() {
      // Si no tenemos un ID, mostramos un error
      if (!this.id) {
        return;
      }

      try {
        // Llamada al backend para obtener los detalles
        const response = await this.$axios.post("/dashboard.php", {
          peticion: this.id, // Enviamos el ID de la tarjeta al backend
        });

        if (response.data && response.data.datos) {
          this.detalles = response.data.datos; // Asignamos los datos recibidos
        } else {
          console.warn("No se encontraron detalles en el backend. Cargando JSON local...");
          await this.cargarDetallesDesdeJSON(); // Intentar cargar desde el JSON local
        }
      } catch (error) {
        console.error("Error al cargar los detalles desde el backend:", error);
        console.warn("Cargando datos desde JSON local...");
        await this.cargarDetallesDesdeJSON(); // Intentar cargar desde el JSON local
      }
    },
    async cargarDetallesDesdeJSON() {
      try {
        const response = await this.$axios.get("/public/saldo_output_fixed.json");

        if (response.data && response.data.data) {
          // Filtrar los datos por el ID proporcionado
          this.detalles = response.data.data.filter((item) => item.id === this.id);
          if (!this.detalles.length) {
            console.warn("No se encontraron detalles en el JSON local para este ID.");
          }
        } else {
          console.error("El JSON local no contiene datos válidos.");
        }
      } catch (error) {
        console.error("Error al cargar los datos desde el JSON local:", error);
      }
    },
    // Convierte los datos en formato CSV
    convertToCSV(data) {
      // Crear encabezados
      const headers = Object.keys(data[0]).join(",");
      // Crear filas
      const rows = data.map((row) =>
        Object.values(row)
          .map((value) => `"${value}"`) // Asegurarse de que los valores estén entre comillas
          .join(",")
      );
      // Unir encabezados y filas
      return [headers, ...rows].join("\n");
    },
    // Descarga el archivo CSV
    downloadCSV() {
      if (!this.detalles.length) {
        console.warn("No hay datos disponibles para descargar.");
        return;
      }

      // Convertir los datos a CSV
      const csvContent = this.convertToCSV(this.detalles);

      // Crear un archivo Blob para el CSV
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });

      // Crear un enlace de descarga
      const link = document.createElement("a");
      const url = URL.createObjectURL(blob);
      link.setAttribute("href", url);
      link.setAttribute("download", `detalles_${this.id}.csv`);
      link.style.visibility = "hidden";

      // Descargar el archivo
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
  },
};
</script>

<style scoped>
.text-center {
  text-align: center;
}

.q-icon {
  display: block;
  margin: 0 auto;
}
</style>

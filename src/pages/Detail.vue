<template>
  <q-page class="q-pa-md">
    <q-card>
      <q-card-section>
        <!-- Botón de Volver -->
        <q-btn
          label=""
          color="primary"
          icon="arrow_back"
          @click="volverAtras"
        />
      </q-card-section>

      <!-- Mostrar los datos detallados -->
      <q-table
        wrap-cells
        dense
        :title="title"
        :data="detalles"
        :columns="columns"
        row-key="index"
        virtual-scroll
        class="sticky-header"
        color="primary"
        :style_="'height: ' + ($q.screen.height - 130) + 'px'"
        separator="cell"
      />

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
import Const from "../assets/const.js";

export default {
  data() {
    return {
      id: null,
      title: null,
      date: null,
      columns: [
        { name: "rut", label: "RUT", align: "left", field: "rut" },
        {
          name: "sucursal",
          label: "Sucursal",
          align: "left",
          field: "sucursal",
        },
        {
          name: "nombreRazonSocial",
          label: "Nombre Razón Social",
          align: "left",
          field: "nombreRazonSocial",
        },
        { name: "saldo", label: "Saldo", align: "right", field: "saldo" },
        {
          name: "creditoDisponible",
          label: "Crédito Disponible",
          align: "right",
          field: "creditoDisponible",
        },
      ],
      detalles: [
        {
          rut: "11111-1",
          sucursal: "00",
          nombreRazonSocial: "Perico",
          saldo: 100000,
          creditoDisponible: 900000,
        },
      ],
    };
  },
  mounted() {
    // Cargar datos detallados al montar el componente
    if (this.$store.state.cards.id === null) {
      this.$router.push("/Dashboard");
    }
    // Obtener los valores del Store
    this.id = this.$store.state.cards.id;
    this.date = this.$store.state.cards.date;

    if (this.id === "cuentas_por_pagar") {
      this.title = "Detalle de cuentas por pagar";
    } else if (this.id === "cuentas_por_cobrar") {
      this.title = "Detalle de cuentas por cobrar";
    }

    this.cargarDetalles();
  },
  methods: {
    volverAtras() {
      this.$router.push("/Dashboard");
    },
    cargarDetalles() {
      this.cargarDetallesDesdeJSON(); // Intentar cargar desde el JSON local
    },
    cargarDetalles() {
    // Configurar la solicitud con el período seleccionado
    const payload = {
      peticion: ["detalles"], // Nombre del endpoint
      Distribuidor: "001", // Cambiar según sea necesario
      selectedPeriod: this.date // Parámetro tomado del calendario
    };

    this.$axios
      .post(`${Const.backend}dashboard.php`, payload)
      .then((response) => {
        console.log("Respuesta del backend:", response.data);

        if (response.data && Array.isArray(response.data.datos)) {
          const datosMapeados = response.data.datos.map((item, index) => ({
            index, // Índice único
            rut: item.RUT || "N/A",
            sucursal: item.Sucursal || "N/A",
            nombreRazonSocial: item.Nombre_Razon_Social || "N/A",
            saldo: item.Saldo || 0,
            creditoDisponible: item.Credito_Disponible || 0
          }));
          this.detalles = datosMapeados;
        } else {
          console.warn("Datos inválidos recibidos:", response.data);
          this.detalles = [];
        }
      })
      .catch((error) => {
        console.error("Error al cargar los detalles:", error);
        this.detalles = [];
      });
  },
    convertToCSV(data) {
      // Crear encabezados
      const headers = this.columns.map((col) => col.label).join(",");

      // Crear filas asegurando que los valores "falsy" como 0 se mantengan
      const rows = data.map((row) =>
        this.columns
          .map((col) => {
            const value = row[col.field];
            return `"${value !== undefined && value !== null ? value : ""}"`; // Aseguramos que 0 sea válido
          })
          .join(",")
      );

      // Unir encabezados y filas
      return [headers, ...rows].join("\n");
    },
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

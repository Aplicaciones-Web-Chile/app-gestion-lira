<template>
  <q-page class="q-pa-md dashboard-page">
    <!-- Selector de Fecha -->
    <q-card class="q-mb-md date-selector">
      <q-card-section class="row items-center justify-between bg-grey-1">
        <div class="text-subtitle1">SELECCIONE UNA FECHA</div>
        <q-btn flat dense icon="event">
          <q-popup-proxy>
            <q-date v-model="selectedDate" mask="YYYY-MM-DD">
              <div class="row items-center justify-end q-pa-sm">
                <q-btn label="OK" color="primary" flat @click="applyDate" v-close-popup />
              </div>
            </q-date>
          </q-popup-proxy>
        </q-btn>
      </q-card-section>
    </q-card>
    <!-- Sección de Tarjetas -->
    <div class="row q-col-gutter-md">
      <div v-for="(card, id) in cards" :key="id" class="col-12 col-sm-6 col-lg-3">
        <q-card :class="['dashboard-card', card.cardClass]">
          <q-card-section>
            <div class="row items-center no-wrap">
              <div class="col">
                <div class="text-subtitle1 text-weight-medium text-white">{{ card.title }}</div>
                <div class="text-caption q-mt-sm text-white">{{ card.subtitle }}</div>
              </div>
              <div class="col-auto">
                <q-btn flat round dense color="white" :icon="card.icon" @click="irADetalle(id)" />
              </div>
            </div>
          </q-card-section>
          
          <q-card-section class="q-pt-none">
            <div class="text-h4 text-weight-bold text-white">
              {{ card.amount }}
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Sección del Gráfico -->
    <q-card class="q-mt-md dashboard-chart-card">
      <q-card-section class="row items-center justify-between bg-grey-1">
        <div class="text-h6">Ventas - Gastos - Rentabilidad</div>
        <div class="row q-gutter-sm">
          <q-select
            v-model="selectedYear"
            :options="years"
            label="Año"
            dense
            outlined
            style="width: 120px"
            @input="filterChart"
          />
          <q-select
            v-model="selectedMonth"
            :options="filteredMonths"
            label="Mes"
            dense
            outlined
            style="width: 120px"
            @input="filterChart"
          />
        </div>
      </q-card-section>
      
      <q-card-section>
        <div class="chart-container">
          <div class="spinner" v-if="loadingChart">
            <q-spinner color="primary" size="3em" :thickness="10" />
          </div>
          <apexchart
            v-else
            type="line"
            height="350"
            :options="chartOptions"
            :series="filteredSeries"
          ></apexchart>
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import Const from "../assets/const.js";
import { Loading } from "quasar";

export default {
  name: 'DashboardPage',
  components: {
    'apexchart': VueApexCharts
  },
  data() {
    const currentYear = new Date().getFullYear();
    return {
      selectedDate: "",
      loading: false,
      selectedYear: null,
      selectedMonth: null,
      currentYear,
      loadingChart: true,
      datos: {
        data: [],
      }, // Almacena los datos obtenidos del JSON
      error: null,
      cardDate: null,
      showDetail: false,
      monthsPeriod: { value: "whatever" },
      yearsPeriod: { value: "whatever" },
      years: [
        currentYear - 1,
        currentYear,
        currentYear + 1,
        currentYear + 2,
      ],
      // Datos de las tarjetas
      cards: {
        estado_resultado: {
          title: "Estado Resultado",
          subtitle: "Periodo al 10/2023",
          amount: "$679,938,635",
          loading: false,
          showDateMenu: false,
          selectedDate: [],
          filtro: "selector",
          cardClass: 'estado-card',
          icon: 'trending_up'
        },
        flujo_de_caja: {
          title: "Flujo de caja",
          subtitle: "Al 04/12/24",
          amount: "$1.234.567.890",
          loading: false,
          selectedDate: [],
          filtro: "selector",
          cardClass: 'flujo-card',
          icon: 'account_balance'
        },
        cuentas_por_cobrar: {
          title: "Cuentas por Cobrar",
          subtitle: "Al 04/12/2024",
          amount: "$422.237.490",
          loading: false,
          selectedDate: [],
          filtro: "selector",
          cardClass: 'cobrar-card',
          icon: 'attach_money'
        },
        cuentas_por_pagar: {
          title: "Cuentas por Pagar",
          subtitle: "Al 04/12/2024",
          amount: "$0",
          loading: false,
          selectedDate: [],
          filtro: "selector",
          cardClass: 'pagar-card',
          icon: 'money_off'
        },
      },

      months: [
        { label: "Enero", value: 1 },
        { label: "Febrero", value: 2 },
        { label: "Marzo", value: 3 },
        { label: "Abril", value: 4 },
        { label: "Mayo", value: 5 },
        { label: "Junio", value: 6 },
        { label: "Julio", value: 7 },
        { label: "Agosto", value: 8 },
        { label: "Septiembre", value: 9 },
        { label: "Octubre", value: 10 },
        { label: "Noviembre", value: 11 },
        { label: "Diciembre", value: 12 },
      ],

      series: [],
      chartOptions: {
        chart: {
          type: 'line',
          height: 350,
          toolbar: {
            show: true
          },
          zoom: {
            enabled: true
          }
        },
        stroke: {
          curve: 'smooth',
          width: 2
        },
        colors: ['#48a9e6', '#66bb6a', '#ef5350'],
        xaxis: {
          categories: ['Dic-2023', 'Ene-2024', 'Feb-2024', 'Mar-2024', 'Abr-2024', 'May-2024', 
                      'Jun-2024', 'Jul-2024', 'Ago-2024', 'Sep-2024', 'Oct-2024', 'Nov-2024'],
        },
        yaxis: {
          labels: {
            formatter: function(value) {
              return '$' + value.toLocaleString('es-CL');
            }
          }
        },
        legend: {
          position: 'top'
        },
        grid: {
          borderColor: '#f1f1f1'
        }
      },
      filteredMonths: [],
    };
  },
  watch: {
    selectedYear() {
      this.filterMonths();
      this.filterChart();
    },
    selectedMonth() {
      this.filterChart();
    },
    selectedYear() {
      this.filterMonths();
    },
  },
  computed: {
    filteredSeries() {
      // Añadir esta propiedad computada
      if (!this.selectedDateRange) {
        return this.series;
      }
      const [startDate, endDate] = this.selectedDateRange;
      const filteredData = this.series[0].data.filter((dataPoint) => {
        const date = new Date(dataPoint.x);
        return date >= new Date(startDate) && date <= new Date(endDate);
      });
      return [
        {
          ...this.series[0],
          data: filteredData,
        },
      ];
    },
  },
  methods: {
    irADetalle(id) {
      // Si no hay fecha seleccionada, usar la fecha actual
      const fecha = this.selectedDate || this.formatDate(new Date());
      
      // Guardar el ID y la fecha en el store
      this.$store.commit('cards/setCardDetails', {
        id: id,
        date: fecha
      });
      
      // Navegar al detalle usando el path correcto
      this.$router.push({
        name: 'detail',
        query: { 
          id: id,
          date: fecha
        }
      });
    },

    loadCardData(peticion) {
      console.log('Cargando datos para:', peticion);
      this.cards[peticion].loading = true;
      const formattedDate = this.selectedDate ? this.formatDate(this.selectedDate) : '';
      
      this.$axios
        .post(Const.backend + "dashboard.php", {
          Distribuidor: "001",
          peticion: peticion,
          selectedPeriod: formattedDate
        })
        .then((response) => {
          console.log('Respuesta para', peticion, ':', response.data);
          if (response.data && response.data.datos) {
            const datosParsed = response.data.datos;
            this.$set(this.cards[peticion], 'amount', this.formatCurrency(datosParsed.amount));
            this.$set(this.cards[peticion], 'subtitle', `Al ${formattedDate}`);
          }
        })
        .catch((error) => {
          console.error("Error al cargar datos de " + peticion + ":", error);
          Const.ErrorHandler(this);
        })
        .finally(() => {
          this.cards[peticion].loading = false;
        });
    },

    applyDate() {
      if (this.selectedDate) {
        console.log('Fecha seleccionada:', this.selectedDate);
        // Lista de todas las tarjetas que necesitamos actualizar
        const cardKeys = ['estado_resultado', 'flujo_de_caja', 'cuentas_por_cobrar', 'cuentas_por_pagar'];
        
        // Actualizar cada tarjeta
        cardKeys.forEach(cardKey => {
          console.log('Actualizando tarjeta:', cardKey);
          this.loadCardData(cardKey);
        });

        // También actualizamos el gráfico
        this.loadChart();
      }
    },

    formatDate(date) {
      if (!date) return "";
      const options = { year: "numeric", month: "2-digit", day: "2-digit" };
      return new Date(date).toLocaleDateString("es-CL", options);
    },
    filterChart() {
      if (this.selectedYear && this.selectedMonth?.value) {
        return this.loadChart();
      }
    },

    loadChart() {
      this.loadingChart = true;
      let valorNull = null;

      this.$axios
        .post(Const.backend + "dashboard.php", {
          Distribuidor: "001",
          peticion: "ventas_gastos_rentabilidad",
          FilterYears: this.selectedYear,
          FilterMonths: this.selectedMonth?.value || valorNull,
        })
        .then((response) => {
          if (response.data && response.data.datos) {
            this.datos = response.data.datos;
            this.loadingChart = false;
          }
        })
        .catch(Const.ErrorHandler.bind(this, this))
        .finally(() => {
          this.loadingChart = false;
        });
    },

    formatCurrency(amount) {
      const formatter = new Intl.NumberFormat("es-CL", {
        style: "currency",
        currency: "CLP",
        minimumFractionDigits: 0
      });
      return formatter.format(amount).replace("CLP", "").trim();
    },

    filterMonths() {
      if (this.selectedYear === this.currentYear) {
        this.filteredMonths = this.months.filter(
          (month) => month.value <= this.currentMonth
        );
      } else {
        this.filteredMonths = this.months;
      }

      // Reset selected month if it's not in the filtered options
      if (
        this.selectedMonth &&
        !this.filteredMonths.some((month) => month.value === this.selectedMonth)
      ) {
        this.selectedMonth = null;
      }
    },
  },
  mounted() {
    // Lógica para cargar datos dinámicos para cada tarjeta
    this.loadCardData("cuentas_por_pagar");
    this.loadCardData("cuentas_por_cobrar");
    this.loadCardData("flujo_de_caja");
    this.loadChart();
  },
};
</script>

<style>
.dashboard-page {
  background: #f5f6fa;
}

.date-selector {
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.date-selector .q-card-section {
  padding: 12px 16px;
}

.dashboard-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  height: 100%;
}

.dashboard-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

/* Clases para los diferentes tipos de tarjetas */
.estado-card {
  background: linear-gradient(135deg, #48a9e6 0%, #0288d1 100%) !important;
}

.flujo-card {
  background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%) !important;
}

.cobrar-card {
  background: linear-gradient(135deg, #26a69a 0%, #00897b 100%) !important;
}

.pagar-card {
  background: linear-gradient(135deg, #ef5350 0%, #e53935 100%) !important;
}

.dashboard-chart-card {
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.chart-container {
  position: relative;
  width: 100%;
  min-height: 350px;
}

.spinner {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(255,255,255,0.8);
}

.q-select {
  .q-field__control {
    background: white;
  }
}
</style>

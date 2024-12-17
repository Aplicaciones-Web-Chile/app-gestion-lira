<!--
  @file Dashboard.vue
  @description Componente principal del dashboard que muestra métricas clave del negocio,
              incluyendo estado de resultados, flujo de caja y cuentas por cobrar/pagar.
              Implementa visualizaciones interactivas y sistema de caché para optimizar rendimiento.
  @author Lira
  @version 1.0.0
-->

<template>
  <div>
    <!-- Contenedor principal del dashboard con padding y clase específica -->
    <q-page class="q-pa-md dashboard-page">
      <!-- Logo corporativo -->
      <div class="logo-container">
        <img src="~assets/logo-lira.png" width="150" alt="Logo Lira" class="logo" />
      </div>
      
      <!-- Selector de Fecha: Permite al usuario filtrar datos por fecha específica -->
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
      
      <!-- Sección de Tarjetas KPI: Muestra métricas clave del negocio -->
      <div class="row q-col-gutter-md">
        <div v-for="(card, id) in cards" :key="id" class="col-12 col-sm-6 col-lg-3">
          <q-card :class="['dashboard-card', card.cardClass]">
            <q-inner-loading :showing="card.loading">
              <q-spinner-dots size="50px" color="white" />
            </q-inner-loading>
            
            <q-card-section :class="{ 'blur-content': card.loading }">
              <div class="row items-center no-wrap">
                <div class="col">
                  <div class="text-subtitle1 text-weight-medium text-white">{{ card.title }}</div>
                  <div class="text-caption q-mt-sm text-white">{{ card.subtitle }}</div>
                </div>
                <div class="col-auto">
                  <q-btn flat round dense color="white" :icon="card.icon" @click="mostrarDetalle(id)" />
                </div>
              </div>
            </q-card-section>
            
            <q-card-section class="q-pt-none" :class="{ 'blur-content': card.loading }">
              <div class="text-h4 text-weight-bold text-white">
                {{ card.amount }}
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <!-- Sección del Gráfico: Visualización de ventas, gastos y rentabilidad -->
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
              :series="series"
            ></apexchart>
          </div>
        </q-card-section>
      </q-card>
    </q-page>

    <!-- Popup para detalles -->
    <q-dialog v-model="showDetailDialog" persistent maximized>
      <q-card class="column no-wrap">
        <q-card-section class="row items-center bg-primary text-white q-px-md">
          <div class="text-h6">{{ detalleTitle }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="col q-pa-md">
          <div v-if="loadingDetalle" class="text-center q-pa-md">
            <q-spinner-dots color="primary" size="40px" />
          </div>
          <div v-else>
            <div class="row items-center justify-between q-mb-md">
              <div class="text-subtitle2">
                Registros encontrados: {{ detalleData.length }}
              </div>
              <q-btn
                color="primary"
                icon="download"
                label="Exportar CSV"
                @click="exportToCSV"
              />
            </div>

            <q-input
              v-model="filter"
              label="Buscar"
              dense
              class="q-mb-md"
            >
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>

            <q-markup-table flat bordered>
              <thead>
                <tr>
                  <th class="text-left">RUT</th>
                  <th class="text-left">Razón Social</th>
                  <th class="text-right">Saldo</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in filteredData" :key="row.RUT">
                  <td class="text-left">{{ row.RUT }}</td>
                  <td class="text-left">{{ row.RAZO }}</td>
                  <td class="text-right">{{ formatCurrency(row.SALD) }}</td>
                </tr>
              </tbody>
            </q-markup-table>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import VueApexCharts from "vue-apexcharts";
import Const from "../assets/const.js";
import { Loading } from "quasar";
import XLSX from 'xlsx';

/**
 * @component DashboardPage
 * @description Componente principal del dashboard que proporciona una vista general
 *              del estado financiero y operativo del negocio.
 */
export default {
  name: 'DashboardPage',
  components: {
    'apexchart': VueApexCharts
  },
  data() {
    const currentYear = new Date().getFullYear();
    return {
      /** @property {string} selectedDate - Fecha seleccionada para filtrar datos */
      selectedDate: "",
      /** @property {boolean} loading - Estado de carga general del componente */
      loading: false,
      /** @property {number|null} loadTimeout - Temporizador para las operaciones de carga */
      loadTimeout: null,
      /** @property {number|null} chartTimeout - Temporizador para la carga del gráfico */
      chartTimeout: null,
      /** @property {number} cacheExpiration - Tiempo de expiración del caché en milisegundos */
      cacheExpiration: 1800000, // 30 minutos
      selectedYear: currentYear,
      selectedMonth: { label: "Enero", value: 1 },
      currentYear,
      loadingChart: true,
      datos: {
        data: [],
      },
      series: [
        {
          name: 'Ventas',
          data: []
        },
        {
          name: 'Gastos',
          data: []
        },
        {
          name: 'Rentabilidad',
          data: []
        }
      ],
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
          categories: [],
        },
        yaxis: {
          labels: {
            formatter: function(value) {
              return new Intl.NumberFormat('es-CL', {
                style: 'currency',
                currency: 'CLP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
              }).format(value).replace('CLP', '').trim();
            }
          }
        },
        tooltip: {
          y: {
            formatter: function(value) {
              return new Intl.NumberFormat('es-CL', {
                style: 'currency',
                currency: 'CLP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
              }).format(value).replace('CLP', '').trim();
            }
          }
        }
      },
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

      years: [
        currentYear - 1,
        currentYear,
        currentYear + 1,
        currentYear + 2,
      ],
      filteredMonths: [],

      // Datos para el popup de detalles
      showDetailDialog: false,
      detalleTitle: '',
      detalleData: [],
      loadingDetalle: false,
      filter: '',
      
      // Configuración de la tabla
      tableColumns: [
        { 
          name: 'RUT', 
          label: 'RUT', 
          field: 'RUT', 
          align: 'left',
          sortable: true 
        },
        { 
          name: 'RAZO', 
          label: 'Razón Social', 
          field: 'RAZO', 
          align: 'left',
          sortable: true 
        },
        { 
          name: 'SALD', 
          label: 'Saldo', 
          field: 'SALD',
          align: 'right',
          sortable: true,
          format: val => this.formatCurrency(val)
        }
      ],
      tablePagination: {
        sortBy: 'SALD',
        descending: true,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 0
      }
    };
  },
  watch: {
    /**
     * Observador para el año seleccionado
     * @param {number} newValue - Nuevo valor seleccionado
     */
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
    filteredData() {
      if (!this.filter) {
        return this.detalleData;
      }
      const searchTerm = this.filter.toLowerCase();
      return this.detalleData.filter(item => 
        (item.RUT || '').toLowerCase().includes(searchTerm) ||
        (item.RAZO || '').toLowerCase().includes(searchTerm) ||
        (item.SALD || '').toString().includes(searchTerm)
      );
    },
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
    /**
     * Navega al detalle de una tarjeta específica
     * @param {string} id - Identificador de la tarjeta
     */
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

    /**
     * Verifica si existen datos en caché para una fecha específica
     * @param {string} date - Fecha en formato YYYY-MM-DD
     * @param {string} cardKey - Identificador de la tarjeta
     * @returns {Object|null} Datos en caché o null si no existen o están expirados
     */
    checkCache(date, cardKey) {
      const cacheKey = `dashboard_${cardKey}_${date}`;
      const cached = localStorage.getItem(cacheKey);
      
      if (cached) {
        const { timestamp, data } = JSON.parse(cached);
        if (Date.now() - timestamp < this.cacheExpiration) {
          return data;
        }
        // Si los datos están expirados, eliminarlos
        localStorage.removeItem(cacheKey);
      }
      return null;
    },

    /**
     * Almacena datos en el caché local
     * @param {string} date - Fecha en formato YYYY-MM-DD
     * @param {string} cardKey - Identificador de la tarjeta
     * @param {Object} data - Datos a almacenar
     */
    saveToCache(date, cardKey, data) {
      const cacheKey = `dashboard_${cardKey}_${date}`;
      const cacheData = {
        timestamp: Date.now(),
        data: data
      };
      localStorage.setItem(cacheKey, JSON.stringify(cacheData));
    },

    /**
     * Carga los datos para una tarjeta específica
     * @param {string} peticion - Identificador de la tarjeta
     * @returns {Promise<void>}
     */
    async loadCardData(peticion) {
      // Limpiar timeout anterior si existe
      if (this.loadTimeout) {
        clearTimeout(this.loadTimeout);
      }

      const formattedDate = this.selectedDate ? this.formatDate(this.selectedDate) : this.formatDate(new Date());
      
      // Verificar caché
      const cachedData = this.checkCache(formattedDate, peticion);
      if (cachedData) {
        this.$set(this.cards[peticion], 'amount', this.formatCurrency(cachedData.amount));
        this.$set(this.cards[peticion], 'subtitle', `Al ${formattedDate}`);
        return;
      }

      // Activar loading
      this.$set(this.cards[peticion], 'loading', true);

      // Configurar timeout para error
      this.loadTimeout = setTimeout(() => {
        if (this.cards[peticion].loading) {
          this.$set(this.cards[peticion], 'loading', false);
          this.$q.notify({
            type: 'negative',
            message: `No se pudo cargar la información de ${this.cards[peticion].title}`,
            position: 'top',
            timeout: 3000
          });
        }
      }, 15000); // 15 segundos de timeout

      // Realizar la petición
      this.$axios
        .post(Const.backend + "dashboard.php", {
          Distribuidor: "001",
          peticion: peticion,
          selectedPeriod: formattedDate
        })
        .then((response) => {
          if (response.data && response.data.datos) {
            const datosParsed = response.data.datos;
            this.$set(this.cards[peticion], 'amount', this.formatCurrency(datosParsed.amount));
            this.$set(this.cards[peticion], 'subtitle', `Al ${formattedDate}`);
            
            // Guardar en caché
            this.saveToCache(formattedDate, peticion, datosParsed);
          }
        })
        .catch((error) => {
          console.error("Error al cargar datos de " + peticion + ":", error);
          this.$q.notify({
            type: 'negative',
            message: `Error al cargar ${this.cards[peticion].title}`,
            position: 'top',
            timeout: 3000
          });
        })
        .finally(() => {
          clearTimeout(this.loadTimeout);
          this.$set(this.cards[peticion], 'loading', false);
        });
    },

    /**
     * Aplica la fecha seleccionada y actualiza los datos
     */
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

    /**
     * Carga los datos del gráfico desde el servidor
     * @returns {Promise<void>}
     */
    async loadChart() {
      if (this.chartTimeout) {
        clearTimeout(this.chartTimeout);
      }

      this.loadingChart = true;
      console.log('Iniciando carga del gráfico:', {
        año: this.selectedYear,
        mes: this.selectedMonth,
        endpoint: Const.backend + "dashboard.php"
      });

      const params = {
        Distribuidor: "001",
        peticion: "ventas_gastos_rentabilidad",
        FilterYears: this.selectedYear,
        FilterMonths: this.selectedMonth?.value || null,
      };

      console.log('Parámetros de la petición:', params);

      this.$axios
        .post(Const.backend + "dashboard.php", params)
        .then((response) => {
          console.log('Estructura completa de la respuesta:', {
            data: response.data,
            datos: response.data?.datos,
            tipo: response.data?.datos ? typeof response.data.datos : 'undefined',
            esArray: Array.isArray(response.data?.datos),
            keys: response.data?.datos ? Object.keys(response.data.datos) : []
          });
          
          if (!response.data?.datos) {
            throw new Error('No hay datos en la respuesta');
          }

          const datos = response.data.datos;
          
          // Verificar si los datos ya vienen en formato ApexCharts
          if (datos.categories && datos.series) {
            console.log('Datos en formato ApexCharts detectados:', datos);
            
            // Actualizar las categorías del eje X
            this.chartOptions = {
              ...this.chartOptions,
              xaxis: {
                categories: datos.categories
              }
            };

            // Actualizar las series
            this.series = datos.series.map(serie => ({
              ...serie,
              data: serie.data.map(value => parseFloat(value) || 0)
            }));

            console.log('Series actualizadas:', this.series);
          } else {
            throw new Error('Formato de datos no válido');
          }
        })
        .catch((error) => {
          console.error("Error detallado al cargar el gráfico:", {
            mensaje: error.message,
            respuesta: error.response?.data,
            status: error.response?.status,
            error: error
          });
          
          this.$q.notify({
            type: 'negative',
            message: `Error al cargar el gráfico: ${error.message}`,
            position: 'top',
            timeout: 5000
          });
        })
        .finally(() => {
          clearTimeout(this.chartTimeout);
          this.loadingChart = false;
        });
    },

    /**
     * Limpia todo el caché relacionado con el dashboard
     */
    clearDashboardCache() {
      const keys = Object.keys(localStorage);
      keys.forEach(key => {
        if (key.startsWith('dashboard_') || key.startsWith('chart_')) {
          localStorage.removeItem(key);
        }
      });
    },

    /**
     * Formatea una fecha al formato requerido
     * @param {string|Date} date - Fecha a formatear
     * @returns {string} Fecha formateada
     */
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

    /**
     * Formatea valores monetarios al formato CLP
     * @param {number} amount - Monto a formatear
     * @returns {string} Monto formateado en formato CLP
     */
    formatCurrency(amount) {
      const formatter = new Intl.NumberFormat("es-CL", {
        style: "currency",
        currency: "CLP",
        minimumFractionDigits: 0
      });
      return formatter.format(amount).replace("CLP", "").trim();
    },

    /**
     * Filtra los meses según el año seleccionado
     */
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

    /**
     * Muestra el detalle de una tarjeta en un popup
     * @param {string} id - Identificador de la tarjeta
     */
    async mostrarDetalle(tipo) {
      try {
        this.loadingDetalle = true;
        this.showDetailDialog = true;
        this.detalleData = [];
        
        // Establecer el título según el tipo de detalle
        this.detalleTitle = tipo === 'cuentas_por_cobrar' ? 'Detalle Cuentas por Cobrar' : 'Detalle Cuentas por Pagar';
        
        // Construir el endpoint
        const endpoint = tipo === 'cuentas_por_cobrar' ? 'cuentas_por_cobrar_detalle' : 'cuentas_por_pagar_detalle';
        
        // Obtener la fecha formateada
        const formattedDate = this.selectedDate ? this.formatDate(this.selectedDate) : this.formatDate(new Date());
        
        // Realizar la petición
        const response = await this.$axios.post(
          Const.backend + "dashboard.php",
          {
            peticion: endpoint,
            Distribuidor: "001",
            selectedPeriod: formattedDate
          }
        );

        console.log('Respuesta completa:', response.data);
        console.log('Estructura de datos:', {
          tieneData: !!response.data,
          tieneDatos: !!response.data?.datos,
          esArray: Array.isArray(response.data?.datos),
          longitud: response.data?.datos?.length,
          primerElemento: response.data?.datos?.[0]
        });

        // Verificar y procesar la respuesta
        if (response.data && response.data.datos && Array.isArray(response.data.datos)) {
          // Asegurarse que cada elemento tenga las propiedades necesarias y los tipos correctos
          this.detalleData = response.data.datos.map(item => {
            const mappedItem = {
              RUT: item.RUTC || item.RUTP || '', // Intentamos obtener RUTC o RUTP
              RAZO: item.RAZO || '',
              SALD: parseFloat(item.SALD || 0)
            };
            console.log('Item mapeado:', mappedItem);
            return mappedItem;
          });
          
          console.log('Datos procesados:', this.detalleData);
          
          // Actualizar el número total de filas para la paginación
          this.tablePagination = {
            ...this.tablePagination,
            rowsNumber: this.detalleData.length
          };

          // Forzar la actualización de la tabla
          this.$nextTick(() => {
            if (this.detalleData.length > 0) {
              console.log('Actualizando tabla con', this.detalleData.length, 'registros');
            }
          });
        } else {
          console.error('La respuesta no tiene el formato esperado:', response.data);
          this.detalleData = [];
        }
      } catch (error) {
        console.error('Error al cargar los detalles:', error);
        this.$q.notify({
          color: 'negative',
          position: 'top',
          message: 'Error al cargar los detalles. Por favor, intente nuevamente.',
          icon: 'warning'
        });
        this.detalleData = [];
      } finally {
        this.loadingDetalle = false;
      }
    },
    exportToCSV() {
      if (!this.detalleData || this.detalleData.length === 0) {
        this.$q.notify({
          message: 'No hay datos para exportar',
          color: 'warning'
        })
        return
      }

      try {
        const BOM = "\uFEFF";
        const headers = ['RUT;Razón Social;Saldo\n'];
        const rows = this.detalleData.map(item => {
          const rut = item.RUT || '';
          const razon = item.RAZO || '';
          const saldo = item.SALD || 0;
          return `${rut};${razon};${saldo}\n`;
        });
        const csvContent = BOM + headers.concat(rows).join('');
        
        // Crear el blob y el link de descarga
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        // Crear URL del blob
        const url = window.URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'detalle_cuentas.csv');
        
        // Agregar link al documento, hacer click y remover
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Liberar el URL
        window.URL.revokeObjectURL(url);
      } catch (error) {
        console.error('Error al exportar:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Error al exportar los datos',
          icon: 'warning'
        });
      }
    },
  },
  /**
   * Hook del ciclo de vida: Mounted
   * Inicializa el componente y carga los datos iniciales
   */
  mounted() {
    // Limpiar caché al montar el componente
    this.clearDashboardCache();
    
    // Cargar datos iniciales
    const cardKeys = ['cuentas_por_pagar', 'cuentas_por_cobrar', 'flujo_de_caja'];
    cardKeys.forEach(key => this.loadCardData(key));
    
    // Cargar el gráfico con los valores iniciales
    this.loadChart();
  },
};
</script>

<style>
/* Estilos específicos para el dashboard */
.dashboard-page {
  background: #f5f6fa;
}

.logo-container {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.logo {
  height: auto;
  object-fit: contain;
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

.blur-content {
  filter: blur(2px);
  opacity: 0.7;
  transition: all 0.3s ease;
}
</style>

<template>
  <q-page padding>
    <q-card class="detail-card">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">{{ title }}</div>
        <q-space />
        <q-btn 
          icon="arrow_back" 
          flat 
          round 
          dense 
          @click="$router.push('/dashboard')" 
          label="Volver"
        />
      </q-card-section>

      <!-- Tabla de datos -->
      <q-card-section>
        <q-table
          wrap-cells
          dense
          :data="detalles"
          :columns="columns"
          row-key="index"
          :rows-per-page-options="[10, 20, 50]"
          class="detail-table"
        >
          <!-- Personalización de las celdas de saldo y crédito -->
          <template v-slot:body-cell-saldo="props">
            <q-td :props="props">
              {{ formatCurrency(props.row.saldo) }}
            </q-td>
          </template>
          
          <template v-slot:body-cell-creditoDisponible="props">
            <q-td :props="props">
              {{ formatCurrency(props.row.creditoDisponible) }}
            </q-td>
          </template>

          <!-- Loading state -->
          <template v-slot:loading>
            <q-inner-loading showing color="primary" />
          </template>
        </q-table>
      </q-card-section>

      <!-- Botones de acción -->
      <q-card-actions align="right" class="text-primary">
        <q-btn
          flat
          label="Descargar CSV"
          icon="download"
          :disable="!detalles.length"
          @click="downloadCSV"
        />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script>
import { date } from 'quasar'
import Const from "../assets/const.js"

export default {
  name: 'DetailPage',
  
  data() {
    return {
      title: '',
      detalles: [],
      loading: true,
      columns: [
        { 
          name: 'rut',
          label: 'RUT',
          align: 'left',
          field: 'rut'
        },
        {
          name: 'sucursal',
          label: 'Sucursal',
          align: 'left',
          field: 'sucursal'
        },
        {
          name: 'nombreRazonSocial',
          label: 'Nombre Razón Social',
          align: 'left',
          field: 'nombreRazonSocial'
        },
        {
          name: 'saldo',
          label: 'Saldo',
          align: 'right',
          field: 'saldo',
          sortable: true
        },
        {
          name: 'creditoDisponible',
          label: 'Crédito Disponible',
          align: 'right',
          field: 'creditoDisponible',
          sortable: true
        }
      ]
    }
  },

  created() {
    // Obtener los parámetros de la URL
    const { id, date } = this.$route.query;
    if (!id || !date) {
      this.$router.push('/dashboard');
      return;
    }
    
    this.setTitle(id);
    this.cargarDetalles(id, date);
  },

  methods: {
    // Configurar el título según el tipo de detalle
    setTitle(id) {
      switch(id) {
        case 'estado_resultado':
          this.title = 'Detalle de Estado de Resultado';
          break;
        case 'flujo_de_caja':
          this.title = 'Detalle de Flujo de Caja';
          break;
        case 'cuentas_por_cobrar':
          this.title = 'Detalle de Cuentas por Cobrar';
          break;
        case 'cuentas_por_pagar':
          this.title = 'Detalle de Cuentas por Pagar';
          break;
        default:
          this.title = 'Detalle';
      }
    },

    // Cargar datos desde la API
    async cargarDetalles(id, selectedDate) {
      this.loading = true;
      try {
        const payload = {
          peticion: "detalles",
          Distribuidor: "001",
          selectedPeriod: selectedDate,
          tipo: id
        };

        console.log('Enviando payload:', payload);

        const response = await this.$axios.post(
          `${Const.backend}dashboard.php`,
          payload
        );

        console.log('Respuesta del servidor:', response.data);

        if (response.data && Array.isArray(response.data.datos)) {
          this.detalles = response.data.datos.map((item, index) => ({
            index,
            rut: item.RUT || 'N/A',
            sucursal: item.Sucursal || 'N/A',
            nombreRazonSocial: item.Nombre_Razon_Social || 'N/A',
            saldo: Number(item.Saldo) || 0,
            creditoDisponible: Number(item.Credito_Disponible) || 0
          }));
        }
      } catch (error) {
        console.error('Error al cargar detalles:', error);
        this.$q.notify({
          color: 'negative',
          message: 'Error al cargar los detalles',
          icon: 'error'
        });
      } finally {
        this.loading = false;
      }
    },

    // Formatear valores de moneda
    formatCurrency(value) {
      return new Intl.NumberFormat('es-CL', {
        style: 'currency',
        currency: 'CLP'
      }).format(value);
    },

    // Descargar datos en CSV
    downloadCSV() {
      if (!this.detalles.length) return;

      const headers = this.columns.map(col => col.label).join(',');
      const rows = this.detalles.map(row => 
        this.columns.map(col => {
          const value = row[col.field];
          return col.name.includes('saldo') || col.name.includes('credito')
            ? this.formatCurrency(value)
            : `"${value}"`
        }).join(',')
      );

      const csvContent = [headers, ...rows].join('\n');
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      
      const url = URL.createObjectURL(blob);
      link.setAttribute('href', url);
      link.setAttribute('download', `${this.title}_${this.$route.query.date}.csv`);
      link.style.visibility = 'hidden';
      
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  }
}
</script>

<style lang="scss">
.detail-card {
  min-height: calc(100vh - 100px);
  margin: 16px;
}

.detail-table {
  .q-table__top,
  .q-table__bottom,
  thead tr:first-child th {
    background-color: #fff;
  }
  
  thead tr th {
    position: sticky;
    z-index: 1;
  }
  
  thead tr:first-child th {
    top: 0;
  }
}

// Responsive adjustments
@media (max-width: 600px) {
  .detail-card {
    margin: 8px;
  }
  
  .q-table {
    & ::v-deep(.q-table__middle) {
      max-height: calc(100vh - 250px);
    }
  }
}
</style>

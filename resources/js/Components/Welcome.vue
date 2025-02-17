<template>
  <Loading v-if="loading" />
  <main v-else class="dashboard-container">
    
    <div class="flex justify-between items-center">
      <h1 class="text-xl font-bold mb-4">Dashboard Administrador</h1>
      <el-select class="!w-48" v-model="selectedYear" @change="fetchDashboardData" placeholder="Selecciona un año">
        <el-option v-for="year in availableYears" :key="year" :label="year" :value="year" />
      </el-select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="card">
        <h2 class="text-lg font-semibold">Tiendas suscritas activas</h2>
        <p class="text-3xl">{{ storeCount }}</p>
      </div>
      <!-- <div class="card">
        <h2 class="text-lg font-semibold">Dinero Recaudado</h2>
        <p class="text-3xl">${{ totalRevenue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
      </div> -->
      <div class="card">
        <h2 class="text-lg font-semibold">Reportes de Soporte</h2>
        <p class="text-3xl">{{ totalReports }}</p>
      </div>
      <div class="p-4 bg-white rounded-lg shadow-md text-center">
        <h3 class="text-lg font-semibold text-gray-700">Total de Ingresos</h3>
        <p class="text-2xl font-bold text-green-600">${{ totalCompanyRevenue.toLocaleString() }} MXN</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-9">
      <h1 class="col-span-full text-lg ml-4 font-bold">General</h1>
      <apexchart type="bar" :options="revenueChartOptions" :series="revenueSeries" class="chart" />
      <apexchart type="bar" :options="reportsChartOptions" :series="reportsSeries" class="chart" />
      <apexchart type="pie" :options="storesChartOptions" :series="storesSeries" class="chart" />
      <apexchart type="pie" :options="subscriptionPlansOptions" :series="subscriptionPlansSeries" class="chart" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-20">
      <h1 class="col-span-full text-lg ml-4 font-bold">Tiendas suscritas</h1>
      <apexchart type="line" :options="salesChartOptions" :series="salesSeries" class="chart" />
      <apexchart type="bar" :options="topStoresChartOptions" :series="topStoresSeries" class="chart" />
    </div>
  </main>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';
import Loading from '@/Components/MyComponents/Loading.vue';
import axios from 'axios';

export default {
  components: { 
    apexchart: VueApexCharts,
    Loading
   },
  data() {
    return {
        selectedYear: new Date().getFullYear(),
        availableYears: Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i),
        loading: false,
        storeCount: 0,
        // totalRevenue: 0,
        totalReports: 0,
        totalCompanyRevenue: 0,
        revenueSeries: [{ name: "Ingresos", data: [0] }], // Valor inicial
        revenueChartOptions: {
          chart: { type: 'bar', toolbar: { show: false } },
          xaxis: { categories: ["Enero"] }, // Categoría por defecto
          title: { 
            text: "Ingresos Mensuales", 
            align: "center", 
            style: { fontSize: '18px', fontWeight: 'bold', color: '#333' } 
          },
          plotOptions: {
            bar: {
              horizontal: false,
              borderRadius: 6,
              columnWidth: "60%"
            }
          },
          colors: ["#17a2b8"],
          fill: {
            type: "gradient",
            gradient: {
              shade: "light",
              type: "vertical",
              gradientToColors: ["#1abc9c"],
              stops: [0, 100]
            }
          },
          tooltip: {
            y: { formatter: (value) => `$${value.toLocaleString()} MXN` }
          }
        },
        salesSeries: [{ name: "Ventas", data: [] }],
        salesChartOptions: {
            chart: { type: 'line' },
            xaxis: { categories: [] },
            title: { text: "Ventas Mensuales acumuladas de tiendas" },
            tooltip: {
                y: {
                formatter: (value) => value >= 1000 ? (value / 1000).toFixed(1) + "K" : value
                }
            },
            dataLabels: {
                formatter: (value) => value >= 1000 ? (value / 1000).toFixed(1) + "K" : value
            },
            yaxis: {
                labels: {
                formatter: (value) => value >= 1000 ? (value / 1000).toFixed(1) + "K" : value
                }
            }
        },
        reportsSeries: [{ name: "Reportes", data: [] }],
        reportsChartOptions: {
            chart: { type: 'bar', toolbar: { show: false } },
            xaxis: { categories: ["Pendiente", "En proceso", "Resuelto"] },
            title: { 
                text: "Estado de Reportes", 
                align: "center", 
                style: { fontSize: '18px', fontWeight: 'bold', color: '#333' } 
            },
            plotOptions: {
                bar: {
                horizontal: false,
                borderRadius: 6,
                columnWidth: "60%"
                }
            },
            dataLabels: { enabled: false },
            colors: ["#dc3545", "#ffc107", "#28a745"], // Rojo, Amarillo, Verde
            fill: {
                type: "gradient",
                gradient: {
                shade: "light",
                type: "vertical",
                gradientToColors: ["#ff5f5f", "#ffdb4d", "#4bd964"],
                stops: [0, 100]
                }
            },
            tooltip: {
                y: { formatter: (value) => value + " reportes" }
            }
        },
        storesSeries: [],
        storesChartOptions: {
            chart: { 
              type: 'pie',
              width: "300px",  // Ancho fijo del gráfico
              height: "300px", // Alto fijo del gráfico
            },
            labels: [],
            title: { text: "Distribución de Tipos de Tienda" },
            fill: {
                type: "gradient",
                gradient: {
                shade: "dark",
                type: "diagonal2",
                gradientToColors: ["#17a2b8", "#ff6347", "#f39c12", "#8e44ad"],
                stops: [0, 100]
                }
            },
            colors: ["#007bff", "#ff4500", "#e67e22", "#6f42c1"]
        },
        topStoresSeries: [{ name: "Ventas", data: [] }],
        topStoresChartOptions: {
            chart: { type: 'bar', toolbar: { show: false } },
            xaxis: { categories: [] },
            title: { text: "Ventas por tienda (Top 5)", align: "center", style: { fontSize: '18px', fontWeight: 'bold', color: '#333' } },
            tooltip: {
                y: { formatter: (value) => value >= 1000 ? (value / 1000).toFixed(1) + "K" : value }
            },
            dataLabels: { 
                enabled: true,
                formatter: (value) => value >= 1000 ? (value / 1000).toFixed(1) + "K" : value,
                style: { colors: ["#fff"] }
            },
            yaxis: {
                labels: { formatter: (value) => value >= 1000 ? (value / 1000).toFixed(1) + "K" : value }
            },
            plotOptions: {
                bar: {
                borderRadius: 8,
                horizontal: false,
                distributed: true
                }
            },
            fill: {
                type: "gradient",
                gradient: {
                shade: "light",
                type: "diagonal1",
                gradientToColors: ["#28a745"],
                stops: [0, 100]
                }
            },
            colors: ["#20c997"]
        },
        subscriptionPlansSeries: [],
        subscriptionPlansOptions: {
          chart: { 
            type: "pie",
            width: "300px",  // Ancho fijo del gráfico
            height: "300px", // Alto fijo del gráfico
          },
          labels: [],
          title: { 
            text: "Distribución de Planes de Suscripción", 
            align: "center", 
            style: { fontSize: '18px', fontWeight: 'bold', color: '#333' }
          },
          dataLabels: { enabled: true },
          colors: ["#FF6384", "#36A2EB"],
        },
    };
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        this.loading = true;
        const response = await axios.get(route('dashboard.fetch-data'), {
          params: { year: this.selectedYear }
        });
        const data = response.data;

        this.storeCount = data.storeCount;
        // this.totalRevenue = data.totalRevenue;
        this.totalReports = data.totalReports;
        this.salesSeries[0].data = data.salesData;
        this.salesChartOptions.xaxis.categories = data.salesMonths;
        this.reportsSeries[0].data = data.reportsByStatus;
        this.storesSeries = data.storeTypeCounts;
        this.storesChartOptions.labels = data.storeTypeLabels;
        this.topStoresSeries[0].data = data.topStoresSales;
        this.topStoresChartOptions.xaxis.categories = data.topStoresNames;
        this.totalCompanyRevenue = data.totalCompanyRevenue || 0;
        this.revenueSeries = [{ name: "Ingresos", data: data.monthlyRevenue.length ? data.monthlyRevenue : [0] }];
        this.revenueChartOptions.xaxis.categories = data.months.length ? data.months : ["Enero"];
        this.subscriptionPlansSeries = data.subscriptionPlansData;
        this.subscriptionPlansOptions.labels = data.subscriptionPlansLabels;
        console.log(data);
      } catch (error) {
        console.error("Error al cargar datos del dashboard", error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  padding: 10px;
}
.card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}
.chart {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
}
</style>


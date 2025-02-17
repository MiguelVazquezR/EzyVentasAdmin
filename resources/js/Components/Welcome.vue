<template>
  <div class="dashboard-container">
    <h1 class="text-xl font-bold mb-4">Dashboard Administrador</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="card">
        <h2 class="text-lg font-semibold">Tiendas Suscritas</h2>
        <p class="text-3xl">{{ storeCount }}</p>
      </div>
      <div class="card">
        <h2 class="text-lg font-semibold">Dinero Recaudado</h2>
        <p class="text-3xl">${{ totalRevenue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
      </div>
      <div class="card">
        <h2 class="text-lg font-semibold">Reportes de Soporte</h2>
        <p class="text-3xl">{{ totalReports }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
      <apexchart type="line" :options="salesChartOptions" :series="salesSeries" class="chart" />
      <apexchart type="bar" :options="reportsChartOptions" :series="reportsSeries" class="chart" />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
      <apexchart type="pie" :options="storesChartOptions" :series="storesSeries" class="chart" />
      <apexchart type="bar" :options="topStoresChartOptions" :series="topStoresSeries" class="chart" />
    </div>
  </div>
</template>

<script>
import VueApexCharts from 'vue3-apexcharts';
import axios from 'axios';

export default {
  components: { apexchart: VueApexCharts },
  data() {
    return {
        storeCount: 0,
        totalRevenue: 0,
        totalReports: 0,
        salesSeries: [{ name: "Ventas", data: [] }],
        salesChartOptions: {
            chart: { type: 'line' },
            xaxis: { categories: [] },
            title: { text: "Ventas Mensuales" },
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
            chart: { type: 'donut' },
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
            title: { text: "Ventas por Tienda", align: "center", style: { fontSize: '18px', fontWeight: 'bold', color: '#333' } },
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
        }
    };
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        const response = await axios.get(route('dashboard.fetch-data'));
        const data = response.data;

        this.storeCount = data.storeCount;
        this.totalRevenue = data.totalRevenue;
        this.totalReports = data.totalReports;
        this.salesSeries[0].data = data.salesData;
        this.salesChartOptions.xaxis.categories = data.salesMonths;
        this.reportsSeries[0].data = data.reportsByStatus;
        this.storesSeries = data.storeTypeCounts;
        this.storesChartOptions.labels = data.storeTypeLabels;
        this.topStoresSeries[0].data = data.topStoresSales;
        this.topStoresChartOptions.xaxis.categories = data.topStoresNames;
        console.log(data);
      } catch (error) {
        console.error("Error al cargar datos del dashboard", error);
      }
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  padding: 20px;
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


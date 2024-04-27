<template>
    <AppLayout title="Reportes de soporte">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Reportes</h1>
            </div>

            <!-- Buscador y filtros -->
            <section class="flex flex-col lg:flex-row justify-between space-y-3 space-x-3 lg:items-center mt-4 mx-2">
                <div class="lg:w-1/4 relative lg:mr-12">
                    <input v-model="searchTemp" @keyup.enter="handleSearch"
                        class="border border-grayD9 rounded-full w-full h-8 pl-9 focus:ring-0 focus:border-primary"
                        placeholder="Buscar reporte" type="search">
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[9px] left-4"></i>
                </div>
                <el-tag v-if="search" size="large" closable @close="handleTagClose">
                    Estas buscando: <b>{{ search }}</b>
                </el-tag>
                <span></span>
                <!-- <div class="flex items-center space-x-3 lg:w-1/5">
                    <el-cascader class="w-full" v-model="filter" :options="options" @change="handleChangeFilter"
                        clearable placeholder="Filtrar" />
                </div> -->
            </section>

            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8">
                <p v-if="localReports.length" class="text-gray66 text-[11px] mb-2">{{ localReports.length }}
                    de {{
                        total_reports }}
                    elementos
                </p>
                <SuscriptionsTable :items="localReports" />
                <p v-if="localReports.length" class="text-gray66 text-[11px] mt-1">{{ localReports.length }}
                    de {{
                        total_reports }}
                    elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-secondary"></i>
                </p>
                <button
                    v-else-if="localReports.length && !search && (total_reports > 2 && localReports.length < total_reports)"
                    @click="fetchItemsByPage"
                    class="w-full text-secondary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import SuscriptionsTable from '@/Components/MyComponents/Suscription/SuscriptionsTable.vue';
import axios from 'axios';

export default {
    data() {
        return {
            searchTemp: null,
            search: null,
            reportsBuffer: [],
            loading: false,
            localReports: this.support_reports,
            filter: null,
            filtered: false,
            showFilter: false, //filtro opciones
            loadingItems: false, //para paginación
            currentPage: 1, //para paginación
            // options: [
            //     {
            //         label: "Tipo de suscripción ",
            //         value: "suscription_period",
            //         children: [
            //             {
            //                 label: 'Mensual',
            //                 value: 'Mensual',
            //             },
            //             {
            //                 label: 'Anual',
            //                 value: 'Anual',
            //             },
            //         ],
            //     },
            //     {
            //         label: "Vendedor",
            //         value: "seller_id",
            //         children: this.sellers,
            //     },
            //     {
            //         label: "Estatus",
            //         value: "status",
            //         children: [
            //             {
            //                 label: 'Pagado',
            //                 value: 'Pagado',
            //             },
            //             {
            //                 label: 'Próximo a vencer ',
            //                 value: 'Próximo a vencer ',
            //             },
            //             {
            //                 label: 'Vencido',
            //                 value: 'Vencido',
            //             },
            //         ],
            //     },
            // ],
        }
    },
    components: {
        AppLayout,
        SuscriptionsTable,
        PrimaryButton,
        InputLabel,
        Loading,
    },
    props: {
        support_reports: Array,
        total_reports: Number,
    },
    methods: {
        handleSearch() {
            this.filter = null;
            this.search = this.searchTemp;
            this.searchTemp = null;
            if (this.search) {
                this.fetchMatches();
            } else {
                this.showAllReports();
            }
        },
        handleTagClose() {
            this.search = null;
            this.showAllReports();
        },
        handleChangeFilter() {
            this.handleTagClose();
            if (this.filter) {
                this.fetchFiltered();
            }
        },
        showAllReports() {
            // solo si hay items en el buffer, para no dejar vacio el arreglo principal
            if (this.reportsBuffer.length) {
                this.localReports = this.reportsBuffer;
                this.reportsBuffer = [];
            }
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('support-reports.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localReports = [...this.localReports, ...response.data.items];
                    this.currentPage++;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
        async fetchMatches() {
            try {
                this.filtered = false;
                this.loading = true;
                const response = await axios.get(route('support-reports.get-matches', { query: this.search }));

                if (response.status === 200) {
                    if (!this.reportsBuffer.length) {
                        this.reportsBuffer = this.localReports;
                    }
                    this.localReports = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchFiltered() {
            try {
                this.loading = true;
                const response = await axios.get(route('suscriptions.get-filters', { prop: this.filter[0], value: this.filter[1] }));

                if (response.status === 200) {
                    // si el bufer no tiene nada aun, guardar las suscripciones
                    if (!this.reportsBuffer.length) {
                        this.reportsBuffer = this.localReports;
                    }
                    this.localReports = response.data.items;
                    this.filtered = true;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('suscriptions.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localReports = { ...this.localReports, ...response.data.items };
                    this.currentPage++;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.loadingItems = false;
            }
        },
    }
}
</script>

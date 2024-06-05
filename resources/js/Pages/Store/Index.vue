<template>
    <AppLayout title="Tiendas (suscripciones)">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Tiendas (suscripciones)</h1>
            </div>

            <!-- Buscador y filtros -->
            <section class="flex flex-col lg:flex-row justify-between space-y-3 space-x-3 lg:items-center mt-4 mx-2">
                <div class="lg:w-1/4 relative lg:mr-12">
                    <input v-model="searchTemp" @keyup.enter="handleSearch"
                        class="border border-grayD9 rounded-full w-full h-8 pl-9 focus:ring-0 focus:border-primary"
                        placeholder="Buscar" type="search">
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[9px] left-4"></i>
                </div>
                <el-tag v-if="search" size="large" closable @close="handleTagClose">
                    Estas buscando: <b>{{ search }}</b>
                </el-tag>
                <div class="flex items-center space-x-3 lg:w-1/5">
                    <el-cascader class="w-full" v-model="filter" :options="options" @change="handleChangeFilter"
                        clearable placeholder="Filtrar" />
                </div>
            </section>

            <Loading v-if="loading" class="mt-20" />
            <section v-else class="mt-8">
                <div v-if="localStores.length">
                    <p class="text-gray66 text-[11px] mb-2">{{ localStores.length }}
                        de {{
                            total_stores }}
                        elementos
                    </p>
                    <StoresTable :items="localStores" :sellers="sellers" />
                    <p class="text-gray66 text-[11px] mt-1">{{ localStores.length }}
                        de {{
                            total_stores }}
                        elementos
                    </p>
                    <p v-if="loadingItems" class="text-xs my-4 text-center">
                        Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-secondary"></i>
                    </p>
                    <button
                        v-else-if="!search && !filtered && (total_stores > 10 && localStores.length < total_stores)"
                        @click="fetchItemsByPage"
                        class="w-full text-secondary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
                </div>
                <el-empty v-else description="No hay tiendas para mostrar" />
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import StoresTable from '@/Components/MyComponents/Store/StoresTable.vue';
import axios from 'axios';

export default {
    data() {
        return {
            searchTemp: null,
            search: null,
            storesBuffer: [],
            loading: false,
            localStores: this.stores,
            filter: null,
            filtered: false,
            showFilter: false, //filtro opciones
            loadingItems: false, //para paginación
            currentPage: 1, //para paginación
            options: [
                {
                    label: "Tipo de suscripción ",
                    value: "suscription_period",
                    children: [
                        {
                            label: 'Periodo de prueba',
                            value: 'Periodo de prueba',
                        },
                        {
                            label: 'Mensual',
                            value: 'Mensual',
                        },
                        {
                            label: 'Trimestral',
                            value: 'Trimestral',
                        },
                        {
                            label: 'Semestral',
                            value: 'Semestral',
                        },
                        {
                            label: 'Anual',
                            value: 'Anual',
                        },
                    ],
                },
                {
                    label: "Vendedor",
                    value: "seller_id",
                    children: this.sellers,
                },
                {
                    label: "Estatus",
                    value: "status",
                    children: [
                        {
                            label: 'Pagado',
                            value: 'Pagado',
                        },
                        {
                            label: 'Próximo a vencer ',
                            value: 'Próximo a vencer ',
                        },
                        {
                            label: 'Vencido',
                            value: 'Vencido',
                        },
                    ],
                },
            ],
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        InputLabel,
        Loading,
        StoresTable,
    },
    props: {
        stores: Object,
        total_stores: Number,
        sellers: Array,
    },
    methods: {
        handleSearch() {
            this.filter = null;
            this.search = this.searchTemp;
            this.searchTemp = null;
            if (this.search) {
                this.fetchMatches();
            } else {
                this.showAllStores();
            }
        },
        handleTagClose() {
            this.search = null;
            this.showAllStores();
        },
        handleChangeFilter() {
            this.handleTagClose();
            if (this.filter) {
                this.fetchFiltered();
            }
        },
        showAllStores() {
            // solo si hay items en el buffer, para no dejar vacio el arreglo principal
            if (this.storesBuffer.length) {
                this.localStores = this.storesBuffer;
                this.storesBuffer = [];
            }
        },
        async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('stores.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localStores = [...this.localStores, ...response.data.items];
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
                const response = await axios.get(route('stores.get-matches', { query: this.search }));

                if (response.status === 200) {
                    if (!this.storesBuffer.length) {
                        this.storesBuffer = this.localStores;
                    }
                    this.localStores = response.data.items;
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
                const response = await axios.get(route('stores.get-filters', { prop: this.filter[0], value: this.filter[1] }));

                if (response.status === 200) {
                    // si el bufer no tiene nada aun, guardar las suscripciones
                    if (!this.storesBuffer.length) {
                        this.storesBuffer = this.localStores;
                    }
                    this.localStores = response.data.items;
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
                const response = await axios.get(route('stores.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localStores = { ...this.localStores, ...response.data.items };
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

<template>
    <AppLayout title="Cupones de descuento">
        <main class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Cupones de descuento</h1>
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

                <!-- botón para agregar nuevo cupón -->
                <PrimaryButton @click="$inertia.get(route('discount-tickets.create'))"><i class="fa-solid fa-plus"></i> Nuevo</PrimaryButton>
            </section>

            <Loading v-if="loading" class="mt-20" />
            <section v-else class="mt-8">
                <div v-if="localDiscountTickets?.length">
                    <p class="text-gray66 text-[11px] mb-2">{{ localDiscountTickets?.length }}
                        de {{
                            total_discount_tickets }}
                        elementos
                    </p>
                    <DiscountTicketsTable :items="localDiscountTickets" />
                    <p class="text-gray66 text-[11px] mt-1">{{ localDiscountTickets?.length }}
                        de {{
                            total_discount_tickets }}
                        elementos
                    </p>
                    <p v-if="loadingItems" class="text-xs my-4 text-center">
                        Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-secondary"></i>
                    </p>
                    <button
                        v-else-if="!search && (total_discount_tickets > 50 && localDiscountTickets?.length < total_discount_tickets)"
                        @click="fetchItemsByPage"
                        class="w-full text-secondary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
                </div>
                <el-empty v-else description="No tienes cupones de descuento registrados" />
            </section>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DiscountTicketsTable from '@/Components/MyComponents/DiscountTicket/DiscountTicketsTable.vue';
import Loading from '@/Components/MyComponents/Loading.vue';

export default {
data() {
    return {
        //buscador
        searchTemp: null,
        search: null,

        //table
        localDiscountTickets: this.discount_tickets,
        loadingItems: false,
        currentPage: 1, //para paginación


        //general
        loading: false,
        discountTicketsBuffer: [],
    }
},
components:{
    DiscountTicketsTable,
    PrimaryButton,
    AppLayout,
    Loading
},
props:{
    total_discount_tickets: Number,
    discount_tickets: Array,
},
methods:{
    handleSearch() {
        this.search = this.searchTemp;
        this.searchTemp = null;
        if (this.search) {
            this.fetchMatches();
        } else {
            this.showAllDiscountTickets();
        }
    },
    handleTagClose() {
        this.search = null;
        this.showAllDiscountTickets();
    },
    showAllDiscountTickets() {
        // solo si hay items en el buffer, para no dejar vacio el arreglo principal
        if (this.discountTicketsBuffer.length) {
            this.localDiscountTickets = this.discountTicketsBuffer;
            this.discountTicketsBuffer = [];
        }
    },
    async fetchItemsByPage() {
            try {
                this.loadingItems = true;
                const response = await axios.get(route('discount-tickets.get-by-page', this.currentPage));

                if (response.status === 200) {
                    this.localDiscountTickets = [...this.localDiscountTickets, ...response.data.items];
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
                this.loading = true;
                const response = await axios.get(route('discount-tickets.get-matches', { query: this.search }));

                if (response.status === 200) {
                    if (!this.discountTicketsBuffer.length) {
                        this.discountTicketsBuffer = this.localDiscountTickets;
                    }
                    this.localDiscountTickets = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
}
}
</script>

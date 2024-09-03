<template>
    <AppLayout title="Catálogo base">
        <div ref="scrollContainer" style="height: 93vh; overflow-y: scroll;" @scroll="handleScroll" class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3 mb-3">
                <h1 class="font-bold text-lg">Catálogo base</h1>
                <PrimaryButton id="start" @click="$inertia.get(route('global-products.create'))" class="!rounded-full">Nuevo producto</PrimaryButton>
            </div>

            <!-- buscador -->
            <div class="lg:w-1/4 relative">
                <input v-model="searchQuery" @keydown.enter="searchProducts" class="input w-full pl-9"
                    placeholder="Buscar código o nombre de producto" type="search">
                <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[10px] left-4"></i>
            </div>

            <!-- Selector de giro -->
            <div class="custom-style text-center mt-3">
                <el-segmented v-model="StoreType" :options="options" />
            </div>

            <!-- tabla -->
            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-5 lg:w-11/12">
                <i v-show="showScrollButton" @click="scrollToTop" class="fa-solid animate-bounce fa-arrow-up rounded-full bg-[#F2F2F2] text-gray9A py-3 px-[14px] fixed bottom-8 right-8 cursor-pointer transition-opacity duration-300"></i>
                <p v-if="localProducts?.length" class="text-gray66 text-[11px]">{{ localProducts?.length }} de {{
                    total_products }} elementos
                </p>
                <GlobalProductTable :products="localProducts" />
                <p v-if="localProducts?.length" class="text-gray66 text-[11px]">{{ localProducts?.length }} de {{
                    total_products }} elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
                </p>
                <button v-else-if="total_products > 15 && localProducts?.length < total_products && localProducts?.length"
                    @click="fetchItemsByPage" class="w-full text-primary my-4 text-xs mx-auto underline ml-6">Cargar más
                    elementos</button>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import GlobalProductTable from '@/Components/MyComponents/GlobalProduct/GlobalProductTable.vue';

export default {
data() {
    return {
        loading: false,
        searchQuery: null,
        localProducts: null,
        total_products: null,
        showScrollButton: false, //muestra el  boton para ir al inicio de la vista
        // paginacion
        loadingItems: false,
        currentPage: 1,
        StoreType: 'Abarrotes',
        options: ['Abarrotes', 'Papelería', 'Cerrajería', 'Ferretería', 'Farmacia'],
    }
},
components:{
AppLayout,
GlobalProductTable,
PrimaryButton,
Loading
},
props:{
},
watch:{
    StoreType() {
      this.fetchProductsForType();  
    }
},
methods:{
    handleScroll() {
        const container = this.$refs.scrollContainer;
        // const scrollHeight = container.scrollHeight;
        const scrollTop = container.scrollTop;
        // const clientHeight = container.clientHeight;

        // Determinar si has llegado al final de la vista
        if (scrollTop > 500) {
            this.showScrollButton = true;
        } else {
            this.showScrollButton = false;
        }
    },
    scrollToTop() {
        const section = document.getElementById('start');
        section.scrollIntoView({ behavior: 'smooth' });
    },
    async fetchItemsByPage() {
        try {
            this.loadingItems = true;
            const response = await axios.post(route('global-products.get-by-page', this.currentPage), {type: this.StoreType});

            if (response.status === 200) {
                this.localProducts = [...this.localProducts, ...response.data.items];
                this.currentPage++;
            }
        } catch (error) {
            console.log(error)
        } finally {
            this.loadingItems = false;
        }
    },
    async searchProducts() {
        if ( this.searchQuery != '') {
            try {
                this.loading = true;
            const response = await axios.get(route('global-products.search'), { params: { query: this.searchQuery } });
            if (response.status == 200) {
                this.localProducts = response.data.items;
            }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        } else {
            this.localProducts = this.global_products;
        }
    },
    async fetchProductsForType() {
        try {
            this.loading = true;
            const response = await axios.get(route('global-products.fetch-for-type', this.StoreType));
            if (response.status == 200) {
                this.localProducts = response.data.global_products;
                this.total_products = response.data.total_products;
            }

        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    
    },
},
mounted() {
    this.fetchProductsForType();
}
}
</script>

<style>
.custom-style .el-segmented {
  --el-segmented-item-selected-color: var(--el-text-color-primary);
  --el-segmented-item-selected-bg-color: #FCDCB5;
  --el-border-radius-base: 16px;
}
</style>
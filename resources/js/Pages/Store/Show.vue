<template>
    <AppLayout title="Detalles de suscripción">
        <div class="px-3 lg:px-12 py-5">
            <!-- back -->
            <div class="ml-3 mt-2">
                <Back />
            </div>

            <p class="font-bold mt-3">Detalles de la suscripción</p>

            <!-- selector de suscriptor -->
            <div class="mt-2 flex justify-between space-x-4">
                <el-select @change="$inertia.get(route('stores.show', store_id))" v-model="store_id"
                    clearable placeholder="Seleccione" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias" class="md:!w-1/3">
                    <el-option v-for="item in stores" :key="item" :label="item.name" :value="item.id" />
                </el-select>
                <PrimaryButton @click="$inertia.visit(route('stores.support', store))">Dar soporte</PrimaryButton>
            </div>

            <h1 class="flex items-center justify-center font-bold mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                {{ store.name }}
            </h1>

            <!-- tabs -->
            <section class="mt-2 mx-auto">
                <el-tabs class="mx-3 md:mx-7 lg:mr-10" v-model="activeTab">
                    <el-tab-pane label="Información general" name="1">
                        <GeneralStoreInfo :item="store" />
                    </el-tab-pane>
                    <el-tab-pane label="Módulos activos" name="2">
                        <ActivatedModules :store="store" />
                    </el-tab-pane>
                    <el-tab-pane label="Historial de pagos" name="3">
                        <PaymentsHistory :store="store" />
                    </el-tab-pane>
                </el-tabs>
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import Loading from '@/Components/MyComponents/Loading.vue';
import GeneralStoreInfo from '@/Pages/Store/Tabs/GeneralStoreInfo.vue';
import PaymentsHistory from '@/Pages/Store/Tabs/PaymentsHistory.vue';
import ActivatedModules from '@/Pages/Store/Tabs/ActivatedModules.vue';
import Back from "@/Components/MyComponents/Back.vue";

export default {
    data() {
        return {
            store_id: this.store.id,
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        GeneralStoreInfo,
        ActivatedModules,
        PaymentsHistory,
        PrimaryButton,
        InputLabel,
        Loading,
        Back
    },
    props: {
        store: Object,
        stores: Array,
    },
    methods: {

    }
}
</script>

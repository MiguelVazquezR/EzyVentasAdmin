<template>
    <AppLayout title="Detalles de suscripción">
        <div class="px-12 py-5">
            <p class="font-bold">Detalles de la suscripción</p>

            <!-- selector de suscriptor -->
            <div class="mt-2 flex justify-between">
                <el-select @change="$inertia.get(route('suscriptions.show', suscription_id))" v-model="suscription_id" clearable
                    placeholder="Seleccione" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias" class="md:!w-1/3">
                    <el-option v-for="item in suscriptions" :key="item" :label="item.name" :value="item.id" />
                </el-select>
                <PrimaryButton>Dar soporte</PrimaryButton>
            </div>

            <!-- back -->
            <div class="ml-3 mt-4">
                <Back />
            </div>

            <h1 class="font-bold text-center my-4">{{ suscription.name }}</h1>

            <!-- tabs -->
            <section class="mt-4 mx-auto">
                <el-tabs class="mx-3 md:mx-7 lg:mr-10" v-model="activeTab">
                    <el-tab-pane label="Información general" name="1">
                        <GeneralSuscriptionInfo :item="suscription" />
                    </el-tab-pane>
                    <el-tab-pane label="Historial de pagos" name="2">
                        <PaymentsHistory :item="suscription" />
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
import GeneralSuscriptionInfo from '@/Pages/Suscription/Tabs/GeneralSuscriptionInfo.vue';
import PaymentsHistory from '@/Pages/Suscription/Tabs/PaymentsHistory.vue';
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';

export default {
    data() {
        return {
           suscription_id: null,
           activeTab: '1',
        }
    },
    components: {
        AppLayout,
        GeneralSuscriptionInfo,
        PaymentsHistory,
        PrimaryButton,
        InputLabel,
        Loading,
        Back
    },
    props: {
        suscription: Object,
        suscriptions: Array,
    },
    methods: {
        
    }
}
</script>

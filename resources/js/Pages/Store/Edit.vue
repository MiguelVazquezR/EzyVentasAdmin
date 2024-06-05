<template>
    <AppLayout title="Editar de suscripción">
        edicion de susc.
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
        store: Object,
    },
    methods: {
        
    }
}
</script>

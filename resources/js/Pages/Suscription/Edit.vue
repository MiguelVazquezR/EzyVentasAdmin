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
import SuscriptionsTable from '@/Components/MyComponents/Suscription/SuscriptionsTable.vue';
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
        SuscriptionsTable,
    },
    props: {
        suscription: Object,
    },
    methods: {
        
    }
}
</script>

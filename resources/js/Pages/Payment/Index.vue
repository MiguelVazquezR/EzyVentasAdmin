<template>
    <AppLayout title="Pagos">
        <main class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Pagos</h1>
            </div>

            <section class="flex justify-end items-center mt-4 mx-2">
                <PrimaryButton @click="$inertia.get(route('payments.create'))"><i class="fa-solid fa-plus"></i> Nuevo</PrimaryButton>
            </section>

            <section class="mt-8">
                <div v-if="localPayments?.length">
                    <p class="text-gray66 text-[11px] mb-2">{{ localPayments?.length }}
                        de {{
                            total_payments }}
                        elementos
                    </p>
                    <PaymentsTable :items="localPayments" />
                    <p class="text-gray66 text-[11px] mt-1">{{ localPayments?.length }}
                        de {{
                            total_payments }}
                        elementos
                    </p>
                    <p v-if="loadingItems" class="text-xs my-4 text-center">
                        Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-secondary"></i>
                    </p>
                    <button
                        v-else-if="(total_payments > 50 && localPayments?.length < total_payments)"
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
import PaymentsTable from '@/Components/MyComponents/Payment/PaymentsTable.vue';

export default {
data() {
    return {
        //table
        localPayments: this.payments,
        loadingItems: false,
        currentPage: 1, //para paginación
    }
},
components:{
    PrimaryButton,
    PaymentsTable,
    AppLayout
},
props:{
    total_payments: Number,
    payments: Array,
},
methods:{
    async fetchItemsByPage() {
        try {
            this.loadingItems = true;
            const response = await axios.get(route('payments.get-by-page', this.currentPage));

            if (response.status === 200) {
                this.localPayments = [...this.localPayments, ...response.data.items];
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

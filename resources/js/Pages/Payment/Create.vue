<template>
    <AppLayout title="Crear registro de pago">
        <main class="px-10 py-7">
            <Back />
            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Crear registro de pago</h1>
                
                <div class="mt-3">
                    <InputLabel value="Tienda*" />
                    <el-select v-model="form.store_id" placeholder="Selecciona la tienda" :fit-input-width="true">
                        <el-option v-for="item in stores" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <InputError :message="form.errors.store_id" />
                </div>
                
                <div class="mt-3">
                    <InputLabel value="Método de pago*" />
                    <el-select v-model="form.payment_method" placeholder="Método de pago*" :fit-input-width="true">
                        <el-option v-for="item in paymentMethods" :key="item" :value="item">
                            <span style="float: left">{{ item }}</span>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.payment_method" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Periodo*" />
                    <el-select v-model="form.suscription_period" placeholder="Selecciona el periodo" :fit-input-width="true">
                        <el-option v-for="item in suscriptionPeriods" :key="item" :value="item">
                            <span style="float: left">{{ item }}</span>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.suscription_period" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Monto*" />
                    <el-input
                        v-model="form.amount"
                        placeholder="Ingresa el monto pagado*"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
                    />
                    <InputError :message="form.errors.amount" />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Notas (opcional)" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Notas" :maxlength="500"
                        show-word-limit clearable />
                    <InputError :message="form.errors.notes" />
                </div>

                <!-- <div class="mt-3 col-span-full">
                    <InputLabel value="Fecha de validación" class="ml-3 mb-1" />
                    <el-date-picker
                        v-model="form.validated_at"
                        type="Fecha de validación"
                        placeholder="Selecciona una fecha"
                    />
                    <InputError :message="form.errors.validated_at" />
                </div> -->

                <div class="col-span-full text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">Crear</PrimaryButton>
                </div>
            </form>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        payment_method: null,
        amount: null,
        suscription_period: null,
        store_id: null,
        status: 'Aprobado',
        notes: null,
        // validated_at: new Date().toISOString().slice(0, 10), // Fecha de hoy en formato YYYY-MM-DD
    });

    return {
        form,
        paymentMethods: ['Transferencia / Depósito', 'Tarjeta de crédito o débito', 'Efectivo'],
        suscriptionPeriods: ['Mensual', 'Anual'],
    }
},
components:{
    AppLayout,
    PrimaryButton,
    InputLabel,
    InputError,
    Back

},
props:{
    stores: Array
},
methods:{
    store() {
        this.form.post(route("payments.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
                });
            },
        });
    },
}
}
</script>

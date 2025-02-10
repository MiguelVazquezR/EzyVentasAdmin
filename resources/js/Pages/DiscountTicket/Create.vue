<template>
    <AppLayout title="Nuevo cupón de descuento">
        <main class="px-10 py-7">
            <Back />
            
            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Crear cupón de descuento</h1>
                
                <div class="mt-3 col-span-full">
                    <InputLabel value="Código del cupón*" class="ml-3 mb-1" />
                    <el-input v-model="form.code" placeholder="Escribe el código del cupón" :maxlength="100" clearable />
                    <InputError :message="form.errors.code" />
                </div>

                <div class="mt-3 col-span-full">
                    <el-checkbox v-model="form.is_percentage_discount" label="Descuento en porcentaje" border />
                    <el-checkbox v-model="form.is_active" label="Cupón activo" border />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Descuento*" class="ml-3 mb-1" />
                    <el-input
                        v-model="form.discount_amount"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
                        :maxlength="form.is_percentage_discount ? 3 : null"
                        :min="form.is_percentage_discount ? 1 : null"
                        :max="form.is_percentage_discount ? 100 : null"
                        @input="handleDiscountInput"
                        placeholder="Escribe el descuento del cupón"
                    >
                        <template #prepend>{{ form.is_percentage_discount ? '%' : '$' }}</template>
                    </el-input>
                    <InputError :message="form.errors.discount_amount" />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Fecha de expiración" class="ml-3 mb-1" />
                    <el-date-picker
                        v-model="form.expired_date"
                        type="Fecha de expiración"
                        placeholder="Selecciona una fecha"
                    />
                    <InputError :message="form.errors.expired_date" />
                </div>

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
        code: null,
        is_percentage_discount: null,
        discount_amount: null,
        is_active: true,
        expired_date: null,
    });

    return {
        form,
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
    
},
methods:{
    store() {
        this.form.post(route("discount-tickets.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
                });
                this.form.reset();
            },
        });
    },
    handleDiscountInput(value) {
        if (this.form.is_percentage_discount) {
            let numericValue = Number(value);
            if (numericValue < 1) {
                this.form.discount_amount = null;
            } else if (numericValue > 100) {
                this.form.discount_amount = 100;
            }
        }
    }
}
}
</script>

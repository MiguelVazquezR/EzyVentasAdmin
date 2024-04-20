<template>
    <AppLayout title="Nuevo usuario">
        <div class="px-4 md:px-10 py-7">
            <Back />

            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Nuevo usuario</h1>

                <div class="col-span-full my-4">
                    <InputLabel value="Foto del usuario" class="ml-3 mb-1" />
                    <InputFilePreview @imagen="saveImage" @cleared="form.userImage = null" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Nombre del usuario*" class="ml-3 mb-1" />
                    <el-input v-model="form.name" placeholder="Escribe el nombre del usuario" :maxlength="100" clearable />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Fecha de ingreso*" class="text-sm ml-3" />
                    <el-date-picker v-model="form.created_at" class="!w-full"
                        type="date" placeholder="Seleccione" :disabled-date="disabledDate" />
                    <InputError :message="form.errors.created_at" />
                </div>

                <div class="mt-3">
                    <InputLabel value="Correo electrónico*" class="ml-3 mb-1" />
                    <el-input v-model="form.email" placeholder="Escribe el correo electrónico" :maxlength="100" clearable />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="mt-3">
                    <InputLabel class="mb-1 ml-3" value="Teléfono *" />
                    <el-input v-model="form.phone"
                    :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
                    :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                    placeholder="Escribe el número de teléfono" />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="mt-5 col-span-full">
                    <InputLabel value="Estatus*" class="ml-3 mb-1" />
                    <el-radio-group v-model="form.is_active" class="ml-4">
                        <el-radio :value="true" size="large">Activo</el-radio>
                        <el-radio :value="false" size="large">Inactivo</el-radio>
                    </el-radio-group>
                </div>
                
                <div v-if="!form.is_active" class="mt-3">
                    <InputLabel value="Fecha de baja*" class="text-sm ml-3" />
                    <el-date-picker v-model="form.disabled_date" class="!w-full"
                        type="date" placeholder="Seleccione" />
                    <InputError :message="form.errors.disabled_date" />
                </div>

                <div v-if="!form.is_active" class="mt-3">
                    <InputLabel value="Motivo de baja*" class="ml-3 mb-1" />
                    <el-input v-model="form.disabled_reason" placeholder="Escribe el motivo de la baja" :maxlength="255" clearable />
                    <InputError :message="form.errors.disabled_reason" />
                </div>

                <div class="col-span-2 text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">Crear usuario</PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: null,
        created_at: null,
        email: null,
        phone: null,
        is_active: true,
        disabled_date: null,
        disabled_reason: null,
        userImage: null,
    });

    return {
        form,
    }
},
components:{
AppLayout,
InputFilePreview,
PrimaryButton,
InputLabel,
InputError,
Back
},
props:{

},
methods:{
    store() {
        this.form.post(route("admins.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha agregado un nuevo usuario",
                    type: "success",
                });
            },
        });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
    saveImage(image) {
        this.form.userImage = image;
    },
}
}
</script>

<template>
    <AppLayout title="Nuevo producto global">
        <div class="px-3 lg:px-10 py-7">
            <Back />

            <section class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 text-sm">
                <div class="flex items-center justify-between">
                    <h1 class="font-bold">Detalles del reporte</h1>
                    <p class="text-primary cursor-pointer underline">Ir a las configuraciones de la Tienda <i class="fa-solid fa-arrow-right ml-2"></i></p>
                </div>
                
                <section class="flex space-x-4">
                    <div class="text-gray99 w-56 mt-4">
                        <p>Número de reporte:</p>
                        <p>Fecha de reporte:</p>
                        <p>Tipo de problema:</p>
                        <p class="mb-5">Descripción del problema:</p>
                        <p>Estado del reporte:</p>
                        <p>Responsable:</p>
                        <p class="mb-5">Comentarios:</p>
                        <p>Nombre de la tienda:</p>
                        <p>Contacto:</p>
                        <p>teléfono:</p>
                    </div>

                    <div class="w-full mt-4">
                        <p>{{ support_report.id }}</p>
                        <p>{{ formatDateTime(support_report.created_at) }}</p>
                        <p>{{ support_report.type }}</p>
                        <p class="mb-5">{{ support_report.description }}</p>
                        <p :class="statusStyles(support_report.status)" class="px-2 text-center rounded-full inline-block">{{ support_report.status }}</p>
                        <p>{{ support_report.store.seller?.name }}</p>
                        <p class="mb-5 bg-yellow-100">{{ support_report.notes ?? '-'}}</p>
                        <p>{{ support_report.store?.name }}</p>
                        <p>{{ support_report.store?.contact_name }}</p>
                        <p>{{ support_report.store?.contact_phone }}</p>
                    </div>
                </section>

                <div class="mt-10">
                    <InputLabel value="Comentarios" class="text-sm ml-2 !text-gray-400" />
                    <el-input v-model="form.notes"
                    :autosize="{ minRows: 3, maxRows: 5 }" type="textarea" placeholder="Escribe tus comentarios (opcional)"
                    :maxlength="255" show-word-limit clearable />
                </div>

                <div class="flex justify-end mt-5">
                    <el-dropdown split-button type="primary" @click="$inertia.get(route('support-reports.index'))">
                        Cerrar
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="changeStatus('process')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                    En proceso
                                </el-dropdown-item>
                                <el-dropdown-item @click="changeStatus('solved')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Resuelto
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data() {
    const form = useForm({
        notes: null,
    });

    return {
        form,
    }
},
components:{
AppLayout,
PrimaryButton,
InputLabel,
Back   
},
props:{
support_report: Object
},
methods:{
    formatDateTime(dateTime) {
        let parsedDate = new Date(dateTime);

        return format(parsedDate, 'd MMM, y • hh:mm a', { locale: es }); // Formato personalizado
    },
    statusStyles(status) {
        if ( status === 'Pendiente' ) {
            return 'text-amber-500 bg-amber-100';
        } else if ( status === 'En proceso' ) {
            return 'text-blue-500 bg-blue-100';
        } else if ( status === 'Resuelto' ) {
            return 'text-green-500 bg-green-100';
        }
    },
    changeStatus(newStatus) {
        this.form.put(route('support-reports.change-status', [ this.support_report.id, newStatus ]), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha cambiado el estatus",
                    type: "success",
                });
                this.$inertia.get(route('support-report.index'));
                this.form.reset();
            },
        });
    },
}
}
</script>

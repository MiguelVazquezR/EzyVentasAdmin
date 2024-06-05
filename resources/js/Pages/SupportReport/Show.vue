<template>
    <AppLayout :title="'Detalles de reporte #' + support_report.id">
        <div class="px-3 lg:px-10 py-7">
            <Back :to="route('support-reports.index')" />

            <section class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 text-sm">
                <div class="flex items-center justify-between">
                    <h1 class="font-bold">Detalles del reporte</h1>
                    <Link :href="route('stores.support', support_report.store.id)" class="text-primary underline">
                    Ir a las configuraciones de la Tienda <i class="fa-solid fa-arrow-right ml-2"></i>
                    </Link>
                </div>

                <section class="text-gray99 grid grid-cols-3 gap-x-3 gap-y-1 mt-4">
                    <p>Número de reporte:</p>
                    <p class="text-gray37 col-span-2">{{ support_report.id }}</p>
                    <p>Fecha de reporte:</p>
                    <p class="text-gray37 col-span-2">{{ formatDateTime(support_report.created_at) }}</p>
                    <p>Tipo de problema:</p>
                    <p class="text-gray37 col-span-2">{{ support_report.type }}</p>
                    <p>Descripción del problema:</p>
                    <p class="text-gray37 col-span-2" style="white-space: pre-line;">{{ support_report.description ??
                        '-' }}</p>
                    <p>Estado del reporte:</p>
                    <p class="col-span-2 relative">
                        <i class="fa-solid fa-circle text-[8px] absolute -left-4 bottom-2"
                            :class="{ 'text-[#14D104]': form.status == 'Resuelto', 'text-[#6FBAFE]': form.status == 'En proceso', 'text-[#9E66F8]': form.status == 'Pendiente', }"></i>
                        <el-select v-model="form.status" @change="changeStatus" placeholder="Seleccione" size="small" class="!w-1/2"
                            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in ['Resuelto', 'En proceso', 'Pendiente']" :key="item" :label="item"
                                :value="item">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-circle text-[8px]"
                                        :class="{ 'text-[#14D104]': item == 'Resuelto', 'text-[#6FBAFE]': item == 'En proceso', 'text-[#9E66F8]': item == 'Pendiente', }"></i>
                                    <span>{{ item }}</span>
                                </div>
                            </el-option>
                        </el-select>
                        <InputError :message="form.errors.status" />
                    </p>
                    <p>Responsable:</p>
                    <p class="text-gray37 col-span-2">{{ support_report.store.seller?.name }}</p>
                </section>
                <section class="mt-7 text-gray99 grid grid-cols-3 gap-x-3 gap-y-1">
                    <p>Nombre de la tienda:</p>
                    <p class="text-gray37 col-span-2">{{ support_report.store?.name }}</p>
                    <p>Contacto:</p>
                    <p class="text-gray37 col-span-2">{{ support_report.store?.contact_name }}</p>
                    <p>Teléfono:</p>
                    <p class="text-gray37 col-span-2">{{ support_report.store?.contact_phone }}</p>
                    <p>Documentos adjuntos</p>
                    <div class="text-gray37 col-span-2">
                        <li v-if="support_report.media?.length" v-for="file in support_report?.media" :key="file"
                            class="flex items-center justify-between col-span-full">
                            <a :href="procesarUrlImagen(file.original_url)" target="_blank" class="flex items-center">
                                <i :class="getFileTypeIcon(file.file_name)"></i>
                                <span class="ml-2">{{ file.file_name }}</span>
                            </a>
                        </li>
                        <p v-else>
                            <i class="fa-regular fa-file-excel mr-3"></i>
                            No hay archivos adjuntos
                        </p>
                    </div>
                </section>
                <section class="mt-6">
                    <h2 class="text-gray37">Comentarios</h2>
                    <ul class="my-3 space-y-3">
                        <CommentsCard v-for="item in support_report.comments" :comment="item" />
                    </ul>
                    <CommentsInput @storedComment="addNewComment" :userList="admins" commentableType="App\Models\SupportReport"
                        :commentableId="support_report.id" />
                </section>

                <!-- <div class="flex justify-end mt-5">
                    <PrimaryButton @click="update" :disabled="!form.isDirty">Guardar cambios</PrimaryButton>
                </div> -->
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Back from "@/Components/MyComponents/Back.vue";
import CommentsInput from "@/Components/MyComponents/CommentsInput.vue";
import CommentsCard from "@/Components/MyComponents/CommentsCard.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const form = useForm({
            status: this.support_report.status,
        });

        return {
            form,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        InputLabel,
        InputError,
        Back,
        Link,
        CommentsInput,
        CommentsCard,
    },
    props: {
        support_report: Object,
        admins: Array,
    },
    methods: {
        addNewComment(newComment) {
            this.support_report.comments.push(newComment);
        },
        formatDateTime(dateTime) {
            let parsedDate = new Date(dateTime);

            return format(parsedDate, 'd MMM, y • hh:mm a', { locale: es }); // Formato personalizado
        },
        changeStatus() {
            this.form.put(route('support-reports.update', this.support_report.id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Estado cambiado",
                        message: "",
                        type: "success",
                    });
                },
            });
        },
        getFileTypeIcon(fileName) {
            // Asocia extensiones de archivo a iconos
            const fileExtension = fileName.split('.').pop().toLowerCase();
            switch (fileExtension) {
                case 'pdf':
                    return 'fa-regular fa-file-pdf text-red-700';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fa-regular fa-image text-blue-300';
                default:
                    return 'fa-regular fa-file-lines';
            }
        },
        // Método para procesar la URL de la imagen manda a la ruta de la app.
        procesarUrlImagen(originalUrl) {
            // Reemplaza la parte inicial de la URL
            const nuevaUrl = originalUrl.replace('https://admin.ezyventas.com/', 'https://ezyventas.com/');
            // const nuevaUrl = originalUrl.replace('http://localhost:8000', 'https://ezyventas.com/'); //para hacer pruebas en local
            return nuevaUrl;
        },
    }
}
</script>

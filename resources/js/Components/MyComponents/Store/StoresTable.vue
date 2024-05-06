<template>
    <div>
        <table class="w-full">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                    <th>ID</th>
                    <th>Nombre de la tienda</th>
                    <th>Contacto</th>
                    <th>Correo</th>
                    <th>Suscrito desde</th>
                    <th>Tipo de suscripción</th>
                    <th>Próximo pago</th>
                    <th>Vendedor</th>
                </tr>
            </thead>
            <tbody>
                <tr @click="$inertia.visit(route('stores.show', item))" v-for="item in items" :key="item.id"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">{{ item.id }}</td>
                    <td><i class="mr-1" :class="getStatusIcon()"></i>{{ item.name }}</td>
                    <td>{{ item.contact_name }}</td>
                    <td>{{ item.user?.email ?? '-' }}</td>
                    <td>{{ formatDateTime(item.created_at) }}</td>
                    <td>
                        <button v-if="item.last_payment" @click.stop="prepareSuscriptionModal(item)" class="underline">
                            {{ item.suscription_period }}
                        </button>
                        <span v-else>{{ item.suscription_period }}</span>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <el-tooltip :content="item.status" placement="left">
                                <i class="fa-solid fa-circle text-[8px]" :style="{ color: statuses[item.status] }"></i>
                            </el-tooltip>
                            <span>{{ formatDateTime(item.next_payment) }}</span>
                        </div>
                    </td>
                    <td>{{ item.seller?.name ?? '-' }}</td>
                    <td class="rounded-e-full text-end">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <button @click.stop
                                class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item :command="'see-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Ver</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'edit-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                        <span class="text-xs">Editar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'delete-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-[14px] mr-2 text-red-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span class="text-xs text-red-600">Eliminar</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- -------------- Modal detalles de suscripción (tienda) starts----------------------- -->
    <Modal :show="suscriptionDetailsModal" @close="suscriptionDetailsModal = false; form.reset()">
      <div class="p-4 relative">
        <i @click="suscriptionDetailsModal = false"
          class="fa-solid fa-xmark text-xs cursor-pointer size-7 flex items-center justify-center absolute right-3"></i>

        <form @submit.prevent="update" class="mt-5 mb-2 md:grid grid-cols-2 gap-3 text-sm">
            <h2 class="font-bold col-span-full ml-4">Detalle de pago de suscripción</h2>
            <div class="border border-[#D9D99D99] rounded-lg p-3 col-span-full">
                <p class="mb-4">Información del contacto</p>
                <p class="font-bold">Tienda:</p>
                <p class="">{{ storeSelected.name }}</p>

                <div class="mt-2 grid grid-cols-3 gap-x-3">
                    <p class="font-bold">Contacto:</p>
                    <p class="font-bold">Teléfono:</p>
                    <p class="font-bold">Correo:</p>
                    <p class="">{{ storeSelected.contact_name }}</p>
                    <p class="">{{ storeSelected.contact_phone }}</p>
                    <p class="">{{ storeSelected.user?.email ?? '-' }}</p>
                </div>
            </div>

            <div class="mx-3 col-span-full">
                <h2 class="font-bold">Información de la suscripción</h2>
                
                <div class="mt-3">
                    <p class="font-bold">Fecha de suscripción:</p>
                    <p class="">{{ formatDateTime(storeSelected.created_at) }}</p>
                    <p class="font-bold pt-1">Tipo de suscripción:</p>
                    <p class="">{{ storeSelected.suscription_period }}</p>
                    <p class="font-bold pt-1">Comprobante de pago:</p>
                    <div class="grid grid-cols-2 gap-2" v-if="storeSelected.last_payment.media?.length > 0">
                        <FileView v-for="file in storeSelected.last_payment.media" :key="file" :file="file" />
                    </div>
                    <p v-else class=" text-gray-400 text-xs">No hay archivos adjuntos</p>
                </div>
                <div class="grid grid-cols-2 col-span-full gap-x-4 mt-2">
                    <p class="pt-1 pl-2">Validación de pago:<i class="ml-2 text-xs" :class="getValidationIcon()"></i></p>
                    <p class="pt-1 pl-2">Agregar suscripción</p>
                    <el-select v-model="form.status" clearable
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in paymentValidations" :key="item" :label="item" :value="item" />
                    </el-select>
                    <el-select v-model="form.suscription" clearable
                        placeholder="Seleccione" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in suscriptions" :key="item" :label="item" :value="item" />
                    </el-select>
                    <InputError :message="form.errors.status" />
                    <InputError :message="form.errors.suscription" />
                    <div v-if="form.status === 'Rechazado'" class="col-span-full">
                        <p class="pt-1">Motivo:</p>
                        <el-input v-model="form.rejected_reason" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
                        placeholder="Ej. El ticket no es válido por..." :maxlength="500" show-word-limit clearable />
                    </div>
                </div>
            </div>

          <div class="col-span-full mt-2">
            <InputLabel value="Comentarios:" class="!text-sm ml-2" />
            <el-input v-model="form.notes" :autosize="{ minRows: 3, maxRows: 5 }" type="textarea"
              placeholder="Escribe tus notas" :maxlength="500" show-word-limit clearable />
          </div>
          <div class="flex justify-end space-x-1 pt-2 pb-1 py-2 col-span-full">
            <PrimaryButton :disabled="form.processing">Confirmar</PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
    <!-- --------------------------- Modal detalles de suscripción (tienda) ends ------------------------------------ -->

    <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
        <template #title>
            <h1>Eliminar suscripción</h1>
        </template>
        <template #content>
            <p>Se eliminará la suscripción y todo lo relacionado a la misma. Es un proceso irreversible. ¿Continuar de
                todas formas?</p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                <DangerButton @click="deleteItem">Eliminar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>
<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import FileView from "@/Components/MyComponents/FileView.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const form = useForm({
            status: null,
            suscription: null,
            rejected_reason: null,
            notes: null,
        });

        return {
            form,
            storeSelected: null, //tienda seleccionada para mostrar los detalles de la suscripción en el modal
            suscriptionDetailsModal: false, //modal de detalle de suscripción
            showDeleteConfirm: false,
            itemIdToDelete: null,
            statuses: {
                'Pagado': '#59E304',
                'Próximo a vencer': '#F68C0F',
                'Vencido': '#F32C2C',
            },
            paymentValidations: ['Aprobado', 'Rechazado', 'En revisión'],
            suscriptions: [
                'Periodo de prueba',
                'Mensual',
                'Trimestral',
                'Semestral',
                'Anual'
             ],
        };
    },
    components: {
        ConfirmationModal,
        PrimaryButton,
        DangerButton,
        CancelButton,
        InputLabel,
        InputError,
        FileView,
        Modal
    },
    props: {
        items: Array,
    },
    methods: {
        prepareSuscriptionModal(store) {
            this.suscriptionDetailsModal = true;
            this.storeSelected = store;

            // preparar formulario de modal
            this.form.status = store.last_payment.status;
            this.form.suscription = store.last_payment.suscription_period;
            this.form.rejected_reason = store.last_payment.rejected_reason;
            this.form.notes = store.last_payment.notes;
        },
        update() {
            this.form.put(route('payments.validate', this.storeSelected.last_payment.id), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Validación enviada a cliente',
                        message: '',
                        type: 'success',
                    });
                    // actualizar datos del pago validado
                    this.storeSelected.status = this.form.status;
                    this.storeSelected.suscription_period = this.form.suscription;
                    this.storeSelected.rejected_reason = this.form.rejected_reason;
                    this.storeSelected.notes = this.form.notes;

                    // cerrar modal
                    this.suscriptionDetailsModal = false;
                },
            });
        },
        formatDateTime(dateTime) {
            let parsedDate = new Date(dateTime);

            return format(parsedDate, 'd MMM, y', { locale: es }); // Formato personalizado
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const itemId = command.split('-')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('stores.show', itemId));
            } else if (commandName == 'edit') {
                this.$inertia.get(route('stores.edit', itemId));
            } else if (commandName == 'delete') {
                this.showDeleteConfirm = true;
                this.itemIdToDelete = itemId;
            }
        },
        getStatusIcon() {
            return 'fa-solid fa-bolt text-green-400';
            return 'fa-solid fa-circle-exclamation text-red-600';
        },
        getValidationIcon() {
            if ( this.form.status === 'Aprobado' ) {
                return 'fa-solid fa-check text-green-400';
            } else if ( this.form.status === 'Rechazado' ) {
                return 'fa-solid fa-xmark text-red-500';
            }
            return 'fa-regular fa-clock text-amber-400';
        },
    },
}
</script>
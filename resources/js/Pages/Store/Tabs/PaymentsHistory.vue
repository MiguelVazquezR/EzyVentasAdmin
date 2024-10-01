<template>
    <section v-if="store.payments?.length" class="mt-7 text-sm w-full overflow-auto">
        <table class="w-full">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                    <th>Fecha de pago</th>
                    <th>Tipo de suscripción</th>
                    <th>Método de pago</th>
                    <th>Monto</th>
                    <th>Estatus de pago</th>
                    <th>Estatus de factura</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in store.payments" :key="item" @click="openDetailsModal(index)"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">{{ formatDate(item.created_at) }}</td>
                    <td>{{ item.suscription_period }}</td>
                    <td>{{ item.payment_method }}</td>
                    <td>${{ item.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    <td>
                        <p :class="{
                            'text-greenSuccess': item.status == 'Aprobado',
                            'text-redDanger': item.status == 'Rechazado',
                            'text-amber-500': item.status == 'En revisión',
                        }">{{ item.status }}</p>
                    </td>
                    <td>
                        <p :class="{
                            'text-greenSuccess': item.invoice_status == 'Enviada',
                            'text-gray99': item.invoice_status == 'No solicitada',
                            'text-amber-500': item.invoice_status == 'Solicitada',
                        }">{{ item.invoice_status }}</p>
                    </td>
                    <td class="rounded-e-full text-end">
                        <!-- <el-dropdown trigger="click" @command="handleCommand">
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
                                    <el-dropdown-item v-if="item.status != 'Aprobado'" :command="'approve-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span class="text-xs">Aprobar</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="item.status != 'Rechazado'" :command="'reject-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                        <span class="text-xs">Rechazar</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
</el-dropdown> -->
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
    <el-empty v-else description="No hay pagos registrados" :image-size="90" />

    <DialogModal :show="showDetailsModal" @close="showDetailsModal = false">
        <template #title>
            <h1>Detalles de pago</h1>
        </template>
        <template #content>
            <section>
                <h1 class="font-bold">Datos generales del pago</h1>
                <span>...</span>
            </section>
            <section class="mt-6">
                <h1 class="font-bold">Factura</h1>
                <div v-if="getStoreCSF">
                    <p>
                        <span>Actualmente contamos con una constancia registrada. </span> <br>
                        <a :href="changeDomain(getStoreCSF.original_url)" target="_blank" class="text-primary">
                            Ver contancia de situación fiscal
                        </a>
                    </p>

                    <div v-if="getSelectedPaymentInvoice" class="mt-6">
                        <span>Ya se ha adjuntado la factura a este pago. </span> <br>
                        <a :href="getSelectedPaymentInvoice.original_url" target="_blank" class="text-primary">
                            Ver factura
                        </a>
                    </div>
                    <div v-else class="mt-6">
                        <InputLabel value="Subir factura (.zip o .rar)" />
                        <FileUploader @files-selected="invoiceForm.invoice = $event" :multiple="false"
                            acceptedFormat="comprimido" />
                        <InputError :message="invoiceForm.errors.invoice" />
                    </div>
                </div>
                <div v-else>
                    <p>No ha registrada ninguna constancia de situación fiscal.</p>
                </div>
            </section>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDetailsModal = false" :disabled="invoiceForm.processing">Cerrar</CancelButton>
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737"
                    :title="'Se notificará al suscriptor, ¿continuar?'"
                    @confirm="notifyFiscalDataError()">
                    <template #reference>
                        <PrimaryButton
                            v-if="store.payments[selectedPaymentIndex].invoice_status != 'Enviada'"
                            :disabled="invoiceForm.processing || notifying" class="!bg-red-700">
                            <i v-if="notifying" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                            Error en datos fiscales
                        </PrimaryButton>
                    </template>
                </el-popconfirm>
                <PrimaryButton @click="storeInvoice"
                    v-if="store.payments[selectedPaymentIndex].invoice_status != 'Enviada'"
                    :disabled="invoiceForm.processing || !invoiceForm.invoice.length">
                    <i v-if="invoiceForm.processing"
                        class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Subir factura
                </PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>

<script>
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from '@/Components/DialogModal.vue';
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";

export default {
    data() {
        const invoiceForm = useForm({
            invoice: [],
        });

        return {
            invoiceForm,
            showDetailsModal: false,
            selectedPaymentIndex: null,
            rejectedReazon: null,
            itemToShow: null,
            notifying: false,
        }
    },
    components: {
        DialogModal,
        CancelButton,
        PrimaryButton,
        InputError,
        InputLabel,
        FileUploader,
    },
    props: {
        store: Object
    },
    computed: {
        getStoreCSF() {
            return this.store.media.find(item => item.collection_name == 'csf');
        },
        getSelectedPaymentInvoice() {
            return this.store.payments[this.selectedPaymentIndex].media.find(item => item.collection_name == 'invoice');
        },
    },
    methods: {
        changeDomain(url) {
            // en local no se hace ningun cambio de dominio
            // if (import.meta.env.VITE_APP_ENV == 'local') return url;

            const oldDomain = 'https://admin.ezyventas.com';
            const newDomain = 'https://ezyventas.com';

            // Reemplaza el dominio antiguo con el nuevo
            if (url.startsWith(oldDomain)) {
                return url.replace(oldDomain, newDomain);
            }
            return url; // Si no coincide, devuelve la URL sin cambios
        },
        storeInvoice() {
            this.invoiceForm.post(route('payments.store-invoice', this.store.payments[this.selectedPaymentIndex]), {
                onSuccess: () => {
                    this.invoiceForm.reset();
                    this.showDetailsModal = false;
                    this.$notify({
                        title: "Factura guardada",
                        type: "success"
                    });
                }
            });
        },
        notifyFiscalDataError() {
            this.invoiceForm.put(route('payments.notify-fiscal-data-error', this.store.payments[this.selectedPaymentIndex]), {
                onStart: () => {
                    this.notifying = true;
                },
                onSuccess: () => {
                    this.invoiceForm.reset();
                    this.showDetailsModal = false;
                    this.$notify({
                        title: "Notificación enviada a suscriptor",
                        type: "success"
                    });
                },
                onFinish: () => {
                    this.notifying = false;
                },
            });
        },
        openDetailsModal(paymentIndex) {
            this.selectedPaymentIndex = paymentIndex;
            this.showDetailsModal = true;
        },
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy • hh:mm a', { locale: es });
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const itemId = command.split('-')[1];
            const item = this.store.payments.find(payment => payment.id == itemId);

            if (commandName == 'see') {
                this.showDetailsModal = true;
                this.itemToShow = item;
            } else if (commandName == 'approve') {
                this.$inertia.put(route('payments.update-status', itemId), { status: 'Aprobado', rejected_reazon: null });
                this.item.status = 'Aprobado';
            } else if (commandName == 'reject') {
                this.$inertia.put(route('payments.update-status', itemId), { status: 'Rechazado', rejected_reazon: this.rejectedReazon });
                this.item.status = 'Rechazado';
            }
        },
    }
}
</script>

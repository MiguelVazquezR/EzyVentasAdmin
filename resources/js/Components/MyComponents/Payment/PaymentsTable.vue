<template>
    <table class="w-full">
        <thead>
            <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                <th>ID</th>
                <th>Metodo de pago</th>
                <th>Periodo</th>
                <th>Monto</th>
                <th>Tienda</th>
                <th>Estatus</th>
                <th>Fecha de pago</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in items" :key="item.id"
                class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight">
                <td class="rounded-s-full">{{ item.id }}</td>
                <td>{{ item.payment_method }}</td>
                <td>{{ item.suscription_period }}</td>
                <td>${{ item.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                <td>{{ item.store?.name }}</td>
                <td>{{ item.status }}</td>
                <td>{{ formatDateTime(item.created_at) ?? '-' }}</td>
                <td class="rounded-e-full text-end">
                    <el-dropdown trigger="click" @command="handleCommand">
                        <button @click.stop
                            class="el-dropdown-link justify-center items-center size-6 hover:bg-primary hover:text-primarylight rounded-full text-primary transition-all duration-200 ease-in-out">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
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

    <!-- Delete confirm modal -->
    <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
        <template #title>
            <h1>Eliminar registro de pago</h1>
        </template>
        <template #content>
            <p>Se eliminará el registro de pago. Es un proceso irreversible. ¿Continuar de
                todas formas?</p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                <DangerButton @click="deleteItem()">Eliminar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
data() {
    return {
        showDeleteConfirm: false,
        itemIdToDelete: null,
    }
},
components:{
    CancelButton,
    ConfirmationModal,
    DangerButton,
},
props: {
    items: Array,
},
methods:{
    formatDateTime(dateTime) {
        if ( dateTime ) {
            let parsedDate = new Date(dateTime);
            return format(parsedDate, 'd MMM, y', { locale: es }); // Formato personalizado
        }
    },
    handleCommand(command) {
        const commandName = command.split('-')[0];
        const itemId = command.split('-')[1];

        if (commandName == 'edit') {
            this.$inertia.get(route('payments.edit', itemId));
        } else if (commandName == 'delete') {
            this.itemIdToDelete = itemId;
            this.showDeleteConfirm = true;
        }
    },
    async deleteItem() {
        try {
            const response = await axios.delete(route('payments.destroy', this.itemIdToDelete));
            if ( response.status === 200 ) {
                //buscar el index del item eliminado
                const itemToDeleteIndex = this.items.findIndex(item => item.id == this.itemIdToDelete);
                if ( itemToDeleteIndex !== -1 ) { //si se encontró el index se elimina del arreglo
                    this.items.splice(itemToDeleteIndex, 1);
                    this.showDeleteConfirm = false;
                    
                    this.$notify({
                        title: "Correcto",
                        message: "",
                        type: "success",
                    });
                }
            }
        } catch (error) {
            console.log(error);
        }
    }
}
}
</script>

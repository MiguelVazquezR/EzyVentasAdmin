<template>
    <table class="w-full">
        <thead>
            <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                <th>ID</th>
                <th>Código</th>
                <th>Descuento</th>
                <th>Creado el</th>
                <th>Expiración</th>
                <th>Estatus</th>
                <th>Veces utilizado</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in items" :key="item.id"
                class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight">
                <td class="rounded-s-full">{{ item.id }}</td>
                <td>{{ item.code }}</td>
                <td v-if="item.is_percentage_discount">{{ item.discount_amount }}%</td>
                <td v-else>${{ item.discount_amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                <td>{{ formatDateTime(item.created_at) ?? '-' }}</td>
                <td>{{ formatDateTime(item.expired_date) ?? '-' }}</td>
                <td>
                    <span class="py-1 px-3 rounded-md" :class="item.is_active ? 'text-green-500 bg-green-100' : 'text-red-600 bg-red-100'">{{ item.is_active ? 'Activo' : 'Inactivo' }}</span>
                </td>
                <td>{{ item.times_used ?? '0' }}</td>
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
                                <el-dropdown-item :command="'toogleStatus-' + item.id">
                                    <svg v-if="item.is_active" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>

                                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="text-xs">{{ item.is_active ? 'Deshabilitar' : 'Habilitar' }}</span>
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
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
data() {
    return {

    }
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
            this.$inertia.get(route('discount-tickets.edit', itemId));
        } else if (commandName == 'toogleStatus') {
            this.toogleStatus(itemId);
        } else if (commandName == 'delete') {
            this.$inertia.get(route('discount-tickets.delete', itemId));
        }
    },
    async toogleStatus(itemId) {
        try {
            const response = await axios.get(route('discount-tickets.toggle-status', itemId));
            if ( response.status === 200 ) {
                //hace toogle en el status del cupón
                const itemToUpdate = this.items.find(item => item.id == itemId);
                itemToUpdate.is_active = !itemToUpdate.is_active;

                this.$notify({
                title: "Correcto",
                message: "",
                type: "success",
            });
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: "Error",
                message: "No se pudo cambiar el estatus del cupón. Inténtalo de nuevo",
                type: "error",
            });
        }
    }
}
}
</script>

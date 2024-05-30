<template>
    <div v-if="items.length">
        <table class="w-full">
            <thead>
                <tr class="*:text-left *:pb-2 *:px-4 *:text-sm">
                    <th># reporte</th>
                    <th>Nombre de la tienda</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Fecha de reporte</th>
                    <th>Responsable</th>
                    <th>Tipo de reporte</th>
                </tr>
            </thead>
            <tbody>
                <tr @click="$inertia.visit(route('support-reports.show', item))" v-for="item in items" :key="item.id"
                    class="*:text-xs *:py-2 *:px-4 hover:bg-primarylight cursor-pointer">
                    <td class="rounded-s-full">{{ item.id }}</td>
                    <td>{{ item.store?.name }}</td>
                    <td>{{ item.store?.contact_name }}</td>
                    <td>{{ item.store?.contact_phone }}</td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <el-tooltip :content="item.status" placement="left">
                                <i class="fa-solid fa-circle text-[8px]"
                                    :class="{ 'text-[#14D104]': item.status == 'Resuelto', 'text-[#6FBAFE]': item.status == 'En proceso', 'text-[#9E66F8]': item.status == 'Pendiente', }"></i>
                            </el-tooltip>
                            <span>{{ formatDateTime(item.created_at) }}</span>
                        </div>
                    </td>
                    <td>{{ item.store?.seller?.name }}</td>
                    <td>{{ item.type }}</td>
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
                                    <el-dropdown-item :command="'settings-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <span class="text-xs">Conf. de la tienda</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'process-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span class="text-xs">Marcar en proceso</span>
                                    </el-dropdown-item>
                                    <el-dropdown-item :command="'solved-' + item.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <span class="text-xs">Marcar como resuelto</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <el-empty v-else description="No hay reportes registrados" />
</template>
<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
    data() {
        return {
        };
    },
    components: {
        ConfirmationModal,
        DangerButton,
        CancelButton,
    },
    props: {
        items: Array,
    },
    methods: {
        formatDateTime(dateTime) {
            let parsedDate = new Date(dateTime);

            return format(parsedDate, 'd MMM, y • hh:mm a', { locale: es }); // Formato personalizado
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const itemId = command.split('-')[1];

            if (commandName == 'see') {
                this.$inertia.get(route('support-reports.show', itemId));
            } else if (commandName == 'settings') {
                const store = this.items.find(item => item.id == itemId).store;
                this.$inertia.visit(route('stores.support', store.id));
            } else if (commandName == 'process' || commandName == 'solved') {
                this.changeStatus(commandName, itemId);
            }
        },
        async changeStatus(commandName, itemId) {
            try {
                const response = await axios.put((route('support-reports.change-status', [itemId, commandName])));
                if (response.status == 200) {
                    const itemIndex = this.items.findIndex(item => item.id == itemId);
                    if (itemIndex != -1) {
                        if (commandName == 'process') {
                            this.items[itemIndex].status = 'En proceso';
                        } else {
                            this.items[itemIndex].status = 'Resuelto';
                        }
                        this.$notify({
                            title: "Éxito",
                            message: "Se ha cambiado el estatus",
                            type: "success",
                        });
                    }
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: "Algo salió mal",
                    message: "No se pudo cambiar el estatus. Inténtalo más tarde",
                    type: "error",
                });
            }
        },
    },
}
</script>
<template>
    <section class="grid grid-cols-2 w-full overflow-auto">
        <!-- seccion izquierda de informacion -->
        <div class="flex space-x-2">
            <div class="w-32 text-[#999999] text-sm space-y-2 mx-4">
                <p>ID:</p>
                <p>Fecha de ingreso:</p>
                <p>Nombre:</p>
                <p>Puesto:</p>
            </div>
            <div class="w-32 text-sm space-y-2 mx-4">
                <p>{{ user.id }}</p>
                <p>{{ formatDate(user.created_at) }}</p>
                <p>{{ user.name }}</p>
                <p>{{ user.employee_properties?.rol ?? '-' }}</p>
            </div>
        </div>

        <!-- seccion derecha de informacion -->
        <div class="flex space-x-2">
            <div class="w-32 text-[#999999] text-sm space-y-2 mx-4">
                <p>Correo electrónico:</p>
                <p>Teléfono:</p>
                <p>Estatus:</p>
            </div>
            <div class="w-32 text-sm space-y-2 mx-4">
                <p>{{ user.email ?? '-' }}</p>
                <p>{{ user.phone ?? '-' }}</p>
                <p :class="user.is_active ? 'text-green-500' : 'text-red-600' ">{{ user.is_active ? 'Activo' : 'Inactivo' }}</p>
            </div>
        </div>
    </section>
</template>

<script>
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';

export default {
data() {
    return {

    }
},
components:{},
props:{
user: Object    
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
}
}
</script>

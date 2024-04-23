<template>
    <section class="grid grid-cols-2 gap-y-7 w-full overflow-auto">

        <!-- seccion izquierda de informacion -->
        <div class="mx-5">
            <h2 class="font-bold text-black my-4">Información de la tienda</h2>
            <div class="flex space-x-2">
                <div class="w-48 text-[#999999] text-sm space-y-2">
                    <p>ID:</p>
                    <p>Fecha de suscripción:</p>
                    <p>Nombre de la tienda:</p>
                    <p>Dirección:</p>
                </div>
                <div class="w-48 text-sm space-y-2">
                    <p>{{ item.id }}</p>
                    <p>{{ formatDate(item.created_at) }}</p>
                    <p>{{ item.name ?? '-' }}</p>
                    <p>{{ item.address ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- seccion derecha de informacion -->
        <div class="mx-5">
            <h2 class="font-bold text-black my-4">Información de la suscripción</h2>
            <div class="flex space-x-2">
                <div class="w-48 text-[#999999] text-sm space-y-2">
                    <p>Tipo de suscripción:</p>
                    <p>Próximo pago:</p>
                    <p>Estatus:</p>
                    <p>Vendedor:</p>
                </div>
                <div class="w-48 text-sm space-y-2">
                    <p>{{ item.suscription_period }}</p>
                    <p>{{ formatDate(item.next_payment) }}</p>
                    <p>{{ item.is_active }}</p>
                    <p>{{ item.seller?.name }}</p>
                </div>
            </div>
        </div>

        <!-- seccion 3 de informaión -->
        <div class="mx-5">
            <h2 class="font-bold text-black my-4">Información del contacto</h2>
            <div class="flex space-x-2">
                <div class="w-48 text-[#999999] text-sm space-y-2">
                    <p>Nombre del contacto:</p>
                    <p>Teléfono:</p>
                    <p>Correo electrónico:</p>
                </div>
                <div class="w-48 text-sm space-y-2">
                    <p>{{ item.contact_name }}</p>
                    <p>{{ item.contact_phone }}</p>
                    <p>{{ item.email ?? '-' }}</p>
                </div>
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
item: Object    
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
}
}
</script>

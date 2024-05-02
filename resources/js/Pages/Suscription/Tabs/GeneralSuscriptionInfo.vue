<template>
    <section class="grid grid-cols-2 gap-y-7 w-full overflow-auto">
        <!-- seccion izquierda de informacion -->
        <div class="mx-5">
            <h2 class="font-bold text-black my-4">Información de la tienda</h2>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <p class="text-[#999999]">ID:</p>
                <p>{{ item.id }}</p>
                <p class="text-[#999999]">Fecha de suscripción:</p>
                <p>{{ formatDate(item.created_at) }}</p>
                <p class="text-[#999999]">Nombre de la tienda:</p>
                <p>{{ item.name ?? '-' }}</p>
                <p class="text-[#999999]">Dirección:</p>
                <p>{{ item.address ?? '-' }}</p>
            </div>
        </div>
        <!-- seccion derecha de informacion -->
        <div class="mx-5">
            <h2 class="font-bold text-black my-4">Información de la suscripción</h2>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <p class="text-[#999999]">Tipo de suscripción:</p>
                <p>{{ item.suscription_period }}</p>
                <p class="text-[#999999]">Próximo pago:</p>
                <p>{{ formatDate(item.next_payment) }}</p>
                <p class="text-[#999999]">Estatus:</p>
                <p>{{ item.is_active }}</p>
                <p class="text-[#999999]">Vendedor:</p>
                <p>{{ item.seller?.name }}</p>
            </div>
        </div>
        <!-- seccion 3 de informaión -->
        <div class="mx-5">
            <h2 class="font-bold text-black my-4">Información del contacto</h2>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <p class="text-[#999999]">Nombre del contacto:</p>
                <p>{{ item.contact_name }}</p>
                <p class="text-[#999999]">Teléfono:</p>
                <p>{{ item.contact_phone }}</p>
                <p class="text-[#999999]">Correo electrónico:</p>
                <p>{{ item.email ?? '-' }}</p>
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
    components: {},
    props: {
        item: Object
    },
    methods: {
        formatDate(dateString) {
            return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
        },
    }
}
</script>

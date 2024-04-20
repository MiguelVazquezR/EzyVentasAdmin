<template>
    <p class="text-sm mx-2">Total de tiendas administradas: <strong class="ml-2">{{ stores.length }}</strong></p>

    <section v-if="stores.length" class="mt-7 text-sm w-full overflow-auto">
        <table>
            <tr class="font-bold *:pb-3 *:px-2">
                <td class="w-14">ID</td>
                <td class="w-40 truncate">Nombre</td>
                <td class="w-40">Contácto</td>
                <td class="w-36">Susripción</td>
                <td class="w-36">Último pago</td>
                <td class="w-32">Estatus</td>
            </tr>
            <tr class="*:px-2" v-for="store in stores" :key="store">
                <td>{{ store.id }}</td>
                <td :title="store.name" class="w-40 truncate">{{ store.name }}</td>
                <td :title="store.contact_name" class="w-40 truncate">{{ store.contact_name }}</td>
                <td>{{ store.plan }}</td>
                <td>{{ formatDate(store.next_payment) }}</td>
                <td>{{ store.is_active }}</td>
            </tr>
        </table>
    </section>
    <p class="text-sm text-center text-gray-400 mt-5" v-else>No administra ninguna tienda</p>
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
stores: Object    
},
methods:{
    formatDate(dateString) {
        return format(parseISO(dateString), 'dd MMMM yyyy', { locale: es });
    },
}
}
</script>

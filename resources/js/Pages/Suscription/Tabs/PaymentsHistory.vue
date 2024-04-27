<template>
    <section v-if="item.payments?.length" class="mt-7 text-sm w-full overflow-auto">
        <table>
            <tr class="font-bold *:pb-3 *:px-2">
                <td class="w-40">Fecha de pago</td>
                <td class="w-40 truncate">Tipo de suscripción</td>
                <td class="w-40">Método de pago</td>
                <td class="w-36">Monto</td>
                <td class="w-36">Venció hace</td>
                <td class="w-32">Próximo pago</td>
            </tr>
            <tr class="*:px-2" v-for="item in item.payments" :key="item">
                <td>{{ formatDate(item.created_at) }}</td>
                <td>{{ item.suscription_period }}</td>
                <td>{{ item.payment_method }}</td>
                <td>${{ item.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",") }}</td>
                <td>{{ item.is_active ?? '-' }}</td>
                <td>{{ formatDate(item.next_payment) }}</td>
            </tr>
        </table>
    </section>
    <p class="text-sm text-center text-gray-400 mt-5" v-else>No hay pagos registrados</p>
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

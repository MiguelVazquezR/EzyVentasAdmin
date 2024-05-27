<template>
    <AppLayout title="Soporte">
        <div class="px-2 lg:px-10 py-7">
            <Back :to="route('stores.show', store)" />
            <div class="flex justify-between items-center mb-4 mt-3">
                <h1 class="font-bold text-lg">Soporte</h1>
            </div>
            <div class="flex items-center space-x-1 justify-between mb-1 *:w-1/3">
                <p></p>
                <p class="text-sm text-center">{{ store.name }}</p>
                <div class="flex justify-end">
                    <ToggleButton :labels="['Configuraciones', 'Historial']" @update="handleToggle" class="w-72" />
                </div>
            </div>

            <section v-if="activeSection === 'Configuraciones'">
                <!-- tabs options -->
                <el-tabs v-model="activeTab" @tab-click="updateURL">
                    <el-tab-pane label="Punto de venta" name="1">
                        <Point :store="store" />
                    </el-tab-pane>
                    <!-- <el-tab-pane label="Usuarios" name="2">
                        <Users :users="users" />
                    </el-tab-pane> -->
                </el-tabs>
            </section>
            <section v-else="activeSection === 'Historial'" class="mt-10">
                <SettingsHistory :store="store" />
            </section>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ToggleButton from '@/Components/MyComponents/ToggleButton.vue';
import Back from '@/Components/MyComponents/Back.vue';
import Point from './Tabs/Point.vue';
import Users from './Tabs/Users.vue';
import SettingsHistory from './Tabs/SettingsHistory.vue';

export default {
    data() {
        return {
            activeSection: 'Configuraciones',
            // configuraciones
            activeTab: '1',
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        ToggleButton,
        Users,
        Point,
        SettingsHistory,
        Back,
    },
    props: {
        store: Object,
    },
    methods: {
        handleToggle(active) {
            this.activeSection = active;
        },
        updateURL(tab) {
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab.props.name);
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
        },
        setActiveTabFromURL() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.activeTab = tab;
            }
        },
    },
    mounted() {
        this.setActiveTabFromURL();
    }
}
</script>
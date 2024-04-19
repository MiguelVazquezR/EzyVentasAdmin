<template>
  <AppLayout title="Suscripciones">
        <div class="px-2 lg:px-10 py-7">
            <!-- header botones -->
            <div class="flex justify-between items-center mx-3">
                <h1 class="font-bold text-lg">Usuarios</h1>
            </div>

            <!-- Buscador -->
            <section class="flex flex-col justify-between lg:flex-row space-x-3 lg:items-center mx-2">
                <div class="lg:w-1/4 relative lg:mr-12">
                    <input v-model="searchTemp" @keyup.enter="handleSearch"
                        class="border border-grayD9 rounded-full w-full h-8 pl-9 focus:ring-0 focus:border-primary"
                        placeholder="Buscar" type="search">
                    <i class="fa-solid fa-magnifying-glass text-xs text-gray99 absolute top-[9px] left-4"></i>
                </div>
                <el-tag v-if="search" class="mt-2 lg:mt-0" size="large" closable @close="handleTagClose">
                    Estas buscando: <b>{{ search }}</b>
                </el-tag>
                <PrimaryButton @click="$inertia.get('admins.create')">Nuevo uruario</PrimaryButton>
            </section>

            <Loading v-if="loading" class="mt-20" />
            <div v-else class="mt-8">
                <p v-if="localUsers.length" class="text-gray66 text-[11px] mb-2">{{ localUsers.length }}
                    de {{
                        total_users }}
                    elementos
                </p>
                <UsersTable :items="localUsers" />
                <p v-if="localUsers.length" class="text-gray66 text-[11px] mt-1">{{ localUsers.length }}
                    de {{
                        total_users }}
                    elementos
                </p>
                <p v-if="loadingItems" class="text-xs my-4 text-center">
                    Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-secondary"></i>
                </p>
                <button
                    v-else-if="localUsers.length && !search && !filtered && (total_users > 10 && localUsers.length < total_users)"
                    @click="fetchItemsByPage"
                    class="w-full text-secondary my-4 text-xs mx-auto underline ml-6">Cargar más elementos</button>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import UsersTable from '@/Components/MyComponents/User/UsersTable.vue';
import axios from 'axios';

export default {
data () {
    return {
        usersBuffer: [],
        searchTemp: null,
        search: null,
        loading: false,
        localUsers: this.users,
        loadingItems: false, //para paginación
        currentPage: 1, //para paginación
    }
},
components:{
    AppLayout,
    PrimaryButton,
    UsersTable,
    Loading,
},
props:{
    users: Object,
    total_users: Number,
},
methods:{
    handleSearch() {
        this.search = this.searchTemp;
        this.searchTemp = null;
        if (this.search) {
            this.fetchMatches();
        } else {
            this.showAllUsers();
        }
    },
    handleTagClose() {
        this.search = null;
        this.showAllUsers();
    },
    showAllUsers() {
        // solo si hay items en el buffer, para no dejar vacio el arreglo principal
        if (this.usersBuffer.length) {
            this.localUsers = this.usersBuffer;
            this.usersBuffer = [];
        }
    },
    async fetchItemsByPage() {
        try {
            this.loadingItems = true;
            const response = await axios.get(route('admins.get-by-page', this.currentPage));

            if (response.status === 200) {
                this.localUsers = [...this.localUsers, ...response.data.items];
                this.currentPage++;
            }
        } catch (error) {
            console.log(error)
        } finally {
            this.loadingItems = false;
        }
    },
    async fetchMatches() {
        try {
            this.loading = true;
            const response = await axios.get(route('admins.get-matches', { query: this.search }));

            if (response.status === 200) {
                if (!this.usersBuffer.length) {
                    this.usersBuffer = this.localUsers;
                }
                this.localUsers = response.data.items;
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
}
}
</script>

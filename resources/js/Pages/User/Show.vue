<template>
    <AppLayout :title="user.name">
        <!-- Sección de fotografía -->
        <section class="bg-[#EDEDED] h-48 relative">
            <div class="border border-[#D9D9D9] rounded-lg absolute top-20 left-12 lg:left-20 h-60 w-72 bg-white">
                <figure class="size-40 flex justify-center items-center mx-auto mt-7">
                    <img class="rounded-full w-full" v-if="user.media?.length" :src="user.media[0]?.original_url" alt="">
                    <i v-else class="fa-regular fa-user text-gray-300 text-7xl"></i>
                </figure>
                <p class="font-bold text-center mt-3">{{ user.name }}</p>
            </div>
        </section>

        <!-- back -->
        <div class="ml-3 mt-4">
            <Back />
        </div>

        <!-- botones -->
        <div class="mt-28 md:mt-4 flex justify-end space-x-2 mx-4">
            <PrimaryButton @click="$inertia.get(route('admins.edit', user.id))">Editar</PrimaryButton>
            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(user.id)">
                <template #reference>
                    <i @click.stop class="fa-regular fa-trash-can text-primary cursor-pointer bg-gray-100 rounded-full p-2"></i>
                </template>
            </el-popconfirm>
        </div>

        <section class="mt-16 xl:mt-4 ml-auto xl:w-2/3">
            <el-tabs class="mx-3 md:mx-7 lg:mr-10" v-model="activeTab">
                <el-tab-pane label="Información general" name="1">
                    <GeneralUserInfo :user="user" />
                </el-tab-pane>
                <el-tab-pane label="Admin tiendas" name="2">
                    <StoresAdmin :stores="user.stores" />
                </el-tab-pane>
            </el-tabs>
        </section>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import GeneralUserInfo from '@/Pages/User/Tabs/GeneralUserInfo.vue';
import StoresAdmin from '@/Pages/User/Tabs/StoresAdmin.vue';
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';

export default {
data() {
    return {
        activeTab: '1',
    }
},
components:{
AppLayout,
StoresAdmin,
PrimaryButton,
GeneralUserInfo,
Back
},
props:{
user: Object
},
methods:{
    async deleteItem(userId) {
        try {
            const response = await axios.delete(route('admins.destroy', userId));
            if (response.status == 200) {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha agregado un nuevo usuario",
                    type: "success",
                });
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: "Error",
                message: "No se pudo eliminar al usuario. Intenta más tarde",
                type: "error",
            });
        } 
        
    }
}
}
</script>

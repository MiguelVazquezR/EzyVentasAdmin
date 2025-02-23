<template>
    <main 
        class="relative z-0 bg-white bg-opacity-70 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden cursor-pointer transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl"
        @click="openVideo(video)"
    >
        <div class="relative">
            <img 
                :src="`https://img.youtube.com/vi/${video.youtube_id}/hqdefault.jpg`" 
                alt="Miniatura del video" 
                class="w-full h-48 object-cover transition-opacity duration-300 hover:opacity-80"
            />

            <!-- Edit button movil -->
            <button @click.stop="$inertia.get(route('tutorials.edit', this.video.id))" class="lg:hidden size-9 rounded-full flex items-center justify-center bg-gray-700 absolute top-3 right-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white font-bold">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>
            </button>

            <!-- Delete button movil -->
            <button @click.stop="showDeleteConfirm = true" class=" lg:hidden size-9 rounded-full flex items-center justify-center bg-gray-700 absolute top-3 left-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white font-bold">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </button>

            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 opacity-0 transition-opacity duration-300 hover:opacity-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="size-12">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z" />
                </svg>

                <!-- Edit button desktop -->
                <button @click.stop="$inertia.get(route('tutorials.edit', this.video.id))" class="hidden lg:flex size-7 rounded-full items-center justify-center hover:bg-gray-700 absolute top-3 right-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white font-bold">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </button>

                <!-- Delete button desktop -->
                <button @click.stop="showDeleteConfirm = true" class="hidden lg:flex size-7 rounded-full items-center justify-center hover:bg-gray-700 absolute top-3 left-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-white font-bold">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="py-3 px-4">
            <h2 class="text-lg font-semibold">{{ video.title }}</h2>
            <p class="flex items-center space-x-1 text-sm text-gray-500 mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span>{{ video.views.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </p>
            <p class="text-gray-700 mt-2 line-clamp-2 text-sm">{{ video.description }}</p>
        </div>
    </main>

    <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
        <template #title>
            <h1>Eliminar video tutorial</h1>
        </template>
        <template #content>
            <p>Se eliminará el video turtorial. Es un proceso irreversible. ¿Continuar de
                todas formas?</p>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1">
                <CancelButton @click="showDeleteConfirm = false">Cancelar</CancelButton>
                <DangerButton @click="deleteItem()">Eliminar</DangerButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from "@/Components/DangerButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
data() {
    return {
        showDeleteConfirm: false,
    }
},
components:{
    ConfirmationModal,
    DangerButton,
    CancelButton
},
props:{
    video: Object
},
emits:['play', 'videoDeleted'],
methods:{
    async openVideo(video) {
        this.$emit('play', video);
    },
    async deleteItem() {
        try {
            const response = await axios.delete(route('tutorials.destroy', this.video.id));
            if ( response.status === 200 ) {
                //emitir evento de eliminacion a componente padre para que remueva el video del index.
                this.$emit('videoDeleted', this.video.id);
                this.showDeleteConfirm = false;
            }
        } catch (error) {
            console.log(error);
        }
    }
}
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
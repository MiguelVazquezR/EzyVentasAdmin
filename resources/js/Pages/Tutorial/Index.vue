<template>
    <AppLayout title="Tutoriales">
        <main class="p-3 md:p-10">
            <div class="flex justify-between">
                <h1 class="text-2xl font-bold mb-6">Videos Tutoriales</h1>
                <PrimaryButton @click="$inertia.get(route('tutorials.create'))" class="self-start">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </PrimaryButton>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Tarjetas de videos -->
                <VideoTutorialCard @play="selectedVideo=$event" @videoDeleted="removeVideo" v-for="video in videos" :key="video.id" :video="video" />
            </div>

            <!-- Modal para reproducir el video -->
            <div v-if="selectedVideo" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 p-4">
                <div class="bg-[#232323] rounded-lg p-6 w-full max-w-3xl relative text-white z-50">
                    <button class="absolute top-3 right-4 text-gray-300 hover:text-red-500" @click="selectedVideo = null">✕</button>
                    <h2 class="text-xl font-bold mb-4">{{ selectedVideo.title }}</h2>
                    <iframe 
                        :src="`https://www.youtube.com/embed/${selectedVideo.youtube_id}?autoplay=1`" 
                        frameborder="0" 
                        allowfullscreen 
                        class="w-full h-[500px]"
                    ></iframe>
                    <p class="text-gray-300 mt-4">{{ selectedVideo.description }}</p>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import VideoTutorialCard from '@/Components/MyComponents/VideoTutorialCard.vue';

export default {
data() {
    return {
        selectedVideo: null
    }
},
components: {
    VideoTutorialCard,
    PrimaryButton,
    AppLayout
},
props: {
    videos: Array
},
methods:{
    removeVideo(videoId) {
        //buscar el index del video eliminado
        const videoIndex = this.videos.findIndex(item => item.id == videoId);
        if ( videoIndex !== -1 ) { //si se encontró el index se elimina del arreglo
            this.videos.splice(videoIndex, 1);
        }
    }
}
};
</script>
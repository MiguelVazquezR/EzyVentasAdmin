<template>
    <AppLayout title="Agregar video tutorial">
        <main class="p-3 md:p-10">
            <Back />
            
            <form @submit.prevent="store"
                class="rounded-lg border border-grayD9 lg:p-5 p-3 lg:w-1/2 mx-auto mt-7 lg:grid lg:grid-cols-2 gap-x-3">
                <h1 class="font-bold ml-2 col-span-full">Subir nuevo video tutorial</h1>
                
                <div class="mt-3 col-span-full">
                    <InputLabel value="Título del tutorial*" class="ml-3 mb-1" />
                    <el-input v-model="form.title" placeholder="Escribe el Título del tutorial" :maxlength="100" clearable />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="ID del vídeo de youtube*" class="ml-3 mb-1" />
                    <el-input v-model="form.youtube_id" placeholder="Escribe el ID del vídeo de youtube" :maxlength="120" clearable />
                    <InputError :message="form.errors.youtube_id" />
                </div>

                <div class="mt-3 col-span-full">
                    <el-checkbox v-model="form.status" label="Tutorial visible" border />
                </div>

                <div class="mt-3 col-span-full">
                    <InputLabel value="Descripción del video*" class="ml-3 mb-1" />
                    <el-input
                        v-model="form.description"
                        maxlength="255"
                        placeholder="Escribe una descipción"
                        show-word-limit
                        type="textarea"
                    />
                </div>

                <div class="col-span-full text-right mt-3">
                    <PrimaryButton class="!rounded-full" :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Subir tutorial
                    </PrimaryButton>
                </div>
            </form>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        title: null,
        youtube_id: null,
        description: null,
        status: true, 
    });

    return {
        form,
    }
},
components:{
    PrimaryButton,
    InputLabel,
    InputError,
    AppLayout,
    Back,
},
methods:{
    store() {
        this.form.post(route("tutorials.store"), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
                });
            },
        });
    },
}
}
</script>
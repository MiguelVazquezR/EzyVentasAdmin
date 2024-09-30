<template>
    <div @click="openFile"
        class="flex space-x-2 border border-grayD9 rounded-md p-2 cursor-pointer hover:border-primary">
        <figure class="h-8 w-1/5">
            <img class="object-contain" :src="getFileTypeIcon()">
        </figure>
        <div class="w-4/5">
            <p :title="file.file_name" class="font-bold text-xs lg:text-sm truncate">{{ file.file_name }}</p>
            <p class="text-[10px] lg:text-xs text-gray99">{{ (file.size / 1000).toFixed(2) }}KB</p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    props: {
        file: Object,
        url: {
            type: String,
            default: null
        }
    },
    methods: {
        getFileTypeIcon() {
            // Asocia extensiones de archivo a iconos
            const fileExtension = this.file.file_name?.split('.').pop().toLowerCase();

            switch (fileExtension) {
                case 'pdf':
                    return '/images/pdf.png';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return '/images/image.png';
                case 'mp4':
                case 'avi':
                case 'mkv':
                case 'mov':
                    return '/images/video.png';
                default:
                    return '/images/doc.png';
            }
        },
        openFile() {
            let fileUrl = this.file.original_url;
            
            // sobreescribir la url por defecto
            if (this.url) {
                fileUrl = this.url;
            }

            // Verificamos si la URL está presente antes de intentar abrir una nueva pestaña
            if (fileUrl) {
                // Abrir el archivo en una nueva pestaña
                window.open(fileUrl, '_blank');
            } else {
                console.error('La URL del archivo no está definida.');
                this.$notify({
                    title: "Error de servidor",
                    message: "No se pudo abrir el archivo. Probablemente no exista o está dañado",
                    type: "error",
                });
            }
        },
    }
}
</script>

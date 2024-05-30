<template>
  <div class="flex space-x-2">
    <figure class="w-10">
      <el-avatar :src="comment.admin.profile_photo_url" :size="36" />
    </figure>
    <div class="w-full flex flex-col">
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-gray37">{{ comment.admin.name }}</h2>
        <span class="text-gray77 text-[10px]">{{ formatDateTime(comment.created_at) }}</span>
      </div>
      <p v-html="comment.content" class="text-gray77 text-justify"></p>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
  name: 'CommentsCard',
  data() {
    return {

    };
  },
  components: {
  },
  props: {
    comment: Object
  },
  methods: {
    formatDateTime(dateTime) {
      let parsedDate = new Date(dateTime);

      return format(parsedDate, 'd MMM, y • hh:mm a', { locale: es }); // Formato personalizado
    },
  },
}
</script>

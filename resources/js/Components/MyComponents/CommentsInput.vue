<template>
  <div v-if="showUsersList" @click="showUsersList = false" class="inset-0 absolute top-0 left-0 z-10"></div>
  <div class="relative w-full">
    <div contenteditable="true" @input="onInput" ref="editor" id="editor" @keypress="checkForAtSign"
      class="bg-transparent border border-grayD9 placeholder:text-gray-400 text-gray-700 text-sm rounded-[5px] focus:border-primary block w-full p-2.5 min-h-[30px] focus:outline-none">
    </div>
    <footer class="bg-transparent rounded-br-[5px] rounded-bl-[5px] p-2 flex justify-between">
      <button @click="showUsersList = !showUsersList" type="button"
        class="text-primary text-xs cursor-pointer">@Mención</button>
      <PrimaryButton v-show="hasContent" type="button" @click="store" :disabled="loading" class="!py-1 !px-2 !text-[11px]">
        Agregar comentario
      </PrimaryButton>
      <transition name="fade">
        <ul v-if="showUsersList"
          class="z-20 border border-grayD9 bg-white absolute -top-32 left-0 rounded-[3px] w-60 h-32 overflow-y-auto">
          <template v-for="item in userList" :key="item.id">
            <li v-if="item.id !== $page.props.auth.user.id" type="button" @click="mentionUser(item)"
              class="flex items-center px-2 py-1 space-x-2 text-xs hover:bg-primarylight cursor-pointer">
              <img class="size-7 rounded-full object-cover" :src="item.profile_photo_url" :alt="item.name" />
              <p>{{ item.name }}</p>
            </li>
          </template>
          <p v-if="!userList.length" class="text-gray-500 text-xs text-center my-8">No hay usuarios para mencionar</p>
        </ul>
      </transition>
    </footer>
  </div>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import axios from "axios";

export default {
  data() {
    return {
      loading: false,
      showUsersList: false,
      mentions: [],
      content: "", // Inicializado como cadena vacía en lugar de null
    };
  },
  components: {
    PrimaryButton,
  },
  props: {
    // Propiedad para recibir y enviar el valor del componente padre
    userList: {
      type: Array,
      default: []
    },
    defaultValue: {
      type: String,
      default: ''
    },
    commentableType: String,
    commentableId: Number,
  },
  // emits: ['content', 'submitComment'], // Emite un evento personalizado para actualizar "value",
  computed: {
    hasContent() {
      return this.content.trim().length > 0;
    }
  },
  methods: {
    clearContent() {
      const editor = this.$refs.editor;
      editor.innerHTML = null;
      this.mentions = [];
      this.content = "";
    },
    mentionUser(user) {
      // Comprobar si el usuario ya ha sido mencionado
      const editor = this.$refs.editor;
      if (!this.mentions.some(mention => mention.id === user.id)) {
        const cursorPosition = editor.selectionEnd;
        const text = editor.innerHTML;
        const mention = `<span id="m-${user.id}" class="text-primary">@${user.name}</span> &nbsp;`;

        // Insertar la mención en el contenido existente
        const newText = text.slice(0, cursorPosition) + mention;

        // Actualizar el contenido del editor usando innerHTML
        editor.innerHTML = newText;

        // Registrar el usuario mencionado en el arreglo
        this.mentions.push({ id: user.id, tag: `@${user.name}` });

        // Enfocar el editor
        editor.focus();
        // Establecer el cursor al final del contenido
        this.setCaretPositionToEnd(editor);

      }
      // Enfocar el editor
      editor.focus();
      // Establecer el cursor al final del contenido
      this.setCaretPositionToEnd(editor);
      // Cerrar la lista de usuarios
      this.showUsersList = false;

      this.content = this.$refs.editor.innerHTML;
      // this.$emit('content', this.content);
    },
    onInput() {
      const editor = this.$refs.editor;
      const text = editor.innerHTML;
      const mentionElements = editor.querySelectorAll('span[id^="m-"]');

      if (!mentionElements.length && this.mentions.length) this.mentions = [];

      // Iterar sobre las menciones en orden inverso para evitar problemas con los índices al borrar
      for (let i = this.mentions.length - 1; i >= 0; i--) {
        const mention = this.mentions[i];
        const mentionId = `m-${mention.id}`;
        const mentionElement = Array.from(mentionElements).find((element) => element.id === mentionId);

        if (mentionElement && mentionElement.innerText !== mention.tag) {
          // Eliminar la mención tanto del editor como del arreglo mentions
          this.mentions.splice(i, 1);
          editor.removeChild(mentionElement);
        }
      }

      this.content = editor.innerHTML;
      // Actualiza el contenido del editor y emite el evento personalizado "content"
      // this.$emit('content', this.content);
    },
    checkForAtSign(event) {
      const editor = this.$refs.editor;
      if (event.key === '@') {
        // Mostrar la lista de usuarios cuando se escribe "@".
        this.showUsersList = true;
        // Enfocar el editor nuevamente para continuar escribiendo.
        editor.focus();
        // Evitar que el carácter "@" aparezca en el editor.
        event.preventDefault();
      }
    },
    setCaretPositionToEnd(elem) {
      const range = document.createRange();
      const sel = window.getSelection();
      range.selectNodeContents(elem);
      range.collapse(false);
      sel.removeAllRanges();
      sel.addRange(range);
    },
    async store() {
      try {
        this.loading = true;
        const response = await axios.post(route('comments.store'), {
          content: this.content,
          commentable_type: this.commentableType,
          commentable_id: this.commentableId,
          mentions: this.mentions,
        });

        if (response.status === 200) {
          this.$notify({
            title: "Comentario creado",
            message: "",
            type: "success",
          });

          this.clearContent();
        }
      } catch (error) {
        console.error(error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    // Establecer el contenido inicial del editor con el valor por defecto y aplicar estilos si es necesario
    if (this.defaultValue) {
      this.$refs.editor.innerHTML = this.defaultValue;
      this.content = this.defaultValue; // Asegurar que el contenido inicial también se actualice
    }
  },
}
</script>

<style scoped>
.slide-fade-enter-active,
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>

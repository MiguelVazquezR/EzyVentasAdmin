<template>
  <main class="p-6 bg-gray-100 rounded-lg shadow-md w-full">
    <h2 class="text-xl font-semibold mb-4">Módulos Activados</h2>
    <div class="grid grid-cols-2 gap-4">
      <div v-for="(module, index) in allModules" :key="module" class="flex justify-between items-center bg-white text-sm p-3 rounded-lg shadow">
        <span class="text-gray-700 font-medium">{{ module }}</span>
        <el-switch
          v-model="activeModules[module]"
          :loading="loadingModules[module]"
          :disabled="index < 6"
          @change="toggleModule(module)"
        />
      </div>
    </div>
  </main>
</template>

<script>
import { ref, onMounted } from "vue";
import { ElMessage } from "element-plus";
import axios from "axios";

export default {
  setup(props) {
  const allModules = [
    "Punto de venta",
    "Reportes",
    "Ventas registradas",
    "Caja",
    "Productos",
    "Configuraciones",
    "Clientes",
    "Gastos",
    "Cotizaciones",
    "Renta de productos",
    "Tienda en línea",
    "Servicios",
  ];

  const activeModules = ref({});
  const loadingModules = ref({});

  onMounted(() => {
    allModules.forEach((mod) => {
      activeModules.value[mod] = props.store.activated_modules.includes(mod);
      loadingModules.value[mod] = false;
    });
  });

  const toggleModule = async (module) => {
    loadingModules.value[module] = true;

    const updatedModules = activeModules.value[module]
      ? [...props.store.activated_modules, module]
      : props.store.activated_modules.filter((mod) => mod !== module);

    try {
      await axios.put(route('stores.update-modules', props.store.id), { modules: updatedModules });
      props.store.activated_modules = updatedModules;
      ElMessage.success("Módulos actualizados correctamente");
    } catch (error) {
      ElMessage.error("Error al actualizar módulos");
      activeModules.value[module] = !activeModules.value[module];
    } finally {
      loadingModules.value[module] = false;
    }
  };

  return { allModules, activeModules, loadingModules, toggleModule };
},
  props:{
    store: Object
  }
};
</script>

<style scoped>
main {
  max-width: 550px;
  margin: auto;
}
.grid {
  display: grid;
}
</style>

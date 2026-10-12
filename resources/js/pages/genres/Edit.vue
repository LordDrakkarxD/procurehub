<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Editar Gênero</h1>
      <p class="text-gray-500 text-sm mt-1">Atualize os dados do gênero</p>
    </div>

    <div v-if="loadingData" class="text-gray-500">Carregando...</div>

    <div v-else class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
          <input v-model="form.name" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div class="flex items-center gap-2">
          <input id="is_active" v-model="form.is_active" type="checkbox"
            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500" />
          <label for="is_active" class="text-sm text-gray-700">Gênero ativo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
          <router-link to="/genres" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">
            Cancelar
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const loadingData = ref(true);

const form = reactive({
  name: '',
  is_active: true,
});

async function fetchData() {
  try {
    const [genreRes] = await Promise.all([
      axios.get(`/api/genres/${route.params.id}`),
    ]);

    const genre = genreRes.data;
    form.name = genre.name;
    form.is_active = genre.is_active;
  } catch (error) {
    alert('Gênero não encontrado.');
    router.push('/genres');
  } finally {
    loadingData.value = false;
  }
}

async function handleSubmit() {
  loading.value = true;
  try {
    const payload: any = {
      name: form.name,
      is_active: form.is_active,
    };

    await axios.put(`/api/genres/${route.params.id}`, payload);
    router.push('/genres');
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar.');
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
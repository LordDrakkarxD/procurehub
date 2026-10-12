<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Gêneros</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie os gêneros do sistema</p>
      </div>
      <router-link
        to="/genres/create"
        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
      >
        + Novo Gênero
      </router-link>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="genres.length === 0" class="p-8 text-center text-gray-500">
        Nenhum gênero cadastrado.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
              <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="genre in genres" :key="genre.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ genre.name }}</td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="genre.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                >
                  {{ genre.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-3">
                <router-link
                  :to="`/genres/${genre.id}/edit`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Editar
                </router-link>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="confirmDelete(genre)"
                >
                  Excluir
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const genres = ref<any[]>([]);
const loading = ref(true);

async function fetchGenres() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/genres');
    genres.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(genre: any) {
  if (!confirm(`Tem certeza que deseja excluir o gênero "${genre.name}"?`)) return;

  try {
    await axios.delete(`/api/genres/${genre.id}`);
    genres.value = genres.value.filter(g => g.id !== genre.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir o gênero.');
  }
}

onMounted(fetchGenres);
</script>
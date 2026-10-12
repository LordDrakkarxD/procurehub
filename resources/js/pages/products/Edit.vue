<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Editar Produto</h1>
      <p class="text-gray-500 text-sm mt-1">Atualize os dados do produto</p>
    </div>

    <div v-if="loadingData" class="text-gray-500">Carregando...</div>

    <div v-else class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
          <input v-model="form.name" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Gênero *</label>
          <select
            v-model="form.genre_id"
            required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          >
            <option :value="null">Sem gênero</option>
            <option v-for="g in genres" :key="g.id" :value="g.id">
              {{ g.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Unidade *</label>
          <input v-model="form.unit" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição *</label>
          <input v-model="form.description" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div class="flex items-center gap-2">
          <input id="is_active" v-model="form.is_active" type="checkbox"
            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500" />
          <label for="is_active" class="text-sm text-gray-700">Produto ativo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
          <router-link to="/products" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">
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
const genres = ref<any[]>([]);

const form = reactive({
  name: '',
  unit: '',
  genre_id: null,
  description: '',
  is_active: true,
});

async function fetchData() {
  try {
    const [productRes, genresRes] = await Promise.all([
      axios.get(`/api/products/${route.params.id}`),
      axios.get('/api/genres'),
    ]);

    const product = productRes.data;
    form.name = product.name;
    form.unit = product.unit;
    form.genre_id = product.genre_id;
    form.description = product.description;
    form.is_active = product.is_active;
    genres.value = genresRes.data;
  } catch (error) {
    alert('Produto não encontrado.');
    router.push('/products');
  } finally {
    loadingData.value = false;
  }
}

async function handleSubmit() {
  loading.value = true;
  try {
    const payload: any = {
      name: form.name,
      unit: form.unit,
      description: form.description,
      is_active: form.is_active,
    };

    await axios.put(`/api/products/${route.params.id}`, payload);
    router.push('/products');
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar.');
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
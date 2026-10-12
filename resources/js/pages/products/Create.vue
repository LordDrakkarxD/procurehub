<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Novo Produto</h1>
      <p class="text-gray-500 text-sm mt-1">Cadastre um novo produto</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
          <input v-model="form.name" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
          <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name[0] }}</p>
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
          <p v-if="errors.unit" class="text-sm text-red-600 mt-1">{{ errors.unit[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Preço</label>
          <input
            :value="priceDisplay"
            type="text"
            inputmode="numeric"
            placeholder="0,00"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            @input="onPriceInput"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição *</label>
          <input v-model="form.description" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
          <p v-if="errors.description" class="text-sm text-red-600 mt-1">{{ errors.description[0] }}</p>
        </div>

        <div class="flex items-center gap-2">
          <input id="is_active" v-model="form.is_active" type="checkbox"
            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500" />
          <label for="is_active" class="text-sm text-gray-700">Produto ativo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60">
            {{ loading ? 'Salvando...' : 'Salvar' }}
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
import { useRouter } from 'vue-router';
import axios from 'axios';
import { formatMoney, formatMoneyInput, parseMoneyInput } from '@/utils/format';

const router = useRouter();
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const genres = ref<any[]>([]);

const priceDisplay = ref('0,00');

const form = reactive({
  name: '',
  unit: '',
  genre_id: null,
  price: 0,
  description: '',
  is_active: true,
});

async function fetchGenres() {
  const { data } = await axios.get('/api/genres');
  genres.value = data;
}

function onPriceInput(event: Event) {
  const raw = (event.target as HTMLInputElement).value;
  priceDisplay.value = formatMoneyInput(raw);
  form.price = parseMoneyInput(raw);
}

async function handleSubmit() {
  loading.value = true;
  errors.value = {};

  try {
    await axios.post('/api/products', form);
    router.push('/products');
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    } else {
      alert('Erro ao salvar o produto.');
    }
  } finally {
    loading.value = false;
  }
}

onMounted(fetchGenres);
</script>
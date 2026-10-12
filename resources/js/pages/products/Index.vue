<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Produtos</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie os produtos do sistema</p>
      </div>
      <router-link
        to="/products/create"
        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
      >
        + Novo Produto
      </router-link>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="products.length === 0" class="p-8 text-center text-gray-500">
        Nenhum produto cadastrado.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Unidade</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Preço</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Gênero</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Descrição</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
              <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ product.name }}</td>
              <td class="px-4 py-3 text-gray-600">{{ product.unit}}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatMoney(product.price) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ product.genre?.name || '—' }}</td>
              <td class="px-4 py-3 text-gray-600">{{ product.description }}</td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="product.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                >
                  {{ product.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-3">
                <router-link
                  :to="`/products/${product.id}/edit`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Editar
                </router-link>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="confirmDelete(product)"
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
import { formatMoney } from '@/utils/format';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref<any[]>([]);
const loading = ref(true);

async function fetchProducts() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/products');
    products.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(product: any) {
  if (!confirm(`Tem certeza que deseja excluir o produto "${product.name}"?`)) return;

  try {
    await axios.delete(`/api/products/${product.id}`);
    products.value = products.value.filter(p => p.id !== product.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir o produto.');
  }
}

onMounted(fetchProducts);
</script>
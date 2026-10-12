<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Pregões</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie os pregões do sistema</p>
      </div>
      <router-link
        to="/biddings/create"
        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
      >
        + Novo Pregão
      </router-link>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="biddings.length === 0" class="p-8 text-center text-gray-500">
        Nenhum pregão cadastrado.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Número</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Título</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Empresa</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Valor</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
              <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="bidding in biddings" :key="bidding.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ bidding.number }}</td>
              <td class="px-4 py-3 text-gray-600">{{ bidding.title }}</td>
              <td class="px-4 py-3 text-gray-600">{{ bidding.company.name }}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatMoney(bidding.total_amount) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ biddingStatuses[bidding.status] || bidding.status }}</td>
              <td class="px-4 py-3 text-right space-x-3">
                <router-link
                  :to="`/biddings/${bidding.id}`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Abrir
                </router-link>
                <router-link
                  :to="`/biddings/${bidding.id}/edit`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Editar
                </router-link>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="confirmDelete(bidding)"
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
import { formatMoney } from '@/utils/format';
import { biddingStatuses } from '@/utils/format';

const biddings = ref<any[]>([]);
const loading = ref(true);

async function fetchBiddings() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/biddings');
    biddings.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(bidding: any) {
  if (!confirm(`Tem certeza que deseja excluir o pregão "${bidding.title}"?`)) return;

  try {
    await axios.delete(`/api/biddings/${bidding.id}`);
    biddings.value = biddings.value.filter(b => b.id !== bidding.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir o pregão.');
  }
}

onMounted(fetchBiddings);
</script>
<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Dashboard</h1>
    <p class="text-gray-500 mb-6">Visão geral do sistema</p>

    <div v-if="loading" class="text-gray-500">Carregando estatísticas...</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Empresas ativas</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.companies }}</p>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Produtos ativos</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.products }}</p>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Pregões</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.biddings }}</p>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Contratos ativos</p>
        <p class="text-3xl font-bold text-sky-600 mt-1">{{ stats.contracts }}</p>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Saldo em contratos</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ formatMoney(stats.balance) }}</p>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Ordens no mês</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.orders_month }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { formatMoney } from '@/utils/format';

const loading = ref(true);
const stats = ref({
  companies: 0,
  products: 0,
  biddings: 0,
  contracts: 0,
  balance: 0,
  orders_month: 0,
});

async function fetchStats() {
  try {
    const { data } = await axios.get('/api/dashboard/stats');
    stats.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

onMounted(fetchStats);
</script>

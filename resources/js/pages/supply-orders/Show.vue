<template>
  <div v-if="loading" class="text-gray-500">Carregando...</div>

  <div v-else-if="order">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
      <div>
        <p class="text-sm text-gray-500">Ordem {{ order.number }}</p>
        <h1 class="text-2xl font-bold text-gray-800">
          Contrato {{ order.contract?.number }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">{{ order.contract?.company?.name }}</p>
      </div>
      <router-link
        :to="`/contracts/${order.contract?.id}`"
        class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Ver contrato
      </router-link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Valor</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ formatMoney(order.amount) }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Data</p>
        <p class="text-lg font-semibold text-gray-800 mt-1">{{ formatDate(order.ordered_at) }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Status</p>
        <p class="text-lg font-semibold text-gray-800 mt-1">{{ order.status }}</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Itens</h2>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Produto</th>
            <th class="px-4 py-3 text-left">Qtd</th>
            <th class="px-4 py-3 text-left">Valor un.</th>
            <th class="px-4 py-3 text-left">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in order.items" :key="item.id" class="border-t">
            <td class="px-4 py-3">{{ item.contract_item?.product?.name }}</td>
            <td class="px-4 py-3">{{ item.quantity }}</td>
            <td class="px-4 py-3">{{ formatMoney(item.unit_price) }}</td>
            <td class="px-4 py-3">{{ formatMoney(item.total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { formatMoney } from '@/utils/format';

const route = useRoute();
const loading = ref(true);
const order = ref<any>(null);

function formatDate(value: string | null) {
  if (!value) return '—';
  return new Date(value).toLocaleDateString('pt-BR');
}

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/supply-orders/${route.params.id}`);
    order.value = data;
  } finally {
    loading.value = false;
  }
});
</script>
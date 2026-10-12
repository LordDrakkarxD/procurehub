<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Ordens de Fornecimento</h1>
        <p class="text-gray-500 text-sm mt-1">Pedidos que consomem saldo dos contratos</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="orders.length === 0" class="p-8 text-center text-gray-500">
        Nenhuma ordem cadastrada.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Número</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Contrato</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Valor</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Data</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <router-link
                  :to="`/supply-orders/${order.id}`"
                  class="font-medium text-sky-700 hover:underline"
                >
                  {{ order.number }}
                </router-link>
              </td>
              <td class="px-4 py-3 text-gray-700">
                {{ order.contract?.number || '—' }}
              </td>
              <td class="px-4 py-3">{{ formatMoney(order.amount) }}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatDate(order.ordered_at) }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">
                  {{ order.status }}
                </span>
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

const orders = ref<any[]>([]);
const loading = ref(true);

function formatDate(value: string | null) {
  if (!value) return '—';
  return new Date(value).toLocaleDateString('pt-BR');
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/supply-orders');
    orders.value = data;
  } finally {
    loading.value = false;
  }
});
</script>
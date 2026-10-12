<template>
  <div v-if="loading" class="text-gray-500">Carregando...</div>

  <div v-else-if="contract">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
      <div>
        <p class="text-sm text-gray-500">Contrato {{ contract.number }}</p>
        <h1 class="text-2xl font-bold text-gray-800">
          {{ contract.company?.name || 'Contrato' }}
        </h1>
        <span class="inline-flex mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">
          {{ contractStatuses[contract.status] || contract.status }}
        </span>
      </div>

      <div class="flex gap-2">
        <router-link
          :to="`/contracts/${contract.id}/edit`"
          class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Editar
        </router-link>
        <router-link
          :to="{ name: 'supply-orders.create', query: { contract_id: String(contract.id) } }"
          class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700"
        >
          Nova Ordem
        </router-link>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Valor total</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ formatMoney(contract.total_amount) }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Saldo</p>
        <p
          class="text-2xl font-bold mt-1"
          :class="Number(contract.balance) <= 0 ? 'text-red-600' : 'text-gray-800'"
        >
          {{ formatMoney(contract.balance) }}
        </p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Pregão</p>
        <router-link
          v-if="contract.bidding"
          :to="`/biddings/${contract.bidding.id}`"
          class="text-lg font-semibold text-sky-700 hover:underline mt-1 block"
        >
          {{ contract.bidding.number }}
        </router-link>
        <p v-else class="text-lg font-semibold text-gray-400 mt-1">—</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Vigência</p>
        <p class="text-lg font-semibold text-gray-800 mt-1">
          {{ formatDate(contract.starts_on) }} — {{ formatDate(contract.ends_on) }}
        </p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
      <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Itens</h2>
      </div>

      <p v-if="!contract.items?.length" class="p-5 text-sm text-gray-500">
        Nenhum item neste contrato.
      </p>

      <table v-else class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Produto</th>
            <th class="px-4 py-3 text-left">Und</th>
            <th class="px-4 py-3 text-left">Qtd</th>
            <th class="px-4 py-3 text-left">Valor un.</th>
            <th class="px-4 py-3 text-left">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in contract.items" :key="item.id" class="border-t">
            <td class="px-4 py-3">{{ item.item_number }}</td>
            <td class="px-4 py-3">{{ item.product?.name }}</td>
            <td class="px-4 py-3">{{ item.product?.unit }}</td>
            <td class="px-4 py-3">{{ item.quantity }}</td>
            <td class="px-4 py-3">{{ formatMoney(item.unit_price) }}</td>
            <td class="px-4 py-3">{{ formatMoney(item.total) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <AttachmentPanel :id="contract.id" type="contract" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import AttachmentPanel from '@/components/AttachmentPanel.vue';
import { formatMoney } from '@/utils/format';
import { contractStatuses } from '@/utils/format';

const route = useRoute();
const loading = ref(true);
const contract = ref<any>(null);

function formatDate(value: string | null) {
  if (!value) return '—';
  return new Date(value).toLocaleDateString('pt-BR');
}

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/contracts/${route.params.id}`);
    contract.value = data;
  } finally {
    loading.value = false;
  }
});
</script>
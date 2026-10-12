<template>
  <div v-if="loading" class="text-gray-500">Carregando...</div>

  <div v-else-if="bidding">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
      <div>
        <p class="text-sm text-gray-500">Pregão {{ bidding.number }}</p>
        <h1 class="text-2xl font-bold text-gray-800">{{ bidding.title }}</h1>
        <span
          class="inline-flex mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700"
        >
          {{ biddingStatuses[bidding.status] || bidding.status }}
        </span>
      </div>

      <div class="flex gap-2">
        <router-link
          :to="`/biddings/${bidding.id}/edit`"
          class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
          Editar
        </router-link>
        <router-link
          :to="{ name: 'contracts.create', query: { bidding_id: String(bidding.id) } }"
          class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700"
        >
          Gerar contrato
        </router-link>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Valor</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ formatMoney(bidding.total_amount) }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Empresa</p>
        <p class="text-lg font-semibold text-gray-800 mt-1">{{ bidding.company?.name || '—' }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Abertura</p>
        <p class="text-lg font-semibold text-gray-800 mt-1">{{ formatDate(bidding.opened_at) }}</p>
      </div>
    </div>

    <div v-if="bidding.description" class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
      <h2 class="font-semibold text-gray-800 mb-2">Descrição</h2>
      <p class="text-gray-600 whitespace-pre-line">{{ bidding.description }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-gray-800">Contratos vinculados</h2>
      </div>
      <p v-if="!bidding.contracts?.length" class="text-sm text-gray-500">Nenhum contrato ainda.</p>
      <ul v-else class="divide-y divide-gray-100">
        <li v-for="c in bidding.contracts" :key="c.id" class="py-3 flex justify-between">
          <router-link :to="`/contracts/${c.id}`" class="text-sky-700 font-medium hover:underline">
            {{ c.number }}
          </router-link>
          <span class="text-sm text-gray-500">
            {{ formatMoney(c.balance) }} / {{ formatMoney(c.total_amount) }}
          </span>
        </li>
      </ul>
    </div>

    <AttachmentPanel :id="bidding.id" type="bidding" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import AttachmentPanel from '@/components/AttachmentPanel.vue';
import { formatMoney } from '@/utils/format';
import { biddingStatuses } from '@/utils/format';

const route = useRoute();
const loading = ref(true);
const bidding = ref<any>(null);

function formatDate(value: string | null) {
  if (!value) return '—';
  return new Date(value).toLocaleDateString('pt-BR');
}

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/biddings/${route.params.id}`);
    bidding.value = data;
  } finally {
    loading.value = false;
  }
});
</script>
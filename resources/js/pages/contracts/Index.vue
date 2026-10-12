<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Contratos</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie os contratos do sistema</p>
      </div>
      <router-link
        to="/contracts/create"
        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
      >
        + Novo Contrato
      </router-link>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="contracts.length === 0" class="p-8 text-center text-gray-500">
        Nenhum contrato cadastrado.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Número</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Pregão</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Empresa</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Valor</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Saldo</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
              <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="contract in contracts" :key="contract.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ contract.number }}</td>
              <td class="px-4 py-3 text-gray-600">{{ contract.bidding?.title }}</td>
              <td class="px-4 py-3 text-gray-600">{{ contract.company?.name }}</td>
              <td class="px-4 py-3 text-gray-600">{{ formatMoney(contract.total_amount) }}</td>
              <td class="font-medium" :class="Number(contract.balance) <= 0 ? 'text-red-600' : 'text-gray-800'">
                {{ formatMoney(contract.balance) }}
              </td>
              <td class="px-4 py-3 text-gray-600">{{ contractStatuses[contract.status] || contract.status }}</td>
              <td class="px-4 py-3 text-right space-x-3">
                <router-link
                  :to="`/contracts/${contract.id}`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Abrir
                </router-link>
                <router-link
                  :to="`/contracts/${contract.id}/edit`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Editar
                </router-link>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="confirmDelete(contract)"
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
import { formatCnpj, formatMoney } from '@/utils/format';
import { contractStatuses } from '@/utils/format';

const contracts = ref<any[]>([]);
const loading = ref(true);

async function fetchContracts() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/contracts');
    contracts.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(contract: any) {
  if (!confirm(`Tem certeza que deseja excluir o contrato "${contract.number}"?`)) return;

  try {
    await axios.delete(`/api/contracts/${contract.id}`);
    contracts.value = contracts.value.filter(c => c.id !== contract.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir o contrato.');
  }
}

onMounted(fetchContracts);
</script>
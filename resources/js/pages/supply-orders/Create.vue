<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Nova Ordem de Fornecimento</h1>
      <p class="text-gray-500 text-sm mt-1">
        Contrato {{ contract?.number }} · Saldo {{ formatMoney(contract?.balance) }}
      </p>
    </div>

    <div v-if="loading" class="text-gray-500">Carregando...</div>

    <form v-else @submit.prevent="handleSubmit" class="space-y-6">
      <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Número *</label>
          <input
            v-model="form.number"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          />
          <p v-if="errors.number" class="text-sm text-red-600 mt-1">{{ errors.number[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Data</label>
          <input
            v-model="form.ordered_at"
            type="date"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Observações</label>
          <textarea
            v-model="form.notes"
            rows="3"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          />
        </div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
          <h2 class="font-semibold text-gray-800">Itens do contrato</h2>
          <p class="text-sm text-gray-500">Informe a quantidade a solicitar. Deixe 0 para não incluir.</p>
        </div>

        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left">Produto</th>
              <th class="px-4 py-3 text-left">Saldo qtd</th>
              <th class="px-4 py-3 text-left">Valor un.</th>
              <th class="px-4 py-3 text-left">Solicitar</th>
              <th class="px-4 py-3 text-left">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in contractItems" :key="item.id" class="border-t">
              <td class="px-4 py-3">{{ item.product?.name }}</td>
              <td class="px-4 py-3">{{ item.quantity_balance }}</td>
              <td class="px-4 py-3">{{ formatMoney(item.unit_price) }}</td>
              <td class="px-4 py-3">
                <input
                  v-model.number="quantities[item.id]"
                  type="number"
                  step="0.001"
                  min="0"
                  :max="item.quantity_balance"
                  class="w-28 px-3 py-2 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                />
              </td>
              <td class="px-4 py-3">
                {{ formatMoney((quantities[item.id] || 0) * Number(item.unit_price)) }}
              </td>
            </tr>
          </tbody>
        </table>

        <div class="px-5 py-4 border-t border-gray-100 text-right">
          <span class="text-sm text-gray-500 mr-2">Total da ordem</span>
          <span class="text-lg font-bold text-gray-800">{{ formatMoney(orderTotal) }}</span>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="submit"
          :disabled="saving"
          class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg disabled:opacity-60"
        >
          {{ saving ? 'Salvando...' : 'Salvar ordem' }}
        </button>
        <router-link
          :to="contract ? `/contracts/${contract.id}` : '/supply-orders'"
          class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800"
        >
          Cancelar
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { formatMoney } from '@/utils/format';

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const saving = ref(false);
const errors = ref<Record<string, string[]>>({});
const contract = ref<any>(null);
const contractItems = ref<any[]>([]);
const quantities = reactive<Record<number, number>>({});

const form = reactive({
  number: '',
  ordered_at: new Date().toISOString().slice(0, 10),
  notes: '',
});

const orderTotal = computed(() => {
  return contractItems.value.reduce((sum, item) => {
    const qty = Number(quantities[item.id] || 0);
    return sum + qty * Number(item.unit_price);
  }, 0);
});

onMounted(async () => {
  const contractId = route.query.contract_id;
  if (!contractId) {
    alert('Abra a ordem a partir de um contrato.');
    router.push('/contracts');
    return;
  }

  try {
    const { data } = await axios.get(`/api/contracts/${contractId}`);
    contract.value = data;
    contractItems.value = data.items || [];
    for (const item of contractItems.value) {
      quantities[item.id] = 0;
    }
  } catch {
    alert('Contrato não encontrado.');
    router.push('/contracts');
  } finally {
    loading.value = false;
  }
});

async function handleSubmit() {
  const items = contractItems.value
    .filter((item) => Number(quantities[item.id]) > 0)
    .map((item) => ({
      contract_item_id: item.id,
      quantity: Number(quantities[item.id]),
    }));

  if (items.length === 0) {
    alert('Informe a quantidade de pelo menos um item.');
    return;
  }

  saving.value = true;
  errors.value = {};

  try {
    const { data } = await axios.post('/api/supply-orders', {
      contract_id: contract.value.id,
      number: form.number,
      ordered_at: form.ordered_at,
      notes: form.notes,
      items,
    });
    router.push(`/supply-orders/${data.id}`);
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
      alert(err.response.data.message || 'Verifique os dados.');
    } else {
      alert('Erro ao salvar a ordem.');
    }
  } finally {
    saving.value = false;
  }
}
</script>
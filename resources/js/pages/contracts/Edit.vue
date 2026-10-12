<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Editar Contrato</h1>
      <p class="text-gray-500 text-sm mt-1">Atualize os dados do contrato</p>
    </div>

    <div v-if="loadingData" class="text-gray-500">Carregando...</div>

    <div v-else class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">

        <div>
          <label
              class="block text-sm font-medium text-gray-700 mb-1.5"
              >Número *</label
          >
          <input
              v-model="form.number"
              type="text"
              required
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          />
          <p v-if="errors.number" class="text-sm text-red-600 mt-1">
              {{ errors.number[0] }}
          </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pregão *</label>
            <select
                v-model="form.bidding_id"
                @change="onBiddingChange"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            >
                <option :value="null">Sem Pregão</option>
                <option v-for="c in biddings" :key="c.id" :value="c.id">
                {{ c.number }} — {{ c.title }}
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Empresa *</label>
            <select
                v-model="form.company_id"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            >
                <option :value="null">Sem Empresa</option>
                <option v-for="c in companies" :key="c.id" :value="c.id">
                {{ c.name }}
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Total</label>
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
          <label
              class="block text-sm font-medium text-gray-700 mb-1.5"
              >Saldo *</label
          >
          <input
              :value="formatMoney(form.balance)"
              disabled
              class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500"
          />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de Abertura *</label>
            <input
                v-model="form.starts_on"
                type="date"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de Termino *</label>
            <input
                v-model="form.ends_on"
                type="date"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status *</label>
            <select
                v-model="form.status"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            >
                <option v-for="(label, value) in contractStatuses" :key="value" :value="value">
                    {{ label }}
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Observações *</label>
            <textarea
                v-model="form.notes"
                rows="4"        
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            />
        </div>

        <AttachmentPanel v-if="form.id" type="contract" :id="form.id" />

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
          <router-link to="/companies" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">
            Cancelar
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AttachmentPanel from '@/components/AttachmentPanel.vue';
import { formatMoney, formatMoneyInput, parseMoneyInput } from '@/utils/format';
import { contractStatuses } from '@/utils/format';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const loadingData = ref(true);

const companies = ref<any[]>([]);
const biddings = ref<any[]>([]);
const errors = ref<Record<string, string[]>>({});

const form = reactive({
  id: 0,
  number: "",
  bidding_id: null,
  company_id: null,
  total_amount: 0,
  starts_on: "",
  ends_on: "",
  status: "",
  notes: "",
  balance: 0,
});

const priceDisplay = ref('0,00');

function onPriceInput(event: Event) {
  const raw = (event.target as HTMLInputElement).value;
  priceDisplay.value = formatMoneyInput(raw);
  form.total_amount = parseMoneyInput(raw);
}

function toDateInput(value: string | null): string {
  if (!value) return '';
  return value.slice(0, 10);
}

function onBiddingChange() {
  const bidding = biddings.value.find((b: any) => b.id === Number(form.bidding_id));
  if (!bidding) return;

  if (bidding.company_id) {
    form.company_id = bidding.company_id;
  }

  if (bidding.total_amount) {
    form.total_amount = Number(bidding.total_amount);
    priceDisplay.value = Number(bidding.total_amount).toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }
}

async function fetchData() {
  try {
    const [contractRes, biddingRes, companyRes] = await Promise.all([
      axios.get(`/api/contracts/${route.params.id}`),
      axios.get('/api/biddings'),
      axios.get('/api/companies'),
    ]);

    const contract = contractRes.data;
    form.id = contract.id;
    form.number = contract.number;
    form.bidding_id = contract.bidding_id;
    form.company_id = contract.company_id;
    form.total_amount = Number(contract.total_amount);
    form.starts_on = toDateInput(contract.starts_on);
    form.ends_on = toDateInput(contract.ends_on);
    form.status = contract.status;
    form.notes = contract.notes;
    companies.value = companyRes.data;
    biddings.value = biddingRes.data;
    priceDisplay.value = Number(contract.total_amount).toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  } catch (error) {
    alert('Contrato não encontrado.');
    router.push('/contracts');
  } finally {
    loadingData.value = false;
  }
}

async function handleSubmit() {
  loading.value = true;
  try {
    const payload: any = {
      number: form.number,
      bidding_id: form.bidding_id,
      company_id: form.company_id,
      total_amount: form.total_amount,
      starts_on: form.starts_on,
      ends_on: form.ends_on,
      status: form.status,
      notes: form.notes,
      balance: form.balance,
    };

    await axios.put(`/api/contracts/${route.params.id}`, payload);
    router.push('/contracts');
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar.');
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
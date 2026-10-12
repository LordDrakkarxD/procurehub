<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Editar Pregão</h1>
      <p class="text-gray-500 text-sm mt-1">Atualize os dados do pregão</p>
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
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Título *</label>
            <input
                v-model="form.title"
                type="text"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição *</label>
            <textarea
                v-model="form.description"
                rows="4"
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Empresa *</label>
            <select
                v-model="form.company_id"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            >
                <option :value="null">Sem empresa</option>
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
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de Abertura *</label>
          <input
              :value="form.opened_at"
              type="date"
              required
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de Vencimento *</label>
          <input
              :value="form.awarded_at"
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
              <option v-for="(label, value) in biddingStatuses" :key="value" :value="value">
                  {{ label }}
              </option>
          </select>
        </div>

        <AttachmentPanel v-if="form.id" type="bidding" :id="form.id" />

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
          <router-link to="/biddings" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">
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
import { formatCnpj } from '@/utils/format';
import { biddingStatuses } from '@/utils/format';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const loadingData = ref(true);
const companies = ref<any[]>([]);

const form = reactive({
  id: 0,
  number: '',
  title: '',
  description: '',
  company_id: null,
  total_amount: 0,
  opened_at: '',
  awarded_at: '',
  status: 'draft',
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

async function fetchData() {
  try {
    const [biddingRes, companyRes] = await Promise.all([
      axios.get(`/api/biddings/${route.params.id}`),
      axios.get('/api/companies'),
    ]);
    
    const bidding = biddingRes.data;  
    form.id = bidding.id;
    form.number = bidding.number;
    form.title = bidding.title;
    form.description = bidding.description;
    form.company_id = bidding.company_id;
    form.total_amount = Number(bidding.total_amount);
    form.opened_at = toDateInput(bidding.opened_at);
    form.awarded_at = toDateInput(bidding.awarded_at);
    form.status = bidding.status;
    companies.value = companyRes.data;
    priceDisplay.value = Number(bidding.total_amount).toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  } catch (error) {
    alert('Pregão não encontrado.');
    router.push('/biddings');
  } finally {
    loadingData.value = false;
  }
}

async function handleSubmit() {
  loading.value = true;
  try {
    const payload: any = {
      number: form.number,
      title: form.title,
      description: form.description,
      company_id: form.company_id,
      total_amount: form.total_amount,
      opened_at: form.opened_at,
      awarded_at: form.awarded_at,
      status: form.status,
    };

    await axios.put(`/api/biddings/${route.params.id}`, payload);
    router.push('/biddings');
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar.');
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
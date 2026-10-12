<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Itens do pregão</h1>
        <p class="text-sm text-gray-500">{{ bidding?.number }} — {{ bidding?.title }}</p>
      </div>
      <router-link :to="`/biddings/${route.params.id}`" class="text-sm text-sky-700 hover:underline">
        Ir para detalhes
      </router-link>
    </div>

    <!-- Adicionar -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
      <h2 class="font-semibold text-gray-800 mb-4">Adicionar produto</h2>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <div class="md:col-span-6">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Produto</label>
          <select
            v-model="form.product_id"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          >
            <option :value="null">Selecione o produto</option>
            <option v-for="p in products" :key="p.id" :value="p.id">
              {{ p.name }} ({{ p.unit }})
            </option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Quantidade</label>
          <input
            v-model="form.quantity"
            type="number"
            step="0.001"
            min="0"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Valor un.</label>
          <input
            :value="priceDisplay"
            inputmode="numeric"
            placeholder="0,00"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            @input="onPriceInput"
          />
        </div>

        <div class="md:col-span-2">
          <button
            type="button"
            class="w-full px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg disabled:opacity-60"
            :disabled="saving"
            @click="addItem"
          >
            Adicionar
          </button>
        </div>
      </div>
    </div>

    <!-- Lista -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">#</th>
            <th class="px-4 py-3 text-left">Produto</th>
            <th class="px-4 py-3 text-left">Und</th>
            <th class="px-4 py-3 text-left">Qtd</th>
            <th class="px-4 py-3 text-left">Valor un.</th>
            <th class="px-4 py-3 text-left">Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-t">
            <td class="px-4 py-3">{{ item.item_number }}</td>
            <td class="px-4 py-3">{{ item.product?.name }}</td>
            <td class="px-4 py-3">{{ item.product?.unit }}</td>
            <td class="px-4 py-3">{{ item.quantity }}</td>
            <td class="px-4 py-3">{{ formatMoney(item.unit_price) }}</td>
            <td class="px-4 py-3">{{ formatMoney(item.total) }}</td>
            <td class="px-4 py-3 text-right">
              <button class="text-red-600 text-sm" @click="removeItem(item)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { formatMoney, formatMoneyInput, parseMoneyInput } from '@/utils/format';
import axios from "axios";

const router = useRouter();
const route = useRoute();
const loadingData = ref(true);
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const products = ref<any[]>([]);
const items = ref<any[]>([]);
const bidding = ref<any>(null);
const saving = ref(false);

const priceDisplay = ref('0,00');

function onPriceInput(event: Event) {
  const raw = (event.target as HTMLInputElement).value;
  priceDisplay.value = formatMoneyInput(raw);
  form.unit_price = parseMoneyInput(raw);
}

const form = reactive({
    bidding_id: null,
    product_id: null,
    item_number: '',
    quantity: 0,
    unit_price: 0,
    total: 0,
});

async function fetchData() {
  try {
    const [biddingRes, productsRes, itemsRes] = await Promise.all([
      axios.get(`/api/biddings/${route.params.id}`),
      axios.get('/api/products'),
      axios.get(`/api/biddings/${route.params.id}/items`),
    ]);

    bidding.value = biddingRes.data;
    products.value = productsRes.data;
    items.value = itemsRes.data;
  } catch (error) {
    alert('Dados não encontrados.');
  } finally {
    loadingData.value = false;
  }
}

async function addItem() {
  if (!form.product_id || !form.quantity) {
    alert('Selecione o produto e a quantidade.');
    return;
  }

  saving.value = true;
  try {
    const { data } = await axios.post(`/api/biddings/${route.params.id}/items`, {
      product_id: form.product_id,
      quantity: form.quantity,
      unit_price: form.unit_price,
    });
    items.value.push(data);
    form.product_id = null;
    form.quantity = 0;
    form.unit_price = 0;
    priceDisplay.value = '0,00';
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar o item.');
  } finally {
    saving.value = false;
  }
}

async function removeItem(item: any) {
  if (!confirm('Tem certeza que deseja excluir este item?')) return;

  loading.value = true;
  try {
    await axios.delete(`/api/biddings/${route.params.id}/items/${item.id}`);
    items.value = items.value.filter(i => i.id !== item.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir.');
  } finally {
    loading.value = false;
  }
}



onMounted(fetchData);
</script>
<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Novo Pregão</h1>
            <p class="text-gray-500 text-sm mt-1">Cadastre um novo Pregão</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
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
                        v-model="form.opened_at"
                        type="date"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de Vencimento *</label>
                    <input
                        v-model="form.awarded_at"
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Anexos</label>

                    <label
                        class="inline-flex items-center px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer"
                    >
                        Selecionar arquivos
                        <input
                        type="file"
                        multiple
                        class="hidden"
                        @change="onFilesSelected"
                        />
                    </label>

                    <ul v-if="pendingFiles.length" class="mt-3 space-y-1">
                        <li
                        v-for="(file, i) in pendingFiles"
                        :key="i"
                        class="text-sm text-gray-600 flex items-center justify-between gap-3"
                        >
                        <span class="truncate">{{ file.name }}</span>
                        <button
                            type="button"
                            class="text-red-600 hover:text-red-800 text-sm"
                            @click="pendingFiles.splice(i, 1)"
                        >
                            Remover
                        </button>
                        </li>
                    </ul>

                    <p v-else class="mt-2 text-sm text-gray-400">
                        Nenhum arquivo selecionado
                    </p>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60"
                    >
                        {{ loading ? "Salvando..." : "Salvar" }}
                    </button>
                    <router-link
                        to="/biddings"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800"
                    >
                        Cancelar
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from "vue-router";
import { formatMoneyInput, parseMoneyInput } from '@/utils/format';
import { biddingStatuses } from '@/utils/format';
import axios from "axios";

const router = useRouter();
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const pendingFiles = ref<File[]>([]);
const companies = ref<any[]>([]);

const priceDisplay = ref('0,00');

function onPriceInput(event: Event) {
  const raw = (event.target as HTMLInputElement).value;
  priceDisplay.value = formatMoneyInput(raw);
  form.total_amount = parseMoneyInput(raw);
}

function onFilesSelected(event: Event) {
  const list = (event.target as HTMLInputElement).files;
  pendingFiles.value = list ? Array.from(list) : [];
}

const form = reactive({
    number: "",
    title: "",
    description: "",
    company_id: null,
    total_amount: 0,
    opened_at: "",
    awarded_at: "",
    status: 'draft',
});

async function fetchCompanies() {
  const { data } = await axios.get('/api/companies');
  companies.value = data;
}

async function handleSubmit() {
    loading.value = true;
    errors.value = {};

    try {
        const { data } = await axios.post('/api/biddings', form);

        for (const file of pendingFiles.value) {
            const fd = new FormData();
            fd.append('file', file);
            fd.append('attachable_type', 'bidding');
            fd.append('attachable_id', String(data.id));
            await axios.post('/api/attachments', fd);
        }

        router.push(`/biddings/${data.id}/items`);
    } catch (err: any) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        } else {
            alert("Erro ao salvar o pregão.");
        }
    } finally {
        loading.value = false;
    }
}

onMounted(fetchCompanies);
</script>

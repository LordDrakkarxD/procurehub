<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Editar Empresa</h1>
      <p class="text-gray-500 text-sm mt-1">Atualize os dados da empresa</p>
    </div>

    <div v-if="loadingData" class="text-gray-500">Carregando...</div>

    <div v-else class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
          <input v-model="form.name" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">CNPJ *</label>
          <input
            :value="form.document"
            type="text"
            maxlength="18"
            placeholder="00.000.000/0000-00"
            @input="form.document = formatCnpj(($event.target as HTMLInputElement).value)"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail *</label>
          <input v-model="form.email" type="email" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div class="flex items-center gap-2">
          <input id="is_active" v-model="form.is_active" type="checkbox"
            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500" />
          <label for="is_active" class="text-sm text-gray-700">Empresa ativa</label>
        </div>

        <AttachmentPanel v-if="form.id" type="company" :id="form.id" />

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
import { formatCnpj } from '@/utils/format';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const loadingData = ref(true);

const form = reactive({
  id: 0,
  name: '',
  document: '',
  email: '',
  is_active: true,
});

async function fetchData() {
  try {
    const [companyRes] = await Promise.all([
      axios.get(`/api/companies/${route.params.id}`),
    ]);

    const company = companyRes.data;
    form.id = company.id;
    form.name = company.name;
    form.document = company.document;
    form.email = company.email;
    form.is_active = company.is_active;
  } catch (error) {
    alert('Empresa não encontrada.');
    router.push('/companies');
  } finally {
    loadingData.value = false;
  }
}

async function handleSubmit() {
  loading.value = true;
  try {
    const payload: any = {
      name: form.name,
      document: form.document,
      email: form.email,
      is_active: form.is_active,
    };

    await axios.put(`/api/companies/${route.params.id}`, payload);
    router.push('/companies');
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar.');
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>
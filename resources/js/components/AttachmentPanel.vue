<template>
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-semibold text-gray-800">Anexos</h3>
        <p class="text-sm text-gray-500">Arquivos vinculados a este registro</p>
      </div>
      <label class="px-3 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg cursor-pointer">
        + Enviar
        <input type="file" class="hidden" :disabled="uploading" @change="onFileChange" />
      </label>
    </div>

    <p v-if="error" class="text-sm text-red-600 mb-3">{{ error }}</p>
    <p v-if="uploading" class="text-sm text-gray-500 mb-3">Enviando...</p>

    <div v-if="loading" class="text-sm text-gray-500">Carregando anexos...</div>

    <div v-else-if="items.length === 0" class="text-sm text-gray-500">
      Nenhum anexo ainda.
    </div>

    <ul v-else class="divide-y divide-gray-100">
      <li v-for="item in items" :key="item.id" class="py-3 flex items-center justify-between gap-3">
        <div class="min-w-0">
          <a :href="item.url" target="_blank" class="text-sm font-medium text-sky-700 hover:underline truncate block">
            {{ item.original_name }}
          </a>
          <p class="text-xs text-gray-400">
            {{ formatSize(item.size) }} · {{ item.uploader?.name || '—' }}
          </p>
        </div>
        <button class="text-sm text-red-600 hover:text-red-800" @click="remove(item)">
          Excluir
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps<{
  type: string; // bidding | contract | supply_order | company
  id?: number;
  token?: string | null;
}>();

const items = ref<any[]>([]);
const loading = ref(false);
const uploading = ref(false);
const error = ref('');

async function fetchItems() {
  if (!props.id && !props.token) return;
  loading.value = true;
  try {
    const { data } = await axios.get('/api/attachments', {
      params: 
      {
         attachable_type: props.type, 
         attachable_id: props.id || undefined,
         token: props.token || undefined,
      },
    });
    items.value = data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function onFileChange(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (!file) return;

  error.value = '';
  uploading.value = true;

  const form = new FormData();
  form.append('file', file);
  form.append('attachable_type', props.type);

  if (props.id) {
    form.append('attachable_id', String(props.id));
  }

  if (props.token) {
    form.append('token', props.token);
  }

  try {
    const { data } = await axios.post('/api/attachments', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    items.value.unshift(data);
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erro ao enviar o arquivo.';
  } finally {
    uploading.value = false;
    (event.target as HTMLInputElement).value = '';
  }
}

async function remove(item: any) {
  if (!confirm(`Excluir "${item.original_name}"?`)) return;
  await axios.delete(`/api/attachments/${item.id}`);
  items.value = items.value.filter(i => i.id !== item.id);
}

function formatSize(bytes: number) {
  if (!bytes) return '0 B';
  const units = ['B', 'KB', 'MB'];
  let i = 0;
  let size = bytes;
  while (size >= 1024 && i < units.length - 1) {
    size /= 1024;
    i++;
  }
  return `${size.toFixed(1)} ${units[i]}`;
}

watch(() => [props.id, props.token], fetchItems);
onMounted(fetchItems);
</script>
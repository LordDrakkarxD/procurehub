<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Nova Empresa</h1>
            <p class="text-gray-500 text-sm mt-1">Cadastre uma nova empresa</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
            <form @submit.prevent="handleSubmit" class="space-y-5">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Nome *</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                    />
                    <p v-if="errors.name" class="text-sm text-red-600 mt-1">
                        {{ errors.name[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CNPJ *</label>
                    <input
                        :value="form.document"
                        type="text"
                        required
                        maxlength="18"
                        placeholder="00.000.000/0000-00"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                        @input="form.document = formatCnpj(($event.target as HTMLInputElement).value)"
                    />
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >E-mail *</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                    />
                    <p v-if="errors.email" class="text-sm text-red-600 mt-1">
                        {{ errors.email[0] }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        id="is_active"
                        v-model="form.is_active"
                        type="checkbox"
                        class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                    />
                    <label for="is_active" class="text-sm text-gray-700"
                        >Empresa ativa</label
                    >
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
                        to="/companies"
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
import { reactive, ref} from "vue";
import { useRouter } from "vue-router";
import AttachmentPanel from "@/components/AttachmentPanel.vue";
import { formatCnpj } from '@/utils/format';
import axios from "axios";

const attachmentToken = crypto.randomUUID();
const router = useRouter();
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});
const pendingFiles = ref<File[]>([]);

function onFilesSelected(event: Event) {
  const list = (event.target as HTMLInputElement).files;
  pendingFiles.value = list ? Array.from(list) : [];
}

const form = reactive({
    name: "",
    document: "",
    email: "",
    is_active: true,
});

async function handleSubmit() {
    loading.value = true;
    errors.value = {};

    try {
        const { data } = await axios.post('/api/companies', form);

        for (const file of pendingFiles.value) {
            const fd = new FormData();
            fd.append('file', file);
            fd.append('attachable_type', 'company');
            fd.append('attachable_id', String(data.id));
            await axios.post('/api/attachments', fd);
        }

        router.push('/companies');
    } catch (err: any) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        } else {
            alert("Erro ao salvar a empresa.");
        }
    } finally {
        loading.value = false;
    }
}
</script>

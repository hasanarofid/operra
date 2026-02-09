<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';

const props = defineProps({
    accounts: Array,
});

const editingAccount = ref(null);
const showModal = ref(false);

const form = useForm({
    lm_username: '',
    lm_password: '',
    telegram_chat_id: '',
    target_butik: '',
    is_active: false,
});

const butiks = [
    'Surabaya 2 Pakuwon',
    'Surabaya 1 Darmo',
    'Gedung Antam (Jakarta)',
    'Balikpapan',
    'Bandung',
    'Bekasi',
    'Bintaro',
    'Bogor',
    'Denpasar',
    'Djuanda',
    'Graha Dipta',
    'Makassar',
    'Medan',
    'Palembang',
    'Pekanbaru',
    'Puri Indah',
    'Semarang',
    'Serpong',
    'Setiabudi One',
];

const openModal = (account) => {
    editingAccount.value = account;
    form.lm_username = account.lm_username;
    form.lm_password = ''; // Always blank for security (or placeholder if set)
    form.telegram_chat_id = account.telegram_chat_id;
    form.target_butik = account.target_butik;
    form.is_active = !!account.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingAccount.value = null;
    form.reset();
};

const updateAccount = () => {
    form.put(route('bot-antam-accounts.update', editingAccount.value.id), {
        onSuccess: () => closeModal(),
    });
};

// Logs Logic
const showLogsModal = ref(false);
const logs = ref([]);
const loadingLogs = ref(false);
const currentLogAccount = ref(null);

const openLogs = async (account) => {
    currentLogAccount.value = account;
    showLogsModal.value = true;
    logs.value = [];
    loadingLogs.value = true;
    try {
        const response = await axios.get(route('admin.bot-antam-accounts.logs', account.id));
        logs.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        loadingLogs.value = false;
    }
};

const closeLogsModal = () => {
    showLogsModal.value = false;
    currentLogAccount.value = null;
    logs.value = [];
};
</script>

<template>
    <Head title="Manage Bot Travelers" />

    <AuthenticatedLayout>
        <template #header>
            Manage Bot Travelers (War Antam)
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4">Active Bot Accounts</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company / User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">LM Username</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Telegram ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Target Butik</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="account in accounts" :key="account.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ account.company_name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ account.id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ account.lm_username || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ account.telegram_chat_id || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ account.target_butik || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="account.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            >
                                                {{ account.is_active ? 'Active' : 'Idle' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button @click="openModal(account)" class="text-operra-600 hover:text-operra-900 font-bold">
                                                Configure
                                            </button>
                                            <button @click="openLogs(account)" class="ml-3 text-blue-600 hover:text-blue-900 font-bold">
                                                Logs
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="accounts.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No accounts found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Configure Bot for {{ editingAccount?.company_name }}
                </h2>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="lm_username" value="Logam Mulia Username" />
                        <TextInput
                            id="lm_username"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.lm_username"
                            placeholder="Email or Username"
                        />
                    </div>

                    <div>
                        <InputLabel for="lm_password" value="Logam Mulia Password" />
                        <TextInput
                            id="lm_password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.lm_password"
                            :placeholder="editingAccount?.has_password ? 'Stored (Leave empty to keep)' : 'Enter Password'"
                        />
                        <p class="text-xs text-gray-500 mt-1">
                            Current Status: {{ editingAccount?.has_password ? '✅ Encrypted Password Saved' : '❌ Not Set' }}
                        </p>
                    </div>

                    <div>
                        <InputLabel for="telegram_chat_id" value="Telegram Chat ID" />
                        <TextInput
                            id="telegram_chat_id"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.telegram_chat_id"
                            placeholder="123456789"
                        />
                    </div>

                    <div>
                        <InputLabel for="target_butik" value="Target Lokasi (Butik)" />
                        <select
                            id="target_butik"
                            v-model="form.target_butik"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-operra-500 dark:focus:border-operra-600 focus:ring-operra-500 dark:focus:ring-operra-600 rounded-md shadow-sm"
                        >
                            <option value="">Pilih Lokasi...</option>
                            <option v-for="butik in butiks" :key="butik" :value="butik">{{ butik }}</option>
                        </select>
                    </div>


                    
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox" 
                            id="is_active" 
                            v-model="form.is_active"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-operra-600 shadow-sm focus:ring-operra-500 dark:focus:ring-operra-600 dark:focus:ring-offset-gray-800"
                        >
                        <InputLabel for="is_active" value="Activate Bot" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="updateAccount">
                        Save Configuration
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Logs Modal -->
        <Modal :show="showLogsModal" @close="closeLogsModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex justify-between items-center">
                    <span>Logs: {{ currentLogAccount?.company_name }}</span>
                    <button @click="openLogs(currentLogAccount)" class="text-sm text-blue-500 hover:underline" :disabled="loadingLogs">
                        Refresh
                    </button>
                </h2>
                
                <div class="mt-4 bg-black rounded-lg p-4 h-96 overflow-y-auto font-mono text-xs">
                    <div v-if="loadingLogs" class="text-green-500 animate-pulse">Loading logs...</div>
                    <template v-else>
                        <div v-for="log in logs" :key="log.id" class="mb-1 border-l-2 pl-2 border-transparent hover:border-gray-700 transition-colors">
                            <span class="text-gray-500">[{{ new Date(log.created_at).toLocaleTimeString() }}]</span>
                            <span :class="{
                                'text-blue-400': log.event_type === 'CHECK',
                                'text-yellow-400': log.event_type === 'FOUND',
                                'text-red-500': log.event_type === 'ERROR',
                                'text-green-500': log.event_type === 'SUCCESS'
                            }" class="mx-2 font-bold">{{ log.event_type }}</span>
                            <span class="text-gray-300">{{ log.message }}</span>
                        </div>
                        <div v-if="logs.length === 0" class="text-gray-500 italic text-center mt-10">
                            No logs found for this account.
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeLogsModal">Close</SecondaryButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

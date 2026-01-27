<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
    waStatus: Object,
    accounts: Array
});

const form = useForm({
    meta_access_token: props.settings.meta_access_token || '',
    meta_webhook_verify_token: props.settings.meta_webhook_verify_token || '',
    meta_app_id: props.settings.meta_app_id || '',
    meta_waba_id: props.settings.meta_waba_id || '',
});

const accountForm = useForm({
    id: null,
    name: '',
    phone_number: '', 
    provider: 'official',
    token: '',
    key: '',
    endpoint: '',
});

const isEditing = ref(false);
const showToken = ref(false);

const submit = () => {
    form.post(route('crm.wa.settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('WhatsApp configuration updated!'),
    });
};

const saveAccount = () => {
    if (isEditing.value) {
        accountForm.put(route('crm.wa.accounts.update', accountForm.id), {
            onSuccess: () => {
                resetAccountForm();
                alert('Account updated successfully!');
            }
        });
    } else {
        accountForm.post(route('crm.wa.accounts.store'), {
            onSuccess: () => {
                resetAccountForm();
                alert('Account added successfully!');
            }
        });
    }
};

const editAccount = (account) => {
    isEditing.value = true;
    accountForm.id = account.id;
    accountForm.name = account.name;
    accountForm.phone_number = account.phone_number;
    accountForm.provider = account.provider;
    accountForm.token = account.api_credentials?.token || '';
    accountForm.key = account.api_credentials?.key || '';
    accountForm.endpoint = account.api_credentials?.endpoint || '';
};

const deleteAccount = (id) => {
    if (confirm('Are you sure you want to delete this account?')) {
        accountForm.delete(route('crm.wa.accounts.destroy', id), {
            onSuccess: () => alert('Account deleted!')
        });
    }
};

const syncAccount = (id) => {
    accountForm.post(route('crm.wa.accounts.sync', id), {
        onSuccess: () => alert('Sync initiated!')
    });
};

const syncTemplates = (id) => {
    accountForm.post(route('crm.wa.accounts.sync-templates', id), {
        onSuccess: () => alert('Templates synced successfully!')
    });
};

const syncFromMeta = () => {
    if (confirm('Import all registered phone numbers from Meta WABA?')) {
        accountForm.post(route('crm.wa.accounts.sync-meta'), {
            onSuccess: () => alert('Accounts imported successfully!'),
            onError: (err) => alert('Sync failed: ' + (err.message || 'Check your WABA ID and Token'))
        });
    }
};

const resetAccountForm = () => {
    isEditing.value = false;
    accountForm.reset();
};
</script>

<template>
    <Head title="WhatsApp Settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                WhatsApp API Configuration
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Global Configuration -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-2xl border-0 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        <h3 class="font-black text-lg mb-6 text-gray-800 dark:text-white uppercase tracking-tighter">Meta WhatsApp Global Configuration</h3>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Permanent Access Token</label>
                                <div class="relative">
                                    <input v-model="form.meta_access_token" :type="showToken ? 'text' : 'password'" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm pr-20">
                                    <button type="button" @click="showToken = !showToken" class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 hover:text-blue-800 transition-colors">
                                        {{ showToken ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Webhook Verify Token</label>
                                    <input v-model="form.meta_webhook_verify_token" type="text" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Meta App ID</label>
                                    <input v-model="form.meta_app_id" type="text" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">WABA ID</label>
                                <input v-model="form.meta_waba_id" type="text" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                            </div>
                            <div class="pt-4">
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 active:scale-95 transition-all w-full md:w-auto">
                                    Update Global Config
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Multi-Account Management -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-2xl border-0 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                        <h3 class="font-black text-lg mb-6 text-gray-800 dark:text-white uppercase tracking-tighter">{{ isEditing ? 'Edit WhatsApp Account' : 'Add New WhatsApp Account' }}</h3>
                        <form @submit.prevent="saveAccount" class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Account Name</label>
                                    <input v-model="accountForm.name" type="text" placeholder="e.g. CS Jakarta" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Phone Number / ID</label>
                                    <input v-model="accountForm.phone_number" type="text" placeholder="628123xxx" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Provider</label>
                                <select v-model="accountForm.provider" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                    <option value="official">Official Meta API</option>
                                    <option value="fonnte">Fonnte</option>
                                    <option value="generic">Generic API</option>
                                </select>
                            </div>
                            <div class="flex flex-col md:flex-row gap-3 pt-4">
                                <button type="submit" :disabled="accountForm.processing" class="bg-green-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-green-700 active:scale-95 transition-all flex-1">
                                    {{ isEditing ? 'Update Account' : 'Add Account' }}
                                </button>
                                <button v-if="isEditing" @click="resetAccountForm" type="button" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List of Accounts -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border-0">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-wrap justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="font-black text-lg text-gray-800 dark:text-white uppercase tracking-tighter">WhatsApp Accounts List</h3>
                        <button @click="syncFromMeta" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 transition-all active:scale-95">
                            Sync From Meta (WABA)
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Name</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Phone</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Provider</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Verified</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="account in accounts" :key="account.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ account.name }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400 font-mono">{{ account.phone_number }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400 capitalize font-medium">{{ account.provider }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span :class="account.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'" class="px-2 py-0.5 rounded-md font-black uppercase text-[10px] tracking-tighter">
                                            {{ account.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div v-if="account.is_verified" class="flex items-center gap-1 text-blue-500 font-bold text-xs">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                            Verified
                                        </div>
                                        <span v-else class="text-gray-400 dark:text-gray-600 text-xs italic">Unverified</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <button @click="editAccount(account)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-[10px] font-black uppercase tracking-widest">Edit</button>
                                            <button @click="syncAccount(account.id)" class="text-green-600 dark:text-green-400 hover:text-green-800 text-[10px] font-black uppercase tracking-widest">Sync</button>
                                            <button v-if="account.provider === 'official'" @click="syncTemplates(account.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-[10px] font-black uppercase tracking-widest">Templates</button>
                                            <button @click="deleteAccount(account.id)" class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="accounts.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-xs italic font-medium">No accounts found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

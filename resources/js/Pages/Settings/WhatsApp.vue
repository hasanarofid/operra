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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Global Configuration -->
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="font-bold text-lg mb-4 text-gray-700">Meta WhatsApp Global Configuration</h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium">Permanent Access Token</label>
                                <input v-model="form.meta_access_token" :type="showToken ? 'text' : 'password'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <button type="button" @click="showToken = !showToken" class="text-xs text-blue-600 mt-1">{{ showToken ? 'Hide' : 'Show' }} Token</button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium">Webhook Verify Token</label>
                                    <input v-model="form.meta_webhook_verify_token" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Meta App ID</label>
                                    <input v-model="form.meta_app_id" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">WABA ID</label>
                                <input v-model="form.meta_waba_id" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="pt-4">
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded text-xs font-bold uppercase shadow hover:bg-blue-700 transition">
                                    Update Global Config
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Multi-Account Management -->
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="font-bold text-lg mb-4 text-gray-700">{{ isEditing ? 'Edit WhatsApp Account' : 'Add New WhatsApp Account' }}</h3>
                        <form @submit.prevent="saveAccount" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium">Account Name</label>
                                    <input v-model="accountForm.name" type="text" placeholder="e.g. CS Jakarta" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">Phone Number / ID</label>
                                    <input v-model="accountForm.phone_number" type="text" placeholder="628123xxx" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Provider</label>
                                <select v-model="accountForm.provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="official">Official Meta API</option>
                                    <option value="fonnte">Fonnte</option>
                                    <option value="generic">Generic API</option>
                                </select>
                            </div>
                            <div class="flex gap-2 pt-4">
                                <button type="submit" :disabled="accountForm.processing" class="bg-green-600 text-white px-6 py-2 rounded text-xs font-bold uppercase shadow hover:bg-green-700 transition">
                                    {{ isEditing ? 'Update Account' : 'Add Account' }}
                                </button>
                                <button v-if="isEditing" @click="resetAccountForm" type="button" class="bg-gray-500 text-white px-4 py-2 rounded text-xs font-bold uppercase shadow hover:bg-gray-600 transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List of Accounts -->
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-lg text-gray-700">WhatsApp Accounts List</h3>
                        <button @click="syncFromMeta" class="bg-blue-600 text-white px-4 py-2 rounded text-xs font-bold uppercase shadow hover:bg-blue-700 transition">
                            Sync From Meta (WABA)
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="account in accounts" :key="account.id">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ account.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ account.phone_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ account.provider }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span :class="account.status === 'active' ? 'text-green-600' : 'text-red-600'" class="font-bold uppercase text-xs">
                                            {{ account.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span v-if="account.is_verified" class="text-blue-500">✅ Yes</span>
                                        <span v-else class="text-gray-400">❌ No</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium flex gap-3">
                                        <button @click="editAccount(account)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                        <button @click="syncAccount(account.id)" class="text-green-600 hover:text-green-900">Sync</button>
                                        <button v-if="account.provider === 'official'" @click="syncTemplates(account.id)" class="text-indigo-600 hover:text-indigo-900">Templates</button>
                                        <button @click="deleteAccount(account.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="accounts.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No accounts found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

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
const showQrModal = ref(false);
const currentQr = ref(null);
const qrLoading = ref(false);
const selectedAccountForQr = ref(null);
const connectionStatus = ref('waiting_scan'); // waiting_scan, connected, expired
const connectedDevice = ref({ phone: '', name: '' });
const activeAppKey = ref('app_live_5f3e2d...');
let pollingInterval = null;

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

const openQrModal = async (account) => {
    selectedAccountForQr.value = account;
    showQrModal.value = true;
    currentQr.value = null;
    qrLoading.value = true;
    connectionStatus.value = 'waiting_scan';
    
    try {
        // Use the new connect endpoint which triggers QR generation
        const response = await axios.post(route('crm.wa.accounts.connect', account.id));
        if (response.data.status) {
            currentQr.value = response.data.qr;
            startPollingStatus(account.id);
        } else {
            alert(response.data.message || 'Gagal mengambil QR Code');
            showQrModal.value = false;
        }
    } catch (error) {
        console.error('QR Error:', error);
        alert('Terjadi kesalahan saat mengambil QR Code');
        showQrModal.value = false;
    } finally {
        qrLoading.value = false;
    }
};

const startPollingStatus = (accountId) => {
    if (pollingInterval) clearInterval(pollingInterval);
    
    pollingInterval = setInterval(async () => {
        try {
            const response = await axios.get(route('crm.wa.accounts.status', accountId));
            const status = response.data.status;
            
            if (status === 'connected') {
                connectionStatus.value = 'connected';
                connectedDevice.value = {
                    phone: response.data.phone,
                    name: response.data.name
                };
                clearInterval(pollingInterval);
                // Refresh data to show active status in list
                window.location.reload(); 
            } else if (status === 'expired') {
                connectionStatus.value = 'expired';
                clearInterval(pollingInterval);
            }
        } catch (error) {
            console.error('Polling Error:', error);
        }
    }, 3000);
};

const disconnectDevice = async (account) => {
    if (!confirm('Apakah Anda yakin ingin memutuskan koneksi perangkat ini?')) return;
    
    try {
        const response = await axios.post(route('crm.wa.accounts.disconnect', account.id));
        if (response.data.status) {
            alert('Koneksi perangkat berhasil diputuskan.');
            window.location.reload();
        } else {
            alert(response.data.message || 'Gagal memutuskan koneksi.');
        }
    } catch (error) {
        alert('Terjadi kesalahan sistem.');
    }
};

const closeQrModal = () => {
    showQrModal.value = false;
    if (pollingInterval) clearInterval(pollingInterval);
};

const generateToken = async () => {
    if (!confirm('Apakah Anda yakin ingin membuat token baru? Token lama akan segera kedaluwarsa.')) return;
    
    try {
        const response = await axios.post(route('crm.wa.generate-token'));
        if (response.data.status) {
            activeAppKey.value = response.data.token;
            alert('Token baru berhasil digenerate.');
        }
    } catch (error) {
        alert('Gagal generate token.');
    }
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
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-black text-lg text-gray-800 dark:text-white uppercase tracking-tighter">
                                {{ isEditing ? 'Edit Akun WhatsApp' : 'Tambah Akun WhatsApp' }}
                            </h3>
                            <div v-if="!isEditing" class="flex items-center gap-2 px-3 py-1 bg-green-50 dark:bg-green-900/20 rounded-full border border-green-100 dark:border-green-800">
                                <span class="h-1.5 w-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-[9px] font-black uppercase text-green-600 dark:text-green-400 tracking-widest">Device Connect Ready</span>
                            </div>
                        </div>
                        
                        <form @submit.prevent="saveAccount" class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nama Akun (Label)</label>
                                    <input v-model="accountForm.name" type="text" placeholder="e.g. CS Jakarta" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Nomor WhatsApp / ID</label>
                                    <input v-model="accountForm.phone_number" type="text" placeholder="628123xxx" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Provider / Metode Koneksi</label>
                                    <select v-model="accountForm.provider" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                        <option value="official">Official Meta API (Cloud API)</option>
                                        <option value="fonnte">Fonnte (QR Scan & API)</option>
                                        <option value="generic">Generic QR Gateway / Webhook</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">API Token / Session Key</label>
                                    <div class="flex gap-2">
                                        <input v-model="accountForm.token" :type="showToken ? 'text' : 'password'" placeholder="Masukkan Token" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                        <button type="button" @click="showToken = !showToken" class="mt-1 px-3 bg-gray-100 dark:bg-gray-700 rounded-xl text-gray-400 hover:text-blue-500 transition-colors">
                                            <svg v-if="showToken" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="accountForm.provider === 'generic'" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                                <p class="text-[10px] text-blue-600 dark:text-blue-400 font-bold uppercase tracking-widest leading-relaxed">
                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    Metode QR Gateway memungkinkan Anda menghubungkan WhatsApp dengan cara scan QR seperti di WA Web. Pastikan server gateway Anda sudah dikonfigurasi.
                                </p>
                            </div>

                            <div class="flex flex-col md:flex-row gap-3 pt-4">
                                <button type="submit" :disabled="accountForm.processing" class="bg-green-600 text-white px-8 py-4 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-green-700 active:scale-95 transition-all flex-1">
                                    {{ isEditing ? 'Simpan Perubahan' : 'Hubungkan Perangkat Baru' }}
                                </button>
                                <button v-if="isEditing" @click="resetAccountForm" type="button" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-4 rounded-xl text-xs font-black uppercase tracking-widest shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                    Batal
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
                                        <div class="flex justify-end gap-3 items-center">
                                            <button v-if="(account.provider === 'fonnte' || account.provider === 'generic') && account.status !== 'active'" @click="openQrModal(account)" class="flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded-lg border border-orange-100 dark:border-orange-800 text-[10px] font-black uppercase tracking-widest hover:bg-orange-100 transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                                Scan QR
                                            </button>
                                            <button v-if="account.status === 'active' && account.provider !== 'official'" @click="disconnectDevice(account)" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg border border-red-100 dark:border-red-800 text-[10px] font-black uppercase tracking-widest hover:bg-red-100 transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                Putuskan
                                            </button>
                                            <button @click="editAccount(account)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-[10px] font-black uppercase tracking-widest">Edit</button>
                                            <button @click="syncAccount(account.id)" class="text-green-600 dark:text-green-400 hover:text-green-800 text-[10px] font-black uppercase tracking-widest">Sync</button>
                                            <button v-if="account.provider === 'official'" @click="syncTemplates(account.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 text-[10px] font-black uppercase tracking-widest">Templates</button>
                                            <button @click="deleteAccount(account.id)" class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest">Hapus</button>
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

                <!-- API Integration & Token Section -->
                <div class="bg-white dark:bg-gray-800 p-8 shadow-lg rounded-2xl border-0 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                        <div class="max-w-2xl">
                            <h3 class="font-black text-xl text-gray-800 dark:text-white uppercase tracking-tighter mb-2">Integrasi API & Token Akses</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                                Gunakan Token Akses untuk menghubungkan layanan pihak ketiga dengan Operra CRM Anda. Token ini memberikan akses penuh ke fungsi pengiriman pesan dan manajemen lead via API.
                            </p>
                        </div>
                        <div class="shrink-0 w-full md:w-auto">
                            <button @click="generateToken" class="w-full md:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-xl shadow-indigo-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                Generate New Token
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4 w-full md:w-auto">
                            <div class="h-12 w-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Active App Key / Token</div>
                                <div class="text-xs font-mono font-bold text-gray-700 dark:text-gray-300 truncate">{{ activeAppKey }}</div>
                            </div>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <button class="flex-1 md:flex-none px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 transition-all">Lihat Dokumentasi API</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Modal -->
        <div v-if="showQrModal" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeQrModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border border-gray-100 dark:border-gray-700">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="text-center">
                            <h3 class="text-lg leading-6 font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-4" id="modal-title">
                                Hubungkan WhatsApp
                            </h3>
                            
                            <!-- Status Badges -->
                            <div class="mb-6 flex justify-center">
                                <div v-if="connectionStatus === 'waiting_scan'" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-full border border-blue-100 dark:border-blue-800 flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 bg-blue-500 rounded-full animate-ping"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Menunggu Scan...</span>
                                </div>
                                <div v-if="connectionStatus === 'connected'" class="px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full border border-green-100 dark:border-green-800 flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 bg-green-500 rounded-full"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest">Terhubung!</span>
                                </div>
                                <div v-if="connectionStatus === 'expired'" class="px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-full border border-red-100 dark:border-red-800 flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 bg-red-500 rounded-full"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest">QR Kedaluwarsa</span>
                                </div>
                            </div>

                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-6 font-medium uppercase tracking-widest">
                                Buka WhatsApp di ponsel Anda > Perangkat Tertaut > Tautkan Perangkat.
                            </p>
                            
                            <div class="flex justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl relative min-h-[250px] items-center overflow-hidden">
                                <div v-if="qrLoading" class="flex flex-col items-center">
                                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-500 mb-3"></div>
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Menyiapkan Sesi...</span>
                                </div>
                                <template v-else>
                                    <!-- QR Image -->
                                    <img v-if="currentQr && connectionStatus === 'waiting_scan'" :src="currentQr" alt="WhatsApp QR Code" class="max-w-full rounded-lg shadow-inner">
                                    
                                    <!-- Success View -->
                                    <div v-if="connectionStatus === 'connected'" class="flex flex-col items-center animate-bounce-slow">
                                        <div class="h-20 w-20 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        <div class="text-sm font-black text-gray-800 dark:text-white">{{ connectedDevice.name }}</div>
                                        <div class="text-[10px] font-mono text-gray-500">{{ connectedDevice.phone }}</div>
                                    </div>

                                    <!-- Expired View -->
                                    <div v-if="connectionStatus === 'expired'" class="flex flex-col items-center">
                                        <button @click="openQrModal(selectedAccountForQr)" class="px-6 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 transition-all">
                                            Muat Ulang QR
                                        </button>
                                    </div>

                                    <div v-if="!currentQr && connectionStatus === 'waiting_scan'" class="text-red-500 text-xs font-bold uppercase tracking-widest">
                                        QR Code tidak tersedia.
                                    </div>
                                </template>
                            </div>

                            <div class="mt-6">
                                <p class="text-[10px] text-gray-400 italic font-medium">
                                    QR Code diperbarui otomatis. Jangan tutup jendela ini sampai perangkat terhubung.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="closeQrModal" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-xs font-black uppercase tracking-widest text-white hover:bg-blue-700 focus:outline-none transition-all sm:ml-3 sm:w-auto">
                            {{ connectionStatus === 'connected' ? 'Selesai' : 'Tutup' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

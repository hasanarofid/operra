<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    settings: Object,
    waStatus: Object,
    accounts: Array
});

// --- Forms ---
const globalForm = useForm({
    meta_access_token: props.settings.meta_access_token || '',
    meta_webhook_verify_token: props.settings.meta_webhook_verify_token || '',
    meta_app_id: props.settings.meta_app_id || '',
    meta_waba_id: props.settings.meta_waba_id || '',
});

const accountForm = useForm({
    id: null,
    name: '',
    phone_number: '', 
    provider: 'internal', // Default to Internal for Operra
    token: '',
    key: '',
    endpoint: '',
});

const generatingToken = ref(false);
const generateToken = async () => {
    generatingToken.value = true;
    try {
        const response = await axios.post(route('crm.wa.settings.generate-token'));
        if (response.data.status) {
            accountForm.token = response.data.token;
        }
    } catch (e) {
        console.error(e);
    } finally {
        generatingToken.value = false;
    }
};

// --- State ---
const isEditing = ref(false);
const showGlobalSettings = ref(false);
const showAddDeviceModal = ref(false);
const deviceStep = ref(1); // 1: Input Details, 2: Scan QR
const qrLoading = ref(false);
const currentQr = ref(null);
const connectionStatus = ref('waiting'); // waiting, connected, error
const connectedDevice = ref(null);
let pollingInterval = null;

// --- Computed Stats ---
const totalDevices = computed(() => props.accounts.length);
const connectedCount = computed(() => props.accounts.filter(a => a.status === 'active').length);
// Mock message count for now, or use a prop if available
const messageCount = ref(0); 

// --- Actions ---

const submitGlobal = () => {
    globalForm.post(route('crm.wa.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by global mixin if flash is present, otherwise explicit:
            Swal.fire({ icon: 'success', title: 'Saved', text: 'Konfigurasi Global disimpan!', timer: 2000, showConfirmButton: false });
            showGlobalSettings.value = false;
        },
    });
};

const openAddDeviceModal = () => {
    accountForm.reset();
    accountForm.provider = 'internal'; // Ensure default
    isEditing.value = false;
    deviceStep.value = 1;
    showAddDeviceModal.value = true;
    currentQr.value = null;
    connectionStatus.value = 'waiting';
};

const editAccount = (account) => {
    accountForm.id = account.id;
    accountForm.name = account.name;
    accountForm.phone_number = account.phone_number;
    accountForm.provider = account.provider;
    accountForm.token = account.api_credentials?.token || '';
    isEditing.value = true;
    
    // For editing, we just show the form, typically no immediate scan needed unless requested
    deviceStep.value = 1;
    showAddDeviceModal.value = true;
};

const saveDeviceAndScan = () => {
    if (isEditing.value) {
        accountForm.put(route('crm.wa.accounts.update', accountForm.id), {
            onSuccess: () => {
                showAddDeviceModal.value = false;
                Swal.fire({ icon: 'success', title: 'Updated', text: 'Data perangkat diperbarui.', timer: 2000, showConfirmButton: false });
            }
        });
        return;
    }

    // Existing Account Store Logic
    accountForm.post(route('crm.wa.accounts.store'), {
        onSuccess: (page) => {
            // Check for explicit ID from backend first (more robust)
            const newAccountId = page.props.flash?.new_account_id;
            
            if (newAccountId) {
                deviceStep.value = 2;
                fetchQrCode(newAccountId);
                return;
            }

            // Fallback: match by phone number
            const newAccount = page.props.accounts.find(a => a.phone_number === accountForm.phone_number); // Note: Backend might sanitize 08 -> 62
            if (newAccount) {
                deviceStep.value = 2;
                fetchQrCode(newAccount.id);
            } else {
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Perangkat ditambahkan, tetapi gagal memuat sesi QR. Silakan coba scan dari daftar.' });
                showAddDeviceModal.value = false;
            }
        },
        onError: () => {
            // Validation errors are handled automatically by Inertia
        }
    });
};

const fetchQrCode = async (accountId) => {
    qrLoading.value = true;
    currentQr.value = null;
    try {
        const response = await axios.post(route('crm.wa.accounts.connect', accountId));
        if (response.data.status) {
            currentQr.value = response.data.qr;
            startPolling(accountId);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: response.data.message || 'Gagal mengambil QR Code' });
        }
    } catch (e) {
        console.error(e);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
    } finally {
        qrLoading.value = false;
    }
};

const startPolling = (accountId) => {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(async () => {
        try {
            const response = await axios.get(route('crm.wa.accounts.status', accountId));
            if (response.data.status === 'connected') {
                connectionStatus.value = 'connected';
                connectedDevice.value = response.data;
                clearInterval(pollingInterval);
                // Reload to update list status
                setTimeout(() => window.location.reload(), 1500); 
            }
        } catch (e) {
            console.error(e);
        }
    }, 3000);
};

const openScanForExisting = (account) => {
    accountForm.phone_number = account.phone_number; // Set for context
    isEditing.value = false; // Not editing details, just scanning
    deviceStep.value = 2;
    showAddDeviceModal.value = true;
    fetchQrCode(account.id);
};

const disconnectDevice = async (id) => {
    Swal.fire({
        title: 'Putuskan koneksi?',
        text: "Perangkat ini akan terputus dari WhatsApp.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, putuskan!'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.post(route('crm.wa.accounts.disconnect', id));
                window.location.reload();
            } catch(e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memutuskan koneksi.' });
            }
        }
    });
};

const deleteAccount = (id) => {
    Swal.fire({
        title: 'Hapus perangkat ini?',
        text: "Anda tidak dapat mengembalikan data ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            accountForm.delete(route('crm.wa.accounts.destroy', id));
        }
    });
};

const syncFromMeta = () => {
    Swal.fire({
        title: 'Sinkronisasi dari Meta?',
        text: "Kami akan mengambil nomor telepon yang terdaftar di WABA ID Anda.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Ya, sinkronkan!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(route('crm.wa.accounts.sync-meta'))
                .then(res => {
                    window.location.reload();
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Gagal sinkronisasi.' });
                });
        }
    });
};

const closeModal = () => {
    showAddDeviceModal.value = false;
    if (pollingInterval) clearInterval(pollingInterval);
};
</script>

<template>
    <Head title="WhatsApp Devices" />

    <AuthenticatedLayout>
        <!-- Fonnte-like Header/Dashboard -->
        <div class="py-6 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="font-bold text-2xl text-gray-800 dark:text-white tracking-tight">
                            WhatsApp Devices
                        </h2>
                        <div v-if="waStatus.is_meta_ready" class="mt-1 flex items-center gap-2">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            <span class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase tracking-widest">Meta API Ready</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button v-if="waStatus.is_meta_ready" @click="syncFromMeta" class="px-4 py-2 text-xs font-bold text-green-600 hover:text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-400 rounded-lg transition-all flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Auto-Sync Meta Devices
                        </button>
                        <button @click="showGlobalSettings = true" class="px-4 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-300 rounded-lg transition-all">
                            System Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Devices Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white">{{ totalDevices }}</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Devices</div>
                        </div>
                    </div>

                    <!-- Connect Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white">{{ connectedCount }}</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Connected</div>
                        </div>
                    </div>

                     <!-- Messages Card (Mock) -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white">{{ messageCount }}</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Messages</div>
                        </div>
                    </div>
                </div>

                <!-- Devices List Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">Devices</h3>
                        <button @click="openAddDeviceModal" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:scale-105 transition-transform shadow-lg">
                            + Add Device
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Device</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Package</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="account in accounts" :key="account.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm text-gray-900 dark:text-white">{{ account.phone_number }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ account.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500 dark:text-gray-400">
                                        {{ account.provider }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span :class="account.status === 'active' ? 'bg-green-500' : 'bg-red-500'" class="w-2.5 h-2.5 rounded-full"></span>
                                            <span class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                                {{ account.status === 'active' ? 'Connected' : 'Disconnect' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <!-- Connect Button -->
                                            <button v-if="account.status !== 'active'" @click="openScanForExisting(account)" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm">
                                                Connect
                                            </button>
                                            
                                            <!-- Disconnect Button -->
                                            <button v-if="account.status === 'active'" @click="disconnectDevice(account.id)" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm">
                                                Disconnect
                                            </button>

                                            <button @click="editAccount(account)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm">
                                                Edit
                                            </button>
                                            <button @click="deleteAccount(account.id)" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors shadow-sm">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="accounts.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm font-medium">
                                        No devices found. Click "Add Device" to start.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer / Help -->
                <div class="text-center text-xs text-gray-400 font-medium">
                    Need help setting up? <a href="#" class="text-blue-500 hover:underline">View Tutorial</a>
                </div>
            </div>
        </div>

        <!-- Add Device Modal -->
        <div v-if="showAddDeviceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">
                        {{ deviceStep === 1 ? (isEditing ? 'Edit Device' : 'Add New Device') : 'Scan QR Code' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Step 1: Input -->
                <div v-if="deviceStep === 1" class="p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Device Name (Optional)</label>
                        <input v-model="accountForm.name" type="text" placeholder="My Business WA" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm focus:ring-0 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">WhatsApp Number</label>
                        <input v-model="accountForm.phone_number" type="text" placeholder="628123456789" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm focus:ring-0 focus:border-indigo-500 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Provider</label>
                            <select v-model="accountForm.provider" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm focus:ring-0 focus:border-indigo-500 transition-all">
                                <option value="internal">Operra (Internal)</option>
                                <option value="fonnte">Fonnte</option>
                                <option value="official">Official API</option>
                            </select>
                        </div>
                         <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">API Token</label>
                            <div class="relative">
                                <input v-model="accountForm.token" type="password" placeholder="API Token" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm focus:ring-0 focus:border-indigo-500 transition-all">
                                <button v-if="accountForm.provider === 'internal'" @click="generateToken" type="button" class="absolute right-2 top-1.5 px-2 py-1 bg-indigo-600 text-white text-[8px] font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                                    {{ generatingToken ? '...' : 'GENERATE' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button @click="saveDeviceAndScan" :disabled="accountForm.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 active:scale-95 transition-all">
                            {{ isEditing ? 'Save Changes' : 'Add & Scan WhatsApp' }}
                        </button>
                    </div>
                </div>

                <!-- Step 2: QR Scan -->
                 <div v-if="deviceStep === 2" class="p-6 text-center">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 min-h-[300px] flex flex-col items-center justify-center relative border border-dashed border-gray-200 dark:border-gray-700">
                        
                        <div v-if="qrLoading" class="flex flex-col items-center gap-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-2 border-indigo-500 border-t-transparent"></div>
                            <span class="text-xs font-bold text-gray-400">Generatig Session...</span>
                        </div>

                         <div v-else-if="currentQr && connectionStatus !== 'connected'" class="space-y-4">
                            <img :src="currentQr" class="w-64 h-64 rounded-lg mix-blend-multiply dark:mix-blend-normal mx-auto shadow-sm" alt="QR Code">
                            <p class="text-xs text-gray-500 font-medium">Open WhatsApp > Linked Devices > Link a Device</p>
                        </div>

                        <div v-else-if="connectionStatus === 'connected'" class="space-y-4 animate-bounce-slow">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h4 class="font-bold text-gray-800 dark:text-white">Connected!</h4>
                            <p class="text-xs text-gray-500">Redirecting...</p>
                        </div>
                    </div>
                    
                    <button v-if="!connectionStatus === 'connected'" @click="deviceStep = 1" class="mt-4 text-xs font-bold text-gray-400 hover:text-gray-600">Back to Details</button>
                </div>
            </div>
        </div>

        <!-- System Settings Slide-over (Right Side) -->
         <div v-if="showGlobalSettings" class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-gray-900/20 backdrop-blur-sm" @click="showGlobalSettings = false"></div>
            <div class="absolute inset-y-0 right-0 max-w-sm w-full bg-white dark:bg-gray-800 shadow-2xl transform transition-transform border-l border-gray-100 dark:border-gray-700 p-6 overflow-y-auto">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="font-bold text-xl text-gray-800 dark:text-white">System Settings</h3>
                    <button @click="showGlobalSettings = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form @submit.prevent="submitGlobal" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Meta WABA ID</label>
                        <input v-model="globalForm.meta_waba_id" type="text" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                    </div>
                     <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">App ID</label>
                        <input v-model="globalForm.meta_app_id" type="text" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                    </div>
                     <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Access Token</label>
                        <input v-model="globalForm.meta_access_token" type="password" class="w-full rounded-xl bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-sm">
                    </div>
                    <button type="submit" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 py-3 rounded-xl text-xs font-black uppercase tracking-widest">
                        Save Configuration
                    </button>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

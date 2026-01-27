<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    apps: Array,
    whatsappAccounts: Array
});

const form = useForm({
    id: null,
    name: '',
    phone_number: '',
    webhook_url: '',
    is_active: true,
    widget_settings: {
        primary_color: '#25D366',
        position: 'right',
        welcome_text: 'Halo! Ada yang bisa kami bantu?',
    }
});

const isEditing = ref(false);
const showSecrets = ref({});
const previewAppKey = ref(null);

const togglePreview = (key) => {
    if (previewAppKey.value === key) {
        previewAppKey.value = null;
        const bubble = document.querySelector('.wa-widget-bubble');
        if (bubble) bubble.remove();
        return;
    }
    
    previewAppKey.value = key;
    const script = document.createElement('script');
    script.src = `/js/wa-widget.js?t=${Date.now()}`;
    script.setAttribute('data-key', key);
    document.body.appendChild(script);
    
    alert('Simulasi diaktifkan! Lihat pojok layar Anda.');
};

const openPreviewPage = (key, type = 'widget') => {
    window.open(route('crm.wa.external-apps.preview', { key: key, type: type }), '_blank');
};

const toggleSecret = (id) => {
    showSecrets.value[id] = !showSecrets.value[id];
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('crm.wa.external-apps.update', form.id), {
            onSuccess: () => {
                resetForm();
            }
        });
    } else {
        form.post(route('crm.wa.external-apps.store'), {
            onSuccess: () => {
                resetForm();
            }
        });
    }
};

const editApp = (app) => {
    isEditing.value = true;
    form.id = app.id;
    form.name = app.name;
    form.phone_number = app.phone_number || '';
    form.webhook_url = app.webhook_url || '';
    form.is_active = !!app.is_active;
    form.widget_settings = app.widget_settings || {
        primary_color: '#25D366',
        position: 'right',
        welcome_text: 'Halo! Ada yang bisa kami bantu?',
    };
};

const deleteApp = (id) => {
    if (confirm('Are you sure you want to delete this app?')) {
        form.delete(route('crm.wa.external-apps.destroy', id));
    }
};

const resetForm = () => {
    isEditing.value = false;
    form.reset();
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    alert('Copied to clipboard!');
};
</script>

<template>
    <Head title="External Apps & Embedding" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                External Apps & WhatsApp Embedding
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-2xl border-0">
                        <h3 class="font-black text-lg mb-6 text-gray-800 dark:text-white uppercase tracking-tighter">{{ isEditing ? 'Edit External App' : 'Register New App' }}</h3>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">App Name</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">WhatsApp Number</label>
                                <select v-model="form.phone_number" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm" required>
                                    <option value="">Select Account</option>
                                    <option v-for="acc in whatsappAccounts" :key="acc.id" :value="acc.phone_number">
                                        {{ acc.name }} ({{ acc.phone_number }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Webhook URL</label>
                                <input v-model="form.webhook_url" type="url" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                            </div>
                            <div class="pt-5 border-t border-gray-100 dark:border-gray-700">
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-500 dark:text-blue-400 mb-4 text-center">Widget Settings</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Color</label>
                                        <input v-model="form.widget_settings.primary_color" type="color" class="mt-1 block w-full h-12 rounded-xl border-gray-100 dark:border-gray-600 appearance-none bg-transparent cursor-pointer">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Position</label>
                                        <select v-model="form.widget_settings.position" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                            <option value="right">Right</option>
                                            <option value="left">Left</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                                <input v-model="form.is_active" type="checkbox" id="is_active" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:bg-gray-800 focus:ring-blue-500 transition-all">
                                <label for="is_active" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Active Status</label>
                            </div>
                            <div class="flex flex-col gap-3 pt-4">
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 active:scale-95 transition-all w-full">
                                    {{ isEditing ? 'Update App' : 'Register App' }}
                                </button>
                                <button v-if="isEditing" @click="resetForm" type="button" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- List -->
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border-0">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">App Details</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Embed Snippets</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="app in apps" :key="app.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-800 dark:text-white">{{ app.name }}</div>
                                        <div class="text-[10px] font-bold text-gray-500 uppercase mt-1">{{ app.phone_number }}</div>
                                        <div class="text-[9px] font-mono mt-2 bg-gray-50 dark:bg-gray-900/50 p-1.5 rounded-lg border border-gray-100 dark:border-gray-800 text-blue-600 dark:text-blue-400 inline-block">{{ app.app_key }}</div>
                                    </td>
                                    <td class="px-6 py-4 space-y-3">
                                        <button @click="copyToClipboard(`<script src='/js/wa-widget.js' data-key='${app.app_key}'></script>`)" class="text-[9px] font-black uppercase tracking-widest block w-full text-center bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 py-2 rounded-xl border border-blue-100 dark:border-blue-800/50 hover:bg-blue-100 transition-all">
                                            Copy Widget Snippet
                                        </button>
                                        <button @click="copyToClipboard(`<iframe src='/embed/inbox?key=${app.app_key}' style='width:100%; height:600px; border:none;'></iframe>`)" class="text-[9px] font-black uppercase tracking-widest block w-full text-center bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 py-2 rounded-xl border border-green-100 dark:border-green-800/50 hover:bg-green-100 transition-all">
                                            Copy Inbox Iframe
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex justify-end gap-3">
                                            <button @click="editApp(app)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-[10px] font-black uppercase tracking-widest transition-all">Edit</button>
                                            <button @click="deleteApp(app.id)" class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest transition-all">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="apps.length === 0">
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 text-xs italic font-medium">No external apps registered yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


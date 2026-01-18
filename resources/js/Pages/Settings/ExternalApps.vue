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
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <h3 class="font-bold text-lg mb-4">{{ isEditing ? 'Edit External App' : 'Register New App' }}</h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium">App Name</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">WhatsApp Number</label>
                                <select v-model="form.phone_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Select Account</option>
                                    <option v-for="acc in whatsappAccounts" :key="acc.id" :value="acc.phone_number">
                                        {{ acc.name }} ({{ acc.phone_number }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Webhook URL</label>
                                <input v-model="form.webhook_url" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="pt-4 border-t">
                                <h4 class="text-xs font-bold uppercase text-gray-400 mb-2">Widget Settings</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium">Color</label>
                                        <input v-model="form.widget_settings.primary_color" type="color" class="mt-1 block w-full h-10 rounded-md border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Position</label>
                                        <select v-model="form.widget_settings.position" class="mt-1 block w-full rounded-md border-gray-300">
                                            <option value="right">Right</option>
                                            <option value="left">Left</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">Active Status</label>
                            </div>
                            <div class="flex gap-2 pt-4">
                                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold uppercase">
                                    {{ isEditing ? 'Update' : 'Create' }}
                                </button>
                                <button v-if="isEditing" @click="resetForm" type="button" class="bg-gray-500 text-white px-4 py-2 rounded text-sm font-bold uppercase">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- List -->
                    <div class="lg:col-span-2 bg-white shadow sm:rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">App Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Embed Snippets</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="app in apps" :key="app.id">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ app.name }}</div>
                                        <div class="text-xs text-gray-500">{{ app.phone_number }}</div>
                                        <div class="text-[10px] font-mono mt-1 bg-gray-100 p-1 rounded">{{ app.app_key }}</div>
                                    </td>
                                    <td class="px-6 py-4 space-y-2">
                                        <button @click="copyToClipboard(`<script src='/js/wa-widget.js' data-key='${app.app_key}'></script>`)" class="text-[10px] block w-full text-left bg-blue-50 text-blue-700 p-1 rounded border border-blue-100">
                                            Copy Widget Snippet
                                        </button>
                                        <button @click="copyToClipboard(`<iframe src='/embed/inbox?key=${app.app_key}' style='width:100%; height:600px; border:none;'></iframe>`)" class="text-[10px] block w-full text-left bg-green-50 text-green-700 p-1 rounded border border-green-100">
                                            Copy Inbox Iframe
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <button @click="editApp(app)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                        <button @click="deleteApp(app.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


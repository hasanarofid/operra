<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    autoReplies: Array,
    whatsappAccounts: Array
});

const form = useForm({
    id: null,
    whatsapp_account_id: '',
    keyword: '',
    response: '',
    match_type: 'exact',
    is_active: true,
});

const isEditing = ref(false);

const saveAutoReply = () => {
    if (isEditing.value) {
        form.put(route('crm.wa.auto-reply.update', form.id), {
            onSuccess: () => {
                resetForm();
                alert('Auto Reply updated successfully!');
            }
        });
    } else {
        form.post(route('crm.wa.auto-reply.store'), {
            onSuccess: () => {
                resetForm();
                alert('Auto Reply added successfully!');
            }
        });
    }
};

const editAutoReply = (reply) => {
    isEditing.value = true;
    form.id = reply.id;
    form.whatsapp_account_id = reply.whatsapp_account_id || '';
    form.keyword = reply.keyword;
    form.response = reply.response;
    form.match_type = reply.match_type;
    form.is_active = !!reply.is_active;
};

const deleteAutoReply = (id) => {
    if (confirm('Are you sure you want to delete this auto reply?')) {
        form.delete(route('crm.wa.auto-reply.destroy', id), {
            onSuccess: () => alert('Auto Reply deleted!')
        });
    }
};

const toggleStatus = (id) => {
    form.post(route('crm.wa.auto-reply.toggle', id), {
        preserveScroll: true
    });
};

const resetForm = () => {
    isEditing.value = false;
    form.reset();
};
</script>

<template>
    <Head title="WhatsApp Auto Reply" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                WhatsApp Auto Reply Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Form Auto Reply -->
                    <div class="lg:col-span-1">
                        <div class="bg-white p-6 shadow sm:rounded-lg">
                            <h3 class="font-bold text-lg mb-4 text-gray-700">
                                {{ isEditing ? 'Edit Auto Reply' : 'Add New Auto Reply' }}
                            </h3>
                            <form @submit.prevent="saveAutoReply" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium">WhatsApp Account</label>
                                    <select v-model="form.whatsapp_account_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="">All Accounts</option>
                                        <option v-for="acc in whatsappAccounts" :key="acc.id" :value="acc.id">
                                            {{ acc.name }} ({{ acc.phone_number }})
                                        </option>
                                    </select>
                                    <p class="text-[10px] text-gray-500 mt-1 italic">Pilih akun spesifik atau "All Accounts" untuk semua akun.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium">Keyword</label>
                                    <input v-model="form.keyword" type="text" placeholder="e.g. Halo, Harga, Info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium">Match Type</label>
                                    <select v-model="form.match_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="exact">Exact Match (Sama Persis)</option>
                                        <option value="contains">Contains (Mengandung Kata)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium">Auto Response Message</label>
                                    <textarea v-model="form.response" rows="4" placeholder="Tulis balasan otomatis di sini..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm"></textarea>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" v-model="form.is_active" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                                </div>

                                <div class="flex gap-2 pt-4">
                                    <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-6 py-2 rounded text-xs font-bold uppercase shadow hover:bg-blue-700 transition w-full">
                                        {{ isEditing ? 'Update Auto Reply' : 'Create Auto Reply' }}
                                    </button>
                                    <button v-if="isEditing" @click="resetForm" type="button" class="bg-gray-500 text-white px-4 py-2 rounded text-xs font-bold uppercase shadow hover:bg-gray-600 transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List Auto Replies -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="font-bold text-lg text-gray-700">Existing Auto Replies</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keyword</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Response</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="reply in autoReplies" :key="reply.id">
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ reply.whatsapp_account ? reply.whatsapp_account.name : 'All Accounts' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-blue-600">
                                                {{ reply.keyword }}
                                                <span class="block text-[10px] text-gray-400 font-normal uppercase italic">({{ reply.match_type }})</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {{ reply.response }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <button @click="toggleStatus(reply.id)" :class="reply.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs font-bold uppercase">
                                                    {{ reply.is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium flex gap-3">
                                                <button @click="editAutoReply(reply)" class="text-blue-600 hover:text-blue-900 uppercase text-xs font-bold">Edit</button>
                                                <button @click="deleteAutoReply(reply.id)" class="text-red-600 hover:text-red-900 uppercase text-xs font-bold">Delete</button>
                                            </td>
                                        </tr>
                                        <tr v-if="autoReplies.length === 0">
                                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No auto replies configured yet.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


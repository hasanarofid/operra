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
                Swal.fire({ icon: 'success', title: 'Success', text: 'Auto Reply updated successfully!', timer: 2000, showConfirmButton: false });
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
                        <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-2xl border-0">
                            <h3 class="font-black text-lg mb-6 text-gray-800 dark:text-white uppercase tracking-tighter">
                                {{ isEditing ? 'Edit Auto Reply' : 'Add New Auto Reply' }}
                            </h3>
                            <form @submit.prevent="saveAutoReply" class="space-y-5">
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">WhatsApp Account</label>
                                    <select v-model="form.whatsapp_account_id" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                        <option value="">All Accounts</option>
                                        <option v-for="acc in whatsappAccounts" :key="acc.id" :value="acc.id">
                                            {{ acc.name }} ({{ acc.phone_number }})
                                        </option>
                                    </select>
                                    <p class="text-[10px] text-gray-500 mt-2 italic">Pilih akun spesifik atau "All Accounts".</p>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Keyword</label>
                                    <input v-model="form.keyword" type="text" placeholder="e.g. Halo, Harga, Info" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Match Type</label>
                                    <select v-model="form.match_type" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                                        <option value="exact">Exact Match (Sama Persis)</option>
                                        <option value="contains">Contains (Mengandung Kata)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Auto Response Message</label>
                                    <textarea v-model="form.response" rows="4" placeholder="Tulis balasan otomatis di sini..." class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm"></textarea>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                                    <input type="checkbox" v-model="form.is_active" id="is_active" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:bg-gray-800 focus:ring-blue-500 transition-all">
                                    <label for="is_active" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Active Status</label>
                                </div>

                                <div class="flex flex-col gap-3 pt-4">
                                    <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 active:scale-95 transition-all w-full">
                                        {{ isEditing ? 'Update Auto Reply' : 'Create Auto Reply' }}
                                    </button>
                                    <button v-if="isEditing" @click="resetForm" type="button" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List Auto Replies -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border-0">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                                <h3 class="font-black text-lg text-gray-800 dark:text-white uppercase tracking-tighter">Existing Auto Replies</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Account</th>
                                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Keyword</th>
                                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Response</th>
                                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Status</th>
                                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                        <tr v-for="reply in autoReplies" :key="reply.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="px-6 py-4 text-xs font-medium text-gray-600 dark:text-gray-400">
                                                {{ reply.whatsapp_account ? reply.whatsapp_account.name : 'All Accounts' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-black text-blue-600 dark:text-blue-400 uppercase tracking-tight">{{ reply.keyword }}</div>
                                                <span class="block text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">Match: {{ reply.match_type }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate font-medium">
                                                {{ reply.response }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <button @click="toggleStatus(reply.id)" :class="reply.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-transparent shadow-sm hover:scale-105 transition-all">
                                                    {{ reply.is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end gap-4">
                                                    <button @click="editAutoReply(reply)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-[10px] font-black uppercase tracking-widest">Edit</button>
                                                    <button @click="deleteAutoReply(reply.id)" class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="autoReplies.length === 0">
                                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-xs italic font-medium">No auto replies configured yet.</td>
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


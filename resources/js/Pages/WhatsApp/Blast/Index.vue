<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    campaigns: Array,
    accounts: Array,
});

const form = useForm({
    name: '',
    whatsapp_account_id: '',
    customer_ids: [],
    message_template: '',
    template_name: '',
    template_data: null,
});

const isCreating = ref(false);

const submit = () => {
    form.post(route('crm.wa.blast.store'), {
        onSuccess: () => {
            isCreating.value = false;
            form.reset();
        },
    });
};

const processCampaign = (id) => {
    Swal.fire({
        title: 'Start sending messages?',
        text: "This will start the blast campaign.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, start!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(route('crm.wa.blast.process', id))
                .then(response => {
                    Swal.fire({ icon: 'success', title: 'Success', text: response.data.message, timer: 3000, showConfirmButton: false });
                    window.location.reload();
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Error', text: error.response?.data?.message || 'Error processing campaign' });
                });
        }
    });
};
</script>

<template>
    <Head title="WhatsApp Blast" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                WhatsApp Marketing (Blast)
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden p-6 border-0">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200 uppercase tracking-tight">Marketing Campaigns</h3>
                        <button @click="isCreating = !isCreating" class="bg-blue-600 text-white px-4 py-2 rounded text-xs font-bold uppercase shadow hover:bg-blue-700 transition">
                            {{ isCreating ? 'Cancel' : 'Create New Blast' }}
                        </button>
                    </div>

                    <!-- Create Form -->
                    <div v-if="isCreating" class="mb-8 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-600">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Campaign Name</label>
                                    <input v-model="form.name" type="text" placeholder="Promo Ramadhan 2026" class="mt-1 block w-full rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all" required>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Sender Account</label>
                                    <select v-model="form.whatsapp_account_id" class="mt-1 block w-full rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all" required>
                                        <option value="">Select Account</option>
                                        <option v-for="account in accounts" :key="account.id" :value="account.id">
                                            {{ account.name }} ({{ account.phone_number }})
                                        </option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Message Content / Template Name</label>
                                    <textarea v-model="form.message_template" rows="5" class="mt-1 block w-full rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all" placeholder="Tulis pesan blast Anda di sini..."></textarea>
                                    <p class="text-[10px] text-gray-500 mt-2 italic">*Jika menggunakan Meta Official, isi dengan nama template.</p>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Recipient IDs (Comma separated)</label>
                                    <input type="text" @change="form.customer_ids = $event.target.value.split(',')" placeholder="1, 2, 3" class="mt-1 block w-full rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" :disabled="form.processing" class="bg-green-600 text-white px-6 py-2 rounded text-xs font-bold uppercase shadow hover:bg-green-700 transition">
                                    Save Campaign Draft
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Campaigns List -->
                    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Campaign Name</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Sender</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Status</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Progress</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="campaign in campaigns" :key="campaign.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700 dark:text-gray-200">{{ campaign.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">{{ campaign.account?.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 inline-flex text-[10px] font-black uppercase tracking-tighter rounded-full" :class="{
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400': campaign.status === 'draft',
                                            'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': campaign.status === 'processing',
                                            'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400': campaign.status === 'completed',
                                        }">{{ campaign.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">
                                        {{ campaign.sent_count }} / {{ campaign.total_recipients }} Sent
                                        <div v-if="campaign.failed_count > 0" class="text-red-500 text-[10px] font-bold">({{ campaign.failed_count }} Failed)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex justify-end gap-3">
                                        <button v-if="campaign.status === 'draft'" @click="processCampaign(campaign.id)" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest shadow hover:bg-blue-700 active:scale-95 transition-all">Process</button>
                                        <button @click="$inertia.delete(route('crm.wa.blast.destroy', campaign.id))" class="text-red-500 hover:text-red-700 text-xs font-bold transition-all">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="campaigns.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No marketing campaigns found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


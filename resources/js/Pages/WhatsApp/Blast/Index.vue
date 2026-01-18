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
    if (confirm('Start sending messages for this campaign?')) {
        axios.post(route('crm.wa.blast.process', id))
            .then(response => {
                alert(response.data.message);
                window.location.reload();
            })
            .catch(error => {
                alert(error.response?.data?.message || 'Error processing campaign');
            });
    }
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-gray-700">Marketing Campaigns</h3>
                        <button @click="isCreating = !isCreating" class="bg-blue-600 text-white px-4 py-2 rounded text-xs font-bold uppercase shadow hover:bg-blue-700 transition">
                            {{ isCreating ? 'Cancel' : 'Create New Blast' }}
                        </button>
                    </div>

                    <!-- Create Form -->
                    <div v-if="isCreating" class="mb-8 p-6 bg-gray-50 rounded-lg border">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium">Campaign Name</label>
                                    <input v-model="form.name" type="text" placeholder="Promo Ramadhan 2026" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium">Sender Account</label>
                                    <select v-model="form.whatsapp_account_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="">Select Account</option>
                                        <option v-for="account in accounts" :key="account.id" :value="account.id">
                                            {{ account.name }} ({{ account.phone_number }})
                                        </option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium mb-1">Message Content / Template Name</label>
                                    <textarea v-model="form.message_template" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Tulis pesan blast Anda di sini..."></textarea>
                                    <p class="text-xs text-gray-500 mt-1 italic">*Jika menggunakan Meta Official, isi dengan nama template.</p>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium">Recipient IDs (Comma separated)</label>
                                    <input type="text" @change="form.customer_ids = $event.target.value.split(',')" placeholder="1, 2, 3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="campaign in campaigns" :key="campaign.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ campaign.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ campaign.account?.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="{
                                            'bg-gray-100 text-gray-600': campaign.status === 'draft',
                                            'bg-blue-100 text-blue-600': campaign.status === 'processing',
                                            'bg-green-100 text-green-600': campaign.status === 'completed',
                                        }">{{ campaign.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ campaign.sent_count }} / {{ campaign.total_recipients }} Sent
                                        <div v-if="campaign.failed_count > 0" class="text-red-500 text-[10px]">({{ campaign.failed_count }} Failed)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex gap-2">
                                        <button v-if="campaign.status === 'draft'" @click="processCampaign(campaign.id)" class="bg-blue-600 text-white px-3 py-1 rounded text-[10px] font-bold uppercase shadow hover:bg-blue-700">Process</button>
                                        <button @click="$inertia.delete(route('crm.wa.blast.destroy', campaign.id))" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
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


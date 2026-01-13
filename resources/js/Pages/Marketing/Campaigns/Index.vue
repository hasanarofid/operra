<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    campaigns: Object
});
</script>

<template>
    <Head title="Marketing Campaigns" />

    <AuthenticatedLayout>
        <template #header>
            MARKETING: CAMPAIGNS
        </template>

        <template #stats>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="text-xs uppercase font-black opacity-70 mb-1">Total Campaigns</div>
                    <div class="text-3xl font-black">{{ campaigns.total }}</div>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                    <div class="rounded-t mb-0 px-6 py-4 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-black text-lg text-gray-700 dark:text-gray-200">All Campaigns</h3>
                            </div>
                            <div class="relative w-full max-w-full flex-grow flex-1 text-right">
                                <button class="bg-purple-500 text-white active:bg-purple-600 text-xs font-black uppercase px-4 py-2 rounded-lg shadow hover:shadow-md transition-all">
                                    Create Campaign
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Name</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Type</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Audience</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="camp in campaigns.data" :key="camp.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-200">{{ camp.name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ camp.type }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            {{ camp.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-200">{{ camp.target_audience_count }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ camp.start_date || '-' }}</td>
                                </tr>
                                <tr v-if="!campaigns.data.length">
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">No campaigns found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


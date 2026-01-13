<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    leads: Object
});
</script>

<template>
    <Head title="Lead Scoring" />

    <AuthenticatedLayout>
        <template #header>
            MARKETING: LEAD SCORING & NURTURING
        </template>

        <template #stats>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-orange-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="text-xs uppercase font-black opacity-70 mb-1">Hot Leads (Score > 80)</div>
                    <div class="text-3xl font-black">{{ leads.data.filter(l => l.lead_score > 80).length }}</div>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                    <div class="rounded-t mb-0 px-6 py-4 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-black text-lg text-gray-700 dark:text-gray-200">Lead Score Rankings</h3>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Customer Name</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Score</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Category</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Last Active</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="lead in leads.data" :key="lead.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ lead.name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ lead.phone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-lg font-black text-gray-800 dark:text-white">{{ lead.lead_score }}</div>
                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                                <div :class="[
                                                    'h-full rounded-full',
                                                    lead.lead_score > 80 ? 'bg-red-500' : 
                                                    lead.lead_score > 50 ? 'bg-orange-500' : 'bg-blue-500'
                                                ]" :style="`width: ${lead.lead_score}%`"></div >
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest',
                                            lead.lead_score > 80 ? 'bg-red-100 text-red-700' : 
                                            lead.lead_score > 50 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'
                                        ]">
                                            {{ lead.lead_score > 80 ? 'Hot' : lead.lead_score > 50 ? 'Warm' : 'Cold' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold uppercase text-gray-500">{{ lead.status }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ lead.updated_at || '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


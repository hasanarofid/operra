<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    blasts: Object
});
</script>

<template>
    <Head title="Marketing Blasts" />

    <AuthenticatedLayout>
        <template #header>
            MARKETING: EMAIL & WHATSAPP BLAST
        </template>

        <template #stats>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="text-xs uppercase font-black opacity-70 mb-1">Total Sent</div>
                    <div class="text-3xl font-black">{{ blasts.total }}</div>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                    <div class="rounded-t mb-0 px-6 py-4 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-black text-lg text-gray-700 dark:text-gray-200">Blast History</h3>
                            </div>
                            <div class="relative w-full max-w-full flex-grow flex-1 text-right">
                                <button class="bg-blue-500 text-white active:bg-blue-600 text-xs font-black uppercase px-4 py-2 rounded-lg shadow hover:shadow-md transition-all">
                                    New Blast
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Subject/Content</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Channel</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Delivery</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Sent At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="blast in blasts.data" :key="blast.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ blast.subject || 'WA Message' }}</div>
                                        <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ blast.content }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-black text-blue-500">{{ blast.channel.toUpperCase() }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ blast.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ blast.success_count }} / {{ blast.total_recipients }}</div>
                                        <div class="w-24 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full mt-1">
                                            <div class="bg-green-500 h-1.5 rounded-full" :style="`width: ${(blast.success_count/blast.total_recipients)*100}%`"></div >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ blast.sent_at || '-' }}</td>
                                </tr>
                                <tr v-if="!blasts.data.length">
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">No blast history found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


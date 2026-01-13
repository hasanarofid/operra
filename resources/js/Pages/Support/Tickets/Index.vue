<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tickets: Object
});
</script>

<template>
    <Head title="Support Tickets" />

    <AuthenticatedLayout>
        <template #header>
            SUPPORT: TICKETING SYSTEM
        </template>

        <template #stats>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-orange-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="text-xs uppercase font-black opacity-70 mb-1">Total Tickets</div>
                    <div class="text-3xl font-black">{{ tickets.total }}</div>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                    <div class="rounded-t mb-0 px-6 py-4 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-black text-lg text-gray-700 dark:text-gray-200">Active Tickets</h3>
                            </div>
                            <div class="relative w-full max-w-full flex-grow flex-1 text-right">
                                <button class="bg-orange-500 text-white active:bg-orange-600 text-xs font-black uppercase px-4 py-2 rounded-lg shadow hover:shadow-md transition-all">
                                    Create Ticket
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">TKT #</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Subject</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Customer</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Priority</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Due At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="tkt in tickets.data" :key="tkt.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-orange-600">{{ tkt.ticket_number }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-200">{{ tkt.subject }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ tkt.customer?.name }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest',
                                            tkt.priority === 'urgent' ? 'bg-red-100 text-red-700' : 
                                            tkt.priority === 'high' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'
                                        ]">
                                            {{ tkt.priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold uppercase text-gray-500">{{ tkt.status }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ tkt.sla_due_at || '-' }}</td>
                                </tr>
                                <tr v-if="!tickets.data.length">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No tickets found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


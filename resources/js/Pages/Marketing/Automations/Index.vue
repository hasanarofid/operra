<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    automations: Object
});
</script>

<template>
    <Head title="Automations" />

    <AuthenticatedLayout>
        <template #header>
            MARKETING: WORKFLOW AUTOMATIONS
        </template>

        <template #stats>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-indigo-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="text-xs uppercase font-black opacity-70 mb-1">Active Automations</div>
                    <div class="text-3xl font-black">{{ automations.total }}</div>
                </div>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                    <div class="rounded-t mb-0 px-6 py-4 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-black text-lg text-gray-700 dark:text-gray-200">Workflows</h3>
                            </div>
                            <div class="relative w-full max-w-full flex-grow flex-1 text-right">
                                <button class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-black uppercase px-4 py-2 rounded-lg shadow hover:shadow-md transition-all">
                                    Create Workflow
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Automation Name</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Trigger</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Steps</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="auto in automations.data" :key="auto.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-200">{{ auto.name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400 text-[10px] font-bold">
                                            {{ auto.trigger_event }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-500">{{ auto.actions?.length || 0 }} steps</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div :class="['h-2 w-2 rounded-full', auto.is_active ? 'bg-green-500' : 'bg-gray-400']"></div>
                                            <span class="text-xs font-bold uppercase">{{ auto.is_active ? 'Active' : 'Paused' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button class="text-indigo-500 font-bold text-[10px] uppercase">Edit</button>
                                    </td>
                                </tr>
                                <tr v-if="!automations.data.length">
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">No automations found. Build your first workflow!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


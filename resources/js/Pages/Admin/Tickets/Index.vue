<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tickets: Array,
});
</script>

<template>
    <Head title="Admin Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Support Tickets (Admin)
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User / Company</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="ticket in tickets" :key="ticket.id">
                                    <td class="px-6 py-4">{{ ticket.subject }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium">{{ ticket.user?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ ticket.user?.email }}</div>
                                        <div class="text-xs text-gray-400">{{ ticket.user?.company?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                              :class="{
                                                  'bg-green-100 text-green-800': ticket.status === 'open',
                                                  'bg-yellow-100 text-yellow-800': ticket.status === 'pending_admin',
                                                  'bg-blue-100 text-blue-800': ticket.status === 'pending_user',
                                                  'bg-red-100 text-red-800': ticket.status === 'closed'
                                              }">
                                            {{ ticket.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ new Date(ticket.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <Link :href="route('admin.tickets.show', ticket.id)" class="text-indigo-600 hover:text-indigo-900">View</Link>
                                    </td>
                                </tr>
                                <tr v-if="tickets.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No tickets found.
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

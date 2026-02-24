<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    tickets: Object,
    filters: Object,
});

const getStatusColor = (status) => {
    switch (status) {
        case "open":
            return "bg-blue-100 text-blue-800";
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        case "resolved":
            return "bg-emerald-100 text-emerald-800";
        case "closed":
            return "bg-gray-100 text-gray-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const getPriorityColor = (priority) => {
    switch (priority) {
        case "urgent":
            return "text-red-600 font-bold";
        case "high":
            return "text-orange-600 font-bold";
        case "normal":
            return "text-blue-600";
        case "low":
            return "text-gray-600";
        default:
            return "text-gray-600";
    }
};
</script>

<template>
    <Head title="Support Tickets" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Support Tickets
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-700">Support Tickets</h3>
                            <!-- <Link :href="route('crm.support.tickets.create')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition">
                                New Ticket
                            </Link> -->
                        </div>

                        <!-- Filter Tabs -->
                        <div class="flex border-b border-gray-200 mb-6">
                            <Link
                                :href="route('crm.support.tickets.index', { view: 'mine' })"
                                class="px-4 py-2 text-sm font-medium focus:outline-none transition-colors duration-200"
                                :class="filters.view === 'mine' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
                            >
                                My Tickets
                            </Link>
                            <Link
                                :href="route('crm.support.tickets.index', { view: 'unassigned' })"
                                class="px-4 py-2 text-sm font-medium focus:outline-none transition-colors duration-200"
                                :class="filters.view === 'unassigned' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
                            >
                                Unassigned (Queue)
                            </Link>
                            <Link
                                :href="route('crm.support.tickets.index', { view: 'all' })"
                                class="px-4 py-2 text-sm font-medium focus:outline-none transition-colors duration-200"
                                :class="filters.view === 'all' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
                            >
                                All Tickets
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="tickets.data.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No tickets found.
                                        </td>
                                    </tr>
                                    <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ ticket.ticket_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ticket.customer?.name || 'Unknown' }}
                                            <div class="text-xs text-gray-400">{{ ticket.customer?.phone }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ticket.subject }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusColor(ticket.status)">
                                                {{ ticket.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" :class="getPriorityColor(ticket.priority)">
                                            {{ ticket.priority }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ticket.sla_due_at ? new Date(ticket.sla_due_at).toLocaleDateString() : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <!-- <Link :href="route('crm.support.tickets.show', ticket.id)" class="text-emerald-600 hover:text-emerald-900">View</Link> -->
                                            <span class="text-gray-400 cursor-not-allowed">View</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <Pagination :links="tickets.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

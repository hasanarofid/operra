<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    stats: Object,
    recentLeads: Array,
    recentChats: Array,
    chartData: Array,
    waAccounts: Array,
    userRole: String,
});

const chartOptions = {
    chart: {
        type: 'area',
        toolbar: { show: false },
        animations: { enabled: true },
        background: 'transparent'
    },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [20, 100, 100, 100]
        }
    },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
    xaxis: {
        categories: props.chartData.map(d => d.date),
        labels: { style: { colors: '#9ca3af' } }
    },
    yaxis: { labels: { style: { colors: '#9ca3af' } } },
    colors: ['#0ea5e9'],
    theme: { mode: 'dark' }
};

const series = [{
    name: 'New Leads',
    data: props.chartData.map(d => d.count)
}];
</script>

<template>
    <Head title="Dashboard CRM" />

    <AuthenticatedLayout>
        <template #header>
            Dashboard CRM & WhatsApp Blast ({{ userRole }})
        </template>

        <template #stats>
            <!-- CRM KPI Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard 
                    title="Total Leads" 
                    :value="stats.total_leads"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </template>
                </StatCard>
                <StatCard 
                    title="Active Chats" 
                    :value="stats.active_chats"
                    :alert="stats.active_chats > 0"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    </template>
                </StatCard>
                <StatCard 
                    title="New Leads Today" 
                    :value="stats.new_leads_today"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    </template>
                </StatCard>
                <StatCard 
                    title="Messages Sent/Received" 
                    :value="stats.messages_today"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </template>
                </StatCard>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
                <!-- Recent Leads Table -->
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Recent Leads</h3>
                            </div>
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                                <Link :href="route('master.customers.index')" class="bg-operra-500 text-white active:bg-operra-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 shadow hover:shadow-md">
                                    Manage Leads
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Name</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Phone</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Status</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Source</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="lead in recentLeads" :key="lead.id">
                                    <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left font-bold text-gray-700 dark:text-gray-200">{{ lead.name }}</th>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">{{ lead.phone }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-700 dark:text-gray-200">
                                        <span :class="[
                                            'px-2 py-1 rounded-full text-xs font-semibold',
                                            lead.status === 'lead' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'
                                        ]">
                                            {{ lead.status }}
                                        </span>
                                    </td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">{{ lead.lead_source }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Lead Growth Chart -->
                <div class="relative flex flex-col min-w-0 break-words bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h6 class="uppercase text-gray-100 mb-1 text-xs font-semibold">Growth</h6>
                                <h2 class="text-white text-xl font-bold">New Leads Trend (7 Days)</h2>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 flex-auto">
                        <VueApexCharts height="350" :options="chartOptions" :series="series" />
                    </div>
                </div>
            </div>

            <div class="w-full xl:w-4/12 px-4">
                <!-- WA Accounts Status (For Admin/Manager) -->
                <div v-if="userRole !== 'sales'" class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">WhatsApp Accounts Status</h3>
                    </div>
                    <div class="p-4">
                        <div v-for="account in waAccounts" :key="account.id" class="mb-4 p-4 border border-gray-100 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-sm text-gray-700 dark:text-gray-200">{{ account.name }}</p>
                                    <p class="text-xs text-gray-500">{{ account.phone_number }}</p>
                                </div>
                                <span :class="account.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-[10px] font-bold uppercase">
                                    {{ account.status }}
                                </span>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ account.agents_count }} Agents Connected
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Conversations -->
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Recent Conversations</h3>
                    </div>
                    <div class="p-4">
                        <div v-for="chat in recentChats" :key="chat.id" class="mb-4 flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors rounded-lg">
                            <div class="h-10 w-10 rounded-full bg-operra-100 flex items-center justify-center text-operra-600 font-bold">
                                {{ chat.customer.name.charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-700 dark:text-gray-200 truncate">{{ chat.customer.name }}</p>
                                <p class="text-xs text-gray-500 truncate italic">Assigned to: {{ chat.assigned_user.name }}</p>
                            </div>
                            <div class="text-right">
                                <span :class="[
                                    'px-2 py-1 rounded-full text-[10px] font-bold uppercase',
                                    chat.status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                                ]">
                                    {{ chat.status }}
                                </span>
                                <p class="text-[10px] text-gray-400 mt-1">{{ new Date(chat.last_message_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</p>
                            </div>
                        </div>
                        <div v-if="recentChats.length === 0" class="text-center py-8 text-gray-500 text-sm">
                            No active conversations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

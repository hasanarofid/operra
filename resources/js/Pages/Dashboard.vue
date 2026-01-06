<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatCard from '@/Components/StatCard.vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    recentActivities: Array,
    chartData: Array,
    actionCenter: Object,
});

const chartOptions = {
    chart: {
        type: 'area',
        toolbar: { show: false },
        animations: { enabled: false },
        background: 'transparent'
    },
    stroke: { curve: 'smooth', width: 2 },
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
    grid: { borderColor: '#f1f1f1' },
    xaxis: {
        categories: props.chartData.map(d => d.date),
        labels: { style: { colors: '#9ca3af' } }
    },
    yaxis: { labels: { style: { colors: '#9ca3af' } } },
    colors: ['#0ea5e9'],
    theme: { mode: 'dark' }
};

const series = [{
    name: 'Revenue',
    data: props.chartData.map(d => d.revenue)
}];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            Dashboard Overview
        </template>

        <template #stats>
            <!-- 1. KPI Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard 
                    title="Today's Orders" 
                    :value="stats.today_orders"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    </template>
                </StatCard>
                <StatCard 
                    title="Monthly Revenue" 
                    :value="formatCurrency(stats.monthly_revenue)"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </template>
                </StatCard>
                <StatCard 
                    title="Inventory Alerts" 
                    :value="stats.low_stock_alerts"
                    :alert="stats.low_stock_alerts > 0"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </template>
                </StatCard>
                <StatCard 
                    title="Pending Approvals" 
                    :value="stats.pending_approvals"
                    :alert="stats.pending_approvals > 5"
                >
                    <template #icon>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </template>
                </StatCard>
            </div>
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
                <!-- 2. Operational Timeline / Recent Orders -->
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Recent Orders</h3>
                            </div>
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                                <button class="bg-operra-500 text-white active:bg-operra-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 shadow hover:shadow-md" type="button">
                                    See all
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Order #</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Customer</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Amount</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="order in recentOrders" :key="order.id">
                                    <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left font-bold text-gray-700 dark:text-gray-200">{{ order.order_number }}</th>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">{{ order.customer_name }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-700 dark:text-gray-200">{{ formatCurrency(order.total_amount) }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        <span :class="[
                                            'px-2 py-1 rounded-full text-xs font-semibold',
                                            order.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                        ]">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Performance Analytics Chart -->
                <div class="relative flex flex-col min-w-0 break-words bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 bg-transparent">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h6 class="uppercase text-gray-100 mb-1 text-xs font-semibold">Overview</h6>
                                <h2 class="text-white text-xl font-bold">Revenue Trend (Last 7 Days)</h2>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 flex-auto">
                        <VueApexCharts height="350" :options="chartOptions" :series="series" />
                    </div>
                </div>
            </div>

            <div class="w-full xl:w-4/12 px-4">
                <!-- 4. Action Center -->
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Action Center</h3>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <!-- Pending Approvals -->
                        <div class="mb-8">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Pending Approvals</h4>
                            <div v-if="actionCenter.approvals.length === 0" class="text-sm text-gray-500 italic">No pending approvals</div>
                            <div v-for="approval in actionCenter.approvals" :key="approval.id" 
                                class="p-4 mb-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-bold text-orange-900 dark:text-orange-200 capitalize">{{ approval.request_type }}</p>
                                        <p class="text-xs text-orange-700 dark:text-orange-400 mt-1 italic">Requested by {{ approval.requester.name }}</p>
                                    </div>
                                    <button class="text-xs bg-white dark:bg-gray-800 px-2 py-1 rounded border border-orange-300 dark:border-orange-700 shadow-sm hover:bg-orange-100 transition-colors">Review</button>
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Warnings -->
                        <div class="mb-8">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Critical Inventory</h4>
                            <div v-if="actionCenter.inventory.length === 0" class="text-sm text-gray-500 italic">No critical stock warnings</div>
                            <div v-for="item in actionCenter.inventory" :key="item.id" 
                                class="p-4 mb-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg shadow-sm">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold text-red-900 dark:text-red-200">{{ item.item_name }}</p>
                                        <p class="text-xs text-red-700 dark:text-red-400 italic">Only {{ item.stock_quantity }} remaining</p>
                                    </div>
                                    <button class="text-xs bg-red-600 text-white px-3 py-1.5 rounded-md hover:bg-red-700 transition-colors font-bold uppercase shadow-md">Restock</button>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Recent Activity</h4>
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li v-for="(activity, idx) in recentActivities" :key="activity.id">
                                        <div class="relative pb-8">
                                            <span v-if="idx !== recentActivities.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <span class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center ring-8 ring-white dark:ring-gray-800 shadow-sm">
                                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                </span>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                                            <span class="font-bold text-gray-900 dark:text-white not-italic">{{ activity.user.name }}</span> {{ activity.description }}
                                                        </p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-xs text-gray-400">
                                                        <time :datetime="activity.created_at">{{ new Date(activity.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
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

const page = usePage();
const enabledModules = computed(() => page.props.auth.user.company?.enabled_modules ?? []);

// Ambil portal dari state Inertia agar reactive
const selectedModule = computed(() => {
    const portal = page.url.split('portal=')[1]?.split('&')[0];
    
    if (portal && enabledModules.value.includes(portal)) {
        return portal;
    }
    
    return enabledModules.value.length === 1 ? enabledModules.value[0] : null;
});

const showLauncher = computed(() => {
    return enabledModules.value.length > 1 && !selectedModule.value;
});

const selectPortal = (id) => {
    router.get(route('dashboard'), { portal: id }, { preserveState: true });
};

const exitPortal = () => {
    router.get(route('dashboard'), {}, { preserveState: true });
};

const modules = [
    {
        id: 'wa_blast',
        name: 'WhatsApp CRM & Blast',
        description: 'Kelola komunikasi pelanggan, blast pesan, dan shared inbox tim.',
        icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
        color: 'bg-green-500',
        link: route('crm.wa.inbox')
    },
    {
        id: 'sales_crm',
        name: 'Sales & Pipeline CRM',
        description: 'Manajemen lead, prospek, deal tracking, dan manajemen sales order.',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        color: 'bg-blue-500',
        link: route('crm.sales.customers.index')
    },
    {
        id: 'marketing_crm',
        name: 'Marketing Automation',
        description: 'Email marketing, WA blast scheduler, lead scoring, dan campaign management.',
        icon: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
        color: 'bg-purple-500',
        link: '#'
    },
    {
        id: 'customer_service',
        name: 'Customer Support CRM',
        description: 'Sistem ticketing, manajemen SLA, knowledge base, dan retensi pelanggan.',
        icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
        color: 'bg-orange-500',
        link: '#'
    },
    {
        id: 'analytical_crm',
        name: 'Business Intelligence',
        description: 'Laporan penjualan mendalam, perilaku pelanggan (behavior), dan analisis LTV.',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        color: 'bg-pink-500',
        link: '#'
    }
];

const filteredModules = computed(() => {
    return modules.filter(m => enabledModules.value.includes(m.id));
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
            <span v-if="showLauncher">Pilih Aplikasi CRM</span>
            <span v-else>Dashboard {{ selectedModule === 'wa_blast' ? 'WhatsApp' : 'Sales' }} CRM</span>
        </template>

        <template #stats>
            <!-- Grid Stats hanya muncul jika NOT launcher mode -->
            <div v-if="!showLauncher" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-if="selectedModule === 'sales_crm' || !selectedModule" class="contents">
                    <StatCard title="Total Leads" :value="stats.total_leads">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </template>
                    </StatCard>
                    <StatCard title="New Leads Today" :value="stats.new_leads_today">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        </template>
                    </StatCard>
                </div>
                
                <div v-if="selectedModule === 'wa_blast' || !selectedModule" class="contents">
                    <StatCard title="Active Chats" :value="stats.active_chats" :alert="stats.active_chats > 0">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        </template>
                    </StatCard>
                    <StatCard title="Messages Sent/Received" :value="stats.messages_today">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </template>
                    </StatCard>
                </div>
            </div>
        </template>

        <!-- Launcher Mode (Kotak-kotak Modul) -->
        <div v-if="showLauncher" class="py-12">
            <div class="max-w-4xl mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-black text-gray-800 dark:text-white">Selamat Datang di Operra</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Silahkan pilih portal CRM yang ingin Anda akses hari ini.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div 
                        v-for="module in filteredModules" 
                        :key="module.id"
                        @click="selectPortal(module.id)"
                        class="group cursor-pointer bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl hover:shadow-2xl border-2 border-transparent hover:border-operra-500 transition-all duration-300 transform hover:-translate-y-2"
                    >
                        <div :class="module.color" class="h-16 w-16 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="module.icon"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 group-hover:text-operra-500 transition-colors">{{ module.name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-6">
                            {{ module.description }}
                        </p>
                        <div class="flex items-center text-operra-500 font-bold text-sm group-hover:gap-2 transition-all">
                            Masuk ke Portal 
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Dashboard Mode -->
        <div v-else>
            <div class="flex flex-wrap mt-4 -mx-4">
                <!-- Row content (Charts, Tables) - Show based on selected module or simplified for both -->
                <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
                    <!-- Chart logic (shared or specific) -->
                    <div v-if="selectedModule === 'sales_crm'" class="relative flex flex-col min-w-0 break-words bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                        <div class="rounded-t mb-0 px-6 py-4 bg-transparent">
                            <h6 class="uppercase text-gray-400 mb-1 text-xs font-bold tracking-widest">Growth Analytics</h6>
                            <h2 class="text-white text-xl font-bold">New Leads Trend</h2>
                        </div>
                        <div class="p-4 flex-auto">
                            <VueApexCharts height="350" :options="chartOptions" :series="series" />
                        </div>
                    </div>

                    <!-- Recent Leads (Sales Module) -->
                    <div v-if="selectedModule === 'sales_crm'" class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                        <div class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">Recent Leads</h3>
                            <Link :href="route('crm.sales.customers.index')" class="bg-operra-500 text-white text-xs font-bold uppercase px-4 py-2 rounded-lg">Manage</Link>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table class="items-center w-full bg-transparent border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Name</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="lead in recentLeads" :key="lead.id">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200">{{ lead.name }}</td>
                                        <td class="px-6 py-4 text-xs">{{ lead.status }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-4/12 px-4">
                     <!-- WA Accounts (WA Module) -->
                     <div v-if="selectedModule === 'wa_blast'" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">WA Accounts</h3>
                        <div v-for="account in waAccounts" :key="account.id" class="mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="font-bold text-sm">{{ account.name }}</p>
                            <p class="text-xs text-gray-500">{{ account.status }}</p>
                        </div>
                     </div>

                     <!-- Recent Conversations (WA Module) -->
                     <div v-if="selectedModule === 'wa_blast'" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">Recent Chats</h3>
                        <div v-for="chat in recentChats" :key="chat.id" class="flex items-center gap-3 mb-4">
                            <div class="h-8 w-8 rounded-full bg-operra-500 flex items-center justify-center text-white font-bold text-xs">
                                {{ chat.customer?.name?.charAt(0) || '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold truncate">{{ chat.customer?.name || 'Unknown' }}</p>
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            <!-- Tombol Switch App (Mobile/Desktop floating) -->
            <button 
                v-if="enabledModules.length > 1"
                @click="exitPortal"
                class="fixed bottom-6 right-6 bg-operra-600 text-white p-4 rounded-full shadow-2xl flex items-center gap-2 hover:bg-operra-700 transition-all z-[60]"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <span class="hidden md:inline font-bold text-sm">Ganti Aplikasi</span>
            </button>
        </div>
    </AuthenticatedLayout>
</template>


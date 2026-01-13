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
    if (page.url.includes('/crm-support/')) return 'customer_service';
    
    const portal = page.url.split('portal=')[1]?.split('&')[0];
    
    if (portal && enabledModules.value.includes(portal)) {
        return portal;
    }
    
    // Explicitly check for marketing_crm from URL if not in split (Inertia might have simplified it)
    if (page.url.includes('portal=marketing_crm')) return 'marketing_crm';
    if (page.url.includes('portal=customer_service')) return 'customer_service';
    
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
        link: route('crm.marketing.dashboard')
    },
    {
        id: 'customer_service',
        name: 'Customer Support CRM',
        description: 'Sistem ticketing, manajemen SLA, knowledge base, dan retensi pelanggan.',
        icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
        color: 'bg-orange-500',
        link: route('crm.support.dashboard')
    }
    // ,
    // {
    //     id: 'analytical_crm',
    //     name: 'Business Intelligence',
    //     description: 'Laporan penjualan mendalam, perilaku pelanggan (behavior), dan analisis LTV.',
    //     icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    //     color: 'bg-pink-500',
    //     link: '#'
    // }
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
            <span v-else>DASHBOARD {{ 
                selectedModule === 'wa_blast' ? 'CRM & WHATSAPP BLAST' : 
                selectedModule === 'marketing_crm' ? 'MARKETING AUTOMATION CRM' : 
                selectedModule === 'customer_service' ? 'CUSTOMER SUPPORT CRM' : 
                'SALES & PIPELINE CRM' 
            }} ({{ userRole?.toUpperCase() }})</span>
        </template>

        <template #stats>
            <!-- Grid Stats hanya muncul jika NOT launcher mode -->
            <div v-if="!showLauncher" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Stat Cards for WA & Sales -->
                <template v-if="selectedModule !== 'marketing_crm' && selectedModule !== 'customer_service'">
                    <StatCard title="Total Leads" :value="stats.total_leads">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Active Chats" :value="stats.active_chats" :alert="stats.active_chats > 0">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="New Leads Today" :value="stats.new_leads_today">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Messages Sent/Received" :value="stats.messages_today">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </template>
                    </StatCard>
                </template>

                <!-- Stat Cards for Marketing CRM -->
                <template v-if="selectedModule === 'marketing_crm'">
                    <StatCard title="Active Campaigns" :value="stats.active_campaigns">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Avg Lead Score" :value="stats.avg_lead_score">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Total Leads" :value="stats.total_leads">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Automations" :value="stats.active_automations">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </template>
                    </StatCard>
                </template>

                <!-- Stat Cards for Customer Service CRM -->
                <template v-if="selectedModule === 'customer_service'">
                    <StatCard title="Open Tickets" :value="stats.open_tickets" :alert="stats.open_tickets > 0">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Urgent Tickets" :value="stats.urgent_tickets" :alert="stats.urgent_tickets > 0">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Resolved Today" :value="stats.resolved_today">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                    </StatCard>

                    <StatCard title="Chat History" :value="stats.messages_today">
                        <template #icon>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m9-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                    </StatCard>
                </template>
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
                    <!-- Chart logic (shared) -->
                    <div v-if="selectedModule === 'sales_crm' || selectedModule === 'wa_blast' || selectedModule === 'marketing_crm'" class="relative flex flex-col min-w-0 break-words bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                        <div class="rounded-t mb-0 px-6 py-4 bg-transparent">
                            <h6 class="uppercase text-gray-400 mb-1 text-xs font-bold tracking-widest">
                                {{ selectedModule === 'marketing_crm' ? 'Campaign Analytics' : 'Growth Analytics' }}
                            </h6>
                            <h2 class="text-white text-xl font-bold">
                                {{ selectedModule === 'marketing_crm' ? 'Campaign Performance' : 'New Leads Trend' }}
                            </h2>
                        </div>
                        <div class="p-4 flex-auto">
                            <VueApexCharts height="350" :options="chartOptions" :series="series" />
                        </div>
                    </div>

                    <!-- Recent Leads (Shared for Sales/WA) or Campaigns (for Marketing) or Tickets (for Support) -->
                    <div v-if="selectedModule === 'sales_crm' || selectedModule === 'wa_blast'" class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                        <div class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">Recent Leads</h3>
                            <Link v-if="selectedModule === 'sales_crm'" :href="route('crm.sales.customers.index')" class="bg-operra-500 text-white text-xs font-bold uppercase px-4 py-2 rounded-lg">Manage</Link>
                            <Link v-else :href="route('crm.wa.inbox')" class="bg-operra-500 text-white text-xs font-bold uppercase px-4 py-2 rounded-lg">Go to Inbox</Link>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table class="items-center w-full bg-transparent border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Name</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left text-nowrap">Phone</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="lead in recentLeads" :key="lead.id" class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200">{{ lead.name }}</td>
                                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">{{ lead.phone }}</td>
                                        <td class="px-6 py-4 text-xs">
                                            <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 font-bold uppercase tracking-wider text-[10px]">
                                                {{ lead.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Support Tickets List -->
                    <div v-if="selectedModule === 'customer_service'" class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                        <div class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">Recent Support Tickets</h3>
                            <Link :href="route('crm.support.tickets.index')" class="bg-operra-500 text-white text-xs font-bold uppercase px-4 py-2 rounded-lg">All Tickets</Link>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table class="items-center w-full bg-transparent border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Ticket</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Subject</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Status</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Priority</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tkt in (stats.recent_tickets || [])" :key="tkt.id" class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200">{{ tkt.ticket_number }}</td>
                                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">
                                            <div class="font-bold">{{ tkt.subject }}</div>
                                            <div class="text-[10px]">{{ tkt.customer?.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span :class="[
                                                'px-2 py-1 rounded-full font-bold uppercase tracking-wider text-[10px]',
                                                tkt.status === 'OPEN' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
                                            ]">
                                                {{ tkt.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-red-500">{{ tkt.priority }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Marketing Campaigns List -->
                    <div v-if="selectedModule === 'marketing_crm'" class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                        <div class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">Recent Campaigns</h3>
                            <Link :href="route('crm.marketing.campaigns.index')" class="bg-operra-500 text-white text-xs font-bold uppercase px-4 py-2 rounded-lg">All Campaigns</Link>
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table class="items-center w-full bg-transparent border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Campaign Name</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Type</th>
                                        <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="camp in (stats.recent_campaigns || [])" :key="camp.id" class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200">{{ camp.name }}</td>
                                        <td class="px-6 py-4 text-xs">{{ camp.type }}</td>
                                        <td class="px-6 py-4 text-xs">
                                            <span class="px-2 py-1 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 font-bold uppercase tracking-wider text-[10px]">
                                                {{ camp.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!(stats.recent_campaigns?.length)">
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 text-sm italic">No campaigns found. Start nurturing your leads!</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-4/12 px-4">
                     <!-- WA Accounts (WA Module) -->
                     <div v-if="selectedModule === 'wa_blast'" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">WhatsApp Accounts</h3>
                        <div v-for="account in waAccounts" :key="account.id" class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600">
                            <div class="flex justify-between items-start mb-1">
                                <p class="font-bold text-sm text-gray-800 dark:text-white">{{ account.name }}</p>
                                <span class="text-[10px] font-black uppercase text-green-500">{{ account.status }}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-2">{{ account.phone_number }}</p>
                            <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                <span class="text-[10px] font-bold uppercase">{{ account.agents_count || 0 }} Agents Connected</span>
                            </div>
                        </div>
                     </div>

                     <!-- Recent Conversations (WA Module) -->
                     <div v-if="selectedModule === 'wa_blast'" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">Recent Chats</h3>
                        <div v-for="chat in recentChats" :key="chat.id" class="flex items-center gap-3 mb-4 last:mb-0">
                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-black text-sm shadow-md">
                                {{ chat.customer?.name?.charAt(0) || '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ chat.customer?.name || 'Unknown' }}</p>
                                    <span class="text-[9px] font-bold text-green-500 uppercase">{{ chat.status }}</span>
                                </div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400">By: {{ chat.assigned_user?.name || 'Unassigned' }}</p>
                            </div>
                        </div>
                     </div>

                     <!-- Top Scored Leads (Marketing Module) -->
                     <div v-if="selectedModule === 'marketing_crm'" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">Top Leads</h3>
                            <Link :href="route('crm.marketing.lead-scoring.index')" class="text-xs font-bold text-operra-500">View All</Link>
                        </div>
                        <div v-for="lead in (stats.top_leads || [])" :key="lead.id" class="flex items-center gap-3 mb-4 last:mb-0">
                            <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-black text-sm shadow-md">
                                {{ lead.name.charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <p class="text-xs font-black text-gray-800 dark:text-white truncate">{{ lead.name }}</p>
                                    <span class="text-[10px] font-black text-purple-500">Score: {{ lead.lead_score }}</span>
                                </div>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ lead.phone }}</p>
                            </div>
                        </div>
                        <div v-if="!(stats.top_leads?.length)" class="text-center py-6 text-gray-400 text-xs italic">
                            No lead data yet.
                        </div>
                     </div>

                     <!-- Marketing Channels Status (Marketing Module) -->
                     <div v-if="selectedModule === 'marketing_crm'" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200">Active Channels</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.438-9.89 9.886-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.743-.975zm11.367-7.405c-.31-.155-1.837-.906-2.12-.1.01-.285-.155-.343-.233-.444-.078-.101-.285-.427-.31-.466-.025-.039-.041-.065-.062-.101-.021-.036-.01-.065.01-.101.021-.036.186-.218.279-.327.093-.108.124-.181.186-.301.062-.12.031-.226-.015-.301-.047-.075-.424-1.02-.581-1.326-.153-.297-.308-.257-.424-.258l-.362-.004c-.124 0-.327.047-.497.233-.17.187-.651.637-.651 1.55s.67 1.797.763 1.921c.093.124 1.321 2.016 3.199 2.826.447.193.795.308 1.068.394.448.142.856.122 1.178.058.359-.071 1.101-.45 1.256-.884.155-.434.155-.806.108-.884-.047-.078-.17-.124-.479-.28z"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">WhatsApp</span>
                                </div>
                                <span class="text-[10px] font-black text-green-500">READY</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">Email</span>
                                </div>
                                <span class="text-[10px] font-black text-blue-500">ACTIVE</span>
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


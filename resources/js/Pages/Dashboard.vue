<script setup>
import { ref, computed, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import StatCard from "@/Components/StatCard.vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    stats: Object,
    recentLeads: Array,
    recentChats: Array,
    chartData: Array,
    waAccounts: Array,
    adminStats: Object,
    systemLeads: Array,
    systemCompanies: Array,
    userRole: String,
});

const page = usePage();
const enabledModules = computed(
    () => page.props.auth.user.company?.enabled_modules ?? [],
);
const pricingPlan = computed(() => page.props.auth.user?.company?.pricing_plan);
const subscriptionStatus = computed(
    () => page.props.auth.user?.company?.subscription_status,
);

// Ambil portal dari state Inertia agar reactive
const selectedModule = computed(() => {
    const portal = page.url.split("portal=")[1]?.split("&")[0];

    if (portal && enabledModules.value.includes(portal)) {
        return portal;
    }

    // Explicitly check for marketing_crm from URL if not in split (Inertia might have simplified it)
    if (page.url.includes("portal=marketing_crm")) return "marketing_crm";
    if (page.url.includes("portal=customer_service")) return "customer_service";

    return enabledModules.value.length === 1 ? enabledModules.value[0] : null;
});

const showLauncher = computed(() => {
    return enabledModules.value.length > 1 && !selectedModule.value;
});

const selectPortal = (id) => {
    router.get(route("dashboard"), { portal: id }, { preserveState: true });
};

const exitPortal = () => {
    router.get(route("dashboard"), {}, { preserveState: true });
};

const modules = [
    {
        id: "wa_blast",
        name: "WhatsApp CRM & Blast",
        description:
            "Kelola komunikasi pelanggan, blast pesan, dan shared inbox tim.",
        icon: "M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z",
        color: "bg-emerald-500",
        link: route("crm.wa.inbox"),
    },
    {
        id: "sales_crm",
        name: "Sales & Pipeline CRM",
        description:
            "Manajemen lead, prospek, deal tracking, dan manajemen sales order.",
        icon: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
        color: "bg-slate-800",
        link: route("crm.sales.customers.index"),
    },
    {
        id: "marketing_crm",
        name: "Marketing Automation",
        description:
            "Email marketing, WA blast scheduler, lead scoring, dan campaign management.",
        icon: "M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z",
        color: "bg-purple-500",
        link: route("crm.marketing.dashboard"),
    },
    {
        id: "customer_service",
        name: "Customer Support CRM",
        description:
            "Sistem ticketing, manajemen SLA, knowledge base, dan retensi pelanggan.",
        icon: "M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z",
        color: "bg-orange-500",
        link: route("crm.support.dashboard"),
    },
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
    return modules.filter((m) => enabledModules.value.includes(m.id));
});

const chartOptions = {
    chart: {
        type: "area",
        toolbar: { show: false },
        animations: { enabled: true },
        background: "transparent",
        sparkline: { enabled: false },
    },
    stroke: { curve: "smooth", width: 4, lineCap: "round" },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0,
            stops: [0, 95],
        },
    },
    markers: {
        size: 5,
        colors: ["#10b981"],
        strokeColors: "#fff",
        strokeWidth: 3,
        hover: { size: 7 },
    },
    dataLabels: { enabled: false },
    grid: {
        borderColor: "rgba(241, 245, 249, 0.1)",
        strokeDashArray: 4,
        padding: { top: 10, right: 10, bottom: 0, left: 10 },
    },
    xaxis: {
        categories: props.chartData.map((d) => d.date),
        labels: {
            style: {
                colors: "#94a3b8",
                fontSize: "10px",
                fontWeight: 600,
            },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: {
                colors: "#94a3b8",
                fontSize: "10px",
                fontWeight: 600,
            },
        },
    },
    colors: ["#10b981"], // Premium Emerald Green
    theme: { mode: "dark" },
};

const series = computed(() => {
    if (
        selectedModule.value === "sales_crm" &&
        props.stats.revenue_weekly_trend
    ) {
        return [
            {
                name: "Weekly Revenue",
                data: props.stats.revenue_weekly_trend.map((d) => d.revenue),
            },
        ];
    }
    return [
        {
            name: "Omnichannel Engagement",
            data: props.chartData.map((d) => d.count),
        },
    ];
});

const chartOptionsComputed = computed(() => {
    const opts = JSON.parse(JSON.stringify(chartOptions));
    if (
        selectedModule.value === "sales_crm" &&
        props.stats.revenue_weekly_trend
    ) {
        opts.xaxis.categories = props.stats.revenue_weekly_trend.map(
            (d) => d.date,
        );
        opts.yaxis.labels.formatter = (val) =>
            "Rp " + val.toLocaleString("id-ID");
    }
    return opts;
});
</script>

<template>
    <Head title="Dashboard CRM" />

    <AuthenticatedLayout>
        <template #header>
            <span v-if="showLauncher">Pilih Aplikasi CRM</span>
            <span v-else
                >DASHBOARD
                {{
                    selectedModule === "wa_blast"
                        ? "CRM & WHATSAPP BLAST"
                        : selectedModule === "marketing_crm"
                          ? "MARKETING AUTOMATION CRM"
                          : selectedModule === "customer_service"
                            ? "CUSTOMER SUPPORT CRM"
                            : "SALES & PIPELINE CRM"
                }}
                ({{ userRole?.toUpperCase() }})</span
            >
        </template>

        <template #stats>
            <!-- Trial Banner -->
            <div
                v-if="subscriptionStatus?.is_trial"
                class="bg-gradient-to-r from-emerald-600 to-emerald-800 text-white px-6 py-4 mb-8 rounded-[24px] shadow-2xl shadow-emerald-500/20 flex items-center justify-between border border-emerald-400/30"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md"
                    >
                        <svg
                            class="w-6 h-6 text-white animate-pulse"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 2m9-2a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <div
                            class="font-black uppercase tracking-[0.2em] text-[10px] text-emerald-100 mb-1"
                        >
                            Masa Percobaan (Trial)
                        </div>
                        <div class="font-bold text-lg leading-none">
                            Sisa waktu:
                            <span class="text-white"
                                >{{ subscriptionStatus.days_left }} hari
                                lagi</span
                            >.
                        </div>
                    </div>
                </div>
                <Link
                    :href="route('billing.index')"
                    class="bg-white text-emerald-700 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                >
                    Upgrade Sekarang
                </Link>
            </div>

            <!-- System Admin Stats (Hanya untuk Super Admin) -->
            <div
                v-if="
                    userRole === 'super-admin' &&
                    page.props.auth.user.company?.is_system_owner &&
                    !selectedModule
                "
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8"
            >
                <StatCard
                    title="Permintaan Custom"
                    :value="adminStats.total_leads_request"
                    :alert="adminStats.new_leads_request > 0"
                >
                    <template #icon>
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                            />
                        </svg>
                    </template>
                </StatCard>

                <StatCard
                    title="Total Perusahaan"
                    :value="adminStats.total_companies"
                >
                    <template #icon>
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            />
                        </svg>
                    </template>
                </StatCard>

                <StatCard
                    title="Langganan Aktif"
                    :value="adminStats.active_subscriptions"
                >
                    <template #icon>
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                            />
                        </svg>
                    </template>
                </StatCard>

                <StatCard
                    title="Total Agents"
                    :value="
                        waAccounts.reduce(
                            (acc, curr) => acc + (curr.agents_count || 0),
                            0,
                        )
                    "
                >
                    <template #icon>
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                    </template>
                </StatCard>
            </div>

            <!-- Grid Stats hanya muncul jika NOT launcher mode -->
            <div
                v-if="!showLauncher"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
            >
                <!-- Stat Cards for Omnichannel Overview -->
                <!-- Stat Cards for Sales CRM & Omnichannel Overview -->
                <template v-if="selectedModule === 'sales_crm'">
                    <StatCard
                        title="Total Sales (Bulan Ini)"
                        :value="
                            'Rp ' +
                            (stats.revenue_this_month?.toLocaleString(
                                'id-ID',
                            ) || 0)
                        "
                        trend="+Rp 2.5M"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard
                        title="Total Pelanggan"
                        :value="stats.total_customers"
                        :trend="'+' + stats.customer_growth + ' baru'"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard
                        title="Total Pesanan"
                        :value="stats.total_orders"
                        :trend="stats.new_orders_today + ' hari ini'"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard
                        title="Low Stock Alert"
                        :value="stats.low_stock_count + ' Produk'"
                        trend="Butuh Restock"
                        trendColor="text-red-500"
                        :alert="stats.low_stock_count > 0"
                        class="cursor-pointer hover:border-emerald-500 transition-all"
                        @click="router.get(route('crm.sales.products.index'))"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                </template>

                <template
                    v-if="
                        selectedModule === 'wa_blast' ||
                        (!selectedModule && !showLauncher)
                    "
                >
                    <StatCard
                        title="Engagement Rate"
                        value="68.2%"
                        trend="+2.4%"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard
                        title="Email Fallback"
                        value="1,240"
                        trend="+15.0%"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard
                        title="Active Chats"
                        :value="stats.active_chats || 0"
                        trend="Live Now"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                    <StatCard
                        title="System Status"
                        value="Optimal"
                        trend="All Green"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                </template>

                <!-- Stat Cards for Marketing CRM -->
                <template v-if="selectedModule === 'marketing_crm'">
                    <StatCard
                        title="Active Campaigns"
                        :value="stats.active_campaigns"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard
                        title="Avg Lead Score"
                        :value="stats.avg_lead_score"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard title="Total Leads" :value="stats.total_leads">
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard
                        title="Automations"
                        :value="stats.active_automations"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                </template>

                <!-- Stat Cards for Customer Service CRM -->
                <template v-if="selectedModule === 'customer_service'">
                    <StatCard
                        title="Open Tickets"
                        :value="stats.open_tickets"
                        :alert="stats.open_tickets > 0"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z"
                                />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard
                        title="Urgent Tickets"
                        :value="stats.urgent_tickets"
                        :alert="stats.urgent_tickets > 0"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard
                        title="Resolved Today"
                        :value="stats.resolved_today"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </template>
                    </StatCard>

                    <StatCard
                        title="Chat History"
                        :value="stats.messages_today"
                    >
                        <template #icon>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 2m9-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </template>
                    </StatCard>
                </template>
            </div>
        </template>

        <!-- Launcher Mode (Kotak-kotak Modul) -->
        <div v-if="showLauncher" class="py-8 md:py-12">
            <div class="max-w-4xl mx-auto px-4">
                <div class="text-center mb-10 md:mb-12">
                    <h2
                        class="text-2xl md:text-3xl font-black text-white md:text-gray-800 md:dark:text-white uppercase tracking-tighter"
                    >
                        Selamat Datang di Operra
                    </h2>
                    <p
                        class="text-white/80 md:text-gray-500 md:dark:text-gray-400 mt-2 text-sm"
                    >
                        Silahkan pilih portal CRM yang ingin Anda akses hari
                        ini.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10">
                    <div
                        v-for="module in filteredModules"
                        :key="module.id"
                        @click="
                            module.id === 'wa_blast'
                                ? null
                                : selectPortal(module.id)
                        "
                        :class="[
                            'group relative cursor-pointer bg-white dark:bg-slate-800 rounded-[48px] p-10 md:p-12 shadow-2xl border border-gray-100 dark:border-slate-700 transition-all duration-700 overflow-hidden',
                            module.id === 'wa_blast'
                                ? 'opacity-50 grayscale cursor-not-allowed'
                                : 'hover:shadow-[0_40px_80px_-20px_rgba(16,185,129,0.15)] hover:border-emerald-500/50 hover:-translate-y-4',
                        ]"
                    >
                        <!-- Background Glow Effect -->
                        <div
                            class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-[80px] group-hover:bg-emerald-500/10 transition-all duration-700"
                        ></div>

                        <div
                            :class="module.color"
                            class="h-20 w-20 md:h-24 md:w-24 rounded-[32px] flex items-center justify-center text-white mb-10 shadow-2xl shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700"
                        >
                            <svg
                                class="w-10 h-10 md:w-12 md:h-12"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    :d="module.icon"
                                ></path>
                            </svg>
                        </div>
                        <h3
                            class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-4 transition-colors uppercase tracking-tight"
                            :class="
                                module.id !== 'wa_blast'
                                    ? 'group-hover:text-emerald-600'
                                    : ''
                            "
                        >
                            {{ module.name }}
                        </h3>
                        <p
                            class="text-slate-500 dark:text-slate-400 text-base md:text-lg leading-relaxed mb-10 font-medium"
                        >
                            {{
                                module.id === "wa_blast"
                                    ? "Fitur ini sedang dalam maintenance sistem."
                                    : module.description
                            }}
                        </p>
                        <div
                            v-if="module.id !== 'wa_blast'"
                            class="flex items-center text-emerald-600 font-black text-sm md:text-base group-hover:gap-5 transition-all uppercase tracking-widest"
                        >
                            Masuk Portal
                            <svg
                                class="w-6 h-6 ml-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M14 5l7 7-7 7M3 12h18"
                                ></path>
                            </svg>
                        </div>
                        <div
                            v-else
                            class="flex items-center text-slate-400 font-black text-sm uppercase tracking-widest"
                        >
                            Segera Hadir
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Dashboard Mode -->
        <div v-else>
            <!-- System Admin Overview (Hanya untuk Super Admin di Dashboard Utama) -->
            <div
                v-if="
                    userRole === 'super-admin' &&
                    page.props.auth.user.company?.is_system_owner &&
                    !selectedModule
                "
                class="flex flex-wrap mt-4 -mx-4 mb-8"
            >
                <div class="w-full xl:w-7/12 px-4 mb-6 xl:mb-0">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden"
                    >
                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50"
                        >
                            <h3
                                class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tighter"
                            >
                                Permintaan CRM Kustom Terbaru
                            </h3>
                            <Link
                                :href="route('admin.leads.index')"
                                class="text-xs font-black text-emerald-500 uppercase tracking-widest hover:underline"
                                >Lihat Semua</Link
                            >
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-700/50"
                                    >
                                        <th class="px-6 py-4">
                                            Nama / Perusahaan
                                        </th>
                                        <th class="px-6 py-4 text-center">
                                            Tipe
                                        </th>
                                        <th class="px-6 py-4 text-right">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="lead in systemLeads"
                                        :key="lead.id"
                                        class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/20 transition-all"
                                    >
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm font-bold text-gray-800 dark:text-white"
                                            >
                                                {{ lead.name }}
                                            </div>
                                            <div
                                                class="text-[10px] text-gray-500 font-medium"
                                            >
                                                {{ lead.company_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-[9px] font-black text-gray-500 uppercase"
                                                >{{ lead.business_type }}</span
                                            >
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span
                                                :class="{
                                                    'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400':
                                                        lead.status === 'new',
                                                    'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400':
                                                        lead.status ===
                                                        'contacted',
                                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400':
                                                        lead.status ===
                                                        'closed',
                                                }"
                                                class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter"
                                            >
                                                {{ lead.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="systemLeads.length === 0">
                                        <td
                                            colspan="3"
                                            class="px-6 py-10 text-center text-gray-400 text-xs italic"
                                        >
                                            Belum ada permintaan kustom masuk.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-5/12 px-4">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden"
                    >
                        <div
                            class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50"
                        >
                            <h3
                                class="text-lg font-black text-gray-800 dark:text-white uppercase tracking-tighter"
                            >
                                Registrasi Perusahaan Baru
                            </h3>
                            <Link
                                :href="route('admin.system.companies.index')"
                                class="text-xs font-black text-emerald-500 uppercase tracking-widest hover:underline"
                                >Lihat Semua</Link
                            >
                        </div>
                        <div class="p-4 space-y-4">
                            <div
                                v-for="company in systemCompanies"
                                :key="company.id"
                                class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-transparent hover:border-emerald-500 transition-all"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black uppercase text-sm shadow-lg shadow-emerald-200/50"
                                    >
                                        {{ company.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-bold text-gray-800 dark:text-white truncate max-w-[150px]"
                                        >
                                            {{ company.name }}
                                        </div>
                                        <div
                                            class="text-[9px] text-gray-500 font-medium"
                                        >
                                            Terdaftar:
                                            {{
                                                new Date(
                                                    company.created_at,
                                                ).toLocaleDateString()
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span
                                        v-if="
                                            company.subscription_ends_at &&
                                            new Date(
                                                company.subscription_ends_at,
                                            ) > new Date()
                                        "
                                        class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-[9px] font-black uppercase tracking-tighter"
                                        >Aktif</span
                                    >
                                    <span
                                        v-else
                                        class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 text-[9px] font-black uppercase tracking-tighter"
                                        >Trial/Expired</span
                                    >
                                </div>
                            </div>
                            <div
                                v-if="systemCompanies.length === 0"
                                class="py-10 text-center text-gray-400 text-xs italic"
                            >
                                Belum ada perusahaan terdaftar.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap mt-4 -mx-4">
                <!-- Row content (Charts, Tables) - Show based on selected module or simplified for both -->
                <div class="w-full xl:w-8/12 mb-12 xl:mb-0 px-4">
                    <!-- Recent Leads (Shared for Sales/WA) -->
                    <div
                        v-if="selectedModule === 'wa_blast'"
                        class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden"
                    >
                        <div
                            class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center"
                        >
                            <h3
                                class="font-bold text-lg text-gray-700 dark:text-gray-200"
                            >
                                Recent Leads
                            </h3>
                            <Link
                                :href="route('crm.wa.inbox')"
                                class="bg-emerald-500 text-white text-[10px] font-black uppercase px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-200"
                                >Go to Inbox</Link
                            >
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Name
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left text-nowrap"
                                        >
                                            Phone
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="lead in recentLeads"
                                        :key="lead.id"
                                        class="border-t border-gray-100 dark:border-gray-700"
                                    >
                                        <td
                                            class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200"
                                        >
                                            {{ lead.name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400"
                                        >
                                            {{ lead.phone }}
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span
                                                class="px-2 py-1 rounded-full bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-300 font-bold uppercase tracking-wider text-[10px]"
                                            >
                                                {{ lead.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="recentLeads.length === 0">
                                        <td
                                            colspan="3"
                                            class="px-6 py-10 text-center text-gray-400 text-xs italic"
                                        >
                                            Belum ada data leads.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Orders (Sales CRM Only) -->
                    <div
                        v-if="selectedModule === 'sales_crm'"
                        class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden"
                    >
                        <div
                            class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center"
                        >
                            <h3
                                class="font-bold text-lg text-gray-700 dark:text-gray-200"
                            >
                                Recent Sales Orders
                            </h3>
                            <Link
                                :href="route('crm.sales.orders.index')"
                                class="bg-emerald-500 text-white text-[10px] font-black uppercase px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-200"
                                >Manage Orders</Link
                            >
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Order #
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left text-nowrap"
                                        >
                                            Customer
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Amount
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="order in stats.recent_orders"
                                        :key="order.id"
                                        class="border-t border-gray-100 dark:border-gray-700"
                                    >
                                        <td
                                            class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200"
                                        >
                                            {{ order.order_number }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400"
                                        >
                                            {{ order.customer_name }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-xs font-bold text-gray-800 dark:text-white"
                                        >
                                            Rp
                                            {{
                                                order.total_amount?.toLocaleString(
                                                    "id-ID",
                                                )
                                            }}
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span
                                                :class="{
                                                    'bg-emerald-100 text-emerald-700':
                                                        order.status ===
                                                        'completed',
                                                    'bg-orange-100 text-orange-700':
                                                        order.status ===
                                                            'pending' ||
                                                        order.status ===
                                                            'processing',
                                                    'bg-red-100 text-red-700':
                                                        order.status ===
                                                        'cancelled',
                                                }"
                                                class="px-2 py-1 rounded-full font-bold uppercase tracking-wider text-[10px]"
                                            >
                                                {{ order.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!stats.recent_orders?.length">
                                        <td
                                            colspan="4"
                                            class="px-6 py-10 text-center text-gray-400 text-xs italic"
                                        >
                                            Belum ada data pesanan terbaru.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Support Tickets List -->
                    <div
                        v-if="selectedModule === 'customer_service'"
                        class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden"
                    >
                        <div
                            class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center"
                        >
                            <h3
                                class="font-bold text-lg text-gray-700 dark:text-gray-200"
                            >
                                Recent Support Tickets
                            </h3>
                            <Link
                                :href="route('crm.support.tickets.index')"
                                class="bg-emerald-500 text-white text-[10px] font-black uppercase px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-200"
                                >All Tickets</Link
                            >
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Ticket
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Subject
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Priority
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="tkt in stats.recent_tickets ||
                                        []"
                                        :key="tkt.id"
                                        class="border-t border-gray-100 dark:border-gray-700"
                                    >
                                        <td
                                            class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200"
                                        >
                                            {{ tkt.ticket_number }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400"
                                        >
                                            <div class="font-bold">
                                                {{ tkt.subject }}
                                            </div>
                                            <div class="text-[10px]">
                                                {{ tkt.customer?.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span
                                                :class="[
                                                    'px-2 py-1 rounded-full font-bold uppercase tracking-wider text-[10px]',
                                                    tkt.status === 'OPEN'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-emerald-100 text-emerald-800',
                                                ]"
                                            >
                                                {{ tkt.status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-xs font-bold text-red-500"
                                        >
                                            {{ tkt.priority }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Marketing Campaigns List -->
                    <div
                        v-if="selectedModule === 'marketing_crm'"
                        class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden"
                    >
                        <div
                            class="rounded-t mb-0 px-6 py-4 border-0 flex justify-between items-center"
                        >
                            <h3
                                class="font-bold text-lg text-gray-700 dark:text-gray-200"
                            >
                                Recent Campaigns
                            </h3>
                            <Link
                                :href="route('crm.marketing.campaigns.index')"
                                class="bg-emerald-500 text-white text-[10px] font-black uppercase px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-200"
                                >All Campaigns</Link
                            >
                        </div>
                        <div class="block w-full overflow-x-auto">
                            <table
                                class="items-center w-full bg-transparent border-collapse"
                            >
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Campaign Name
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Type
                                        </th>
                                        <th
                                            class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 py-3 text-xs uppercase font-bold text-left"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="camp in stats.recent_campaigns ||
                                        []"
                                        :key="camp.id"
                                        class="border-t border-gray-100 dark:border-gray-700"
                                    >
                                        <td
                                            class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-200"
                                        >
                                            {{ camp.name }}
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            {{ camp.type }}
                                        </td>
                                        <td class="px-6 py-4 text-xs">
                                            <span
                                                class="px-2 py-1 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 font-bold uppercase tracking-wider text-[10px]"
                                            >
                                                {{ camp.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!stats.recent_campaigns?.length">
                                        <td
                                            colspan="3"
                                            class="px-6 py-10 text-center text-gray-400 text-sm italic"
                                        >
                                            No campaigns found. Start nurturing
                                            your leads!
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Chart logic (shared) -->
                    <div
                        v-if="
                            selectedModule === 'sales_crm' ||
                            selectedModule === 'wa_blast' ||
                            selectedModule === 'marketing_crm'
                        "
                        class="relative flex flex-col min-w-0 break-words bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden"
                    >
                        <div class="rounded-t mb-0 px-6 py-4 bg-transparent">
                            <h6
                                class="uppercase text-gray-400 mb-1 text-xs font-bold tracking-widest"
                            >
                                {{
                                    selectedModule === "sales_crm"
                                        ? "Revenue Analytics"
                                        : selectedModule === "marketing_crm"
                                          ? "Campaign Analytics"
                                          : "Growth Analytics"
                                }}
                            </h6>
                            <h2 class="text-white text-xl font-bold">
                                {{
                                    selectedModule === "sales_crm"
                                        ? "Revenue Trend (7 Days)"
                                        : selectedModule === "marketing_crm"
                                          ? "Campaign Performance"
                                          : "New Leads Trend"
                                }}
                            </h2>
                        </div>
                        <div class="p-4 flex-auto">
                            <VueApexCharts
                                height="350"
                                :options="chartOptionsComputed"
                                :series="series"
                            />
                        </div>
                    </div>
                </div>

                <div class="w-full xl:w-4/12 px-4">
                    <!-- Quick Actions (Sales CRM) -->
                    <div
                        v-if="selectedModule === 'sales_crm'"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6"
                    >
                        <h3
                            class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200"
                        >
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <Link
                                :href="route('crm.sales.orders.create')"
                                class="flex flex-col items-center justify-center p-5 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100/50 dark:border-emerald-800/50 hover:border-emerald-500 transition-all group shadow-sm"
                            >
                                <div
                                    class="h-12 w-12 bg-emerald-500 text-white rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-emerald-200/50 group-hover:scale-110 transition-transform"
                                >
                                    <svg
                                        class="w-7 h-7"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase text-emerald-800 dark:text-emerald-400 tracking-widest"
                                    >New Order</span
                                >
                            </Link>
                            <Link
                                :href="route('crm.sales.customers.create')"
                                class="flex flex-col items-center justify-center p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 hover:border-emerald-500 transition-all group shadow-sm"
                            >
                                <div
                                    class="h-12 w-12 bg-slate-800 text-white rounded-xl flex items-center justify-center mb-3 shadow-lg group-hover:scale-110 transition-transform"
                                >
                                    <svg
                                        class="w-7 h-7"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2.5"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                                        />
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase text-slate-700 dark:text-slate-400 tracking-widest"
                                    >Add Lead</span
                                >
                            </Link>
                        </div>
                    </div>

                    <!-- Top Products (Sales CRM) -->
                    <div
                        v-if="
                            selectedModule === 'sales_crm' &&
                            stats.top_selling_products?.length
                        "
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="font-bold text-lg text-gray-700 dark:text-gray-200 uppercase tracking-tighter"
                            >
                                Top Selling Products
                            </h3>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="(
                                    product, index
                                ) in stats.top_selling_products"
                                :key="product.id"
                                class="flex items-center gap-3"
                            >
                                <div
                                    class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-black text-gray-500 text-xs"
                                >
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex justify-between items-center mb-1"
                                    >
                                        <p
                                            class="text-xs font-black text-gray-800 dark:text-white truncate"
                                        >
                                            {{ product.name }}
                                        </p>
                                        <span
                                            class="text-[10px] font-black text-emerald-600 tracking-tighter"
                                            >{{ product.total_sold }} Sold</span
                                        >
                                    </div>
                                    <div
                                        class="w-full bg-gray-100 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden"
                                    >
                                        <div
                                            class="bg-emerald-500 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(16,185,129,0.5)]"
                                            :style="{
                                                width:
                                                    (product.total_sold /
                                                        stats
                                                            .top_selling_products[0]
                                                            .total_sold) *
                                                        100 +
                                                    '%',
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Distribution (Sales CRM) -->
                    <div
                        v-if="
                            selectedModule === 'sales_crm' &&
                            stats.order_status_distribution?.length
                        "
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6"
                    >
                        <h3
                            class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200 uppercase tracking-tighter"
                        >
                            Order Distribution
                        </h3>
                        <div class="space-y-3">
                            <div
                                v-for="status in stats.order_status_distribution"
                                :key="status.status"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-xl border border-transparent hover:border-emerald-500/20 transition-all"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        :class="{
                                            'bg-orange-500':
                                                status.status === 'pending',
                                            'bg-slate-500':
                                                status.status === 'processing',
                                            'bg-emerald-500':
                                                status.status === 'completed',
                                            'bg-red-500':
                                                status.status === 'cancelled',
                                        }"
                                        class="h-2 w-2 rounded-full shadow-[0_0_8px_rgba(0,0,0,0.1)]"
                                    ></div>
                                    <span
                                        class="text-[10px] font-black uppercase text-gray-600 dark:text-gray-300 tracking-widest"
                                        >{{ status.status }}</span
                                    >
                                </div>
                                <span
                                    class="font-black text-xs text-gray-800 dark:text-white"
                                    >{{ status.count }}</span
                                >
                            </div>
                        </div>
                    </div>
                    <!-- Your Package Info (WA Module) -->
                    <div
                        v-if="selectedModule === 'wa_blast' && pricingPlan"
                        class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white rounded-[24px] shadow-2xl shadow-emerald-500/10 p-8 mb-8 relative overflow-hidden"
                    >
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16"
                        ></div>

                        <div class="relative z-10">
                            <h3
                                class="font-black text-xs uppercase tracking-widest mb-1 text-emerald-200"
                            >
                                Paket Saat Ini
                            </h3>
                            <div class="text-2xl font-black mb-4 uppercase">
                                {{ pricingPlan.name }}
                            </div>

                            <div class="space-y-3 mb-2">
                                <div
                                    v-for="(
                                        feature, index
                                    ) in pricingPlan.features"
                                    :key="index"
                                    class="flex items-start gap-3 text-sm font-bold text-white/90"
                                >
                                    <svg
                                        class="w-5 h-5 shrink-0 text-emerald-200"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M5 13l4 4L19 7"
                                        ></path>
                                    </svg>
                                    {{ feature }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WA Accounts (WA Module) -->
                    <div
                        v-if="selectedModule === 'wa_blast'"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6"
                    >
                        <h3
                            class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200"
                        >
                            WhatsApp Accounts
                        </h3>
                        <div
                            v-for="account in waAccounts"
                            :key="account.id"
                            class="mb-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600"
                        >
                            <div class="flex justify-between items-start mb-1">
                                <p
                                    class="font-bold text-sm text-gray-800 dark:text-white"
                                >
                                    {{ account.name }}
                                </p>
                                <span
                                    class="text-[10px] font-black uppercase text-emerald-500"
                                    >{{ account.status }}</span
                                >
                            </div>
                            <p
                                class="text-[10px] text-gray-500 dark:text-gray-400 mb-2"
                            >
                                {{ account.phone_number }}
                            </p>
                            <div
                                class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400"
                            >
                                <svg
                                    class="w-3.5 h-3.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                <span class="text-[10px] font-bold uppercase"
                                    >{{ account.agents_count || 0 }} Agents
                                    Connected</span
                                >
                            </div>
                        </div>
                        <div
                            v-if="waAccounts.length === 0"
                            class="text-center py-6 text-gray-400 text-xs italic"
                        >
                            Belum ada akun WhatsApp terhubung.
                        </div>
                    </div>

                    <!-- Recent Conversations (WA Module) -->
                    <div
                        v-if="selectedModule === 'wa_blast'"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"
                    >
                        <h3
                            class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200"
                        >
                            Recent Chats
                        </h3>
                        <div
                            v-for="chat in recentChats"
                            :key="chat.id"
                            class="flex items-center gap-3 mb-4 last:mb-0"
                        >
                            <div
                                class="h-10 w-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-black text-sm shadow-md"
                            >
                                {{ chat.customer?.name?.charAt(0) || "?" }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <p
                                        class="text-xs font-black text-gray-800 dark:text-white truncate"
                                    >
                                        {{ chat.customer?.name || "Unknown" }}
                                    </p>
                                    <span
                                        class="text-[9px] font-bold text-emerald-500 uppercase"
                                        >{{ chat.status }}</span
                                    >
                                </div>
                                <p
                                    class="text-[10px] text-gray-500 dark:text-gray-400"
                                >
                                    By:
                                    {{
                                        chat.assigned_user?.name || "Unassigned"
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="recentChats.length === 0"
                            class="text-center py-6 text-gray-400 text-xs italic"
                        >
                            Belum ada percakapan terbaru.
                        </div>
                    </div>

                    <!-- Top Scored Leads (Marketing Module) -->
                    <div
                        v-if="selectedModule === 'marketing_crm'"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <h3
                                class="font-bold text-lg text-gray-700 dark:text-gray-200"
                            >
                                Top Leads
                            </h3>
                            <Link
                                :href="
                                    route('crm.marketing.lead-scoring.index')
                                "
                                class="text-xs font-black text-emerald-500 uppercase tracking-widest"
                                >View All</Link
                            >
                        </div>
                        <div
                            v-for="lead in stats.top_leads || []"
                            :key="lead.id"
                            class="flex items-center gap-3 mb-4 last:mb-0"
                        >
                            <div
                                class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-black text-sm shadow-md"
                            >
                                {{ lead.name.charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center">
                                    <p
                                        class="text-xs font-black text-gray-800 dark:text-white truncate"
                                    >
                                        {{ lead.name }}
                                    </p>
                                    <span
                                        class="text-[10px] font-black text-purple-500"
                                        >Score: {{ lead.lead_score }}</span
                                    >
                                </div>
                                <p
                                    class="text-[10px] text-gray-500 dark:text-gray-400 truncate"
                                >
                                    {{ lead.phone }}
                                </p>
                            </div>
                        </div>
                        <div
                            v-if="!stats.top_leads?.length"
                            class="text-center py-6 text-gray-400 text-xs italic"
                        >
                            No lead data yet.
                        </div>
                    </div>

                    <!-- Marketing Channels Status (Marketing Module) -->
                    <div
                        v-if="selectedModule === 'marketing_crm'"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6"
                    >
                        <h3
                            class="font-bold text-lg mb-4 text-gray-700 dark:text-gray-200"
                        >
                            Active Channels
                        </h3>
                        <div class="space-y-3">
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-xl"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.438-9.89 9.886-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.743-.975zm11.367-7.405c-.31-.155-1.837-.906-2.12-.1.01-.285-.155-.343-.233-.444-.078-.101-.285-.427-.31-.466-.025-.039-.041-.065-.062-.101-.021-.036-.01-.065.01-.101.021-.036.186-.218.279-.327.093-.108.124-.181.186-.301.062-.12.031-.226-.015-.301-.047-.075-.424-1.02-.581-1.326-.153-.297-.308-.257-.424-.258l-.362-.004c-.124 0-.327.047-.497.233-.17.187-.651.637-.651 1.55s.67 1.797.763 1.921c.093.124 1.321 2.016 3.199 2.826.447.193.795.308 1.068.394.448.142.856.122 1.178.058.359-.071 1.101-.45 1.256-.884.155-.434.155-.806.108-.884-.047-.078-.17-.124-.479-.28z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-bold text-gray-700 dark:text-gray-200"
                                        >WhatsApp</span
                                    >
                                </div>
                                <span
                                    class="text-[10px] font-black text-emerald-500"
                                    >READY</span
                                >
                            </div>
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-xl"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-bold text-gray-700 dark:text-gray-200"
                                        >Email</span
                                    >
                                </div>
                                <span
                                    class="text-[10px] font-black text-slate-500"
                                    >ACTIVE</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Switch App (Mobile/Desktop floating) -->
            <button
                v-if="enabledModules.length > 1"
                @click="exitPortal"
                class="fixed bottom-6 right-6 bg-emerald-600 text-white p-4 rounded-full shadow-2xl flex items-center gap-2 hover:bg-emerald-700 transition-all z-[60] hover:scale-110 active:scale-95"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16m-7 6h7"
                    ></path>
                </svg>
                <span class="hidden md:inline font-bold text-sm"
                    >Ganti Aplikasi</span
                >
            </button>
        </div>
    </AuthenticatedLayout>
</template>

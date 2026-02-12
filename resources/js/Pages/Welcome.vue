<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    pricingPlans: Array,
});

const form = useForm({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    business_type: 'UMKM',
    message: '',
});

const showSuccess = ref(false);

const submit = () => {
    form.post(route('request.custom'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showSuccess.value = true;
            setTimeout(() => showSuccess.value = false, 5000);
        },
    });
};

const isNavScrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        isNavScrolled.value = window.scrollY > 20;
    });
});
</script>

<template>
    <Head>
        <title>Operra - Solusi Komunikasi Terpadu & Omnichannel CRM Indonesia</title>
        <meta name="description" content="Operra adalah platform Omnichannel CRM terbaik untuk UMKM. Kelola WhatsApp, Email, dan data pelanggan dalam satu dashboard profesional." />
        <meta name="keywords" content="CRM UMKM, WhatsApp Blast, Omnichannel Indonesia, Manajemen Pelanggan, Operra CRM" />
    </Head>

    <div class="text-gray-900 antialiased bg-[#f8fafc] selection:bg-operra-100 selection:text-operra-700">
        <!-- Navbar -->
        <nav :class="[
            'fixed top-0 z-[100] w-full transition-all duration-500 px-6 py-4',
            isNavScrolled ? 'bg-white/80 backdrop-blur-xl border-b border-gray-100 py-3 shadow-sm' : 'bg-transparent'
        ]">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-3">
                    <img src="/images/operra_system.png" alt="Operra Logo" class="h-10 md:h-16 w-auto" />
                </Link>
                
                <div class="hidden lg:flex items-center gap-10 text-sm font-semibold text-gray-600">
                    <a href="#fitur" class="hover:text-operra-600 transition-colors">Fitur</a>
                    <a href="#solusi" class="hover:text-operra-600 transition-colors">Solusi</a>
                    <a href="#pricing" class="hover:text-operra-600 transition-colors">Harga</a>
                </div>

                <div class="flex items-center gap-4">
                    <template v-if="canLogin">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                            class="rounded-full bg-operra-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-operra-600/20 hover:bg-operra-700 hover:-translate-y-0.5 transition-all">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-gray-600 hover:text-operra-600 transition-colors px-4">
                                Masuk
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" 
                                class="rounded-full bg-operra-600 px-6 py-2.5 text-sm font-bold text-white shadow-xl shadow-operra-600/20 hover:bg-operra-700 hover:-translate-y-0.5 transition-all">
                                Daftar Gratis
                            </Link>
                        </template>
                    </template>
                </div>
            </div>
        </nav>

        <main>
            <!-- Hero Section -->
            <section class="relative min-h-screen flex items-center pt-28 pb-20 overflow-hidden">
                <!-- Background Decorations -->
                <div class="absolute inset-0 -z-10">
                    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-operra-50 rounded-full blur-[120px] -mr-96 -mt-96 opacity-60"></div>
                    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-cyan-50 rounded-full blur-[100px] -ml-44 -mb-44 opacity-50"></div>
                </div>

                <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-operra-50 border border-operra-100 text-operra-700 text-xs font-bold uppercase tracking-widest mb-8 animate-fade-in">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-operra-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-operra-500"></span>
                            </span>
                            Omnichannel CRM untuk Masa Depan UMKM
                        </div>
                            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight leading-[1.1] mb-8">
                                Satu Pintu untuk <br/>
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-operra-600 to-cyan-600">Omnichannel Komunikasi</span>
                            </h1>
                            <p class="text-gray-600 text-lg md:text-xl max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed mb-10">
                                Kelola Email, riwayat pesanan pelanggan, dan integrasi pesan dalam satu dashboard terpadu. Lebih efisien, lebih profesional, dan terpusat.
                            </p>
                        <div class="flex flex-col sm:flex-row justify-center lg:justify-start items-center gap-4 lg:gap-6">
                            <Link :href="route('register')" class="w-full sm:w-auto text-center rounded-full bg-operra-600 px-10 py-5 text-lg font-bold text-white shadow-2xl shadow-operra-600/30 hover:bg-operra-700 hover:-translate-y-1 transition-all">
                                Mulai Sekarang — Gratis
                            </Link>
                            <a href="#fitur" class="w-full sm:w-auto text-center rounded-full bg-white px-10 py-5 text-lg font-bold text-gray-700 border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm">
                                Pelajari Fitur
                            </a>
                        </div>
                        
                        <div class="mt-12 flex items-center justify-center lg:justify-start gap-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                            <div class="text-xs font-bold uppercase tracking-widest text-gray-400">Terpercaya Oleh:</div>
                            <div class="flex items-center gap-6">
                                <span class="font-black italic text-gray-500 text-sm md:text-base">UMKM Lokal</span>
                                <span class="font-black italic text-gray-500 text-sm md:text-base">Retail Store</span>
                                <span class="font-black italic text-gray-500 text-sm md:text-base">Agensi Digital</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative hidden lg:block">
                        <div class="absolute inset-0 bg-operra-200/20 blur-[80px] rounded-full scale-75"></div>
                        <div class="relative bg-white p-4 rounded-[40px] shadow-2xl border border-gray-100">
                            <img src="/images/operra_system.png" alt="Operra Unified Dashboard" class="w-full h-auto rounded-[32px] shadow-inner" />
                            
                            <!-- Floating Elements -->
                            <!-- Floating Elements (WhatsApp Masked) -->
                            <!-- <div class="absolute -top-6 -right-6 bg-white p-6 rounded-3xl shadow-xl border border-gray-50 animate-bounce-slow">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .004 5.408.001 12.045c0 2.121.554 4.189 1.602 6.006L0 24l6.117-1.605a11.81 11.81 0 005.923 1.577h.005c6.635 0 12.046-5.41 12.049-12.048a11.811 11.811 0 00-3.526-8.528"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-gray-900">WhatsApp</div>
                                        <div class="text-[10px] text-green-500 font-bold">Terhubung</div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-3xl shadow-xl border border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-operra-500 flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-gray-900">Email Fallback</div>
                                        <div class="text-[10px] text-operra-500 font-bold text-nowrap">Siap Mengirim</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Fitur Section -->
            <section id="fitur" class="py-24 bg-white relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center max-w-3xl mx-auto mb-20">
                        <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-operra-600 mb-4">Fitur Unggulan</h2>
                        <h3 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight mb-6">Omnichannel: Komunikasi yang Lebih Manusiawi</h3>
                        <p class="text-gray-500 text-lg font-medium">
                            Jangan biarkan pelanggan menunggu. Dengan Operra, semua saluran komunikasi berada di genggaman Anda.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Card 1 -->
                        <div class="group p-8 rounded-[40px] bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-2xl hover:border-operra-100 transition-all duration-500">
                            <div class="h-16 w-16 rounded-2xl bg-operra-100 text-operra-600 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Email Fallback</h4>
                            <p class="text-gray-500 leading-relaxed font-medium">
                                Kirim otomatis Invoice PDF dan notifikasi via Email saat WhatsApp sedang bermasalah. Bisnis tetap jalan, pelanggan tetap terinfokan.
                            </p>
                        </div>

                        <!-- Card 2 -->
                        <div class="group p-8 rounded-[40px] bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-2xl hover:border-operra-100 transition-all duration-500">
                            <div class="h-16 w-16 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Tracking Portal</h4>
                            <p class="text-gray-500 leading-relaxed font-medium">
                                Berikan link pelacakan mandiri untuk pelanggan. Mereka bisa cek status pesanan, pembayaran, dan download invoice tanpa harus tanya CS.
                            </p>
                        </div>

                        <!-- Card 3 -->
                        <div class="group p-8 rounded-[40px] bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-2xl hover:border-operra-100 transition-all duration-500">
                            <div class="h-16 w-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Stock Alert</h4>
                            <p class="text-gray-500 leading-relaxed font-medium">
                                Notifikasi otomatis via Email ke Owner/Admin saat stok menipis. Jangan biarkan potensi penjualan hilang karena stok kosong.
                            </p>
                        </div>

                        <!-- Card 4 -->
                        <div class="group p-8 rounded-[40px] bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-2xl hover:border-operra-100 transition-all duration-500">
                            <div class="h-16 w-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Sales Analytics</h4>
                            <p class="text-gray-500 leading-relaxed font-medium">
                                Visualisasi data penjualan, ranking sales order, dan performa tim dalam grafik menarik. Keputusan bisnis jadi lebih terukur.
                            </p>
                        </div>

                        <!-- Card 5 -->
                        <div class="group p-8 rounded-[40px] bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-2xl hover:border-operra-100 transition-all duration-500">
                            <div class="h-16 w-16 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Payment Reminder</h4>
                            <p class="text-gray-500 leading-relaxed font-medium">
                                Sistem penagihan otomatis via Email untuk invoice yang mendekati atau sudah melewati jatuh tempo (Account Receivable).
                            </p>
                        </div>

                        <!-- Card 6 (Unified Inbox Commented) -->
                        <!-- <div class="group p-8 rounded-[40px] bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-2xl hover:border-operra-100 transition-all duration-500">
                            <div class="h-16 w-16 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Unified Inbox</h4>
                            <p class="text-gray-500 leading-relaxed font-medium">
                                Kelola ribuan pesan WhatsApp dari berbagai nomor dalam satu dashboard terpadu yang efisien dan profesional.
                            </p>
                        </div> -->
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section id="pricing" class="py-24 bg-[#f8fafc] relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="text-center mb-20">
                        <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-operra-600 mb-4">Rencana Harga</h2>
                        <h3 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">Investasi Murah untuk <span class="text-operra-600">Hasil Mewah</span></h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div 
                            v-for="plan in pricingPlans" 
                            :key="plan.id"
                            :class="plan.is_popular ? 'bg-operra-600 shadow-2xl shadow-operra-600/30 scale-105 z-10' : 'bg-white border border-gray-100'"
                            class="relative rounded-[40px] p-10 transition-all hover:-translate-y-2 flex flex-col"
                        >
                            <div v-if="plan.badge" class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-gray-950 text-white text-[10px] font-black uppercase tracking-widest whitespace-nowrap shadow-xl">
                                {{ plan.badge }}
                            </div>

                            <div class="mb-10">
                                <h3 :class="plan.is_popular ? 'text-white' : 'text-gray-900'" class="text-xl font-black uppercase mb-2">{{ plan.name }}</h3>
                                <div class="flex items-baseline gap-1">
                                    <span :class="plan.is_popular ? 'text-white' : 'text-gray-900'" class="text-4xl font-black">
                                        {{ plan.price > 0 ? 'Rp' + (plan.price / 1000) + 'k' : 'Custom' }}
                                    </span>
                                    <span v-if="plan.price > 0" :class="plan.is_popular ? 'text-white/70' : 'text-gray-400'" class="text-sm font-bold">/ bulan</span>
                                </div>
                            </div>

                            <ul class="space-y-4 mb-10 flex-1">
                                <li v-for="feature in plan.features" :key="feature" :class="plan.is_popular ? 'text-white/90' : 'text-gray-500'" class="flex items-start gap-3 text-sm font-semibold">
                                    <svg :class="plan.is_popular ? 'text-white' : 'text-operra-500'" class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    {{ feature }}
                                </li>
                            </ul>

                            <Link 
                                v-if="plan.price > 0"
                                :href="route('register', { plan: plan.slug })"
                                :class="plan.is_popular ? 'bg-white text-operra-600 hover:bg-gray-50' : 'bg-gray-100 text-gray-900 hover:bg-gray-200'" 
                                class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm text-center transition-all active:scale-95 shadow-sm"
                            >
                                {{ plan.cta_text }}
                            </Link>

                            <a 
                                v-else
                                href="#custom"
                                :class="plan.is_popular ? 'bg-white text-operra-600 hover:bg-gray-50' : 'bg-gray-100 text-gray-900 hover:bg-gray-200'" 
                                class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm text-center transition-all active:scale-95 shadow-sm"
                            >
                                {{ plan.cta_text }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Custom Request Section -->
            <section id="custom" class="py-24 bg-white relative">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="max-w-6xl mx-auto bg-gray-950 rounded-[60px] p-8 md:p-20 border border-gray-800 shadow-2xl flex flex-wrap items-center overflow-hidden relative">
                        <!-- BG Decoration -->
                        <div class="absolute top-0 right-0 w-96 h-96 bg-operra-600/20 rounded-full blur-[100px] -mr-44 -mt-44 opacity-50"></div>
                        
                        <div class="w-full lg:w-1/2 mb-12 lg:mb-0 lg:pr-16 relative z-10">
                            <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight mb-8">Solusi Kustom untuk <span class="text-operra-400 text-nowrap">Bisnis Besar</span></h2>
                            <p class="text-gray-400 text-lg leading-relaxed mb-10 font-medium">
                                Kami mengerti setiap bisnis memiliki alur yang unik. Tim kami siap membangun fitur kustom, integrasi API, hingga server On-Premise khusus untuk Anda.
                            </p>
                            <div class="space-y-8">
                                <div class="flex items-center gap-6 group">
                                    <div class="h-14 w-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-operra-400 group-hover:bg-operra-400 group-hover:text-white transition-all">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold text-lg">On-Premise Ready</h4>
                                        <p class="text-gray-500 text-sm">Data & Server di bawah kendali keamanan Anda sendiri.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 group">
                                    <div class="h-14 w-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-operra-400 group-hover:bg-operra-400 group-hover:text-white transition-all">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4m4-4l-4 4M6 16l-4-4 4-4"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold text-lg">Integrasi Menyeluruh</h4>
                                        <p class="text-gray-500 text-sm">Hubungkan dengan sistem internal yang sudah ada.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full lg:w-1/2 relative z-10">
                            <form @submit.prevent="submit" class="bg-white p-8 md:p-12 rounded-[50px] shadow-2xl relative">
                                <div v-if="showSuccess" class="mb-8 p-4 bg-green-50 border border-green-100 rounded-2xl text-green-700 text-sm font-bold text-center">
                                    Permintaan terkirim! Tim kami akan segera menghubungi Anda.
                                </div>

                                <div class="space-y-4 mb-8">
                                    <input v-model="form.name" type="text" placeholder="Nama Lengkap" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-gray-900 text-sm focus:border-operra-500 focus:ring-0 transition-all font-medium" required />
                                    <input v-model="form.company_name" type="text" placeholder="Nama Perusahaan" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-gray-900 text-sm focus:border-operra-500 focus:ring-0 transition-all font-medium" required />
                                    <input v-model="form.email" type="email" placeholder="Email Bisnis" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-gray-900 text-sm focus:border-operra-500 focus:ring-0 transition-all font-medium" required />
                                    <input v-model="form.phone" type="text" placeholder="Nomor WhatsApp" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-gray-900 text-sm focus:border-operra-500 focus:ring-0 transition-all font-medium" required />
                                    <select v-model="form.business_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-gray-900 text-sm focus:border-operra-500 focus:ring-0 transition-all font-medium">
                                        <option value="UMKM">UMKM / Retail</option>
                                        <option value="Enterprise">Enterprise / Besar</option>
                                        <option value="Government">Instansi Pemerintah</option>
                                    </select>
                                    <textarea v-model="form.message" placeholder="Ceritakan kebutuhan unik Anda..." rows="3" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-gray-900 text-sm focus:border-operra-500 focus:ring-0 transition-all font-medium" required></textarea>
                                </div>
                                
                                <button type="submit" :disabled="form.processing" class="w-full py-5 rounded-2xl bg-gray-950 text-white font-black uppercase tracking-widest text-sm hover:bg-gray-800 transition-all shadow-xl disabled:opacity-50">
                                    Dapatkan Proposal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-950 pt-24 pb-12 border-t border-gray-900">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 md:gap-8 mb-20 text-center md:text-left">
                    <div class="md:col-span-1">
                        <Link :href="route('welcome')" class="inline-flex items-center gap-3 mb-8">
                            <img src="/images/operra_system.png" alt="Operra" class="h-14 w-auto brightness-0 invert" />
                        </Link>
                        <p class="text-gray-500 font-medium leading-relaxed">
                            Solusi Omnichannel CRM masa kini. Membantu UMKM bertumbuh melalui komunikasi terpadu yang profesional.
                        </p>
                    </div>
                    
                    <div class="md:ml-auto">
                        <span class="block text-xs font-black uppercase tracking-widest text-operra-400 mb-8">Eksplorasi</span>
                        <ul class="space-y-4 text-sm font-bold text-gray-400">
                            <li><a href="#fitur" class="hover:text-white transition-colors">Fitur Utama</a></li>
                            <li><a href="#pricing" class="hover:text-white transition-colors">Paket Harga</a></li>
                            <li><a href="#custom" class="hover:text-white transition-colors">Custom Solution</a></li>
                        </ul>
                    </div>

                    <div class="md:ml-auto">
                        <span class="block text-xs font-black uppercase tracking-widest text-operra-400 mb-8">Perusahaan</span>
                        <ul class="space-y-4 text-sm font-bold text-gray-400">
                            <li><Link :href="route('login')" class="hover:text-white transition-colors">Masuk</Link></li>
                            <li><Link :href="route('register')" class="hover:text-white transition-colors">Daftar Akun</Link></li>
                            <li><a href="#" class="hover:text-white transition-colors">Pusat Bantuan</a></li>
                        </ul>
                    </div>

                    <div class="md:ml-auto">
                        <span class="block text-xs font-black uppercase tracking-widest text-operra-400 mb-8">Legalitas</span>
                        <ul class="space-y-4 text-sm font-bold text-gray-400">
                            <li><Link :href="route('privacy.policy')" class="hover:text-white transition-colors">Kebijakan Privasi</Link></li>
                            <li><Link :href="route('terms.service')" class="hover:text-white transition-colors">Syarat & Ketentuan</Link></li>
                        </ul>
                    </div>
                </div>
                
                <div class="pt-12 border-t border-gray-900 flex flex-wrap items-center justify-between text-gray-500 text-[10px] font-black uppercase tracking-[0.2em] text-center md:text-left gap-4">
                    <div>© 2026 Operra Omnichannel CRM. <br class="md:hidden"/> Created by <a href="https://hasanarofid.site" class="text-white hover:text-operra-400 transition-colors">hasanarofid</a></div>
                    <div class="flex items-center gap-6">
                        <a href="#" class="hover:text-white transition-colors">Instagram</a>
                        <a href="#" class="hover:text-white transition-colors">LinkedIn</a>
                        <a href="#" class="hover:text-white transition-colors">WhatsApp</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 1s ease-out forwards;
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-bounce-slow {
    animation: bounce-slow 4s ease-in-out infinite;
}
</style>

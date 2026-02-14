<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const isNavScrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        isNavScrolled.value = window.scrollY > 20;
    });
});
</script>

<template>
    <Head>
        <title>Fitur Operra - Omnichannel CRM Terbaik Indonesia</title>
    </Head>

    <div class="text-gray-900 antialiased bg-[#f8fafc]">
        <!-- Reuse Navbar -->
        <nav :class="[ 'fixed top-0 z-[100] w-full transition-all duration-500 px-6 py-4', isNavScrolled ? 'bg-white/80 backdrop-blur-xl border-b border-gray-100 py-3 shadow-sm' : 'bg-transparent' ]">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-3">
                    <img src="/images/operra_system.png" alt="Operra Logo" class="h-10 md:h-16 w-auto" />
                </Link>
                <div class="hidden lg:flex items-center gap-10 text-sm font-semibold text-gray-600">
                    <Link :href="route('features')" class="text-operra-600">Fitur</Link>
                    <Link :href="route('solutions')" class="hover:text-operra-600 transition-colors">Solusi</Link>
                    <Link :href="route('pricing')" class="hover:text-operra-600 transition-colors">Harga</Link>
                </div>
                <div class="flex items-center gap-4">
                    <Link :href="route('login')" class="text-sm font-bold text-gray-600 hover:text-operra-600 transition-colors px-4">Masuk</Link>
                    <Link :href="route('register')" class="rounded-full bg-operra-600 px-6 py-2.5 text-sm font-bold text-white shadow-xl shadow-operra-600/20 hover:bg-operra-700 hover:-translate-y-0.5 transition-all">Daftar Gratis</Link>
                </div>
            </div>
        </nav>

        <main class="pt-32 pb-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-20">
                    <h1 class="text-xs font-bold uppercase tracking-[0.3em] text-operra-600 mb-4">Fitur Unggulan</h1>
                    <h2 class="text-4xl md:text-6xl font-black text-gray-900 tracking-tight mb-6">Omnichannel CRM untuk Mengakselerasi Bisnis Anda</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature Cards (Extracted from Welcome.vue) -->
                    <div class="group p-8 rounded-[40px] bg-white border border-gray-100 hover:shadow-2xl transition-all duration-500">
                        <div class="h-16 w-16 rounded-2xl bg-operra-100 text-operra-600 flex items-center justify-center mb-8">
                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">Email Fallback</h4>
                        <p class="text-gray-500 leading-relaxed font-medium">Kirim otomatis Invoice PDF dan notifikasi via Email saat WhatsApp sedang bermasalah.</p>
                    </div>

                    <div class="group p-8 rounded-[40px] bg-white border border-gray-100 hover:shadow-2xl transition-all duration-500">
                        <div class="h-16 w-16 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center mb-8">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">Tracking Portal</h4>
                        <p class="text-gray-500 leading-relaxed font-medium">Berikan link pelacakan mandiri untuk pelanggan cek status pesanan & download invoice.</p>
                    </div>

                    <div class="group p-8 rounded-[40px] bg-white border border-gray-100 hover:shadow-2xl transition-all duration-500">
                        <div class="h-16 w-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mb-8">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-4">Stock Alert</h4>
                        <p class="text-gray-500 leading-relaxed font-medium">Notifikasi otomatis via Email saat stok menipis. Pantau inventori secara real-time.</p>
                    </div>

                    <!-- App Previews could go here or as a separate section -->
                    <div class="col-span-full mt-24">
                        <div class="text-center mb-16">
                            <h3 class="text-3xl font-black text-gray-900">Visual Preview Aplikasi</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                             <div class="rounded-3xl border border-gray-100 shadow-xl overflow-hidden bg-white p-4">
                                <h4 class="text-sm font-bold text-gray-800 mb-4 px-2">Dashboard Analytics</h4>
                                <img src="/images/app_preview_dashboard.png" alt="Preview Dashboard" class="w-full h-auto rounded-xl grayscale hover:grayscale-0 transition-all duration-700" />
                             </div>
                             <div class="rounded-3xl border border-gray-100 shadow-xl overflow-hidden bg-white p-4">
                                <h4 class="text-sm font-bold text-gray-800 mb-4 px-2">Sales Pipeline (Kanban)</h4>
                                <img src="/images/app_preview_pipeline.png" alt="Preview Pipeline" class="w-full h-auto rounded-xl grayscale hover:grayscale-0 transition-all duration-700" />
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Reuse Footer Simple -->
        <footer class="bg-gray-950 py-12 border-t border-gray-900 text-center">
            <Link :href="route('welcome')">
                <img src="/images/operra_system.png" alt="Operra" class="h-10 w-auto brightness-0 invert mx-auto mb-6" />
            </Link>
            <p class="text-gray-500 text-xs font-black uppercase tracking-widest">© 2026 Operra Omnichannel CRM</p>
        </footer>
    </div>
</template>

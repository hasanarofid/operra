<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Operra - Business Operations Control Panel" />

    <div class="text-gray-800 antialiased">
        <!-- Navbar -->
        <nav class="absolute top-0 z-50 flex w-full flex-wrap items-center justify-between px-2 py-3">
            <div class="container mx-auto flex flex-wrap items-center justify-between px-4">
                <div class="relative flex w-full justify-between lg:static lg:block lg:w-auto lg:justify-start">
                    <Link :href="route('dashboard')" class="mr-4 inline-block whitespace-nowrap py-2 text-sm font-bold uppercase leading-relaxed text-white">
                        <div class="flex items-center gap-2">
                            <ApplicationLogo class="h-10 w-auto" />
                            <span class="text-xl font-extrabold tracking-tighter">OPERRA</span>
                        </div>
                    </Link>
                </div>
                
                <div class="flex flex-grow items-center bg-white lg:bg-transparent lg:shadow-none" id="example-navbar-warning">
                    <ul class="flex list-none flex-col lg:ml-auto lg:flex-row">
                        <li v-if="canLogin" class="flex items-center">
                            <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                                class="mb-3 ml-3 rounded-full bg-white px-6 py-2 text-xs font-bold uppercase text-gray-800 shadow outline-none transition-all duration-150 ease-linear hover:shadow-md focus:outline-none active:bg-gray-100 lg:mb-0 lg:mr-1">
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link :href="route('login')" class="px-5 py-4 text-xs font-bold uppercase text-white lg:py-2 hover:text-indigo-200 transition-colors">
                                    Log In
                                </Link>
                                <Link v-if="canRegister" :href="route('register')" 
                                    class="mb-3 ml-3 rounded-full bg-indigo-600 px-6 py-2 text-xs font-bold uppercase text-white shadow outline-none transition-all duration-150 ease-linear hover:bg-indigo-700 hover:shadow-md focus:outline-none lg:mb-0 lg:mr-1">
                                    Get Started
                                </Link>
                            </template>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <!-- Hero Section -->
            <div class="relative flex min-h-screen content-center items-center justify-center pb-32 pt-16">
                <div class="absolute top-0 h-full w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-1.2.1&auto=format&fit=crop&w=2426&q=80');">
                    <span id="blackOverlay" class="absolute h-full w-full bg-gradient-to-b from-gray-900 via-gray-900/80 to-gray-900 opacity-90"></span>
                </div>
                <div class="container relative mx-auto">
                    <div class="flex flex-wrap items-center">
                        <div class="ml-auto mr-auto w-full px-4 text-center lg:w-8/12">
                            <div class="pr-0">
                                <div class="mb-6 flex justify-center">
                                    <ApplicationLogo class="h-24 w-auto" />
                                </div>
                                <h1 class="text-6xl font-black text-white uppercase tracking-tighter leading-none mb-6">
                                    Control Your Business <br/>
                                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">With Precision</span>
                                </h1>
                                <p class="mt-4 text-xl text-gray-300 max-w-2xl mx-auto font-light leading-relaxed">
                                    Sistem internal cerdas untuk UKM dan startup. Kelola pesanan, stok barang, hingga aktivitas staf dalam satu dasbor kendali yang kuat dan terukur.
                                </p>
                                <div class="mt-12 flex flex-wrap justify-center gap-6">
                                    <Link :href="route('register')" class="rounded-full bg-indigo-600 px-10 py-5 font-bold text-white shadow-2xl transition-all hover:bg-indigo-700 hover:-translate-y-1">
                                        Mulai Sekarang
                                    </Link>
                                    <Link :href="route('login')" class="rounded-full bg-white/10 backdrop-blur-md px-10 py-5 font-bold text-white border border-white/20 transition-all hover:bg-white/20 hover:-translate-y-1">
                                        Demo Sistem
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pointer-events-none absolute bottom-0 left-0 right-0 top-auto w-full overflow-hidden" style="height: 70px;">
                    <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                        <polygon class="fill-current text-gray-300" points="2560 0 2560 100 0 100"></polygon>
                    </svg>
                </div>
            </div>

            <!-- Features Section -->
            <section class="-mt-24 bg-gray-50 pb-20">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap">
                        <div class="w-full px-4 pt-6 text-center md:w-4/12 lg:pt-12">
                            <div class="relative mb-8 flex min-w-0 flex-col break-words rounded-2xl bg-white shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                                <div class="flex-auto px-8 py-10">
                                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 p-3 text-center shadow-inner">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    </div>
                                    <h6 class="text-xl font-extrabold uppercase tracking-tight mb-3">Orders Tracking</h6>
                                    <p class="mb-4 text-gray-500 leading-relaxed">
                                        Pantau alur pesanan dari pending hingga selesai secara real-time untuk kepuasan pelanggan maksimal.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full px-4 text-center md:w-4/12">
                            <div class="relative mb-8 flex min-w-0 flex-col break-words rounded-2xl bg-white shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                                <div class="flex-auto px-8 py-10">
                                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 p-3 text-center shadow-inner">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    <h6 class="text-xl font-extrabold uppercase tracking-tight mb-3">Inventory Management</h6>
                                    <p class="mb-4 text-gray-500 leading-relaxed">
                                        Sistem stok otomatis dengan peringatan stok menipis, mencegah bottlenecks dalam operasional Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full px-4 pt-6 text-center md:w-4/12">
                            <div class="relative mb-8 flex min-w-0 flex-col break-words rounded-2xl bg-white shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                                <div class="flex-auto px-8 py-10">
                                    <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 p-3 text-center shadow-inner">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </div>
                                    <h6 class="text-xl font-extrabold uppercase tracking-tight mb-3">Staff Performance</h6>
                                    <p class="mb-4 text-gray-500 leading-relaxed">
                                        Log aktivitas transparan untuk memantau produktivitas tim dan pengambilan keputusan berbasis fakta.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-40 flex flex-wrap items-center">
                        <div class="ml-auto mr-auto w-full px-4 md:w-5/12">
                            <div class="mb-8 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600 p-4 text-center text-white shadow-2xl">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <h3 class="mb-4 text-4xl font-black uppercase tracking-tighter leading-none">
                                Dirancang untuk <br/>
                                <span class="text-indigo-600">Pertumbuhan UKM</span>
                            </h3>
                            <p class="mb-6 text-lg font-light leading-relaxed text-gray-600">
                                Operra bukan sekadar dasbor, melainkan sistem saraf pusat bisnis Anda. Kami memahami kebutuhan SME untuk memiliki kontrol penuh tanpa kerumitan teknis.
                            </p>
                            <p class="mb-8 text-lg font-light leading-relaxed text-gray-600">
                                Dengan fitur analitik MRR dan monitoring biaya AI, Anda dapat merencanakan skalabilitas bisnis dengan data akurat di tangan.
                            </p>
                            <Link :href="route('dashboard')" class="inline-flex items-center gap-2 font-bold text-indigo-600 group">
                                <span>Lihat Demo Sistem</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </Link>
                        </div>
                        <div class="ml-auto mr-auto w-full px-4 md:w-5/12 pt-12 md:pt-0">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-3xl blur-2xl opacity-20"></div>
                                <div class="relative flex min-w-0 flex-col break-words rounded-3xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
                                    <img alt="..." src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=2850&q=80" class="w-full object-cover h-64" />
                                    <div class="p-10">
                                        <h4 class="text-xl font-black uppercase tracking-tight mb-4 text-gray-900 text-center">Satu Panel, Semua Kendali</h4>
                                        <p class="text-md font-light text-gray-600 text-center leading-relaxed italic">
                                            "Operra membantu kami memangkas waktu approval biaya hingga 70%. Transparansi stok kini bukan lagi masalah bagi tim gudang kami."
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom CTA Section -->
            <section class="relative bg-gray-900 py-32 overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white"></path>
                    </svg>
                </div>
                <div class="container mx-auto px-4 relative z-10">
                    <div class="flex flex-wrap justify-center text-center">
                        <div class="w-full px-4 lg:w-8/12">
                            <h2 class="text-5xl font-black text-white uppercase tracking-tighter mb-6">Siap Optimalkan Bisnis Anda?</h2>
                            <p class="mb-12 text-xl leading-relaxed text-gray-400 font-light max-w-2xl mx-auto">
                                Bergabunglah dengan puluhan startup yang telah menggunakan Operra untuk mengontrol operasional mereka secara cerdas.
                            </p>
                            <div class="flex flex-wrap justify-center gap-6">
                                <Link :href="route('register')" class="rounded-full bg-white px-10 py-5 font-bold text-gray-900 hover:bg-indigo-50 transition-all hover:-translate-y-1 shadow-2xl">
                                    Daftar Sekarang
                                </Link>
                                <a href="#features" class="rounded-full border-2 border-white/20 px-10 py-5 font-bold text-white hover:bg-white/10 transition-all hover:-translate-y-1">
                                    Pelajari Fitur
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="relative bg-white pt-20 pb-10 border-t border-gray-100">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="w-full md:w-4/12 px-4 mb-12 md:mb-0">
                        <div class="flex items-center gap-3 mb-6">
                            <ApplicationLogo class="h-10 w-auto" />
                            <span class="text-2xl font-black tracking-tighter text-gray-900">OPERRA</span>
                        </div>
                        <p class="text-gray-500 leading-relaxed font-light">
                            Sistem kendali operasional cerdas untuk pertumbuhan tanpa batas. Dirancang khusus untuk SME dan Startup modern.
                        </p>
                    </div>
                    <div class="w-full md:w-7/12 px-4">
                        <div class="flex flex-wrap items-top justify-end">
                            <div class="px-4 mb-8 md:mb-0">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Product</span>
                                <ul class="space-y-4">
                                    <li><Link class="text-gray-600 hover:text-indigo-600 font-medium transition-colors" :href="route('dashboard')">Dashboard</Link></li>
                                    <li><a class="text-gray-600 hover:text-indigo-600 font-medium transition-colors" href="#">Features</a></li>
                                    <li><a class="text-gray-600 hover:text-indigo-600 font-medium transition-colors" href="#">Pricing</a></li>
                                </ul>
                            </div>
                            <div class="px-4 ml-12">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-6">Company</span>
                                <ul class="space-y-4">
                                    <li><Link class="text-gray-600 hover:text-indigo-600 font-medium transition-colors" :href="route('login')">Login</Link></li>
                                    <li><Link class="text-gray-600 hover:text-indigo-600 font-medium transition-colors" :href="route('register')">Register</Link></li>
                                    <li><a class="text-gray-600 hover:text-indigo-600 font-medium transition-colors" href="https://hasanarofid.site" target="_blank">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-20 pt-10 border-t border-gray-100 flex flex-wrap items-center justify-between">
                    <div class="text-sm text-gray-400 font-medium">
                        © 2026 Operra System. All rights reserved.
                    </div>
                    <div class="text-sm text-gray-400 font-medium">
                        Developed by <a href="https://hasanarofid.site" class="text-indigo-600 hover:text-indigo-800" target="_blank">hasanarofid</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Menambahkan transisi halus */
.transition-all {
    transition: all 0.3s ease;
}
</style>

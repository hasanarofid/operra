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
                            <ApplicationLogo class="h-8 w-auto invert brightness-200" />
                            <span>Operra</span>
                        </div>
                    </Link>
                </div>
                
                <div class="flex flex-grow items-center bg-white lg:bg-transparent lg:shadow-none" id="example-navbar-warning">
                    <ul class="flex list-none flex-col lg:ml-auto lg:flex-row">
                        <li v-if="canLogin" class="flex items-center">
                            <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                                class="mb-3 ml-3 rounded bg-white px-4 py-2 text-xs font-bold uppercase text-gray-800 shadow outline-none transition-all duration-150 ease-linear hover:shadow-md focus:outline-none active:bg-gray-100 lg:mb-0 lg:mr-1">
                                Dashboard
                            </Link>
                            <template v-else>
                                <Link :href="route('login')" class="px-3 py-4 text-xs font-bold uppercase text-white lg:py-2">
                                    Log In
                                </Link>
                                <Link v-if="canRegister" :href="route('register')" 
                                    class="mb-3 ml-3 rounded bg-white px-4 py-2 text-xs font-bold uppercase text-gray-800 shadow outline-none transition-all duration-150 ease-linear hover:shadow-md focus:outline-none active:bg-gray-100 lg:mb-0 lg:mr-1">
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
                <div class="absolute top-0 h-full w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1267&q=80');">
                    <span id="blackOverlay" class="absolute h-full w-full bg-black opacity-75"></span>
                </div>
                <div class="container relative mx-auto">
                    <div class="flex flex-wrap items-center">
                        <div class="ml-auto mr-auto w-full px-4 text-center lg:w-8/12">
                            <div class="pr-12">
                                <h1 class="text-5xl font-bold text-white uppercase tracking-wider">
                                    Operra Business System
                                </h1>
                                <p class="mt-4 text-lg text-gray-300">
                                    Sistem internal cerdas untuk UKM dan startup. Kelola pesanan, stok barang, hingga aktivitas staf dalam satu dasbor kendali yang kuat dan terukur.
                                </p>
                                <div class="mt-10">
                                    <Link :href="route('register')" class="rounded-full bg-indigo-600 px-8 py-4 font-bold text-white shadow-lg transition-all hover:bg-indigo-700">
                                        Mulai Sekarang
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
            <section class="-mt-24 bg-gray-300 pb-20">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap">
                        <div class="w-full px-4 pt-6 text-center md:w-4/12 lg:pt-12">
                            <div class="relative mb-8 flex min-w-0 flex-col break-words rounded-lg bg-white shadow-lg">
                                <div class="flex-auto px-4 py-5">
                                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-400 p-3 text-center text-white shadow-lg">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    </div>
                                    <h6 class="text-xl font-bold">Orders Tracking</h6>
                                    <p class="mb-4 mt-2 text-gray-600 italic">
                                        Pantau alur pesanan dari pending hingga selesai secara real-time untuk kepuasan pelanggan maksimal.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full px-4 text-center md:w-4/12">
                            <div class="relative mb-8 flex min-w-0 flex-col break-words rounded-lg bg-white shadow-lg">
                                <div class="flex-auto px-4 py-5">
                                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-indigo-400 p-3 text-center text-white shadow-lg">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    <h6 class="text-xl font-bold">Inventory Management</h6>
                                    <p class="mb-4 mt-2 text-gray-600 italic">
                                        Sistem stok otomatis dengan peringatan stok menipis, mencegah bottlenecks dalam operasional Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full px-4 pt-6 text-center md:w-4/12">
                            <div class="relative mb-8 flex min-w-0 flex-col break-words rounded-lg bg-white shadow-lg">
                                <div class="flex-auto px-4 py-5">
                                    <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-full bg-green-400 p-3 text-center text-white shadow-lg">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    </div>
                                    <h6 class="text-xl font-bold">Staff Performance</h6>
                                    <p class="mb-4 mt-2 text-gray-600 italic">
                                        Log aktivitas transparan untuk memantau produktivitas tim dan pengambilan keputusan berbasis fakta.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-32 flex flex-wrap items-center">
                        <div class="ml-auto mr-auto w-full px-4 md:w-5/12">
                            <div class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 p-3 text-center text-indigo-600 shadow-lg">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            </div>
                            <h3 class="mb-2 text-3xl font-semibold leading-normal">
                                Dirancang untuk Pertumbuhan
                            </h3>
                            <p class="mb-4 mt-4 text-lg font-light leading-relaxed text-gray-700 italic">
                                Operra bukan sekadar dasbor, melainkan sistem saraf pusat bisnis Anda. Kami memahami kebutuhan SME untuk memiliki kontrol penuh tanpa kerumitan teknis.
                            </p>
                            <p class="mb-4 mt-0 text-lg font-light leading-relaxed text-gray-700 italic">
                                Dengan fitur analitik MRR dan monitoring biaya AI, Anda dapat merencanakan skalabilitas bisnis dengan data akurat di tangan.
                            </p>
                            <Link :href="route('dashboard')" class="mt-8 font-bold text-gray-800 transition-all hover:text-indigo-600">
                                Lihat Demo Sistem
                            </Link>
                        </div>
                        <div class="ml-auto mr-auto w-full px-4 md:w-4/12">
                            <div class="relative mb-6 flex min-w-0 flex-col break-words rounded-lg bg-indigo-600 shadow-lg shadow-indigo-200">
                                <img alt="..." src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1051&q=80" class="w-full rounded-t-lg align-middle" />
                                <blockquote class="relative mb-4 p-8">
                                    <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 583 95" class="absolute left-0 block w-full" style="height: 95px; top: -94px;">
                                        <polygon points="-30,95 583,95 583,65" class="fill-current text-indigo-600"></polygon>
                                    </svg>
                                    <h4 class="text-xl font-bold text-white uppercase">Satu Panel, Semua Kendali</h4>
                                    <p class="mt-2 text-md font-light text-white italic">
                                        "Operra membantu kami memangkas waktu approval biaya hingga 70%. Transparansi stok kini bukan lagi masalah bagi tim gudang kami."
                                    </p>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom CTA Section -->
            <section class="relative bg-gray-900 py-20">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center text-center">
                        <div class="w-full px-4 lg:w-6/12">
                            <h2 class="text-4xl font-bold text-white uppercase">Siap Optimalkan Bisnis Anda?</h2>
                            <p class="mb-4 mt-4 text-lg leading-relaxed text-gray-500 italic">
                                Bergabunglah dengan puluhan startup yang telah menggunakan Operra untuk mengontrol operasional mereka secara cerdas.
                            </p>
                            <div class="mt-10 flex justify-center gap-4">
                                <Link :href="route('register')" class="rounded-lg bg-white px-6 py-3 font-bold text-gray-800 hover:bg-gray-100 transition-all">
                                    Daftar Sekarang
                                </Link>
                                <a href="#features" class="rounded-lg border border-gray-700 px-6 py-3 font-bold text-white hover:bg-gray-800 transition-all">
                                    Pelajari Fitur
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="relative bg-gray-300 pb-6 pt-8">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap text-center lg:text-left">
                    <div class="w-full px-4 lg:w-6/12">
                        <h4 class="text-3xl font-semibold">Operra Business System</h4>
                        <h5 class="mb-2 mt-0 text-lg text-gray-700 italic">
                            Sistem kendali operasional cerdas untuk pertumbuhan tanpa batas.
                        </h5>
                        <div class="mt-6 flex justify-center lg:justify-start gap-4">
                            <button class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-indigo-400 shadow-lg outline-none transition-transform hover:scale-110">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </button>
                            <button class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-red-600 shadow-lg outline-none transition-transform hover:scale-110">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="w-full px-4 lg:w-6/12">
                        <div class="mb-6 flex flex-wrap items-top">
                            <div class="ml-auto w-full px-4 lg:w-4/12">
                                <span class="mb-2 block text-sm font-bold uppercase text-gray-600">Links</span>
                                <ul class="list-none">
                                    <li><Link class="block pb-2 text-sm font-semibold text-gray-700 hover:text-gray-900" :href="route('dashboard')">Dashboard</Link></li>
                                    <li><Link class="block pb-2 text-sm font-semibold text-gray-700 hover:text-gray-900" :href="route('login')">Login</Link></li>
                                    <li><Link class="block pb-2 text-sm font-semibold text-gray-700 hover:text-gray-900" :href="route('register')">Register</Link></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-6 border-gray-400" />
                <div class="flex flex-wrap items-center justify-center md:justify-between">
                    <div class="mx-auto w-full px-4 text-center md:w-4/12">
                        <div class="py-1 text-sm font-semibold text-gray-600">
                            © 2026 Operra System. 
                            <span class="block text-xs mt-1">Developed by <a href="https://hasanarofid.site" class="text-indigo-600 hover:text-indigo-800" target="_blank">hasanarofid</a></span>
                        </div>
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

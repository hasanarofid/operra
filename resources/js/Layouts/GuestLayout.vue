<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#f8fafc] relative overflow-hidden font-sans antialiased">
        <!-- Background Elements matching Welcome page -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-operra-50 rounded-full blur-[100px] -mr-44 -mt-44 opacity-60"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-cyan-50 rounded-full blur-[100px] -ml-44 -mb-44 opacity-50"></div>
        </div>

        <div class="z-10 flex flex-col items-center">
            <Link href="/">
                <div class="p-4 bg-white rounded-[32px] shadow-2xl shadow-operra-500/10 border border-gray-100 mb-8 hover:scale-105 transition-transform">
                    <ApplicationLogo class="h-14 w-auto" />
                </div>
            </Link>
        </div>

        <div class="z-10 w-full sm:max-w-md mt-6 px-8 md:px-12 py-12 md:py-16 bg-white border border-gray-100 shadow-2xl shadow-operra-600/5 overflow-hidden rounded-[40px] md:rounded-[60px]">
            <slot />
        </div>

        <div class="z-10 mt-12 text-gray-300 text-[10px] font-black uppercase tracking-[0.3em]">
            © 2026 <span class="text-operra-500">OPERRA</span> Omnichannel
        </div>
    </div>
</template>

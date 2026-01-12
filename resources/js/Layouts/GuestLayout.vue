<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-950 relative overflow-hidden">
        <!-- Background Elements matching Welcome page -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-operra-600/10 via-gray-950 to-gray-950"></div>
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-operra-600/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-600/10 rounded-full blur-[120px]"></div>
        </div>

        <div class="z-10 flex flex-col items-center">
            <Link href="/">
                <div class="p-4 bg-white/5 rounded-3xl backdrop-blur-xl border border-white/10 mb-8 hover:scale-105 transition-transform">
                    <ApplicationLogo class="h-16 w-16 fill-current text-white" />
                </div>
            </Link>
        </div>

        <div class="z-10 w-full sm:max-w-md mt-6 px-8 py-10 bg-white/5 backdrop-blur-2xl border border-white/10 shadow-2xl overflow-hidden sm:rounded-[40px]">
            <slot />
        </div>

        <div class="z-10 mt-8 text-gray-500 text-[10px] font-black uppercase tracking-widest">
            © 2026 OPERRA SYSTEM
        </div>
    </div>
</template>

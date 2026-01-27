<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        503: 'Service Unavailable',
        500: 'Server Error',
        404: 'Page Not Found',
        403: 'Forbidden',
        419: 'Page Expired',
        429: 'Too Many Requests',
    }[props.status];
});

const description = computed(() => {
    return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
        419: 'Your session has expired. Please go back and refresh the page.',
        429: 'Too many requests. Please slow down and try again later.',
    }[props.status];
});
</script>

<template>
    <Head :title="title" />
    
    <div class="fixed inset-0 flex items-center justify-center bg-gray-900 overflow-hidden text-center p-4">
        <!-- Background Elements -->
         <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-operra-600/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-operra-600/20 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-600/20 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative z-10 max-w-xl w-full">
            <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-white/5 border border-white/10 mb-8 backdrop-blur-xl shadow-2xl relative overflow-hidden group">
                 <div class="absolute inset-0 bg-gradient-to-br from-operra-600/20 to-purple-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                 <h1 class="text-5xl font-black text-white/90 relative z-10">{{ status }}</h1>
            </div>

            <h2 class="text-4xl md:text-5xl font-black text-white mb-6 uppercase tracking-tighter">{{ title }}</h2>
            <p class="text-gray-400 text-lg md:text-xl font-medium mb-12 max-w-lg mx-auto leading-relaxed">
                {{ description }}
            </p>

            <Link href="/" class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-operra-600 to-blue-600 hover:from-operra-500 hover:to-blue-500 text-white font-black uppercase tracking-widest text-sm shadow-xl shadow-operra-600/30 transform hover:-translate-y-1 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </Link>
        </div>
    </div>
</template>

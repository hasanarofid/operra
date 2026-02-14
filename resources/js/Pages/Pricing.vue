<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    pricingPlans: Array,
});

const isNavScrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        isNavScrolled.value = window.scrollY > 20;
    });
});
</script>

<template>
    <Head>
        <title>Harga Operra - Paket CRM Terjangkau untuk UMKM</title>
    </Head>

    <div class="text-gray-900 antialiased bg-[#f8fafc]">
        <nav :class="[ 'fixed top-0 z-[100] w-full transition-all duration-500 px-6 py-4', isNavScrolled ? 'bg-white/80 backdrop-blur-xl border-b border-gray-100 py-3 shadow-sm' : 'bg-transparent' ]">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-3">
                    <img src="/images/operra_system.png" alt="Operra Logo" class="h-10 md:h-16 w-auto" />
                </Link>
                <div class="hidden lg:flex items-center gap-10 text-sm font-semibold text-gray-600">
                    <Link :href="route('features')" class="hover:text-operra-600 transition-colors">Fitur</Link>
                    <Link :href="route('solutions')" class="hover:text-operra-600 transition-colors">Solusi</Link>
                    <Link :href="route('pricing')" class="text-operra-600">Harga</Link>
                </div>
                <div class="flex items-center gap-4">
                    <Link :href="route('login')" class="text-sm font-bold text-gray-600 hover:text-operra-600 transition-colors px-4">Masuk</Link>
                    <Link :href="route('register')" class="rounded-full bg-operra-600 px-6 py-2.5 text-sm font-bold text-white shadow-xl shadow-operra-600/20 hover:bg-operra-700 hover:-translate-y-0.5 transition-all">Daftar Gratis</Link>
                </div>
            </div>
        </nav>

        <main class="pt-32 pb-24">
            <div class="max-w-7xl mx-auto px-6">
                 <div class="text-center mb-20">
                    <h1 class="text-xs font-bold uppercase tracking-[0.3em] text-operra-600 mb-4">Rencana Harga</h1>
                    <h2 class="text-4xl md:text-6xl font-black text-gray-900 tracking-tight">Investasi Murah untuk <span class="text-operra-600">Hasil Mewah</span></h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div 
                        v-for="plan in pricingPlans" 
                        :key="plan.id"
                        :class="plan.is_popular ? 'bg-operra-600 shadow-2xl scale-105 z-10' : 'bg-white border border-gray-100'"
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
                            class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm text-center transition-all shadow-sm"
                        >
                            {{ plan.cta_text }}
                        </Link>
                        <Link 
                            v-else
                            :href="route('solutions')"
                            :class="plan.is_popular ? 'bg-white text-operra-600 hover:bg-gray-50' : 'bg-gray-100 text-gray-900 hover:bg-gray-200'" 
                            class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm text-center transition-all shadow-sm"
                        >
                            {{ plan.cta_text }}
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-gray-950 py-12 border-t border-gray-900 text-center text-gray-500 text-xs font-black uppercase tracking-widest">
            © 2026 Operra Omnichannel CRM
        </footer>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { ref } from 'vue';

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
</script>

<template>
    <Head title="Operra - Multi-CRM & WhatsApp Blast Solution" />

    <div class="text-gray-800 antialiased bg-gray-950">
        <!-- Navbar -->
        <nav class="fixed top-0 z-[100] w-full px-4 py-6 bg-gray-950/50 backdrop-blur-lg border-b border-white/5">
            <div class="container mx-auto flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-2 text-white">
                    <ApplicationLogo class="h-8 md:h-10 w-auto" />
                    <span class="text-xl md:text-2xl font-black tracking-tighter uppercase">OPERRA</span>
                </Link>
                
                <div class="hidden md:flex items-center gap-8 text-sm font-bold uppercase text-gray-400 tracking-widest">
                    <a href="#pricing" class="hover:text-white transition-colors">Pricing</a>
                    <a href="#custom" class="hover:text-white transition-colors">Custom Solution</a>
                </div>

                <div class="flex items-center gap-4">
                    <template v-if="canLogin">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" 
                            class="rounded-full bg-operra-600 px-6 py-2 text-xs font-bold uppercase text-white shadow-xl transition-all hover:bg-operra-700">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="hidden md:block text-xs font-bold uppercase text-white hover:text-operra-200 transition-colors">
                                Log In
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" 
                                class="rounded-full bg-white px-6 py-2 text-xs font-bold uppercase text-gray-950 shadow-xl transition-all hover:bg-gray-100">
                                Get Started
                            </Link>
                        </template>
                    </template>
                </div>
            </div>
        </nav>

        <main>
            <!-- Hero Section -->
            <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <div class="absolute inset-0 bg-gradient-to-b from-operra-600/20 via-gray-950 to-gray-950"></div>
                    <div class="absolute top-1/4 -left-20 w-96 h-96 bg-operra-600/30 rounded-full blur-[120px]"></div>
                    <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px]"></div>
                </div>

                <div class="container relative z-10 mx-auto px-4 text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 text-operra-400 text-xs font-bold uppercase tracking-widest mb-8">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-operra-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-operra-500"></span>
                        </span>
                        The Next Generation Multi-CRM
                    </div>
                    <h1 class="text-5xl md:text-8xl font-black text-white uppercase tracking-tighter leading-[0.9] mb-8">
                        Scale Your Business <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-operra-400 via-indigo-400 to-cyan-400">With Intelligent CRM</span>
                    </h1>
                    <p class="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto font-medium leading-relaxed mb-12">
                        Dari WhatsApp Blast hingga Sales Pipeline. Satu platform, semua jenis CRM yang bisnis Anda butuhkan untuk tumbuh lebih cepat.
                    </p>
                    <div class="flex flex-col md:flex-row justify-center items-center gap-6">
                        <Link :href="route('register')" class="w-full md:w-auto rounded-full bg-operra-600 px-12 py-5 text-lg font-black text-white shadow-2xl shadow-operra-600/20 hover:bg-operra-700 hover:-translate-y-1 transition-all">
                            DAFTAR SEKARANG
                        </Link>
                        <a href="#pricing" class="w-full md:w-auto rounded-full bg-white/5 backdrop-blur-md px-12 py-5 text-lg font-black text-white border border-white/10 hover:bg-white/10 transition-all">
                            LIHAT HARGA
                        </a>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section id="pricing" class="py-32 bg-gray-950 relative overflow-hidden">
                <div class="container mx-auto px-4 relative z-10">
                    <div class="text-center mb-20">
                        <h2 class="text-4xl md:text-6xl font-black text-white uppercase tracking-tighter mb-4">Pilih Paket Sesuai <span class="text-operra-500">Kebutuhan</span></h2>
                        <p class="text-gray-400 font-medium">Investasi terbaik untuk pertumbuhan bisnis jangka panjang Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                        <div 
                            v-for="plan in pricingPlans" 
                            :key="plan.id"
                            :class="plan.is_popular ? 'bg-operra-600 shadow-2xl shadow-operra-600/20 scale-105 z-10 border-transparent' : 'bg-white/5 border-white/10'"
                            class="relative rounded-[40px] p-10 border transition-all hover:-translate-y-2 flex flex-col"
                        >
                            <div v-if="plan.badge" class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-white text-gray-900 text-[10px] font-black uppercase tracking-widest whitespace-nowrap shadow-xl">
                                {{ plan.badge }}
                            </div>

                            <div class="mb-8">
                                <h3 class="text-2xl font-black text-white uppercase mb-2">{{ plan.name }}</h3>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-white text-4xl font-black">
                                        {{ plan.price > 0 ? 'Rp' + (plan.price / 1000) + 'k' : 'Custom' }}
                                    </span>
                                    <span v-if="plan.price > 0" class="text-white/60 text-sm font-bold">/ bulan</span>
                                </div>
                            </div>

                            <ul class="space-y-4 mb-10 flex-1">
                                <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-3 text-white text-sm font-medium">
                                    <svg class="w-5 h-5 shrink-0 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    {{ feature }}
                                </li>
                            </ul>

                            <button :class="plan.is_popular ? 'bg-white text-operra-600' : 'bg-white/10 text-white hover:bg-white/20'" class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm transition-all">
                                {{ plan.cta_text }}
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Custom Request Section -->
            <section id="custom" class="py-32 bg-gray-900 relative">
                <div class="container mx-auto px-4">
                    <div class="max-w-5xl mx-auto bg-gray-950 rounded-[50px] p-8 md:p-16 border border-white/5 shadow-2xl flex flex-wrap items-center">
                        <div class="w-full lg:w-1/2 mb-12 lg:mb-0 lg:pr-12">
                            <h2 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-6">Butuh CRM yang <br/> <span class="text-operra-500">Sangat Spesifik?</span></h2>
                            <p class="text-gray-400 text-lg leading-relaxed mb-8">
                                Kami berpengalaman membangun CRM kustom untuk berbagai industri. Ceritakan kebutuhan unik bisnis Anda, dan biarkan kami membangun solusinya.
                            </p>
                            <div class="space-y-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-operra-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold">On-Premise Ready</h4>
                                        <p class="text-gray-500 text-sm">Server di kantor Anda sendiri.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-operra-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4m4-4l-4 4M6 16l-4-4 4-4"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold">Custom Development</h4>
                                        <p class="text-gray-500 text-sm">Fitur unik sesuai alur bisnis Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full lg:w-1/2">
                            <form @submit.prevent="submit" class="bg-white/5 p-8 md:p-10 rounded-[40px] border border-white/10">
                                <div v-if="showSuccess" class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-2xl text-green-400 text-sm font-bold text-center">
                                    Permintaan terkirim! Tim kami akan segera menghubungi Anda.
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <input v-model="form.name" type="text" placeholder="Nama Anda" class="bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all" required />
                                    <input v-model="form.company_name" type="text" placeholder="Nama Perusahaan" class="bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all" required />
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <input v-model="form.email" type="email" placeholder="Email Bisnis" class="bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all" required />
                                    <input v-model="form.phone" type="text" placeholder="Nomor WA (e.g 0812...)" class="bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all" required />
                                </div>
                                <select v-model="form.business_type" class="w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all mb-4">
                                    <option value="UMKM">UMKM / Retail</option>
                                    <option value="Enterprise">Perusahaan Menengah / Besar</option>
                                    <option value="Government">Instansi / Pemerintah</option>
                                </select>
                                <textarea v-model="form.message" placeholder="Ceritakan kebutuhan CRM Anda..." rows="4" class="w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all mb-6" required></textarea>
                                
                                <button type="submit" :disabled="form.processing" class="w-full py-5 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/20 disabled:opacity-50">
                                    KIRIM REQUEST KUSTOM
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-950 pt-20 pb-10 border-t border-white/5">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap items-center justify-between text-center md:text-left">
                    <div class="w-full md:w-1/3 mb-12 md:mb-0">
                        <Link :href="route('welcome')" class="inline-flex items-center gap-3 mb-6">
                            <ApplicationLogo class="h-10 w-auto" />
                            <span class="text-2xl font-black tracking-tighter text-white">OPERRA</span>
                        </Link>
                        <p class="text-gray-500 font-medium leading-relaxed">
                            Pusat Kendali Bisnis Modern. <br/> Solusi Multi-CRM Terintegrasi WhatsApp.
                        </p>
                    </div>
                    <div class="w-full md:w-2/3 flex flex-wrap justify-center md:justify-end gap-12 md:gap-24">
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-[0.2em] text-operra-500 mb-6">Portal</span>
                            <ul class="space-y-4 text-sm font-bold text-gray-400">
                                <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                                <li><a href="#custom" class="hover:text-white transition-colors">Custom Solution</a></li>
                            </ul>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-[0.2em] text-operra-500 mb-6">Akses</span>
                            <ul class="space-y-4 text-sm font-bold text-gray-400">
                                <li><Link :href="route('login')" class="hover:text-white transition-colors">Login</Link></li>
                                <li><Link :href="route('register')" class="hover:text-white transition-colors">Daftar</Link></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mt-20 pt-10 border-t border-white/5 flex flex-wrap items-center justify-between text-gray-500 text-[10px] font-black uppercase tracking-widest">
                    <div>© 2026 Operra Multi-CRM System</div>
                    <div>Powered by <a href="https://hasanarofid.site" class="text-white">HasanArofid</a></div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}
</style>

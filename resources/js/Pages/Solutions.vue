<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

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
        <title>Solusi Kustom Operra - CRM untuk Enterprise & Instansi</title>
    </Head>

    <div class="text-gray-900 antialiased bg-[#f8fafc]">
        <nav :class="[ 'fixed top-0 z-[100] w-full transition-all duration-500 px-6 py-4', isNavScrolled ? 'bg-white/80 backdrop-blur-xl border-b border-gray-100 py-3 shadow-sm' : 'bg-transparent' ]">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <Link :href="route('welcome')" class="flex items-center gap-3">
                    <img src="/images/operra_system.png" alt="Operra Logo" class="h-10 md:h-16 w-auto" />
                </Link>
                <div class="hidden lg:flex items-center gap-10 text-sm font-semibold text-gray-600">
                    <Link :href="route('features')" class="hover:text-operra-600 transition-colors">Fitur</Link>
                    <Link :href="route('solutions')" class="text-operra-600">Solusi</Link>
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
                <div class="max-w-6xl mx-auto bg-gray-950 rounded-[60px] p-8 md:p-20 border border-gray-800 shadow-2xl flex flex-wrap items-center overflow-hidden relative">
                    <div class="w-full lg:w-1/2 mb-12 lg:mb-0 lg:pr-16 relative z-10">
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight mb-8">Solusi Kustom untuk <span class="text-operra-400">Bisnis Besar</span></h1>
                        <p class="text-gray-400 text-lg leading-relaxed mb-10 font-medium">Bangun CRM eksklusif dengan fitur unik, integrasi API, dan server On-Premise.</p>
                        <div class="space-y-8">
                            <div class="flex items-center gap-6 group">
                                <div class="h-14 w-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-operra-400">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <h4 class="text-white font-bold text-lg">On-Premise Ready</h4>
                            </div>
                            <div class="flex items-center gap-6 group">
                                <div class="h-14 w-14 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-operra-400">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4m4-4l-4 4M6 16l-4-4 4-4"></path></svg>
                                </div>
                                <h4 class="text-white font-bold text-lg">Integrasi Menyeluruh</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-1/2 relative z-10">
                        <form @submit.prevent="submit" class="bg-white p-8 md:p-12 rounded-[50px] shadow-2xl relative">
                            <div v-if="showSuccess" class="mb-8 p-4 bg-green-50 text-green-700 text-sm font-bold text-center border border-green-100 rounded-2xl">Permintaan terkirim! Tim kami akan segera menghubungi Anda.</div>
                            <div class="space-y-4 mb-8">
                                <input v-model="form.name" type="text" placeholder="Nama Lengkap" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-sm" required />
                                <input v-model="form.company_name" type="text" placeholder="Nama Perusahaan" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-sm" required />
                                <input v-model="form.email" type="email" placeholder="Email Bisnis" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-sm" required />
                                <input v-model="form.phone" type="text" placeholder="Nomor WhatsApp" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-sm" required />
                                <select v-model="form.business_type" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-sm font-medium">
                                    <option value="UMKM">UMKM / Retail</option>
                                    <option value="Enterprise">Enterprise / Besar</option>
                                    <option value="Government">Instansi Pemerintah</option>
                                </select>
                                <textarea v-model="form.message" placeholder="Kebutuhan Anda..." rows="3" class="w-full bg-gray-50 border-gray-100 rounded-2xl p-4 text-sm" required></textarea>
                            </div>
                            <button type="submit" :disabled="form.processing" class="w-full py-5 rounded-2xl bg-gray-950 text-white font-black uppercase tracking-widest text-sm disabled:opacity-50">Dapatkan Proposal</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-gray-950 py-12 border-t border-gray-900 text-center text-gray-500 text-xs font-black uppercase tracking-widest">
            © 2026 Operra Omnichannel CRM
        </footer>
    </div>
</template>

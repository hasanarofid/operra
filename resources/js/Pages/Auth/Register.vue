<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const props = defineProps({
    plan: String,
});

const form = useForm({
    name: '',
    email: '',
    company_name: '',
    password: '',
    password_confirmation: '',
    plan: props.plan || '',
});

const planDetails = {
    'starter': {
        name: 'Starter (UMKM)',
        price: 'Rp 149k',
        features: [
            'Email Fallback (100/mo)',
            'Notifikasi Stok Rendah',
            'Shared Inbox (2 Agent)',
            'Portal Pelacakan Pesanan',
            'Laporan Harian Penjualan'
        ]
    },
    'business-pro': {
        name: 'Business Pro',
        price: 'Rp 399k',
        features: [
            'Portal Pelacakan Publik',
            'Broadcasting Email Fallback',
            'Unlimited Agents',
            'Integrasi API Omnichannel',
            'Analitik Penjualan Lanjutan',
            'Priority Support'
        ]
    }
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

</script>

<template>
    <GuestLayout>
        <Head title="Daftar Akun" />

        <div class="mb-12 text-center px-1">
            <!-- Plan Info Card -->
            <div v-if="form.plan && planDetails[form.plan]" class="mb-10 p-6 rounded-[40px] bg-operra-50 border border-operra-100 text-left relative overflow-hidden shadow-sm">
                <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                    <svg class="w-20 h-20 text-operra-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99z"/></svg>
                </div>
                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div class="px-4 py-1.5 bg-operra-600 rounded-full text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-operra-600/20">
                        Paket Terpilih
                    </div>
                    <button type="button" @click="form.plan = ''" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-operra-600 transition-colors cursor-pointer p-1">
                        Ubah Paket
                    </button>
                </div>
                <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tighter mb-1">{{ planDetails[form.plan].name }}</h3>
                <div class="text-operra-600 font-bold mb-4">{{ planDetails[form.plan].price }} / bulan</div>
                
                <ul class="space-y-2">
                    <li v-for="feature in planDetails[form.plan].features" :key="feature" class="flex items-center text-xs text-gray-500 font-semibold">
                        <svg class="w-4 h-4 text-operra-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        {{ feature }}
                    </li>
                </ul>
            </div>

            <div v-else-if="form.plan" class="inline-block px-5 py-2 rounded-full bg-operra-50 text-operra-600 text-[10px] font-black uppercase tracking-widest mb-6 border border-operra-100">
                Paket: {{ form.plan.replace('-', ' ') }}
            </div>

            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4 leading-none">Daftar Akun Baru</h2>
            <p class="text-gray-500 text-sm font-medium">Mulai kelola bisnis Anda dengan Operra CRM.</p>
        </div>


        <form @submit.prevent="submit" class="space-y-6">
            <!-- Plan Selection if Empty -->
            <div v-if="!form.plan" class="p-6 rounded-[32px] bg-gray-50 border border-gray-100 mb-10 shadow-inner">
                <InputLabel value="Pilih Paket Layanan" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-5" />
                <div class="grid grid-cols-2 gap-4">
                    <button 
                        type="button"
                        v-for="(details, slug) in planDetails" 
                        :key="slug"
                        @click="form.plan = slug"
                        :class="form.plan === slug ? 'bg-white border-operra-500 text-operra-600 shadow-xl shadow-operra-600/10' : 'bg-white border-gray-100 text-gray-400 hover:border-operra-300'"
                        class="p-5 rounded-2xl border text-left transition-all group"
                    >
                        <div class="text-[10px] font-black uppercase tracking-tight mb-2 group-hover:text-operra-600 transition-colors">{{ details.name }}</div>
                        <div class="text-sm font-black text-gray-900">{{ details.price }}</div>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.plan" />
            </div>

            <div class="space-y-6">
                <div>
                    <InputLabel for="name" value="Nama Lengkap" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                    <TextInput
                        id="name"
                        type="text"
                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Contoh: Hasan"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="company_name" value="Nama Bisnis / Perusahaan" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                    <TextInput
                        id="company_name"
                        type="text"
                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                        v-model="form.company_name"
                        required
                        placeholder="Contoh: Operra Digital"
                    />
                    <InputError class="mt-2" :message="form.errors.company_name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email Bisnis" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="nama@bisnis.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="password" value="Kata Sandi" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                        <div class="relative">
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full pr-14 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-6 text-gray-400 hover:text-operra-600 transition-colors"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>
                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Sandi" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                        <div class="relative">
                            <TextInput
                                id="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="block w-full pr-14 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 flex items-center pr-6 text-gray-400 hover:text-operra-600 transition-colors"
                            >
                                <svg v-if="!showConfirmPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>
            </div>


            <div class="pt-8">
                <button
                    class="w-full py-6 rounded-3xl bg-operra-600 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/30 disabled:opacity-50 active:scale-[0.98]"
                    :disabled="form.processing"
                >
                    Daftar Sekarang
                </button>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-50 text-center">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-4">Sudah memiliki akun?</p>
                <Link
                    :href="route('login')"
                    class="mt-2 inline-block px-10 py-4 rounded-2xl bg-[#f8fafc] text-sm font-black text-gray-900 border border-gray-100 hover:bg-white hover:border-operra-500 hover:text-operra-600 transition-all uppercase tracking-widest"
                >
                    Masuk ke Sistem
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

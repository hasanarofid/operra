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
            '1 Akun WhatsApp',
            'Manajemen Lead Dasar',
            'Shared Inbox (2 Agent)',
            'Follow-up Otomatis',
            'Laporan Harian via WA'
        ]
    },
    'business-pro': {
        name: 'Business Pro',
        price: 'Rp 399k',
        features: [
            'Multi-Account WhatsApp (Up to 5)',
            'Sales Pipeline & Deal Tracking',
            'Unlimited Agents',
            'WhatsApp Blast (Scheduler)',
            'API Integration Ready',
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

        <div class="mb-10 text-center px-2">
            <!-- Plan Info Card -->
            <div v-if="form.plan && planDetails[form.plan]" class="mb-8 p-4 rounded-3xl bg-operra-600/10 border border-operra-500/30 text-left relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                    <svg class="w-16 h-16 text-operra-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99z"/></svg>
                </div>
                <div class="flex justify-between items-start mb-2 relative z-10">
                    <div class="px-3 py-1 bg-operra-600 rounded-full text-[9px] font-black uppercase tracking-widest text-white shadow-lg">
                        Paket Terpilih
                    </div>
                    <button type="button" @click="form.plan = ''" class="text-[9px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-colors cursor-pointer p-1">
                        Ubah Paket
                    </button>
                </div>
                <h3 class="text-xl font-black text-white uppercase tracking-tighter">{{ planDetails[form.plan].name }}</h3>
                <div class="text-operra-400 font-bold mb-3">{{ planDetails[form.plan].price }} / bulan</div>
                
                <ul class="space-y-1.5">
                    <li v-for="feature in planDetails[form.plan].features" :key="feature" class="flex items-center text-[10px] text-gray-400 font-medium">
                        <svg class="w-3 h-3 text-operra-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        {{ feature }}
                    </li>
                </ul>
            </div>

            <div v-else-if="form.plan" class="inline-block px-4 py-1.5 rounded-full bg-operra-600/20 text-operra-400 text-[10px] font-black uppercase tracking-widest mb-4 border border-operra-500/30">
                Paket: {{ form.plan.replace('-', ' ') }}
            </div>

            <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-2 leading-none">Daftar Akun Baru</h2>
            <p class="text-gray-400 text-sm font-medium">Mulai kelola bisnis Anda dengan Operra CRM.</p>
        </div>


        <form @submit.prevent="submit" class="space-y-5">
            <!-- Plan Selection if Empty -->
            <div v-if="!form.plan" class="p-5 rounded-3xl bg-white/5 border border-white/10 mb-8">
                <InputLabel value="Pilih Paket Layanan" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-4" />
                <div class="grid grid-cols-2 gap-3">
                    <button 
                        type="button"
                        v-for="(details, slug) in planDetails" 
                        :key="slug"
                        @click="form.plan = slug"
                        :class="form.plan === slug ? 'bg-operra-600 border-operra-500 text-white' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10'"
                        class="p-4 rounded-2xl border text-left transition-all"
                    >
                        <div class="text-[10px] font-black uppercase tracking-tight mb-1">{{ details.name }}</div>
                        <div class="text-xs font-bold">{{ details.price }}</div>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.plan" />
            </div>

            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                <TextInput
                    id="name"
                    type="text"
                    class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Contoh: Hasan"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="company_name" value="Nama Bisnis / Perusahaan" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                <TextInput
                    id="company_name"
                    type="text"
                    class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                    v-model="form.company_name"
                    required
                    placeholder="Contoh: Operra Digital"
                />
                <InputError class="mt-2" :message="form.errors.company_name" />
            </div>

            <div>
                <InputLabel for="email" value="Email Bisnis" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="nama@bisnis.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="password" value="Kata Sandi" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                    <div class="relative">
                        <TextInput
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            class="block w-full pr-12 bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-white transition-colors"
                        >
                            <svg v-if="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Sandi" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                    <div class="relative">
                        <TextInput
                            id="password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            class="block w-full pr-12 bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-white transition-colors"
                        >
                            <svg v-if="!showConfirmPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>


            <div class="pt-4">
                <button
                    class="w-full py-5 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/20 disabled:opacity-50 active:scale-[0.98]"
                    :disabled="form.processing"
                >
                    DAFTAR SEKARANG
                </button>
            </div>

            <div class="mt-8 text-center border-t border-white/5 pt-6">
                <p class="text-gray-500 text-xs font-medium">Sudah memiliki akun?</p>
                <Link
                    :href="route('login')"
                    class="mt-2 inline-block text-sm font-black text-white hover:text-operra-400 transition-colors uppercase tracking-[0.2em]"
                >
                    MASUK KE SISTEM
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

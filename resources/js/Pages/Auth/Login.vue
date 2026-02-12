<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log In" />

        <div class="mb-12 text-center">
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">Pusat Kendali</h2>
            <p class="text-gray-500 text-sm font-medium">Masuk untuk mengelola omnichannel komunikasi Anda.</p>
        </div>

        <div v-if="status" class="mb-8 p-5 bg-operra-50 border border-operra-100 rounded-3xl text-operra-700 text-sm font-bold text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div>
                    <InputLabel for="email" value="Email Bisnis" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@perusahaan.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Kata Sandi" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                    <div class="relative">
                        <TextInput
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            class="block w-full pr-14 bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-6 text-gray-400 hover:text-operra-600 transition-colors"
                        >
                            <svg v-if="!showPassword" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div class="mt-8 flex items-center justify-between">
                <label class="flex items-center group cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded-lg border-gray-200 bg-gray-50 text-operra-600 focus:ring-operra-500/20" />
                    <span class="ms-3 text-[11px] font-black text-gray-400 group-hover:text-gray-600 transition-colors uppercase tracking-widest"
                        >Ingat Saya</span
                    >
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-[11px] font-black text-operra-600 hover:text-operra-700 transition-colors uppercase tracking-widest"
                >
                    Lupa Sandi?
                </Link>
            </div>

            <div class="mt-12">
                <button
                    class="w-full py-6 rounded-3xl bg-operra-600 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/30 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Masuk ke Sistem
                </button>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-50 text-center">
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-4">Belum memiliki akun?</p>
                <Link
                    :href="route('welcome') + '#pricing'"
                    class="inline-block px-10 py-4 rounded-2xl bg-[#f8fafc] text-sm font-black text-gray-900 border border-gray-100 hover:bg-white hover:border-operra-500 hover:text-operra-600 transition-all uppercase tracking-widest"
                >
                    Lihat Paket Harga
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

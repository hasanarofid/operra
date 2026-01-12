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

        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-2">Selamat Datang</h2>
            <p class="text-gray-400 text-sm font-medium">Masuk ke pusat kendali bisnis Anda.</p>
        </div>

        <div v-if="status" class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-2xl text-green-400 text-sm font-bold text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email Bisnis" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@perusahaan.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-6">
                <InputLabel for="password" value="Kata Sandi" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />

                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-12 bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-white transition-colors"
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

            <div class="mt-6 flex items-center justify-between">
                <label class="flex items-center group cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-white/10 bg-white/5 text-operra-600 focus:ring-operra-500" />
                    <span class="ms-2 text-xs font-bold text-gray-500 group-hover:text-gray-300 transition-colors uppercase tracking-wider"
                        >Ingat Saya</span
                    >
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs font-bold text-operra-500 hover:text-operra-400 transition-colors uppercase tracking-wider"
                >
                    Lupa Sandi?
                </Link>
            </div>

            <div class="mt-10">
                <button
                    class="w-full py-5 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/20 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    MASUK KE SISTEM
                </button>
            </div>

            <div class="mt-8 text-center">
                <p class="text-gray-500 text-xs font-medium">Belum memiliki akun?</p>
                <Link
                    :href="route('register')"
                    class="mt-2 inline-block text-sm font-black text-white hover:text-operra-400 transition-colors uppercase tracking-[0.2em]"
                >
                    HUBUNGI SALES KAMI
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Sandi" />

        <div class="mb-8 text-center">
            <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-2">Lupa Sandi?</h2>
            <p class="text-gray-400 text-sm font-medium leading-relaxed px-4">
                Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan tautan reset kata sandi.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-2xl text-green-400 text-sm font-bold text-center"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email Terdaftar" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-8">
                <button
                    class="w-full py-5 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/20 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    KIRIM TAUTAN RESET
                </button>
            </div>

            <div class="mt-8 text-center border-t border-white/5 pt-6">
                <Link
                    :href="route('login')"
                    class="text-sm font-black text-white hover:text-operra-400 transition-colors uppercase tracking-[0.2em]"
                >
                    KEMBALI KE LOGIN
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

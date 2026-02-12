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

        <div class="mb-12 text-center">
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">Lupa Sandi?</h2>
            <p class="text-gray-500 text-sm font-medium leading-relaxed px-4">
                Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan tautan reset kata sandi.
            </p>
        </div>

        <div v-if="status" class="mb-8 p-5 bg-operra-50 border border-operra-100 rounded-3xl text-operra-700 text-sm font-bold text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email Terdaftar" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-12">
                <button
                    class="w-full py-6 rounded-3xl bg-operra-600 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/30 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Kirim Tautan Reset
                </button>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-50 text-center">
                <Link
                    :href="route('login')"
                    class="inline-block px-10 py-4 rounded-2xl bg-[#f8fafc] text-sm font-black text-gray-900 border border-gray-100 hover:bg-white hover:border-operra-500 hover:text-operra-600 transition-all uppercase tracking-widest"
                >
                    Kembali ke Login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

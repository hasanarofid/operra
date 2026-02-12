<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="mb-12 text-center">
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">Konfirmasi Akses</h2>
            <p class="text-gray-500 text-sm font-medium leading-relaxed px-4">
                Ini adalah area aman. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Kata Sandi" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-12 flex justify-end">
                <button
                    class="w-full py-6 rounded-3xl bg-operra-600 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/30 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Konfirmasi
                </button>
            </div>
        </form>
    </GuestLayout>
</template>

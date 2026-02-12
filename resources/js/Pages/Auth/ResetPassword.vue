<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="mb-12 text-center">
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">Reset Kata Sandi</h2>
            <p class="text-gray-500 text-sm font-medium leading-relaxed px-4">
                Silakan masukkan kata sandi baru Anda untuk mengamankan akun.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
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

            <div>
                <InputLabel for="password" value="Kata Sandi Baru" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Sandi Baru" class="text-gray-400 font-black uppercase text-[10px] tracking-[0.2em] ml-1 mb-3" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="block w-full bg-gray-50 border-gray-100 rounded-2xl px-6 py-4 text-gray-900 font-bold focus:bg-white focus:border-operra-500 focus:ring-4 focus:ring-operra-500/10 transition-all placeholder:text-gray-300"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-12">
                <button
                    class="w-full py-6 rounded-3xl bg-operra-600 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/30 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Kata Sandi
                </button>
            </div>
        </form>
    </GuestLayout>
</template>

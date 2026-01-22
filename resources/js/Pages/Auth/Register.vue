<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showPassword = ref(false);

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
            <div v-if="form.plan" class="inline-block px-4 py-1.5 rounded-full bg-operra-600/20 text-operra-400 text-[10px] font-black uppercase tracking-widest mb-4 border border-operra-500/30">
                Paket: {{ form.plan.replace('-', ' ') }}
            </div>
            <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-2 leading-none">Daftar Akun Baru</h2>
            <p class="text-gray-400 text-sm font-medium">Mulai kelola bisnis Anda dengan Operra CRM.</p>
        </div>


        <form @submit.prevent="submit" class="space-y-5">
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
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div>
                    <InputLabel for="password_confirmation" value="Konfirmasi Sandi" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                    <TextInput
                        id="password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="flex items-center gap-2 mt-2">
                <button type="button" @click="showPassword = !showPassword" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest hover:text-white transition-colors">
                    {{ showPassword ? 'Sembunyikan' : 'Tampilkan' }} Kata Sandi
                </button>
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

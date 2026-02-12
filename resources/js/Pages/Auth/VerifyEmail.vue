<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-12 text-center">
            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">Verifikasi Email</h2>
            <p class="text-gray-500 text-sm font-medium leading-relaxed px-4">
                Terima kasih telah mendaftar! Harap verifikasi email Anda dengan mengeklik tautan yang baru saja kami kirimkan.
            </p>
        </div>

        <div
            class="mb-8 p-5 bg-operra-50 border border-operra-100 rounded-3xl text-operra-700 text-sm font-bold text-center"
            v-if="verificationLinkSent"
        >
            Tautan verifikasi baru telah dikirim ke alamat email Anda.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-12 space-y-6">
                <button
                    class="w-full py-6 rounded-3xl bg-operra-600 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/30 disabled:opacity-50 active:scale-[0.98]"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Kirim Ulang Email Verifikasi
                </button>

                <div class="text-center pt-4">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-[11px] font-black text-gray-400 hover:text-operra-600 transition-colors uppercase tracking-widest underline decoration-2 underline-offset-8"
                    >
                        Keluar (Logout)
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

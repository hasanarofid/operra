<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    business_type: 'UMKM',
    message: '',
});

const showSuccess = ref(false);

const submit = () => {
    form.post(route('request.custom'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showSuccess.value = true;
            // Kita tidak perlu redirect ke login karena ini request jasa
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Hubungi Kami" />

        <div class="mb-10 text-center px-2">
            <h2 class="text-3xl font-black text-white uppercase tracking-tighter mb-2 leading-none">Mulai CRM Anda</h2>
            <p class="text-gray-400 text-sm font-medium">Lengkapi data untuk mendapatkan penawaran terbaik dari tim kami.</p>
        </div>

        <div v-if="showSuccess" class="mb-8 p-6 bg-green-500/20 border border-green-500/50 rounded-[30px] text-green-400 text-sm font-bold text-center animate-bounce">
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Permintaan Terkirim!<br/>
            Tim kami akan segera menghubungi Anda melalui WhatsApp.
        </div>

        <form v-else @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                <TextInput
                    id="name"
                    type="text"
                    class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                    v-model="form.name"
                    required
                    autofocus
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <InputLabel for="email" value="Email Bisnis" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                        v-model="form.email"
                        required
                        placeholder="nama@bisnis.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div>
                    <InputLabel for="phone" value="Nomor WA" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                    <TextInput
                        id="phone"
                        type="text"
                        class="block w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                        v-model="form.phone"
                        required
                        placeholder="0812..."
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
            </div>

            <div>
                <InputLabel for="business_type" value="Tipe Bisnis" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                <select 
                    v-model="form.business_type" 
                    class="w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all"
                >
                    <option value="UMKM" class="bg-gray-900">UMKM / Retail</option>
                    <option value="Enterprise" class="bg-gray-900">Perusahaan Menengah / Besar</option>
                    <option value="Government" class="bg-gray-900">Instansi / Pemerintah</option>
                </select>
                <InputError class="mt-2" :message="form.errors.business_type" />
            </div>

            <div>
                <InputLabel for="message" value="Kebutuhan Anda" class="text-gray-400 font-bold uppercase text-[10px] tracking-widest ml-1 mb-2" />
                <textarea 
                    v-model="form.message" 
                    placeholder="Ceritakan sedikit tentang kebutuhan CRM Anda..." 
                    rows="3" 
                    class="w-full bg-white/5 border-white/10 rounded-2xl p-4 text-white focus:border-operra-500 focus:ring-0 transition-all text-sm"
                    required
                ></textarea>
                <InputError class="mt-2" :message="form.errors.message" />
            </div>

            <div class="pt-4">
                <button
                    class="w-full py-5 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest hover:bg-operra-700 transition-all shadow-xl shadow-operra-600/20 disabled:opacity-50 active:scale-[0.98]"
                    :disabled="form.processing"
                >
                    KIRIM PENAWARAN
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

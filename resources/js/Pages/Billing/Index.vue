<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    payments: Array,
    plan: Object,
    subscription_ends_at: String,
});

const form = useForm({
    months: 1,
    amount: props.plan?.price || 0,
    proof: null,
});

const calculateTotal = () => {
    form.amount = (props.plan?.price || 0) * form.months;
};

const submit = () => {
    form.post(route('billing.store'), {
        onSuccess: () => form.reset(),
    });
};

const daysLeft = computed(() => {
    if (!props.subscription_ends_at) return 0;
    const diff = new Date(props.subscription_ends_at) - new Date();
    return Math.ceil(diff / (1000 * 60 * 60 * 24));
});

</script>

<template>
    <Head title="Billing & Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            Billing & Pembayaran
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Status Langganan -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Status Langganan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Paket Aktif</div>
                            <div class="text-2xl font-black text-operra-600 dark:text-operra-400">{{ plan?.name }}</div>
                            <div class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(plan?.price) }} / bulan</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-1">Masa Aktif Berakhir</div>
                            <div class="text-2xl font-black" :class="daysLeft < 3 ? 'text-red-500' : 'text-gray-800 dark:text-white'">
                                {{ subscription_ends_at ? new Date(subscription_ends_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) : '-' }}
                            </div>
                            <div v-if="daysLeft > 0" class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ daysLeft }} Hari Lagi</div>
                            <div v-else class="text-sm font-bold text-red-500">Expired</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                             <div class="text-center">
                                <div class="text-sm text-gray-500 font-bold uppercase tracking-wider mb-2">Status Akun</div>
                                <span v-if="daysLeft > 0" class="px-4 py-2 rounded-full bg-green-100 text-green-700 font-black uppercase text-xs">
                                    ACTIVE
                                </span>
                                <span v-else class="px-4 py-2 rounded-full bg-red-100 text-red-700 font-black uppercase text-xs">
                                    SUSPENDED
                                </span>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Form Pembayaran -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Informasi Rekening Tujuan</h3>
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-xl border border-blue-100 dark:border-blue-800 text-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/2560px-Bank_Central_Asia.svg.png" class="h-8 mx-auto mb-4" alt="BCA">
                            <div class="text-sm text-gray-500 uppercase font-bold tracking-widest mb-1">Bank Central Asia (BCA)</div>
                            <div class="text-3xl font-black text-gray-800 dark:text-white mb-2 selection:bg-blue-200">4650499269</div>
                            <div class="text-sm font-bold text-gray-600 dark:text-gray-300 uppercase">a.n Akhmad Hasan Arofid</div>
                        </div>
                        <div class="mt-4 text-xs text-center text-gray-400">
                            Silakan transfer sesuai total tagihan, lalu upload bukti transfer di form berikut.
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Konfirmasi Pembayaran</h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Durasi Perpanjangan (Bulan)</label>
                                <select v-model="form.months" @change="calculateTotal" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 focus:border-operra-500 focus:ring-operra-500">
                                    <option :value="1">1 Bulan</option>
                                    <option :value="3">3 Bulan</option>
                                    <option :value="6">6 Bulan</option>
                                    <option :value="12">12 Bulan (1 Tahun)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Total Transfer</label>
                                <div class="text-2xl font-black text-operra-600 dark:text-operra-400">
                                    {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(form.amount) }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Bukti Transfer</label>
                                <input type="file" @input="form.proof = $event.target.files[0]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-operra-50 file:text-operra-700 hover:file:bg-operra-100" />
                                <div v-if="form.errors.proof" class="text-red-500 text-xs mt-1">{{ form.errors.proof }}</div>
                            </div>

                            <button type="submit" :disabled="form.processing" class="w-full py-3 rounded-xl bg-operra-600 text-white font-black uppercase tracking-widest hover:bg-operra-700 transition-all shadow-lg disabled:opacity-50">
                                Kirim Konfirmasi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Riwayat Pembayaran -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Riwayat Pembayaran</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 uppercase font-black text-gray-500 text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3">Durasi</th>
                                    <th class="px-6 py-3 text-center">Bukti</th>
                                    <th class="px-6 py-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="pay in payments" :key="pay.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">{{ new Date(pay.payment_date).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4 font-bold">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(pay.amount) }}</td>
                                    <td class="px-6 py-4">{{ pay.months }} Bulan</td>
                                    <td class="px-6 py-4 text-center">
                                        <a :href="'/storage/' + pay.proof_of_payment" target="_blank" class="text-operra-600 hover:underline text-xs font-bold">Lihat</a>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': pay.status === 'pending',
                                                'bg-green-100 text-green-800': pay.status === 'approved',
                                                'bg-red-100 text-red-800': pay.status === 'rejected'
                                            }">
                                            {{ pay.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="payments.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada riwayat pembayaran.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

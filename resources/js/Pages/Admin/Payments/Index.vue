<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    payments: Array,
});

const selectedPayment = ref(null);
const showModal = ref(false);

const openVerifyModal = (payment) => {
    selectedPayment.value = payment;
    showModal.value = true;
};

const verify = (status) => {
    if (!confirm(`Apakah Anda yakin ingin mengubah status menjadi ${status}?`)) return;

    router.patch(route('admin.payments.verify', selectedPayment.value.id), {
        status: status,
        notes: ''
    }, {
        onSuccess: () => {
            showModal.value = false;
            selectedPayment.value = null;
        }
    });
};

</script>

<template>
    <Head title="Verifikasi Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            Verifikasi Pembayaran
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Daftar Pembayaran Masuk</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 uppercase font-black text-gray-500 text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Perusahaan</th>
                                    <th class="px-6 py-3">Paket</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3">Durasi</th>
                                    <th class="px-6 py-3 text-center">Bukti</th>
                                    <th class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="pay in payments" :key="pay.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ new Date(pay.payment_date).toLocaleDateString() }}</td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ pay.company?.name }}</div>
                                        <div class="text-xs text-gray-500">{{ pay.company?.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 uppercase text-xs font-bold text-gray-500">{{ pay.company?.plan?.name }}</td>
                                    <td class="px-6 py-4 font-bold text-operra-600 dark:text-operra-400">{{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(pay.amount) }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ pay.months }} Bulan</td>
                                    <td class="px-6 py-4 text-center">
                                        <a :href="'/storage/' + pay.proof_of_payment" target="_blank" class="text-operra-600 hover:underline text-xs font-bold">Lihat Foto</a>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div v-if="pay.status === 'pending'" class="flex justify-end gap-2">
                                            <button @click="openVerifyModal(pay)" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors">
                                                Verifikasi
                                            </button>
                                        </div>
                                        <span v-else class="px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                                            :class="{
                                                'bg-green-100 text-green-800': pay.status === 'approved',
                                                'bg-red-100 text-red-800': pay.status === 'rejected'
                                            }">
                                            {{ pay.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="payments.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400 italic">Belum ada data pembayaran.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Verifikasi -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Verifikasi Pembayaran</h3>
                    <div class="mb-6">
                        <div class="text-sm text-gray-500 mb-1">Perusahaan</div>
                        <div class="font-bold text-gray-900 dark:text-white">{{ selectedPayment?.company?.name }}</div>
                        <div class="mt-4 text-sm text-gray-500 mb-1">Total Tagihan</div>
                        <div class="font-bold text-2xl text-operra-600 dark:text-operra-400">
                            {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(selectedPayment?.amount) }}
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button @click="verify('rejected')" class="flex-1 py-3 rounded-xl border border-red-200 text-red-600 font-bold hover:bg-red-50 transition-colors">
                            Tolak
                        </button>
                        <button @click="verify('approved')" class="flex-1 py-3 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 transition-colors shadow-lg">
                            Terima & Aktifkan
                        </button>
                    </div>
                    <button @click="showModal = false" class="w-full mt-4 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 font-bold">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

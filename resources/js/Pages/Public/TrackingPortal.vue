<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    token: String,
});

const searchForm = useForm({
    token: props.token || '',
});

const order = ref(null);
const loading = ref(false);
const error = ref(null);

const track = () => {
    if (!searchForm.token) return;
    loading.value = true;
    error.value = null;
    
    // Simulation of API call
    setTimeout(() => {
        if (searchForm.token === 'DEMO123') {
            order.value = {
                number: 'SO-2026-0001',
                customer: 'Budi Santoso',
                date: '12 Feb 2026',
                status: 'Dikirim',
                items: [
                    { name: 'Produk A', qty: 2, price: 50000 },
                    { name: 'Produk B', qty: 1, price: 150000 },
                ],
                total: 250000,
                tracking_history: [
                    { time: '12 Feb 2026 14:00', note: 'Pesanan diambil kurir' },
                    { time: '12 Feb 2026 10:00', note: 'Pesanan diproses' },
                    { time: '11 Feb 2026 22:00', note: 'Pembayaran diterima' },
                ]
            };
        } else {
            error.value = 'Nomor pesanan atau token tidak ditemukan.';
            order.value = null;
        }
        loading.value = false;
    }, 1000);
};

if (props.token) track();
</script>

<template>
    <Head title="Lacak Pesanan - Operra Omnichannel" />

    <div class="min-h-screen bg-[#f8fafc] text-gray-900 font-sans antialiased pb-20">
        <!-- Minimal Navbar -->
        <nav class="bg-white border-b border-gray-100 py-6 px-6 mb-12">
            <div class="max-w-3xl mx-auto flex items-center justify-between">
                <img src="/images/operra_system.png" alt="Operra" class="h-10 w-auto" />
                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400">Order Tracking Portal</div>
            </div>
        </nav>

        <main class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-4 uppercase">Lacak Pesanan Anda</h1>
                <p class="text-gray-500 font-medium">Masukkan nomor pesanan atau token untuk melihat status terbaru.</p>
            </div>

            <!-- Search Box -->
            <div class="bg-white p-2 rounded-[30px] shadow-2xl shadow-operra-500/10 border border-gray-100 mb-12 flex items-center gap-2">
                <input 
                    v-model="searchForm.token" 
                    type="text" 
                    placeholder="Contoh: DEMO123" 
                    class="flex-1 bg-transparent border-none focus:ring-0 px-6 font-bold text-gray-800 placeholder:text-gray-300"
                    @keyup.enter="track"
                />
                <button 
                    @click="track"
                    :disabled="loading"
                    class="bg-operra-600 text-white px-8 py-4 rounded-[24px] font-black uppercase tracking-widest text-xs hover:bg-operra-700 transition-all disabled:opacity-50"
                >
                    {{ loading ? 'Mencari...' : 'Lacak Sekarang' }}
                </button>
            </div>

            <!-- Error State -->
            <div v-if="error" class="bg-red-50 border border-red-100 text-red-700 p-6 rounded-[30px] text-center font-bold mb-12">
                {{ error }}
            </div>

            <!-- Order Result -->
            <div v-if="order" class="space-y-8 animate-fade-in">
                <!-- Status Card -->
                <div class="bg-white rounded-[40px] p-8 shadow-xl border border-gray-50 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="px-4 py-2 rounded-full bg-operra-100 text-operra-700 text-xs font-black uppercase tracking-widest">
                            {{ order.status }}
                        </span>
                    </div>
                    
                    <div class="mb-10">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nomor Pesanan</div>
                        <h2 class="text-2xl font-black text-gray-900">{{ order.number }}</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Nama Pelanggan</div>
                            <div class="font-bold text-gray-800">{{ order.customer }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Tanggal Pesanan</div>
                            <div class="font-bold text-gray-800">{{ order.date }}</div>
                        </div>
                    </div>
                </div>

                <!-- History Tracking -->
                <div class="bg-white rounded-[40px] p-8 shadow-xl border border-gray-50">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-8">Riwayat Pengiriman</h3>
                    <div class="space-y-8 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-gray-100">
                        <div v-for="(step, i) in order.tracking_history" :key="i" class="relative pl-12">
                            <div :class="i === 0 ? 'bg-operra-500' : 'bg-gray-300'" class="absolute left-0 top-1 w-7 h-7 rounded-full border-4 border-white shadow-sm transition-colors"></div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase">{{ step.time }}</div>
                                <div :class="i === 0 ? 'text-gray-900 font-black' : 'text-gray-500 font-bold'" class="text-sm">{{ step.note }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items & Invoice Download -->
                <div class="bg-gray-950 rounded-[40px] p-8 md:p-12 shadow-2xl text-white">
                    <h3 class="text-sm font-black text-operra-400 uppercase tracking-widest mb-8">Detail Pesanan</h3>
                    <div class="space-y-4 mb-10">
                        <div v-for="item in order.items" :key="item.name" class="flex justify-between items-center py-4 border-b border-gray-800 last:border-0">
                            <div>
                                <div class="font-bold">{{ item.name }}</div>
                                <div class="text-[10px] text-gray-500">{{ item.qty }} x Rp{{ (item.price/1000) }}k</div>
                            </div>
                            <div class="font-black">Rp{{ (item.price * item.qty / 1000) }}k</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-10 pt-4 border-t border-gray-800">
                        <div class="text-xl font-black uppercase">Total Akhir</div>
                        <div class="text-3xl font-black text-operra-400">Rp{{ (order.total / 1000) }}k</div>
                    </div>
                    <button class="w-full py-5 rounded-3xl bg-white text-gray-950 font-black uppercase tracking-widest text-sm hover:bg-gray-200 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download PDF Invoice
                    </button>
                </div>
            </div>
        </main>

        <footer class="mt-20 text-center">
            <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                Powered by <span class="text-operra-500">Operra Omnichannel CRM</span>
            </p>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}
</style>

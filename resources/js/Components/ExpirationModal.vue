<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

const page = usePage();
const company = computed(() => page.props.auth.user.company);
const status = computed(() => company.value?.subscription_status);
const isExpired = computed(() => {
    return status.value?.is_expired && !route().current('billing.*');
});

</script>

<template>
    <div v-if="isExpired" class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/80 backdrop-blur-md p-4">
        <div class="relative bg-gray-900/90 w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden border border-white/10">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-32 -left-32 w-64 h-64 bg-operra-600/30 rounded-full blur-[80px]"></div>
                <div class="absolute -bottom-32 -right-32 w-64 h-64 bg-red-600/20 rounded-full blur-[80px]"></div>
            </div>
            <div class="p-8 text-center relative z-10">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-red-500 to-pink-600 shadow-lg shadow-red-500/30 mb-8 animate-pulse">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <h2 class="text-4xl font-black text-white mb-4 uppercase tracking-tighter leading-none">Masa Aktif <br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-pink-400">Berakhir</span></h2>
                <p class="text-gray-300 text-lg mb-10 leading-relaxed font-medium">
                    Akses Anda telah dikunci sementara. <br/> Perpanjang langganan untuk melanjutkan bisnis Anda tanpa henti.
                </p>

                <div class="space-y-6">
                    <Link :href="route('billing.index')" class="block w-full py-5 rounded-2xl bg-gradient-to-r from-operra-600 to-blue-600 hover:from-operra-500 hover:to-blue-500 text-white font-black uppercase tracking-widest text-sm shadow-xl shadow-operra-600/30 transform hover:-translate-y-1 transition-all duration-300">
                        PERPANJANG SEKARANG
                    </Link>
                    <div class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-4">
                        Hubungi Support jika ini kesalahan
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
});

const steps = [
    {
        title: "Selamat Datang di Operra!",
        description: "Dashboard CRM Multi-Portal pertama Anda. Di sini Anda bisa mengelola seluruh hubungan pelanggan dalam satu tempat.",
        icon: "🚀",
        color: "bg-blue-500"
    },
    {
        title: "Pilih Portal Anda",
        description: "Gunakan Launcher di Dashboard untuk berpindah antar portal: WA Blast, Sales CRM, Marketing, atau Customer Service.",
        icon: "🎯",
        color: "bg-purple-500"
    },
    {
        title: "Integrasi WhatsApp",
        description: "Hubungkan nomor WhatsApp Anda di menu WA Blast untuk mulai mengirim pesan dan mengelola chat secara otomatis.",
        icon: "📱",
        color: "bg-green-500"
    },
    {
        title: "Pantau Performa",
        description: "Gunakan fitur Analytical CRM untuk melihat laporan penjualan, statistik chat, dan performa tim Anda secara real-time.",
        icon: "📊",
        color: "bg-orange-500"
    },
    {
        title: "Siap Mulai?",
        description: "Semua fitur sudah siap digunakan. Jika butuh bantuan, hubungi tim support kami melalui menu bantuan.",
        icon: "✨",
        color: "bg-operra-600"
    }
];

const currentStep = ref(0);
const form = useForm({});

const nextStep = () => {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
    } else {
        completeOnboarding();
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const completeOnboarding = () => {
    form.post(route('onboarding.complete'), {
        preserveScroll: true,
    });
};

const progressWidth = computed(() => {
    return ((currentStep.value + 1) / steps.length) * 100 + '%';
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-950/80 backdrop-blur-md transition-opacity"></div>

        <!-- Modal Content -->
        <div class="relative bg-white dark:bg-gray-900 rounded-[40px] w-full max-w-xl overflow-hidden shadow-2xl border border-white/10">
            <!-- Progress Bar -->
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gray-100 dark:bg-gray-800">
                <div 
                    class="h-full bg-operra-600 transition-all duration-500 ease-out"
                    :style="{ width: progressWidth }"
                ></div>
            </div>

            <div class="p-8 md:p-12">
                <!-- Icon & Step Counter -->
                <div class="flex justify-between items-start mb-8">
                    <div 
                        :class="steps[currentStep].color"
                        class="w-16 h-16 rounded-3xl flex items-center justify-center text-3xl shadow-lg transform transition-transform duration-500 hover:scale-110"
                    >
                        {{ steps[currentStep].icon }}
                    </div>
                    <div class="text-[10px] font-black uppercase text-gray-400 tracking-widest bg-gray-50 dark:bg-gray-800 px-3 py-1 rounded-full">
                        Step {{ currentStep + 1 }} of {{ steps.length }}
                    </div>
                </div>

                <!-- Content with transition -->
                <div class="min-h-[200px]">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-4 uppercase tracking-tighter leading-none">
                        {{ steps[currentStep].title }}
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                        {{ steps[currentStep].description }}
                    </p>
                </div>

                <!-- Footer Buttons -->
                <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button 
                        @click="completeOnboarding"
                        class="order-2 sm:order-1 text-sm font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors uppercase tracking-widest"
                    >
                        Skip Panduan
                    </button>

                    <div class="order-1 sm:order-2 flex gap-3 w-full sm:w-auto">
                        <button 
                            v-if="currentStep > 0"
                            @click="prevStep"
                            class="flex-1 sm:flex-none px-6 py-3 rounded-2xl font-bold text-gray-500 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                        >
                            Kembali
                        </button>
                        
                        <button 
                            @click="nextStep"
                            class="flex-1 sm:flex-none px-10 py-4 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest text-xs hover:bg-operra-700 shadow-xl shadow-operra-600/30 transition-all transform active:scale-95"
                        >
                            {{ currentStep === steps.length - 1 ? 'Mulai Sekarang!' : 'Selanjutnya' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-operra-600/10 rounded-full blur-3xl"></div>
            <div class="absolute -top-12 -left-12 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl"></div>
        </div>
    </div>
</template>

<style scoped>
/* Optional: Add custom animations here if needed */
</style>


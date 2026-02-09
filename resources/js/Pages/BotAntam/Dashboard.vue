<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    account: Object,
});

const logs = ref([]);
const loadingLogs = ref(false);

const fetchLogs = async () => {
    loadingLogs.value = true;
    try {
        const response = await axios.get(route('bot_antam.logs.index'));
        logs.value = response.data;
    } catch (error) {
        console.error("Failed to fetch logs", error);
    } finally {
        loadingLogs.value = false;
    }
};

const contactAdmin = () => {
    window.location.href = route('bot_antam.support.index');
};

const maskString = (str) => {
    if (!str || str.length <= 4) return '****';
    return str.substring(0, 3) + '****' + str.substring(str.length - 2);
};

onMounted(() => {
    fetchLogs();
    setInterval(fetchLogs, 5000);
});
</script>

<template>
    <Head title="Bot Antam Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bot Antam Control Panel (Managed)
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4"
                     :class="account?.is_active ? 'border-green-500' : 'border-red-500'">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold">Bot Status</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="relative flex h-3 w-3">
                                  <span v-if="account?.is_active" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3" :class="account?.is_active ? 'bg-green-500' : 'bg-red-500'"></span>
                                </span>
                                <span class="font-mono text-sm">
                                    {{ account?.is_active ? 'RUNNING / ACTIVE' : 'STOPPED / IDLE' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 uppercase tracking-widest">Pricing Plan</p>
                            <span class="font-bold text-operra-600 dark:text-operra-400">MANAGED SERVICE</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Config Summary (Read Only) -->
                    <div class="md:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-fit">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Current Configuration</h3>
                            
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Logam Mulia Account</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ maskString(account?.lm_username) }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-xs text-gray-500 dark:text-gray-400 uppercase">Telegram Notification</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                        <span v-if="account?.telegram_chat_id" class="text-green-600 dark:text-green-400 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                            In Use ({{ maskString(account.telegram_chat_id) }})
                                        </span>
                                        <span v-else class="text-red-500 text-xs">Not Configured</span>
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-8 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-100 dark:border-yellow-800">
                                <p class="text-xs text-yellow-800 dark:text-yellow-200">
                                    <strong>Need Changes?</strong><br>
                                    For security and stability, configuration changes are handled by our admin team.
                                </p>
                                <button @click="contactAdmin" class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 bg-operra-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-operra-500 focus:bg-operra-500 active:bg-operra-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Contact Admin / Support
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Logs -->
                    <div class="md:col-span-2 bg-black text-green-400 overflow-hidden shadow-sm sm:rounded-lg font-mono text-xs flex flex-col h-96 md:h-auto">
                        <div class="p-4 bg-gray-900 border-b border-gray-800 flex justify-between items-center shrink-0">
                            <h3 class="font-bold flex items-center gap-2">
                                <span class="animate-pulse w-2 h-2 bg-green-500 rounded-full"></span>
                                Live Execution Logs
                            </h3>
                            <span v-if="loadingLogs" class="text-gray-500 text-[10px]">Syncing...</span>
                        </div>
                        <div class="p-4 overflow-y-auto grow custom-scrollbar bg-black/90">
                            <div v-for="log in logs" :key="log.id" class="mb-1 border-l-2 pl-2 border-transparent hover:border-gray-700 transition-colors">
                                <span class="text-gray-600 dark:text-gray-500">[{{ new Date(log.created_at).toLocaleTimeString() }}]</span>
                                <span :class="{
                                    'text-blue-400 font-bold': log.event_type === 'CHECK',
                                    'text-yellow-400 font-bold': log.event_type === 'FOUND',
                                    'text-red-500 font-bold': log.event_type === 'ERROR',
                                    'text-green-500 font-bold': log.event_type === 'SUCCESS'
                                }" class="mx-2">{{ log.event_type }}</span>
                                <span class="text-gray-300">{{ log.message }}</span>
                            </div>
                            <div v-if="logs.length === 0" class="text-gray-600 italic mt-4 text-center">
                                Waiting for bot execution...
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #1a1a1a; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #333; 
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555; 
}
</style>

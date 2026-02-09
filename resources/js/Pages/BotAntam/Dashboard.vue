<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    account: Object,
});

const logs = ref([]);
const loadingLogs = ref(false);
const showGuide = ref(false);

const form = useForm({
    lm_username: props.account?.lm_username || '',
    lm_password: '', // Default blank
    telegram_chat_id: props.account?.telegram_chat_id || '',
});

const submitConfig = () => {
    form.post(route('bot_antam.config.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('lm_password');
        },
    });
};

const openGuide = () => {
    showGuide.value = true;
};

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
                    <!-- Config Form (Editable) -->
                    <div class="md:col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-fit">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Bot Configuration</h3>
                            
                            <form @submit.prevent="submitConfig" class="space-y-4">
                                <div>
                                    <InputLabel for="lm_username" value="Logam Mulia Username" />
                                    <TextInput
                                        id="lm_username"
                                        type="text"
                                        class="mt-1 block w-full text-sm"
                                        v-model="form.lm_username"
                                        placeholder="Username / Email"
                                    />
                                    <InputError class="mt-2" :message="form.errors.lm_username" />
                                </div>

                                <div>
                                    <InputLabel for="lm_password" value="Logam Mulia Password" />
                                    <TextInput
                                        id="lm_password"
                                        type="password"
                                        class="mt-1 block w-full text-sm"
                                        v-model="form.lm_password"
                                        :placeholder="account?.has_password ? 'Saved (Leave blank to keep)' : 'Enter Password'"
                                    />
                                    <InputError class="mt-2" :message="form.errors.lm_password" />
                                </div>

                                <div>
                                    <InputLabel for="telegram_chat_id" value="Telegram Chat ID" />
                                    <div class="flex gap-2">
                                        <TextInput
                                            id="telegram_chat_id"
                                            type="text"
                                            class="mt-1 block w-full text-sm"
                                            v-model="form.telegram_chat_id"
                                            placeholder="Ex: 5195571609"
                                        />
                                        <SecondaryButton @click.prevent="openGuide" class="mt-1 px-2" title="How to get ID?">
                                            ?
                                        </SecondaryButton>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.telegram_chat_id" />
                                    <p v-if="account?.telegram_chat_id" class="text-xs text-green-600 dark:text-green-400 mt-1">
                                        Currently active: {{ maskString(account.telegram_chat_id) }}
                                    </p>
                                </div>

                                <div class="pt-4 border-t dark:border-gray-700">
                                    <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Save Configuration
                                    </PrimaryButton>
                                </div>
                            </form>

                            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                <p class="text-[10px] text-blue-800 dark:text-blue-200">
                                    <strong>Note:</strong> Changes may require Admin approval. Ensure your Logam Mulia credentials are correct to avoid account locking.
                                </p>
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
        <!-- Help Modal -->
        <Modal :show="showGuide" @close="showGuide = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    How to get Telegram Chat ID
                </h2>
                <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li>Open Telegram app.</li>
                    <li>Search for <strong>@userinfobot</strong>.</li>
                    <li>Start a chat with the bot.</li>
                    <li>Copy the number labeled as <strong>Id</strong>.</li>
                    <li>Paste it into the configuration field.</li>
                </ol>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showGuide = false">Close</SecondaryButton>
                </div>
            </div>
        </Modal>
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

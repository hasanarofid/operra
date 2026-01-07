<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    sessions: Array,
    whatsappAccounts: Array,
});

const selectedSession = ref(null);
const messages = ref([]);
const newMessage = ref('');
const messageContainer = ref(null);
const isLoading = ref(false);

const selectSession = async (session) => {
    selectedSession.value = session;
    isLoading.value = true;
    try {
        const response = await axios.get(route('crm.chat.show', session.id));
        messages.value = response.data.messages;
        scrollToBottom();
    } catch (error) {
        console.error('Failed to load messages', error);
    } finally {
        isLoading.value = false;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedSession.value) return;

    const text = newMessage.value;
    newMessage.value = '';

    try {
        const response = await axios.post(route('crm.chat.send', selectedSession.value.id), {
            message: text
        });
        messages.value.push(response.data);
        scrollToBottom();
    } catch (error) {
        console.error('Failed to send message', error);
        alert('Gagal mengirim pesan');
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    });
};

const formatTime = (date) => {
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Chat Inbox" />

    <AuthenticatedLayout>
        <template #header>
            CRM Chat Inbox
        </template>

        <template #stats>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 border border-white/20 text-white">
                    <div class="text-xs uppercase font-bold opacity-70">Total Conversations</div>
                    <div class="text-2xl font-bold">{{ sessions.length }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 border border-white/20 text-white">
                    <div class="text-xs uppercase font-bold opacity-70">Active Channels</div>
                    <div class="text-2xl font-bold">{{ whatsappAccounts.length }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-lg p-4 border border-white/20 text-white">
                    <div class="text-xs uppercase font-bold opacity-70">Messages Sent Today</div>
                    <div class="text-2xl font-bold">24</div>
                </div>
            </div>
        </template>

        <div class="flex h-[calc(100vh-280px)] bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <!-- Sidebar: Session List -->
            <div class="w-80 md:w-96 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50/50 dark:bg-gray-800/50">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="relative">
                        <input type="text" placeholder="Search chats..." class="w-full pl-9 pr-4 py-2 bg-gray-100 dark:bg-gray-700 border-none rounded-lg text-sm focus:ring-2 focus:ring-operra-500 dark:text-white">
                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div v-for="session in sessions" :key="session.id" 
                        @click="selectSession(session)"
                        :class="['group p-4 cursor-pointer hover:bg-white dark:hover:bg-gray-700 transition-all border-b border-gray-100 dark:border-gray-700 relative', 
                                selectedSession?.id === session.id ? 'bg-white dark:bg-gray-700 shadow-sm z-10' : '']">
                        <div v-if="selectedSession?.id === session.id" class="absolute left-0 top-0 bottom-0 w-1 bg-operra-500"></div>
                        <div class="flex gap-3">
                            <div class="relative">
                                <div class="h-12 w-12 rounded-full bg-operra-100 dark:bg-operra-900/30 flex items-center justify-center text-operra-600 dark:text-operra-400 font-bold text-lg">
                                    {{ session.customer.name.charAt(0) }}
                                </div>
                                <div v-if="session.status === 'open'" class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center mb-1">
                                    <h4 class="font-bold text-sm text-gray-800 dark:text-gray-100 truncate">{{ session.customer.name }}</h4>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ formatTime(session.last_message_at) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate flex items-center gap-1">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                                    {{ session.customer.phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-[#e5ddd5] dark:bg-gray-950 relative">
                <!-- Background Pattern (Optional) -->
                <div class="absolute inset-0 opacity-5 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>

                <template v-if="selectedSession">
                    <!-- Chat Header -->
                    <div class="z-10 p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-operra-500 flex items-center justify-center text-white font-bold shadow-md">
                                {{ selectedSession.customer.name.charAt(0) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-800 dark:text-gray-200">{{ selectedSession.customer.name }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                                    <span class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Active on {{ selectedSession.whatsapp_account.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="p-2 text-gray-400 hover:text-operra-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div ref="messageContainer" class="flex-1 p-6 overflow-y-auto space-y-6 z-10 custom-scrollbar">
                        <div v-for="msg in messages" :key="msg.id" 
                            :class="['flex w-full', msg.sender_type === 'user' ? 'justify-end' : 'justify-start']">
                            <div :class="['max-w-[75%] p-3 rounded-2xl shadow-md relative', 
                                    msg.sender_type === 'user' ? 'bg-operra-600 text-white rounded-tr-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-tl-none']">
                                <div class="text-[13px] leading-relaxed">{{ msg.message_body }}</div>
                                <div :class="['text-[9px] mt-1.5 flex items-center justify-end gap-1', msg.sender_type === 'user' ? 'text-operra-100' : 'text-gray-400']">
                                    {{ formatTime(msg.created_at) }}
                                    <svg v-if="msg.sender_type === 'user'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        </div>
                        <div v-if="isLoading" class="flex justify-center py-10">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-operra-500"></div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-10">
                        <form @submit.prevent="sendMessage" class="flex gap-3 items-center max-w-4xl mx-auto">
                            <button type="button" class="text-gray-400 hover:text-operra-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
                            <button type="button" class="text-gray-400 hover:text-operra-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg></button>
                            <input v-model="newMessage" type="text" 
                                class="flex-1 rounded-xl border-none bg-white dark:bg-gray-700 dark:text-white shadow-inner focus:ring-2 focus:ring-operra-500 py-3 px-5 text-sm" 
                                placeholder="Tulis pesan balasan...">
                            <button type="submit" 
                                class="h-12 w-12 rounded-xl bg-operra-600 hover:bg-operra-700 text-white flex items-center justify-center shadow-lg transition-all active:scale-95">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </button>
                        </form>
                    </div>
                </template>
                <template v-else>
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-500 p-10 z-10 text-center">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-full shadow-xl mb-6">
                            <svg class="w-24 h-24 text-operra-500 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-2">Pilih Percakapan Prospek</h3>
                        <p class="max-w-md text-sm text-gray-400">Klik salah satu daftar di sebelah kiri untuk mulai membalas pesan dan mengelola lead secara real-time.</p>
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
}
</style>


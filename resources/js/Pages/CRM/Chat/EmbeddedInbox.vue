<script setup>
import EmbeddedLayout from '@/Layouts/EmbeddedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, watch } from 'vue';
import axios from 'axios';
import { database } from '@/firebase';
import { ref as dbRef, onValue } from "firebase/database";

const props = defineProps({
    app: Object,
    sessions: Array,
    whatsappAccounts: Array,
    availableStatuses: Array,
});

const sessionsList = ref(props.sessions.map(s => ({
    ...s,
    session_unread_count: s.unread_count || 0
})));

const searchQuery = ref('');
const selectedSession = ref(null);
const filteredSessions = ref([]);
const showSidebar = ref(true);
const messages = ref([]);
const newMessage = ref('');
const messageContainer = ref(null);
const isLoading = ref(false);

const handleSearch = () => {
    let filtered = sessionsList.value;
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(session => {
            return session.customer.name.toLowerCase().includes(query) || 
                   session.customer.phone.includes(query);
        });
    }
    filteredSessions.value = filtered;
};

watch(sessionsList, () => {
    handleSearch();
}, { deep: true });

const resetSelection = () => {
    selectedSession.value = null;
    showSidebar.value = true;
};

onMounted(() => {
    filteredSessions.value = sessionsList.value;
    
    // Firebase listener (Global Inbox for the app's channel)
    const globalInboxRef = dbRef(database, `inbox/global`);
    
    onValue(globalInboxRef, (snapshot) => {
        const data = snapshot.val();
        if (data) {
            Object.values(data).forEach((incoming) => {
                const { session, message } = incoming;
                
                if (props.app.phone_number && session.whatsapp_account?.phone_number !== props.app.phone_number) {
                    return;
                }

                const index = sessionsList.value.findIndex(s => s.id === session.id);
                const isUnread = message.sender_type === 'customer' && (!selectedSession.value || selectedSession.value.id !== session.id);
                
                const updatedSession = { 
                    ...session, 
                    is_unread: isUnread || (index !== -1 && sessionsList.value[index].is_unread),
                    session_unread_count: incoming.session_unread_count || 0,
                    last_message_at: message.created_at
                };

                if (index !== -1) {
                    sessionsList.value.splice(index, 1);
                }
                sessionsList.value.unshift(updatedSession);
                sessionsList.value.sort((a, b) => new Date(b.last_message_at) - new Date(a.last_message_at));

                if (selectedSession.value && selectedSession.value.id === session.id) {
                    const isMessageExist = messages.value.some(m => m.id === message.id);
                    if (!isMessageExist) {
                        messages.value.push(message);
                        scrollToBottom();
                    }
                }
            });
        }
    });
});

const selectSession = async (session) => {
    selectedSession.value = session;
    showSidebar.value = false;
    
    isLoading.value = true;
    try {
        const response = await axios.get(route('crm.wa.chat.show', session.id));
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
        const response = await axios.post(route('crm.wa.chat.send', selectedSession.value.id), {
            message: text
        });
        messages.value.push(response.data);
        scrollToBottom();
    } catch (error) {
        console.error('Failed to send message', error);
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
    <Head title="WhatsApp Inbox" />

    <EmbeddedLayout>
        <div class="flex h-screen bg-white dark:bg-gray-800 overflow-hidden">
            <!-- Sidebar -->
            <div :class="['w-full md:w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50/50 dark:bg-gray-800/50 transition-all duration-300', 
                          !showSidebar && 'hidden md:flex']">
                <div class="p-4 bg-white dark:bg-gray-800 shadow-sm z-20">
                    <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="Search..." 
                        class="w-full py-2 bg-gray-100 dark:bg-gray-700 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all">
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div v-for="session in filteredSessions" :key="session.id" @click="selectSession(session)"
                        :class="['p-4 cursor-pointer transition-all border-b border-gray-100 dark:border-gray-700', 
                                selectedSession?.id === session.id ? 'bg-white dark:bg-gray-700 shadow-sm' : 'hover:bg-white dark:hover:bg-gray-700']">
                        <div class="flex gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 font-bold">
                                {{ session.customer.name.charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-sm truncate" :class="session.is_unread ? 'text-blue-600' : 'text-gray-800 dark:text-gray-100'">
                                        {{ session.customer.name }}
                                    </h4>
                                    <span class="text-[10px] text-gray-400">{{ formatTime(session.last_message_at) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 truncate">{{ session.customer.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div :class="['flex-1 flex flex-col bg-[#e5ddd5] dark:bg-gray-950 relative', showSidebar && 'hidden md:flex']">
                <template v-if="selectedSession">
                    <!-- Chat Header -->
                    <div class="z-10 p-3 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                        <button @click="resetSelection" class="md:hidden p-2 -ml-2 text-gray-500">Back</button>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">{{ selectedSession.customer.name.charAt(0) }}</div>
                        <div class="font-bold text-sm text-gray-800 dark:text-gray-200">{{ selectedSession.customer.name }}</div>
                    </div>

                    <!-- Messages -->
                    <div ref="messageContainer" class="flex-1 p-4 overflow-y-auto space-y-4 custom-scrollbar">
                        <div v-for="msg in messages" :key="msg.id" :class="['flex w-full', msg.sender_type === 'user' ? 'justify-end' : 'justify-start']">
                            <div :class="['max-w-[85%] p-2.5 rounded-lg shadow-sm', msg.sender_type === 'user' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200']">
                                <div class="text-sm leading-relaxed">{{ msg.message_body }}</div>
                                <div class="text-[9px] mt-1 text-right" :class="msg.sender_type === 'user' ? 'text-blue-100' : 'text-gray-400'">{{ formatTime(msg.created_at) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Input -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <input v-model="newMessage" type="text" class="flex-1 rounded-lg border-none bg-white dark:bg-gray-700 dark:text-white py-2 px-4 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Type a message...">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold">Send</button>
                        </form>
                    </div>
                </template>
                <template v-else>
                    <div class="flex-1 flex items-center justify-center text-gray-400">
                        <p>Select a conversation to start chatting</p>
                    </div>
                </template>
            </div>
        </div>
    </EmbeddedLayout>
</template>


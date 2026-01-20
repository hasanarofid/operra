<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { database } from '@/firebase';
import { ref as dbRef, onValue, off } from "firebase/database";

const { props: pageProps } = usePage();

const props = defineProps({
    sessions: Array,
    whatsappAccounts: Array,
});

const sessionsList = ref(props.sessions.map(s => ({
    ...s,
    session_unread_count: s.unread_count || 0
})));
const selectedSession = ref(null);
const showSidebar = ref(true); // Control sidebar visibility on mobile
const messages = ref([]);
const newMessage = ref('');
const messageContainer = ref(null);
const isLoading = ref(false);

const resetSelection = () => {
    selectedSession.value = null;
    showSidebar.value = true;
};

onMounted(() => {
    // 1. Listen ke Firebase Realtime Database
    if (database) {
        const userInboxRef = dbRef(database, `inbox/users/${pageProps.auth.user.id}`);
        
        onValue(userInboxRef, (snapshot) => {
            const data = snapshot.val();
            if (data) {
                // Firebase mengembalikan objek, kita butuh data terbaru
                // Karena kita nge-set data dengan key = session_id di backend
                Object.values(data).forEach((incoming) => {
                    const { session, message } = incoming;

                    // Update daftar session (pindahkan ke paling atas)
                    const index = sessionsList.value.findIndex(s => s.id === session.id);
                    
                    // Tambahkan flag unread jika pesan dari customer dan bukan session yang sedang dibuka
                    const isUnread = message.sender_type === 'customer' && (!selectedSession.value || selectedSession.value.id !== session.id);
                    const updatedSession = { 
                        ...session, 
                        is_unread: isUnread || (index !== -1 && sessionsList.value[index].is_unread),
                        session_unread_count: incoming.session_unread_count || 0,
                        last_message_at: message.created_at // Update timestamp terakhir
                    };

                    if (index !== -1) {
                        sessionsList.value.splice(index, 1);
                    }
                    sessionsList.value.unshift(updatedSession);
                    
                    // Urutkan list agar pesan terbaru selalu di paling atas
                    sessionsList.value.sort((a, b) => {
                        return new Date(b.last_message_at) - new Date(a.last_message_at);
                    });

                    // Jika sedang membuka session tersebut, tambah pesan secara real-time
                    if (selectedSession.value && selectedSession.value.id === session.id) {
                        // Cek agar tidak duplikat (karena Firebase onValue trigger tiap ada perubahan)
                        const isMessageExist = messages.value.some(m => m.id === message.id);
                        if (!isMessageExist) {
                            messages.value.push(message);
                            scrollToBottom();
                        }
                    }
                });
            }
        });
    }

    // Backup: Tetap biarkan Laravel Echo jika masih ingin digunakan
    if (window.Echo) {
        // ... (Echo logic if needed)
    }
});

const selectSession = async (session) => {
    selectedSession.value = session;
    showSidebar.value = false; // Hide sidebar on mobile
    
    // Kurangi unread status secara bertahap (lokal)
    const sessionInList = sessionsList.value.find(s => s.id === session.id);
    if (sessionInList && sessionInList.session_unread_count > 0) {
        sessionInList.session_unread_count--;
        if (sessionInList.session_unread_count === 0) {
            sessionInList.is_unread = false;
        }
    }

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
    return new Date(date).toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
};

const updateCustomerStatus = async (newStatus) => {
    if (!selectedSession.value) return;
    
    try {
        await axios.patch(route('crm.sales.customers.update-status', selectedSession.value.customer_id), {
            status: newStatus
        });
        selectedSession.value.customer.status = newStatus;
        
        // Update status di sessionsList juga
        const session = sessionsList.value.find(s => s.id === selectedSession.value.id);
        if (session) {
            session.customer.status = newStatus;
        }
        
    } catch (error) {
        console.error('Gagal update status', error);
        alert('Gagal memperbarui status customer');
    }
};
</script>

<template>
    <Head title="Chat Inbox" />

    <AuthenticatedLayout>
        <template #header>
            CRM CHAT INBOX
        </template>

        <template #stats>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 md:gap-4 mb-2 md:mb-0">
                <div class="bg-[#0ea5e9]/20 backdrop-blur-sm rounded-lg p-4 md:p-6 border border-white/10 text-white shadow-lg">
                    <div class="text-[10px] uppercase font-black tracking-widest opacity-80 mb-1">Total Chats</div>
                    <div class="text-2xl md:text-3xl font-black">{{ sessionsList.length }}</div>
                </div>
                <div class="bg-[#0ea5e9]/20 backdrop-blur-sm rounded-lg p-4 md:p-6 border border-white/10 text-white shadow-lg">
                    <div class="text-[10px] uppercase font-black tracking-widest opacity-80 mb-1">Channels</div>
                    <div class="text-2xl md:text-3xl font-black">{{ whatsappAccounts.length }}</div>
                </div>
                <div class="bg-[#0ea5e9]/20 backdrop-blur-sm rounded-lg p-4 md:p-6 border border-white/10 text-white shadow-lg col-span-2 md:col-span-1">
                    <div class="text-[10px] uppercase font-black tracking-widest opacity-80 mb-1">Sent Today</div>
                    <div class="text-2xl md:text-3xl font-black">24</div>
                </div>
            </div>
        </template>

        <div class="flex h-[calc(100vh-250px)] min-h-[600px] bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <!-- Sidebar: Session List -->
            <div :class="['w-full md:w-80 lg:w-[400px] border-r border-gray-200 dark:border-gray-700 flex flex-col bg-gray-50 dark:bg-gray-800/50 transition-all duration-300', 
                          !showSidebar && 'hidden md:flex']">
                <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <input type="text" placeholder="Search chats..." class="w-full pl-10 pr-4 py-3 bg-gray-100 dark:bg-gray-900 border-none rounded-xl text-sm focus:ring-1 focus:ring-blue-500 text-gray-700 dark:text-white placeholder-gray-500">
                        <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <div v-for="session in sessionsList" :key="session.id" 
                        @click="selectSession(session)"
                        :class="['group p-4 cursor-pointer transition-all border-b border-gray-100 dark:border-gray-700 relative', 
                                selectedSession?.id === session.id 
                                ? 'bg-white dark:bg-gray-700 shadow-sm' 
                                : 'hover:bg-white dark:hover:bg-gray-700/50']">
                        
                        <div class="flex gap-3">
                            <div class="relative shrink-0">
                                <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-black text-lg shadow-inner">
                                    {{ session.customer?.name?.charAt(0) || '?' }}
                                </div>
                                <div v-if="session.status === 'open'" class="absolute bottom-0.5 right-0.5 h-3 w-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-0.5">
                                    <h4 class="font-bold text-[13px] truncate pr-2 text-gray-800 dark:text-blue-400 group-hover:text-blue-600 dark:group-hover:text-blue-300">
                                        {{ session.customer?.name || 'Unknown' }}
                                    </h4>
                                    <span class="text-[10px] text-gray-400 font-medium shrink-0">{{ formatTime(session.last_message_at) }}</span>
                                </div>
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-[11px] truncate text-gray-500 dark:text-gray-400">
                                        {{ session.customer?.phone || 'No Phone' }}
                                    </p>
                                    <div v-if="session.session_unread_count > 0" class="min-w-[18px] h-[18px] px-1 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                        <span class="text-[9px] text-white font-black">{{ session.session_unread_count }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span :class="[
                                        'text-[9px] px-2 py-0.5 rounded font-black uppercase tracking-wider',
                                        session.customer?.status === 'customer' ? 'bg-green-500/10 text-green-600 dark:bg-green-500/20 dark:text-green-400' : 
                                        session.customer?.status === 'prospect' ? 'bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' : 
                                        session.customer?.status === 'lost' ? 'bg-red-500/10 text-red-600 dark:bg-red-500/20 dark:text-red-400' : 'bg-gray-500/10 text-gray-600 dark:bg-gray-500/20 dark:text-gray-400'
                                    ]">
                                        {{ session.customer?.status || 'Lead' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div :class="['flex-1 flex flex-col bg-gray-50 dark:bg-gray-900 relative transition-all duration-300', 
                          showSidebar && 'hidden md:flex']">
                
                <template v-if="selectedSession">
                    <!-- Chat Header -->
                    <div class="z-10 p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-3">
                            <button @click="resetSelection" class="md:hidden p-2 -ml-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            
                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-black shadow-lg shrink-0">
                                {{ selectedSession.customer?.name?.charAt(0) || '?' }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="font-black text-gray-800 dark:text-white truncate text-sm">{{ selectedSession.customer?.name || 'Unknown' }}</div>
                                    <select 
                                        v-if="selectedSession.customer"
                                        v-model="selectedSession.customer.status" 
                                        @change="updateCustomerStatus($event.target.value)"
                                        class="text-[9px] uppercase font-black px-2 py-0.5 h-5 rounded border border-gray-200 dark:border-none bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 focus:ring-1 focus:ring-blue-500 cursor-pointer"
                                    >
                                        <option value="lead">Lead</option>
                                        <option value="prospect">Prospect</option>
                                        <option value="customer">Customer</option>
                                        <option value="lost">Lost</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest opacity-70">Active on {{ selectedSession.whatsapp_account?.name || 'WA Account' }}</span>
                                </div>
                            </div>
                        </div>
                        <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                        </button>
                    </div>

                    <!-- Messages -->
                    <div ref="messageContainer" class="flex-1 p-6 overflow-y-auto space-y-4 z-10 custom-scrollbar bg-[#f0f2f5] dark:bg-gray-900">
                        <div v-for="msg in messages" :key="msg.id" 
                            :class="['flex w-full', msg.sender_type === 'user' ? 'justify-end' : 'justify-start']">
                            <div :class="['max-w-[70%] p-3 px-4 rounded-2xl shadow-sm relative', 
                                    msg.sender_type === 'user' ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-white rounded-tl-none border border-gray-100 dark:border-gray-700']">
                                <div class="text-[13px] leading-relaxed font-medium">
                                    {{ msg.message_type === 'text' ? msg.message_body : 'non-text message' }}
                                </div>
                                <div class="text-[9px] mt-1.5 flex items-center justify-end gap-1 font-bold" :class="msg.sender_type === 'user' ? 'text-blue-100' : 'text-gray-400'">
                                    {{ formatTime(msg.created_at) }}
                                </div>
                            </div>
                        </div>
                        <div v-if="isLoading" class="flex justify-center py-10">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-10">
                        <form @submit.prevent="sendMessage" class="flex gap-3 items-center max-w-6xl mx-auto">
                            <button type="button" class="text-gray-400 hover:text-blue-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                            <button type="button" class="text-gray-400 hover:text-blue-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            </button>
                            <input v-model="newMessage" type="text" 
                                class="flex-1 rounded-xl border border-gray-200 dark:border-none bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white shadow-inner focus:ring-1 focus:ring-blue-500 py-3 px-5 text-sm placeholder-gray-500" 
                                placeholder="Tulis pesan balasan...">
                            <button type="submit" 
                                class="h-11 w-11 shrink-0 rounded-xl bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-lg transition-all active:scale-95">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </button>
                        </form>
                    </div>
                </template>
                <template v-else>
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-400 p-10 z-10 text-center">
                        <div class="bg-white dark:bg-gray-800 p-10 rounded-full shadow-xl mb-8 border border-gray-100 dark:border-gray-700">
                            <svg class="w-24 h-24 text-blue-500 opacity-20" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-800 dark:text-white mb-3">Pilih Percakapan Chat</h3>
                        <p class="max-w-sm text-sm text-gray-500 dark:text-gray-400 font-medium">Klik salah satu daftar di sebelah kiri untuk mulai mengelola komunikasi pelanggan secara real-time.</p>
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




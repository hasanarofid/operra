<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { nextTick, ref, watch, onMounted } from 'vue';

const props = defineProps({
    ticket: Object,
});

const form = useForm({
    message: '',
});

const messagesContainer = ref(null);

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

onMounted(() => {
    scrollToBottom();
});

watch(() => props.ticket.messages, () => {
    nextTick(scrollToBottom);
}, { deep: true });

const submitReply = () => {
    form.post(route('support.reply', props.ticket.id), {
        onSuccess: () => {
             form.reset();
             nextTick(scrollToBottom);
        },
    });
};
</script>

<template>
    <Head :title="`Detail Komplain #${ticket.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('support.index')" class="bg-white/10 hover:bg-white/20 p-2 rounded-xl transition-all">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div class="flex flex-col">
                    <span class="text-white text-[10px] font-black uppercase tracking-widest opacity-70">Detail Tiket Komplain</span>
                    <h2 class="font-black text-xl text-white leading-tight uppercase tracking-tighter">
                        {{ ticket.subject }}
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-[30px] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-[700px]">
                    
                    <!-- Ticket Info Bar -->
                    <div class="px-8 py-4 bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">ID: #{{ ticket.id }}</span>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter"
                                :class="{
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': ticket.status === 'open',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': ticket.status === 'pending_user' || ticket.status === 'pending_admin',
                                    'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': ticket.status === 'closed'
                                }">
                                {{ ticket.status.replace('_', ' ') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Prioritas:</span>
                             <span class="text-[10px] font-black uppercase tracking-widest" :class="{
                                'text-red-500': ticket.priority === 'urgent',
                                'text-orange-500': ticket.priority === 'high',
                                'text-blue-500': ticket.priority === 'normal',
                                'text-gray-500': ticket.priority === 'low'
                            }">{{ ticket.priority }}</span>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div ref="messagesContainer" class="flex-1 p-8 overflow-y-auto space-y-6 scroll-smooth bg-gray-50/30 dark:bg-gray-900/20">
                        <div v-for="msg in ticket.messages" :key="msg.id" 
                             class="flex flex-col"
                             :class="msg.user_id == $page.props.auth.user.id ? 'items-end' : 'items-start'">
                            
                            <div class="flex items-end gap-2 max-w-[85%]">
                                <div v-if="msg.user_id != $page.props.auth.user.id" class="h-8 w-8 rounded-full bg-operra-500 flex items-center justify-center text-white text-[10px] font-black uppercase shrink-0">
                                    {{ msg.user?.name?.substring(0, 1) || 'A' }}
                                </div>
                                
                                <div class="flex flex-col" :class="msg.user_id == $page.props.auth.user.id ? 'items-end' : 'items-start'">
                                    <div class="px-5 py-3 rounded-2xl shadow-sm text-sm font-medium"
                                         :class="msg.user_id == $page.props.auth.user.id 
                                            ? 'bg-operra-600 text-white rounded-br-none' 
                                            : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none border border-gray-100 dark:border-gray-600'">
                                        <p class="whitespace-pre-wrap leading-relaxed">{{ msg.message }}</p>
                                    </div>
                                    <span class="text-[9px] font-bold text-gray-400 mt-1 uppercase tracking-wider">
                                        {{ msg.user_id == $page.props.auth.user.id ? 'Anda' : (msg.user?.name ?? 'Admin Operra') }} • {{ new Date(msg.created_at).toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' }) }}
                                    </span>
                                </div>

                                <div v-if="msg.user_id == $page.props.auth.user.id" class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 text-[10px] font-black uppercase shrink-0">
                                    {{ $page.props.auth.user.name.substring(0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Area -->
                    <div class="p-6 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                        <form @submit.prevent="submitReply" class="flex items-end gap-3">
                            <div class="relative flex-1">
                                <textarea
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-transparent focus:border-operra-500 focus:ring-0 rounded-2xl p-4 transition-all text-sm font-medium text-gray-700 dark:text-gray-300 min-h-[50px] max-h-[150px] resize-none"
                                    v-model="form.message"
                                    rows="1"
                                    placeholder="Tulis balasan Anda di sini..."
                                    required
                                    @keydown.enter.exact.prevent="submitReply"
                                ></textarea>
                            </div>
                            <button type="submit" 
                                class="bg-operra-600 hover:bg-operra-700 text-white p-4 rounded-2xl shadow-lg shadow-operra-600/20 transition-all active:scale-95 disabled:opacity-50"
                                :disabled="form.processing || !form.message.trim()">
                                <svg v-if="!form.processing" class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                                <svg v-else class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </button>
                        </form>
                        <p class="text-[10px] text-gray-400 mt-2 ml-1 font-medium italic">Tekan Enter untuk mengirim balasan.</p>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom scrollbar for message area */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark ::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>

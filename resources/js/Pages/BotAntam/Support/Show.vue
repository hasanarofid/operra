<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { nextTick, ref, watch } from 'vue';

const props = defineProps({
    ticket: Object,
    auth: Object, 
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

watch(() => props.ticket.messages, () => {
    nextTick(scrollToBottom);
}, { deep: true, immediate: true });


const submitReply = () => {
    form.post(route('bot_antam.support.reply', props.ticket.id), {
        onSuccess: () => {
             form.reset();
             nextTick(scrollToBottom);
        },
    });
};
</script>

<template>
    <Head :title="`Ticket #${ticket.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('bot_antam.support.index')" class="text-gray-500 hover:text-gray-700">
                    &larr; Back
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ ticket.subject }} <span class="text-sm font-normal text-gray-500">#{{ ticket.id }}</span>
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-[600px]">
                    
                    <!-- Messages Area -->
                    <div ref="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-4 bg-gray-50 dark:bg-gray-900">
                        <div v-for="msg in ticket.messages" :key="msg.id" 
                             class="flex flex-col max-w-[80%]"
                             :class="msg.user_id == $page.props.auth.user.id ? 'self-end items-end' : 'self-start items-start'">
                            
                            <div class="p-3 rounded-lg shadow-sm"
                                 :class="msg.user_id == $page.props.auth.user.id ? 'bg-operra-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200'">
                                <p class="whitespace-pre-wrap text-sm">{{ msg.message }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400 mt-1">
                                {{ msg.user_id == $page.props.auth.user.id ? 'You' : (msg.user?.name ?? 'Admin') }} • {{ new Date(msg.created_at).toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Reply Area -->
                    <div class="p-4 bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                        <form @submit.prevent="submitReply" class="flex gap-2">
                             <textarea
                                class="flex-1 appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="form.message"
                                rows="2"
                                placeholder="Type your reply..."
                                required
                            ></textarea>
                            <PrimaryButton class="h-auto px-6" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Send
                            </PrimaryButton>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    mediaMessages: Object
});

const selectedMedia = ref(null);
const showModal = ref(false);

const openPreview = (message) => {
    selectedMedia.value = message;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedMedia.value = null;
};

const deleteMedia = (id) => {
    if (confirm('Are you sure you want to delete this media file?')) {
        router.delete(route('crm.wa.media.destroy', id));
    }
};

const getFileExtension = (path) => {
    return path.split('.').pop().toLowerCase();
};

const isImage = (path) => {
    return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(getFileExtension(path));
};

const isVideo = (path) => {
    return ['mp4', 'webm', 'ogg'].includes(getFileExtension(path));
};

const getFileUrl = (path) => {
    return `/storage/${path}`;
};
</script>

<template>
    <Head title="WhatsApp Media Gallery" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                WhatsApp Media Gallery
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                    <h3 class="font-black text-lg text-gray-800 dark:text-white uppercase tracking-tighter mb-8">Media Inventory</h3>
                    <div v-if="mediaMessages.data.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                        <div v-for="message in mediaMessages.data" :key="message.id" class="relative aspect-square bg-gray-50 dark:bg-gray-900/50 rounded-2xl overflow-hidden group border border-gray-100 dark:border-gray-800 transition-all hover:scale-105 hover:shadow-2xl">
                            <img v-if="isImage(message.attachment_path)" :src="getFileUrl(message.attachment_path)" class="w-full h-full object-cover cursor-pointer" @click="openPreview(message)">
                            <div v-else class="w-full h-full flex flex-col items-center justify-center cursor-pointer" @click="openPreview(message)">
                                <div class="p-3 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-gray-500">{{ getFileExtension(message.attachment_path) }}</span>
                            </div>
                            
                            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-[2px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button @click="deleteMedia(message.id)" class="text-white bg-red-500/80 hover:bg-red-600 p-3 rounded-2xl transition-all hover:scale-110 shadow-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h14"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-32 text-gray-400 dark:text-gray-600 italic font-medium">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        No media files found in WhatsApp history.
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/90 backdrop-blur-md p-4" @click.self="closeModal">
            <div class="max-w-5xl w-full bg-white dark:bg-gray-800 rounded-3xl overflow-hidden relative shadow-2xl">
                <button @click="closeModal" class="absolute top-4 right-4 z-10 p-2 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-800 dark:text-white hover:bg-red-500 hover:text-white transition-all shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="p-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">Media Preview</h4>
                </div>
                <div class="flex items-center justify-center bg-neutral-100 dark:bg-black p-4 min-h-[300px]">
                    <img v-if="isImage(selectedMedia.attachment_path)" :src="getFileUrl(selectedMedia.attachment_path)" class="max-w-full max-h-[80vh] rounded-xl shadow-xl">
                    <video v-else-if="isVideo(selectedMedia.attachment_path)" :src="getFileUrl(selectedMedia.attachment_path)" controls class="max-w-full max-h-[80vh] rounded-xl shadow-xl"></video>
                    <div v-else class="text-center p-20">
                        <div class="w-20 h-20 mx-auto bg-gray-200 dark:bg-gray-800 rounded-3xl flex items-center justify-center mb-6">
                             <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="mb-8 font-bold text-gray-800 dark:text-white">Format file tidak mendukung pratinjau langsung.</p>
                        <a :href="getFileUrl(selectedMedia.attachment_path)" download class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-blue-700 transition-all shadow-lg">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                             Download File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


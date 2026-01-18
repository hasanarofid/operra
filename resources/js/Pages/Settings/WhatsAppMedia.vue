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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div v-if="mediaMessages.data.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <div v-for="message in mediaMessages.data" :key="message.id" class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden group">
                            <img v-if="isImage(message.attachment_path)" :src="getFileUrl(message.attachment_path)" class="w-full h-full object-cover cursor-pointer" @click="openPreview(message)">
                            <div v-else class="w-full h-full flex flex-col items-center justify-center cursor-pointer" @click="openPreview(message)">
                                <span class="text-xs font-bold uppercase text-gray-400">{{ getFileExtension(message.attachment_path) }}</span>
                            </div>
                            
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="deleteMedia(message.id)" class="text-white bg-red-600 p-2 rounded-full hover:bg-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h14"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-20 text-gray-500 italic">No media files found.</div>
                </div>
            </div>
        </div>

        <!-- Preview Modal (Simplistic for now) -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.self="closeModal">
            <div class="max-w-4xl w-full bg-white rounded-lg p-4 relative">
                <button @click="closeModal" class="absolute -top-10 right-0 text-white text-2xl">&times;</button>
                <img v-if="isImage(selectedMedia.attachment_path)" :src="getFileUrl(selectedMedia.attachment_path)" class="w-full h-auto">
                <video v-else-if="isVideo(selectedMedia.attachment_path)" :src="getFileUrl(selectedMedia.attachment_path)" controls class="w-full h-auto"></video>
                <div v-else class="text-center p-10">
                    <p class="mb-4">Preview not available.</p>
                    <a :href="getFileUrl(selectedMedia.attachment_path)" download class="bg-blue-600 text-white px-4 py-2 rounded">Download File</a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


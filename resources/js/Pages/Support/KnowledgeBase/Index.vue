<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    articles: Object
});
</script>

<template>
    <Head title="Knowledge Base" />

    <AuthenticatedLayout>
        <template #header>
            SUPPORT: KNOWLEDGE BASE
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden">
                    <div class="rounded-t mb-0 px-6 py-4 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-black text-lg text-gray-700 dark:text-gray-200">Articles</h3>
                            </div>
                            <div class="relative w-full max-w-full flex-grow flex-1 text-right">
                                <button class="bg-operra-500 text-white active:bg-operra-600 text-xs font-black uppercase px-4 py-2 rounded-lg shadow hover:shadow-md transition-all">
                                    Create Article
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Title</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Category</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-6 py-3 text-xs uppercase font-black text-left text-gray-500 dark:text-gray-400">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="art in articles.data" :key="art.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-gray-200">{{ art.title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ art.category }}</td>
                                    <td class="px-6 py-4 text-xs">
                                        <span :class="['px-2 py-1 rounded-full font-black uppercase tracking-widest text-[10px]', art.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700']">
                                            {{ art.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ art.created_at || '-' }}</td>
                                </tr>
                                <tr v-if="!articles.data.length">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No articles found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


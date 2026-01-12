<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    requests: Array
});

const updateStatus = (id, status) => {
    router.patch(route('admin.leads.update-status', id), { status });
};

const deleteRequest = (id) => {
    if (confirm('Hapus data ini?')) {
        router.delete(route('admin.leads.destroy', id));
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'new': return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
        case 'contacted': return 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400';
        case 'closed': return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
        default: return 'bg-gray-100 text-gray-700';
    }
};
</script>

<template>
    <Head title="Admin - Custom CRM Requests" />

    <AuthenticatedLayout>
        <template #header>
            Custom CRM Requests
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-black text-gray-800 dark:text-white uppercase mb-6 tracking-tighter">Calon Klien Custom CRM</h2>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="py-4 px-4 text-xs font-black uppercase text-gray-400 tracking-widest">Klien & Perusahaan</th>
                                        <th class="py-4 px-4 text-xs font-black uppercase text-gray-400 tracking-widest">Kontak</th>
                                        <th class="py-4 px-4 text-xs font-black uppercase text-gray-400 tracking-widest">Tipe Bisnis</th>
                                        <th class="py-4 px-4 text-xs font-black uppercase text-gray-400 tracking-widest">Pesan</th>
                                        <th class="py-4 px-4 text-xs font-black uppercase text-gray-400 tracking-widest">Status</th>
                                        <th class="py-4 px-4 text-xs font-black uppercase text-gray-400 tracking-widest text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="req in requests" :key="req.id" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-900/20 transition-all">
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-gray-800 dark:text-gray-100">{{ req.name }}</div>
                                            <div class="text-[10px] text-gray-500 font-bold uppercase">{{ req.company_name }}</div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ req.email }}</div>
                                            <div class="text-xs font-black text-operra-500">{{ req.phone }}</div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-[10px] font-black uppercase text-gray-500">
                                                {{ req.business_type }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 max-w-xs">
                                            <p class="text-xs text-gray-500 truncate" :title="req.message">{{ req.message }}</p>
                                        </td>
                                        <td class="py-4 px-4">
                                            <select 
                                                @change="updateStatus(req.id, $event.target.value)"
                                                :class="getStatusClass(req.status)"
                                                class="text-[10px] font-black uppercase tracking-widest rounded-full border-none px-4 py-1.5 focus:ring-0 cursor-pointer"
                                                :value="req.status"
                                            >
                                                <option value="new">NEW</option>
                                                <option value="contacted">CONTACTED</option>
                                                <option value="closed">CLOSED</option>
                                            </select>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <button @click="deleteRequest(req.id)" class="text-red-500 hover:text-red-700 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="requests.length === 0">
                                        <td colspan="6" class="py-20 text-center">
                                            <div class="text-gray-400 text-sm font-black uppercase tracking-[0.2em]">Belum ada permintaan masuk</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


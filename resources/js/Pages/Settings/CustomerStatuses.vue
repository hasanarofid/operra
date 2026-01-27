<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    statuses: Array
});

const form = useForm({
    id: null,
    name: '',
    color: '#94a3b8',
    order: 0,
});

const isEditing = ref(false);

const submit = () => {
    if (isEditing.value) {
        form.put(route('crm.wa.customer-statuses.update', form.id), {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post(route('crm.wa.customer-statuses.store'), {
            onSuccess: () => resetForm(),
        });
    }
};

const editStatus = (status) => {
    isEditing.value = true;
    form.id = status.id;
    form.name = status.name;
    form.color = status.color;
    form.order = status.order;
};

const deleteStatus = (id) => {
    if (confirm('Are you sure you want to delete this status?')) {
        form.delete(route('crm.wa.customer-statuses.destroy', id));
    }
};

const resetForm = () => {
    isEditing.value = false;
    form.reset();
};
</script>

<template>
    <Head title="Lead Statuses" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Lead Statuses
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-2xl border-0 h-fit">
                    <h3 class="font-black text-lg mb-6 text-gray-800 dark:text-white uppercase tracking-tighter">{{ isEditing ? 'Edit Status' : 'Add New Status' }}</h3>
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Status Name</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Identity Color</label>
                            <div class="flex gap-2 mt-1">
                                <input v-model="form.color" type="color" class="h-11 w-12 rounded-xl border-gray-100 dark:border-gray-600 bg-transparent cursor-pointer">
                                <input v-model="form.color" type="text" class="flex-1 rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm uppercase font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Display Order</label>
                            <input v-model="form.order" type="number" class="mt-1 block w-full rounded-xl bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 text-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-0 transition-all text-sm">
                        </div>
                        <div class="flex flex-col gap-3 pt-4">
                            <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 active:scale-95 transition-all w-full">
                                {{ isEditing ? 'Update Status' : 'Create Status' }}
                            </button>
                            <button v-if="isEditing" @click="resetForm" type="button" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition-all">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden border-0">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Order</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Identity</th>
                                <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Preview</th>
                                <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="status in statuses" :key="status.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 text-xs font-bold text-gray-400">{{ status.order }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-black uppercase tracking-widest text-gray-800 dark:text-white">{{ status.name }}</div>
                                    <div class="text-[9px] font-mono text-gray-400 mt-1">{{ status.color }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :style="{ backgroundColor: status.color }" class="px-3 py-1 rounded-full text-white text-[9px] font-black uppercase tracking-widest shadow-sm">
                                        {{ status.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-4">
                                        <button @click="editStatus(status)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 text-[10px] font-black uppercase tracking-widest">Edit</button>
                                        <button @click="deleteStatus(status.id)" class="text-red-500 hover:text-red-700 text-[10px] font-black uppercase tracking-widest">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


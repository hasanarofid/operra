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
                <div class="bg-white p-6 shadow sm:rounded-lg h-fit">
                    <h3 class="font-bold text-lg mb-4">{{ isEditing ? 'Edit Status' : 'Add New Status' }}</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Status Name</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Color</label>
                            <div class="flex gap-2 mt-1">
                                <input v-model="form.color" type="color" class="h-10 w-12 rounded border-gray-300">
                                <input v-model="form.color" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm uppercase">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Display Order</label>
                            <input v-model="form.order" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex gap-2 pt-4">
                            <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold uppercase">
                                {{ isEditing ? 'Update' : 'Create' }}
                            </button>
                            <button v-if="isEditing" @click="resetForm" type="button" class="bg-gray-500 text-white px-4 py-2 rounded text-sm font-bold uppercase">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="lg:col-span-2 bg-white shadow sm:rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preview</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="status in statuses" :key="status.id">
                                <td class="px-6 py-4 text-sm">{{ status.order }}</td>
                                <td class="px-6 py-4 text-sm font-bold uppercase text-gray-900">{{ status.name }}</td>
                                <td class="px-6 py-4">
                                    <span :style="{ backgroundColor: status.color }" class="px-2 py-1 rounded text-white text-[10px] font-bold uppercase">
                                        {{ status.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <button @click="editStatus(status)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button @click="deleteStatus(status.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


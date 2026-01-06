<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object
});

const form = useForm({
    company_name: props.settings.company_name || '',
    company_address: props.settings.company_address || '',
    company_phone: props.settings.company_phone || '',
    company_email: props.settings.company_email || '',
    currency: props.settings.currency || 'IDR',
});

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Settings updated successfully!'),
    });
};
</script>

<template>
    <Head title="Company Settings" />

    <AuthenticatedLayout>
        <template #header>
            System Settings
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Company Profile</h3>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        <form @submit.prevent="submit" class="max-w-2xl">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                                    <input v-model="form.company_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
                                    <input v-model="form.currency" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                                    <input v-model="form.company_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                    <input v-model="form.company_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Address</label>
                                <textarea v-model="form.company_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>

                            <div class="mt-6">
                                <button type="submit" :disabled="form.processing" class="bg-operra-500 text-white px-6 py-2 rounded-md font-bold uppercase text-xs shadow hover:bg-operra-600 transition-colors">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


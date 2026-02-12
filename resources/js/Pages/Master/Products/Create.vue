<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    route_names: Object
});

const form = useForm({
    name: '',
    sku: '',
    description: '',
    purchase_price: 0,
    selling_price: 0,
    min_stock: 5,
});

const submit = () => {
    form.post(route(props.route_names.store));
};
</script>

<template>
    <Head title="Create Product" />

    <AuthenticatedLayout>
        <template #header>
            Create New Product
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Product Details</h3>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        <form @submit.prevent="submit" class="max-w-2xl">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                                    <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                    <input v-model="form.sku" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Price</label>
                                    <input v-model="form.purchase_price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selling Price</label>
                                    <input v-model="form.selling_price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Stock Alert</label>
                                    <input v-model="form.min_stock" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>

                            <div class="mt-6 flex gap-4">
                                <button type="submit" :disabled="form.processing" class="bg-operra-500 text-white px-6 py-2 rounded-md font-bold uppercase text-xs shadow hover:bg-operra-600 transition-all duration-150">
                                    Save Product
                                </button>
                                <Link :href="route(route_names.index)" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md font-bold uppercase text-xs shadow hover:bg-gray-300 transition-all duration-150">
                                    Cancel
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


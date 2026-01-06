<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    item_name: '',
    sku: '',
    stock_quantity: 0,
    unit_price: 0,
});

const submit = () => {
    form.post(route('inventory.store'));
};
</script>

<template>
    <Head title="Add Inventory Item" />

    <AuthenticatedLayout>
        <template #header>
            Add New Inventory Item
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Item Details</h3>
                    </div>
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        <form @submit.prevent="submit" class="max-w-xl">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item Name</label>
                                <input v-model="form.item_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <div v-if="form.errors.item_name" class="text-red-500 text-xs mt-1">{{ form.errors.item_name }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                <input v-model="form.sku" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <div v-if="form.errors.sku" class="text-red-500 text-xs mt-1">{{ form.errors.sku }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Quantity</label>
                                <input v-model="form.stock_quantity" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <div v-if="form.errors.stock_quantity" class="text-red-500 text-xs mt-1">{{ form.errors.stock_quantity }}</div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                <input v-model="form.unit_price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-operra-500 focus:ring-operra-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <div v-if="form.errors.unit_price" class="text-red-500 text-xs mt-1">{{ form.errors.unit_price }}</div>
                            </div>

                            <div class="mt-6 flex gap-4">
                                <button type="submit" :disabled="form.processing" class="bg-operra-500 text-white px-4 py-2 rounded-md font-bold uppercase text-xs shadow hover:bg-operra-600 transition-colors">
                                    Save Item
                                </button>
                                <Link :href="route('inventory.index')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-bold uppercase text-xs shadow hover:bg-gray-300 transition-colors">
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


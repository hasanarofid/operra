<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    products: Object,
    route_names: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const deleteProduct = (id) => {
    if (confirm('Are you want to delete this product?')) {
        router.delete(route(props.route_names.destroy, id));
    }
};
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            Master Data: Products
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                                <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Product Catalog</h3>
                            </div>
                            <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                                <Link :href="route(route_names.create)" class="bg-operra-500 text-white active:bg-operra-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150 shadow hover:shadow-md">
                                    Create Product
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">SKU</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Product Name</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Sell Price</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Min Stock</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="product in products.data" :key="product.id">
                                    <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left font-bold text-gray-700 dark:text-gray-200">{{ product.sku }}</th>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">{{ product.name }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-700 dark:text-gray-200">{{ formatCurrency(product.selling_price) }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">{{ product.min_stock }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 flex gap-2">
                                        <Link :href="route(route_names.edit, product.id)" class="text-indigo-500 hover:text-indigo-700 font-bold uppercase text-xs">Edit</Link>
                                        <button @click="deleteProduct(product.id)" class="text-red-500 hover:text-red-700 font-bold uppercase text-xs">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                        <!-- Pagination links here -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


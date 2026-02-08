<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    customers: Object,
    canCreate: Boolean,
});

const page = usePage();
const currentPortal = computed(() => {
    if (route().current('crm.wa.*')) return 'wa_blast';
    if (route().current('crm.sales.*')) return 'sales_crm';
    return null;
});

const createRoute = computed(() => {
    return currentPortal.value === 'wa_blast' ? 'crm.wa.leads.create' : 'crm.sales.customers.create';
});

const editRouteName = computed(() => {
    return currentPortal.value === 'wa_blast' ? 'crm.wa.leads.edit' : 'crm.sales.customers.edit';
});

const destroyRouteName = computed(() => {
    return currentPortal.value === 'wa_blast' ? 'crm.wa.leads.destroy' : 'crm.sales.customers.destroy';
});

const deleteCustomer = (customer) => {
    if (!confirm('Are you sure you want to delete this lead?')) return;

    router.delete(route(destroyRouteName.value, customer.id), {
        onSuccess: () => {
            // Toast handled by flash message in layout
        },
        onError: () => {
             alert('Failed to delete lead.');
        }
    });
};
</script>

<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ currentPortal === 'wa_blast' ? 'CRM: Manage Leads' : 'Master Data: Customers' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-xl overflow-hidden p-6">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full max-w-full flex-grow flex-1">
                                <h3 class="font-bold text-lg text-gray-700 dark:text-gray-200">
                                    {{ currentPortal === 'wa_blast' ? 'Leads List' : 'Customers List' }}
                                </h3>
                            </div>
                            <div class="relative w-full max-w-full flex-grow flex-1 text-right" v-if="canCreate">
                                <Link :href="route(createRoute)" class="bg-blue-600 text-white active:bg-blue-700 text-xs font-bold uppercase px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150">
                                    Add {{ currentPortal === 'wa_blast' ? 'Lead' : 'Customer' }}
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto mt-4">
                        <table class="items-center w-full bg-transparent border-collapse">
                            <thead>
                                <tr>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Name</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Email</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Phone</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Status</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Source</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Assigned To</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">Address</th>
                                    <th class="px-6 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-300 align-middle border border-solid border-gray-100 dark:border-gray-600 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="cust in customers.data" :key="cust.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left font-bold text-gray-700 dark:text-gray-200">{{ cust.name }}</th>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">{{ cust.email }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-700 dark:text-gray-200">{{ cust.phone }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                        <span :class="[
                                            'px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                            cust.status === 'customer' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                        ]">
                                            {{ cust.status || 'lead' }}
                                        </span>
                                    </td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">
                                        {{ cust.lead_source || '-' }}
                                    </td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-600 dark:text-gray-400">
                                        {{ cust.assigned_sales ? cust.assigned_sales.name : '-' }}
                                    </td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-gray-700 dark:text-gray-200">{{ cust.address }}</td>
                                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-center">
                                        <div class="flex items-center justify-center gap-2" v-if="canCreate">
                                            <Link :href="route(editRouteName, cust.id)" class="text-blue-500 hover:text-blue-700 font-bold uppercase text-[10px]">
                                                Edit
                                            </Link>
                                            <button @click="deleteCustomer(cust)" class="text-red-500 hover:text-red-700 font-bold uppercase text-[10px]">
                                                Delete
                                            </button>
                                        </div>
                                        <div v-else>
                                            <span class="text-gray-400 text-[10px] italic">Restricted</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


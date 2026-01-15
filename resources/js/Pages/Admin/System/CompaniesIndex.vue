<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    companies: Array,
    pricingPlans: Array,
});

const selectedCompany = ref(null);
const showEditModal = ref(false);

const form = useForm({
    pricing_plan_id: '',
    subscription_ends_at: '',
    status: '',
});

const openEditModal = (company) => {
    selectedCompany.value = company;
    form.pricing_plan_id = company.pricing_plan_id || '';
    form.subscription_ends_at = company.subscription_ends_at ? new Date(company.subscription_ends_at).toISOString().split('T')[0] : '';
    form.status = company.status;
    showEditModal.value = true;
};

const submit = () => {
    form.patch(route('admin.system.companies.update', selectedCompany.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const isExpired = (dateString) => {
    if (!dateString) return false;
    return new Date(dateString) < new Date();
};
</script>

<template>
    <Head title="Monitoring Pelanggan - System Admin" />

    <AuthenticatedLayout>
        <template #header>
            Monitoring Pelanggan & Langganan
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-3xl p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tighter">Daftar Pelanggan CRM</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pantau status langganan dan penggunaan modul semua tenant.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Perusahaan</th>
                                    <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Paket</th>
                                    <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Masa Berlaku</th>
                                    <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Status</th>
                                    <th class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                <tr v-for="company in companies" :key="company.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-800 dark:text-white">{{ company.name }}</div>
                                        <div class="text-[10px] text-gray-400 uppercase font-bold">{{ company.users_count }} Users Active</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 rounded-lg bg-operra-100 dark:bg-operra-900/30 text-operra-600 dark:text-operra-400 text-[10px] font-black uppercase tracking-widest border border-operra-500/20">
                                            {{ company.plan?.name || 'BELUM BERLANGGANAN' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div :class="[isExpired(company.subscription_ends_at) ? 'text-red-500' : 'text-gray-600 dark:text-gray-300']" class="text-xs font-bold">
                                            {{ formatDate(company.subscription_ends_at) }}
                                        </div>
                                        <div v-if="isExpired(company.subscription_ends_at)" class="text-[8px] font-black uppercase text-red-400 tracking-widest">EXPIRED</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span :class="[
                                            'px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest',
                                            company.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                        ]">
                                            {{ company.status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <button @click="openEditModal(company)" class="p-2 text-gray-400 hover:text-operra-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Subscription Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-[100] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="showEditModal = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-sm transition-opacity"></div>
                
                <div class="relative bg-white dark:bg-gray-800 rounded-[32px] w-full max-w-md p-8 shadow-2xl border border-white/5">
                    <div class="mb-6 text-center">
                        <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-tighter">Edit Langganan</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedCompany?.name }}</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5 ml-1">Pilih Paket</label>
                            <select v-model="form.pricing_plan_id" class="w-full bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl p-3 text-sm focus:border-operra-500 focus:ring-0 transition-all dark:text-white">
                                <option value="">Tanpa Paket</option>
                                <option v-for="plan in pricingPlans" :key="plan.id" :value="plan.id">{{ plan.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5 ml-1">Berakhir Pada</label>
                            <input v-model="form.subscription_ends_at" type="date" class="w-full bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl p-3 text-sm focus:border-operra-500 focus:ring-0 transition-all dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1.5 ml-1">Status Perusahaan</label>
                            <select v-model="form.status" class="w-full bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-gray-600 rounded-2xl p-3 text-sm focus:border-operra-500 focus:ring-0 transition-all dark:text-white">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="showEditModal = false" class="flex-1 py-3 rounded-2xl font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">Batal</button>
                            <button type="submit" :disabled="form.processing" class="flex-1 py-3 rounded-2xl bg-operra-600 text-white font-black uppercase tracking-widest text-xs hover:bg-operra-700 shadow-xl shadow-operra-600/20 transition-all disabled:opacity-50">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


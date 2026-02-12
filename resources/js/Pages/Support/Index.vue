<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    tickets: Array,
});

const showModal = ref(false);
const form = useForm({
    subject: '',
    message: '',
    priority: 'normal',
});

const openModal = () => {
    form.reset();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submitTicket = () => {
    form.post(route('support.store'), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Head title="Komplain & Bantuan" />

    <AuthenticatedLayout>
        <template #header>
            PUSAT BANTUAN & KOMPLAIN
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tighter">Riwayat Komplain</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">Sampaikan kendala atau pertanyaan Anda langsung ke tim Operra.</p>
                    </div>
                    <button @click="openModal" class="bg-operra-600 hover:bg-operra-700 text-white font-black uppercase text-xs tracking-widest px-6 py-3 rounded-xl shadow-lg shadow-operra-600/20 transition-all active:scale-95 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                        Buat Laporan Baru
                    </button>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-[30px] shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-8 py-5">Subjek / Masalah</th>
                                    <th class="px-8 py-5">Status</th>
                                    <th class="px-8 py-5">Prioritas</th>
                                    <th class="px-8 py-5">Update Terakhir</th>
                                    <th class="px-8 py-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-gray-800 dark:text-gray-100 uppercase tracking-tight group-hover:text-operra-600 transition-colors">{{ ticket.subject }}</div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-1 max-w-md font-medium">
                                            {{ ticket.messages?.[0]?.message || 'Tidak ada pratinjau pesan.' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter"
                                            :class="{
                                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': ticket.status === 'open',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': ticket.status === 'pending_user' || ticket.status === 'pending_admin',
                                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': ticket.status === 'closed'
                                            }">
                                            {{ ticket.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-[10px] font-black uppercase tracking-widest" :class="{
                                        'text-red-500': ticket.priority === 'urgent',
                                        'text-orange-500': ticket.priority === 'high',
                                        'text-blue-500': ticket.priority === 'normal',
                                        'text-gray-500': ticket.priority === 'low'
                                    }">
                                        {{ ticket.priority }}
                                    </td>
                                    <td class="px-8 py-6 text-xs text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">
                                        {{ new Date(ticket.updated_at).toLocaleString('id-ID', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <Link :href="route('support.show', ticket.id)" class="inline-flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-operra-500 hover:text-white dark:hover:bg-operra-600 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 rounded-xl transition-all shadow-sm">
                                            Buka Pesan
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7-7 7M3 12h18"></path></svg>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="tickets.length === 0">
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="h-20 w-20 bg-gray-50 dark:bg-gray-900/50 rounded-full flex items-center justify-center text-gray-300 dark:text-gray-700 mb-4">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Belum ada riwayat komplain.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Ticket Modal -->
        <Modal :show="showModal" @close="closeModal" maxWidth="xl">
            <div class="p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tighter">
                        Laporan Komplain Baru
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-medium">Jelaskan detail kendala Anda agar kami dapat membantu lebih cepat.</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <InputLabel for="subject" value="Subjek / Ringkasan Masalah" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1" />
                        <TextInput
                            id="subject"
                            type="text"
                            class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-transparent focus:border-operra-500 focus:ring-0 rounded-2xl p-4 transition-all"
                            v-model="form.subject"
                            placeholder="Apa yang terjadi? (Contoh: Gagal upload media)"
                            required
                        />
                         <p v-if="form.errors.subject" class="text-red-500 text-[10px] font-bold mt-2 ml-1">{{ form.errors.subject }}</p>
                    </div>

                    <div>
                        <InputLabel for="priority" value="Tingkat Prioritas" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1" />
                        <select
                            id="priority"
                            v-model="form.priority"
                            class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-transparent focus:border-operra-500 focus:ring-0 rounded-2xl p-4 transition-all text-sm font-bold text-gray-700 dark:text-gray-300"
                        >
                            <option value="low">LOW - Bisa Menunggu</option>
                            <option value="normal">NORMAL - Perlu Diperhatikan</option>
                            <option value="high">HIGH - Mengganggu Operasional</option>
                            <option value="urgent">URGENT - Sistem Terhenti</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel for="message" value="Detail Pesan" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1" />
                        <textarea
                            id="message"
                            class="mt-1 block w-full bg-gray-50 dark:bg-gray-900 border-transparent focus:border-operra-500 focus:ring-0 rounded-2xl p-4 transition-all text-sm font-medium text-gray-700 dark:text-gray-300 min-h-[150px]"
                            v-model="form.message"
                            placeholder="Tuliskan detail kronologi atau kendala Anda di sini..."
                            required
                        ></textarea>
                        <p v-if="form.errors.message" class="text-red-500 text-[10px] font-bold mt-2 ml-1">{{ form.errors.message }}</p>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <SecondaryButton @click="closeModal" class="flex-1 justify-center py-4 rounded-2xl font-black uppercase tracking-widest text-xs">Batalkan</SecondaryButton>
                    <PrimaryButton class="flex-1 justify-center py-4 rounded-2xl bg-operra-600 hover:bg-operra-700 shadow-xl shadow-operra-600/20 font-black uppercase tracking-widest text-xs" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submitTicket">
                        Kirim Laporan
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

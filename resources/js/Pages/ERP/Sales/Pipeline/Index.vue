<script setup>
import AppLayout from "../../../../Layouts/AuthenticatedLayout.vue";
import { ref, computed } from "vue";
import { useForm, router, Link } from "@inertiajs/vue3";
import draggable from "vuedraggable";

const props = defineProps({
    stages: Array,
    totalValue: Number,
});

const localStages = ref(props.stages);

// Drag & Drop Configuration
const dragOptions = computed(() => ({
    animation: 300,
    group: "deals",
    disabled: false,
    ghostClass: "ghost-card",
    dragClass: "dragging-card",
}));

// Handle Drop Event
const onDrop = (evt, newStageId) => {
    if (evt.added) {
        const orderId = evt.added.element.id;

        // Backend Update
        router.post(
            route("crm.sales.pipeline.update", orderId),
            {
                stage_id: newStageId,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Success feedback can be added here
                },
                onError: () => {
                    // Revert if failed
                    router.reload();
                },
            },
        );
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <AppLayout title="Sales Pipeline">
        <template #header>
            <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
            >
                <div>
                    <h2
                        class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tighter"
                    >
                        SALES <span class="text-emerald-500">PIPELINE</span>
                    </h2>
                    <p
                        class="text-sm text-gray-500 dark:text-gray-400 font-medium"
                    >
                        Kelola peluang penjualan Anda dengan antarmuka Kanban
                        premium.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl shadow-sm border border-emerald-500/10 flex flex-col items-end group/total"
                        title="Akumulasi nilai seluruh pesanan dalam pipeline"
                    >
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-emerald-500 opacity-50 group-hover/total:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400"
                                >Total Potensi</span
                            >
                        </div>
                        <span
                            class="text-xl font-black text-emerald-600 dark:text-emerald-400 leading-none"
                        >
                            {{ formatCurrency(totalValue) }}
                        </span>
                    </div>
                    <Link
                        :href="route('crm.sales.orders.create')"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-2xl shadow-lg shadow-emerald-600/20 transition-all active:scale-95 group flex items-center justify-center"
                        title="Tambah Pesanan Baru"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 group-hover:rotate-90 transition-transform duration-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12 overflow-x-auto custom-scrollbar">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-6 min-w-max pb-8">
                    <!-- Columns -->
                    <div
                        v-for="stage in localStages"
                        :key="stage.id"
                        class="w-80 flex-shrink-0 group/column"
                    >
                        <!-- Header -->
                        <div
                            class="bg-white dark:bg-gray-800 p-4 rounded-t-3xl shadow-sm border-t-4 border-x border-gray-100 dark:border-gray-700"
                            :style="{ borderTopColor: stage.color }"
                        >
                            <div class="flex justify-between items-center mb-1">
                                <h3
                                    class="font-black text-gray-800 dark:text-white uppercase tracking-tighter"
                                >
                                    {{ stage.name }}
                                </h3>
                                <div
                                    class="h-6 w-6 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center"
                                >
                                    <span
                                        class="text-[10px] font-black text-gray-500"
                                        >{{ stage.orders.length }}</span
                                    >
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex-1 h-1 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden"
                                >
                                    <div
                                        class="h-full rounded-full transition-all duration-500"
                                        :style="{
                                            width: stage.probability + '%',
                                            backgroundColor: stage.color,
                                        }"
                                    ></div>
                                </div>
                                <span
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-tighter"
                                    >{{ stage.probability }}% Prob.</span
                                >
                            </div>
                        </div>

                        <!-- Draggable Area -->
                        <div
                            class="bg-gray-50/50 dark:bg-gray-900/50 p-3 min-h-[600px] rounded-b-3xl border-x border-b border-gray-100 dark:border-gray-700 backdrop-blur-sm transition-colors group-hover/column:bg-emerald-50/30 dark:group-hover/column:bg-emerald-900/10"
                        >
                            <draggable
                                v-model="stage.orders"
                                item-key="id"
                                v-bind="dragOptions"
                                @change="(evt) => onDrop(evt, stage.id)"
                                class="space-y-4 min-h-[500px]"
                            >
                                <template #item="{ element }">
                                    <div
                                        class="bg-white dark:bg-gray-800 p-5 rounded-[24px] shadow-sm border border-gray-100 dark:border-gray-700 cursor-grab active:cursor-grabbing hover:shadow-xl hover:shadow-emerald-900/5 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group/card"
                                    >
                                        <!-- Card Indicator -->
                                        <div
                                            class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-full -mr-12 -mt-12 transition-transform group-hover/card:scale-150"
                                        ></div>

                                        <div class="relative z-10">
                                            <div
                                                class="flex justify-between items-start mb-3"
                                            >
                                                <span
                                                    class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-lg border border-emerald-500/10"
                                                >
                                                    {{ element.so_number }}
                                                </span>
                                                <span
                                                    class="text-[10px] font-bold text-gray-400 tabular-nums"
                                                >
                                                    {{
                                                        new Date(
                                                            element.created_at,
                                                        ).toLocaleDateString()
                                                    }}
                                                </span>
                                            </div>

                                            <div
                                                class="font-black text-gray-800 dark:text-white mb-1 leading-tight group-hover/card:text-emerald-600 transition-colors"
                                            >
                                                {{
                                                    element.customer?.name ||
                                                    "Unknown Customer"
                                                }}
                                            </div>

                                            <div
                                                class="text-lg font-black text-emerald-600 dark:text-emerald-400 mb-4 tracking-tighter"
                                            >
                                                {{
                                                    formatCurrency(
                                                        element.total_amount,
                                                    )
                                                }}
                                            </div>

                                            <div
                                                class="flex justify-between items-center pt-3 border-t border-gray-50 dark:border-gray-700"
                                            >
                                                <div
                                                    class="flex items-center gap-1.5"
                                                >
                                                    <div
                                                        class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"
                                                    ></div>
                                                    <span
                                                        class="text-[10px] font-black uppercase tracking-widest text-gray-400"
                                                    >
                                                        {{
                                                            element.probability ||
                                                            stage.probability
                                                        }}% Deal
                                                    </span>
                                                </div>

                                                <!-- Action Button -->
                                                <Link
                                                    :href="
                                                        route(
                                                            'erp.sales-order.show',
                                                            element.id,
                                                        )
                                                    "
                                                    class="h-8 w-8 bg-gray-50 dark:bg-gray-700 rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="h-4 w-4"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                                                        />
                                                    </svg>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>

                    <!-- Add Column Placeholder -->
                    <div
                        class="w-80 flex-shrink-0 flex items-center justify-center"
                    >
                        <button
                            class="w-full h-[200px] border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-[32px] flex flex-col items-center justify-center gap-2 text-gray-400 hover:border-emerald-500 hover:text-emerald-500 transition-all group"
                        >
                            <div
                                class="h-12 w-12 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-colors"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                            </div>
                            <span
                                class="font-black text-xs uppercase tracking-widest"
                                >Tambah Kolom</span
                            >
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.ghost-card {
    opacity: 0.2;
    background: #10b981 !important;
    border: 2px dashed #10b981 !important;
}

.dragging-card {
    transform: rotate(2deg) scale(1.05);
    box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25) !important;
    cursor: grabbing !important;
}

.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 20px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #10b981;
}
</style>

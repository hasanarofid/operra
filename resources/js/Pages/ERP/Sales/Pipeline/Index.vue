<script setup>
import AppLayout from '../../../../Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import draggable from 'vuedraggable';

const props = defineProps({
    stages: Array,
    totalValue: Number,
});

const localStages = ref(props.stages);

// Drag & Drop Configuration
const dragOptions = computed(() => ({
    animation: 200,
    group: "deals",
    disabled: false,
    ghostClass: "ghost",
}));

// Handle Drop Event
const onDrop = (evt, newStageId) => {
    if (evt.added) {
        const orderId = evt.added.element.id;
        
        // Optimistic UI Update (Already handled by vuedraggable v-model)
        
        // Backend Update
        router.post(route('crm.sales.pipeline.update', orderId), {
            stage_id: newStageId
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: Toast notification
            },
            onError: () => {
                // Revert if failed (Reload page)
                router.reload();
            }
        });
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};

</script>

<template>
    <AppLayout title="Sales Pipeline">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Sales Pipeline (Kanban)
                </h2>
                <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-bold">
                    Total Potensi: {{ formatCurrency(totalValue) }}
                </div>
            </div>
        </template>

        <div class="py-12 overflow-x-auto">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex space-x-4 min-w-max pb-4">
                    
                    <!-- Columns -->
                    <div v-for="stage in localStages" :key="stage.id" class="w-80 flex-shrink-0">
                        <!-- Header -->
                        <div class="bg-white p-3 rounded-t-lg shadow-sm border-b-4" :style="{ borderColor: stage.color }">
                            <div class="flex justify-between items-center">
                                <h3 class="font-bold text-gray-700">{{ stage.name }}</h3>
                                <span class="bg-gray-100 text-xs px-2 py-1 rounded-full text-gray-500">{{ stage.orders.length }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1">Probability: {{ stage.probability }}%</div>
                        </div>

                        <!-- Draggable Area -->
                        <div class="bg-gray-100 p-2 min-h-[500px] rounded-b-lg shadow-inner">
                            <draggable
                                v-model="stage.orders"
                                :component-data="{ name: 'fade' }"
                                item-key="id"
                                v-bind="dragOptions"
                                @change="(evt) => onDrop(evt, stage.id)"
                                class="space-y-3 min-h-[100px]"
                            >
                                <template #item="{ element }">
                                    <div class="bg-white p-4 rounded shadow cursor-move hover:shadow-md transition bg-opacity-90 border border-gray-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="text-xs font-bold text-blue-600">#{{ element.so_number }}</span>
                                            <span class="text-[10px] text-gray-400">{{ new Date(element.created_at).toLocaleDateString() }}</span>
                                        </div>
                                        <div class="font-semibold text-gray-800 mb-1 truncate">{{ element.customer?.name || 'Unknown Customer' }}</div>
                                        <div class="text-sm text-green-600 font-bold mb-2">{{ formatCurrency(element.total_amount) }}</div>
                                        
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                            <div class="text-xs text-gray-500">
                                                {{ element.probability || stage.probability }}% Deal
                                            </div>
                                            <!-- Action Button (View) -->
                                            <a :href="route('erp.sales-order.show', element.id)" class="text-gray-400 hover:text-indigo-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}
</style>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
    trend: String,
    trendColor: {
        type: String,
        default: 'text-emerald-500'
    },
    alert: Boolean
});
</script>

<template>
    <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 rounded-xl mb-6 xl:mb-0 shadow-lg border-l-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
         :class="alert ? 'border-red-500 bg-red-50/10' : 'border-operra-500'">
        <div class="flex-auto p-5">
            <div class="flex items-center justify-between">
                <div class="relative w-full pr-4">
                    <h5 class="text-gray-400 uppercase font-black text-[10px] tracking-widest mb-1">
                        {{ title }}
                    </h5>
                    <div class="flex items-baseline gap-2">
                        <span class="font-black text-2xl text-gray-800 dark:text-white tracking-tighter">
                            {{ value }}
                        </span>
                        <span v-if="trend" :class="trendColor" class="text-[10px] font-bold">
                            {{ trend }}
                        </span>
                    </div>
                </div>
                <div class="relative w-auto shrink-0">
                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-inner rounded-2xl"
                         :class="alert ? 'bg-red-500 shadow-red-200/50' : 'bg-gradient-to-br from-operra-500 to-indigo-600 shadow-operra-200/50'">
                        <slot name="icon"></slot>
                    </div>
                </div>
            </div>
            <div v-if="alert" class="mt-3 flex items-center gap-1">
                <span class="flex h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-red-500 uppercase tracking-tighter">Attention Required</span>
            </div>
        </div>
    </div>
</template>

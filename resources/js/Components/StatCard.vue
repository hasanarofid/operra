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
    <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 rounded mb-6 xl:mb-0 shadow-lg border-t-4 transition-all duration-300"
         :class="alert ? 'border-red-500' : 'border-operra-500'">
        <div class="flex-auto p-4">
            <div class="flex flex-wrap">
                <div class="relative w-full pr-4 max-w-full flex-grow flex-1">
                    <h5 class="text-gray-400 uppercase font-bold text-xs">
                        {{ title }}
                    </h5>
                    <span class="font-bold text-xl text-gray-700 dark:text-white">
                        {{ value }}
                    </span>
                </div>
                <div class="relative w-auto pl-4 flex-initial">
                    <div class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 shadow-lg rounded-full"
                         :class="alert ? 'bg-red-500' : 'bg-operra-500'">
                        <slot name="icon"></slot>
                    </div>
                </div>
            </div>
            <p v-if="trend" class="text-sm text-gray-400 mt-4">
                <span :class="trendColor" class="mr-2 font-bold">
                    {{ trend }}
                </span>
                <span class="whitespace-nowrap italic text-xs">Since last month</span>
            </p>
        </div>
    </div>
</template>

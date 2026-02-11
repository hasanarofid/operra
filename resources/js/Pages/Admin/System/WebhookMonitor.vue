<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    logs: Object, // Paginator object
});

const autoRefresh = ref(false);
let intervalId = null;

const toggleAutoRefresh = () => {
    autoRefresh.value = !autoRefresh.value;
    if (autoRefresh.value) {
        startPolling();
    } else {
        stopPolling();
    }
};

const startPolling = () => {
    intervalId = setInterval(() => {
        router.reload({
            only: ["logs"],
            preserveScroll: true,
            preserveState: true,
        });
    }, 5000); // Poll every 5 seconds
};

const stopPolling = () => {
    if (intervalId) clearInterval(intervalId);
};

onUnmounted(() => {
    stopPolling();
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
};

// Helper to extract useful info from payload
const getMessageSummary = (payload) => {
    if (!payload) return "-";
    try {
        if (payload.object === "whatsapp_business_account") {
            const changes = payload.entry?.[0]?.changes?.[0]?.value;
            if (changes?.messages?.[0]) {
                const msg = changes.messages[0];
                const type = msg.type;
                let content = `[${type}]`;

                if (type === "text") content = msg.text?.body;
                else if (type === "image")
                    content += " " + (msg.image?.caption || "");
                else if (type === "document")
                    content += " " + (msg.document?.filename || "");
                else if (type === "button")
                    content += " " + (msg.button?.text || "");
                else if (type === "interactive") {
                    content +=
                        " " +
                        (msg.interactive?.type === "button_reply"
                            ? msg.interactive.button_reply.title
                            : msg.interactive?.type === "list_reply"
                              ? msg.interactive.list_reply.title
                              : "");
                }

                const from = msg.from;
                const name = changes.contacts?.[0]?.profile?.name || from;

                return {
                    is_message: true,
                    from: from,
                    name: name,
                    content: content,
                    phone_id: changes.metadata?.phone_number_id,
                    display_phone: changes.metadata?.display_phone_number,
                };
            } else if (changes?.statuses?.[0]) {
                const status = changes.statuses[0];
                return {
                    is_message: false,
                    type: "status_update",
                    status: status.status,
                    recipient: status.recipient_id,
                    phone_id: changes.metadata?.phone_number_id,
                };
            }
        }
        return { is_message: false, type: "unknown", raw: "Complex payload" };
    } catch (e) {
        return { is_message: false, type: "error", raw: "Parse Error" };
    }
};

const expandedLogs = ref(new Set());
const toggleExpand = (id) => {
    if (expandedLogs.value.has(id)) {
        expandedLogs.value.delete(id);
    } else {
        expandedLogs.value.add(id);
    }
};
</script>

<template>
    <Head title="WhatsApp Webhook Monitor" />

    <AuthenticatedLayout>
        <template #header> WhatsApp Webhook Monitor </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-3xl p-6 md:p-8"
                >
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4"
                    >
                        <div>
                            <h2
                                class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tighter"
                            >
                                Webhook Logs
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Real-time incoming webhook data from Meta &
                                providers.
                            </p>
                        </div>
                        <button
                            @click="toggleAutoRefresh"
                            :class="[
                                'px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2',
                                autoRefresh
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-500/20'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                            ]"
                        >
                            <span
                                :class="{
                                    'animate-pulse bg-green-500': autoRefresh,
                                    'bg-gray-400': !autoRefresh,
                                }"
                                class="w-2 h-2 rounded-full"
                            ></span>
                            {{
                                autoRefresh
                                    ? "Auto Refresh On"
                                    : "Auto Refresh Off"
                            }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-gray-100 dark:border-gray-700"
                                >
                                    <th
                                        class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest w-1"
                                    >
                                        Expand
                                    </th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest"
                                    >
                                        Time
                                    </th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest"
                                    >
                                        Provider
                                    </th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest"
                                    >
                                        Summary
                                    </th>
                                    <th
                                        class="py-4 px-4 text-[10px] font-black uppercase text-gray-400 tracking-widest"
                                    >
                                        WABA ID / Phone
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-50 dark:divide-gray-700/50"
                            >
                                <template
                                    v-for="log in logs.data"
                                    :key="log.id"
                                >
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer"
                                        @click="toggleExpand(log.id)"
                                    >
                                        <td class="py-4 px-4 text-center">
                                            <svg
                                                :class="{
                                                    'rotate-90':
                                                        expandedLogs.has(
                                                            log.id,
                                                        ),
                                                }"
                                                class="w-4 h-4 text-gray-400 transition-transform"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7"
                                                ></path>
                                            </svg>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div
                                                class="font-bold text-gray-800 dark:text-gray-200 text-xs"
                                            >
                                                {{ formatDate(log.created_at) }}
                                            </div>
                                            <div
                                                class="text-[9px] text-gray-400 font-mono"
                                            >
                                                {{ log.sender_ip }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span
                                                :class="[
                                                    'px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest border',
                                                    log.provider === 'meta'
                                                        ? 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800'
                                                        : 'bg-orange-50 text-orange-600 border-orange-200 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-800',
                                                ]"
                                            >
                                                {{ log.provider }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div
                                                v-if="
                                                    getMessageSummary(
                                                        log.payload,
                                                    ).is_message
                                                "
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <span
                                                        class="font-bold text-gray-800 dark:text-gray-200 text-xs"
                                                        >{{
                                                            getMessageSummary(
                                                                log.payload,
                                                            ).name
                                                        }}</span
                                                    >
                                                    <span
                                                        class="text-[9px] text-gray-400"
                                                        >({{
                                                            getMessageSummary(
                                                                log.payload,
                                                            ).from
                                                        }})</span
                                                    >
                                                </div>
                                                <div
                                                    class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-1 font-mono bg-gray-50 dark:bg-gray-900/50 px-2 py-1 rounded inline-block"
                                                >
                                                    {{
                                                        getMessageSummary(
                                                            log.payload,
                                                        ).content
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                v-else-if="
                                                    getMessageSummary(
                                                        log.payload,
                                                    ).type === 'status_update'
                                                "
                                            >
                                                <span
                                                    class="text-xs text-gray-500 dark:text-gray-400"
                                                >
                                                    Status update:
                                                    <strong
                                                        class="uppercase"
                                                        :class="{
                                                            'text-green-500':
                                                                getMessageSummary(
                                                                    log.payload,
                                                                ).status ===
                                                                'read',
                                                            'text-blue-500':
                                                                getMessageSummary(
                                                                    log.payload,
                                                                ).status ===
                                                                'delivered',
                                                            'text-gray-500':
                                                                getMessageSummary(
                                                                    log.payload,
                                                                ).status ===
                                                                'sent',
                                                        }"
                                                        >{{
                                                            getMessageSummary(
                                                                log.payload,
                                                            ).status
                                                        }}</strong
                                                    >
                                                    for
                                                    {{
                                                        getMessageSummary(
                                                            log.payload,
                                                        ).recipient
                                                    }}
                                                </span>
                                            </div>
                                            <div v-else>
                                                <span
                                                    class="text-xs text-gray-400 italic"
                                                    >Non-message payload</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            class="py-4 px-4 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{
                                                getMessageSummary(log.payload)
                                                    .phone_id || "-"
                                            }}
                                        </td>
                                    </tr>
                                    <!-- EXPANDED ROW -->
                                    <tr
                                        v-if="expandedLogs.has(log.id)"
                                        class="bg-gray-50 dark:bg-gray-900/50 animate-fadeIn"
                                    >
                                        <td colspan="5" class="p-4">
                                            <div
                                                class="bg-gray-900 text-gray-300 rounded-lg p-4 font-mono text-[10px] overflow-x-auto"
                                            >
                                                <div
                                                    class="flex justify-between items-center mb-2 border-b border-gray-700 pb-2"
                                                >
                                                    <span
                                                        class="font-bold text-white"
                                                        >RAW PAYLOAD</span
                                                    >
                                                    <span class="text-gray-500"
                                                        >ID: {{ log.id }}</span
                                                    >
                                                </div>
                                                <pre>{{
                                                    JSON.stringify(
                                                        log.payload,
                                                        null,
                                                        2,
                                                    )
                                                }}</pre>
                                            </div>
                                            <div
                                                v-if="log.headers"
                                                class="mt-2 text-[10px] text-gray-500"
                                            >
                                                <strong>Headers:</strong>
                                                {{
                                                    JSON.stringify(log.headers)
                                                }}
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-if="logs.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="py-8 text-center text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        No logs found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="mt-6 flex justify-between items-center"
                        v-if="logs.data.length > 0"
                    >
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Showing {{ logs.from }} to {{ logs.to }} of
                            {{ logs.total }} logs
                        </div>
                        <div class="flex gap-1">
                            <template
                                v-for="(link, key) in logs.links"
                                :key="key"
                            >
                                <div
                                    v-if="link.url === null"
                                    class="px-3 py-1 text-xs text-gray-400 border border-gray-200 dark:border-gray-700 rounded bg-gray-50 dark:bg-gray-800"
                                    v-html="link.label"
                                ></div>
                                <Link
                                    v-else
                                    :href="link.url"
                                    class="px-3 py-1 text-xs border rounded transition-colors"
                                    :class="
                                        link.active
                                            ? 'bg-operra-600 text-white border-operra-600'
                                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                                    "
                                    v-html="link.label"
                                >
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

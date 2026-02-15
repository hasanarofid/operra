<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { database } from "@/firebase";
import { ref as dbRef, onValue } from "firebase/database";
import OnboardingModal from "@/Components/OnboardingModal.vue";
import ExpirationModal from "@/Components/ExpirationModal.vue";
import ToastNotification from "@/Components/ToastNotification.vue";

// Global variable to persist across page navigations (Inertia re-mounts)
let globalLastSoundPlayedMessageId = null;
let globalAudio = null;

const collapseShow = ref("hidden");
const isSidebarCollapsed = ref(localStorage.getItem("sidebarCollapsed") === "true");
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    localStorage.setItem("sidebarCollapsed", isSidebarCollapsed.value);
};
const isDark = ref(localStorage.getItem("theme") !== "light");
const realTimeUnreadCount = ref(null);
const showPermissionPrompt = ref(false);
let unsubInbox = null;
let unsubNotif = null;

const page = usePage();

const playNotificationSound = () => {
    if (!globalAudio) {
        globalAudio = new Audio("/sound/sound.mp3");
    }
    console.log("[Operra] Memutar suara notifikasi...");
    globalAudio.play().catch((e) => {
        console.warn(
            "[Operra] Suara gagal diputar (biasanya karena belum ada interaksi user di halaman ini):",
            e,
        );
        showPermissionPrompt.value = true;
    });
};

const hasRole = (role) => page.props.auth.user.roles.includes(role);
const hasPermission = (permission) =>
    page.props.auth.user.permissions.includes(permission);
const isModuleEnabled = (module) =>
    page.props.auth.user.company?.enabled_modules?.includes(module);
const hasPlan = (planSlug) =>
    page.props.auth.user.company?.pricing_plan?.slug === planSlug;
const hasMinimumPlan = (planSlug) => {
    const plans = ["starter", "business-pro", "enterprise"];
    const userPlanIdx = plans.indexOf(
        page.props.auth.user.company?.pricing_plan?.slug || "starter",
    );
    const requiredPlanIdx = plans.indexOf(planSlug);
    return userPlanIdx >= requiredPlanIdx;
};

const currentPortal = computed(() => {
    if (route().current("crm.wa.*")) return "wa_blast";
    if (route().current("crm.sales.*")) return "sales_crm";
    if (route().current("crm.marketing.*")) return "marketing_crm";
    if (route().current("crm.support.*")) return "customer_service";

    // Check query parameter using Inertia URL state
    const portal = page.url.split("portal=")[1]?.split("&")[0];
    if (portal) return portal;

    // Fallback if only 1 module enabled
    const enabled = page.props.auth.user.company?.enabled_modules ?? [];
    return enabled.length === 1 ? enabled[0] : null;
});

const getPortalName = (portalId) => {
    const names = {
        wa_blast: "WhatsApp CRM",
        sales_crm: "Sales CRM",
        marketing_crm: "Marketing CRM",
        customer_service: "Customer Support",
        bot_antam: "War Antam Portal",
        analytical_crm: "Analytical CRM",
    };
    return names[portalId] || "Pilih Aplikasi";
};

const requestPermissions = async () => {
    // 1. Request Notification Permission
    if ("Notification" in window) {
        try {
            const permission = await Notification.requestPermission();
            console.log("[Operra] Notification permission:", permission);
            if (permission === "granted" || permission === "denied") {
                sessionStorage.setItem(
                    "operra_notification_prompt_dismissed",
                    "true",
                );
            }
        } catch (e) {
            console.error(
                "[Operra] Error requesting notification permission:",
                e,
            );
        }
    }

    // 2. Unlock Audio
    // Create audio if not exists
    if (!globalAudio) {
        globalAudio = new Audio("/sound/sound.mp3");
    }

    // Play a very short sound to unlock audio context
    globalAudio.muted = true; // Mute first to be safe
    globalAudio
        .play()
        .then(() => {
            globalAudio.pause();
            globalAudio.muted = false;
            globalAudio.currentTime = 0;
            console.log("[Operra] Audio system unlocked successfully");
            showPermissionPrompt.value = false;

            // Optional: Save to session storage that user has dismissed/enabled for this session
            sessionStorage.setItem("operra_audio_unlocked", "true");
            sessionStorage.setItem(
                "operra_notification_prompt_dismissed",
                "true",
            );
        })
        .catch((e) => {
            console.error("[Operra] Gagal unlock audio:", e);
            // Even if it fails, we close the prompt to not annoy the user,
            // it will reappear if playNotificationSound fails again later
            showPermissionPrompt.value = false;
        });
};

const dismissPermissionPrompt = () => {
    showPermissionPrompt.value = false;
    sessionStorage.setItem("operra_notification_prompt_dismissed", "true");
};

function toggleCollapseShow(classes) {
    collapseShow.value = classes;
}

function toggleTheme() {
    isDark.value = !isDark.value;
}

watch(
    isDark,
    (val) => {
        if (val) {
            document.documentElement.classList.add("dark");
            localStorage.setItem("theme", "dark");
        } else {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("theme", "light");
        }
    },
    { immediate: true },
);

onMounted(() => {
    // Check if we should show prompt
    const isAudioUnlocked = sessionStorage.getItem("operra_audio_unlocked");
    const isPromptDismissed = sessionStorage.getItem(
        "operra_notification_prompt_dismissed",
    );

    if (
        !isPromptDismissed &&
        (("Notification" in window && Notification.permission !== "granted") ||
            !isAudioUnlocked)
    ) {
        // Delay slightly for better UX
        setTimeout(() => {
            showPermissionPrompt.value = true;
        }, 1500);
    }

    // Global notification sound listener
    if (page.props.auth.user && database) {
        const userInboxRef = dbRef(
            database,
            `inbox/users/${page.props.auth.user.id}`,
        );
        unsubInbox = onValue(userInboxRef, (snapshot) => {
            const data = snapshot.val();
            if (data) {
                const incomingData = Object.values(data);

                // Temukan pesan terbaru dari customer untuk play sound
                const latestCustomerMessage = incomingData
                    .map((d) => d.message)
                    .filter((m) => m.sender_type === "customer")
                    .sort((a, b) => b.id - a.id)[0];

                if (
                    latestCustomerMessage &&
                    latestCustomerMessage.id !== globalLastSoundPlayedMessageId
                ) {
                    // Hanya bunyikan suara jika bukan loading pertama kali (saat web pertama kali dibuka)
                    if (globalLastSoundPlayedMessageId !== null) {
                        playNotificationSound();
                    }
                    globalLastSoundPlayedMessageId = latestCustomerMessage.id;
                }
            }
        });

        // Global unread count listener
        const userNotificationRef = dbRef(
            database,
            `notifications/users/${page.props.auth.user.id}`,
        );
        unsubNotif = onValue(userNotificationRef, (snapshot) => {
            const data = snapshot.val();
            if (data && typeof data.unread_count !== "undefined") {
                realTimeUnreadCount.value = data.unread_count;
            }
        });
    }
});

onUnmounted(() => {
    if (unsubInbox) unsubInbox();
    if (unsubNotif) unsubNotif();
});
</script>

<template>
    <div>
        <!-- Sidebar -->
        <nav
            class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white dark:bg-gray-900 flex flex-wrap items-center justify-between relative z-50 transition-all duration-300"
            :class="isSidebarCollapsed ? 'md:w-[85px]' : 'md:w-[280px]'"
        >
            <div
                class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto"
            >
                <!-- Toggler (Desktop) -->
                <button
                    @click="toggleSidebar"
                    class="hidden md:flex absolute -right-3 top-20 bg-emerald-500 text-white w-6 h-6 rounded-full items-center justify-center shadow-lg hover:bg-emerald-600 transition-all z-[60]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition-transform duration-300"
                        :class="isSidebarCollapsed ? 'rotate-180' : ''"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="3"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </button>
                <!-- Mobile Header (Visible only on mobile) -->
                <div
                    class="md:hidden flex items-center justify-between w-full mb-3"
                >
                    <!-- Left: Toggler -->
                    <button
                        class="cursor-pointer text-gray-500 p-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                        type="button"
                        v-on:click="
                            toggleCollapseShow(
                                'bg-white dark:bg-gray-800 shadow-2xl p-6',
                            )
                        "
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7"
                            ></path>
                        </svg>
                    </button>

                    <!-- Middle: Brand -->
                    <Link
                        class="flex items-center gap-2 text-gray-800 dark:text-gray-100"
                        :href="route('dashboard')"
                    >
                        <ApplicationLogo class="h-10 w-auto" />
                        <span
                            class="text-operra-600 dark:text-operra-400 font-black tracking-tighter text-2xl"
                            >OPERRA</span
                        >
                    </Link>

                    <!-- Right: Mobile Quick Actions -->
                    <ul class="flex items-center list-none gap-0.5">
                        <li
                            v-if="isModuleEnabled('wa_blast')"
                            class="inline-block relative"
                        >
                            <Link
                                :href="route('crm.wa.inbox')"
                                class="text-gray-400 hover:text-operra-500 block p-2 transition-colors relative"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    ></path>
                                </svg>
                                <span
                                    v-if="
                                        (realTimeUnreadCount ??
                                            $page.props.unreadCount) > 0
                                    "
                                    class="absolute top-1 right-1 flex h-3 w-3"
                                >
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3 w-3 bg-red-500 text-[7px] items-center justify-center font-bold text-white"
                                    >
                                        {{
                                            realTimeUnreadCount ??
                                            $page.props.unreadCount
                                        }}
                                    </span>
                                </span>
                            </Link>
                        </li>
                        <li>
                            <button
                                @click="toggleTheme"
                                class="text-gray-400 hover:text-operra-500 block p-2 transition-colors"
                            >
                                <svg
                                    v-if="isDark"
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 3v1m0 18v1m9-9h1m-18 0H2m3.364-7.364l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                    />
                                </svg>
                            </button>
                        </li>
                        <li>
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        class="text-gray-400 hover:text-operra-500 block p-2 transition-colors"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            ></path>
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')"
                                        >Profile</DropdownLink
                                    >
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        >Log Out</DropdownLink
                                    >
                                </template>
                            </Dropdown>
                        </li>
                    </ul>
                </div>

                <!-- Desktop Brand & Multi-module App Switcher -->
                <div class="flex flex-col w-full px-4">
                    <!-- Brand (Desktop Only) -->
                    <Link
                        class="hidden md:block text-left md:pb-2 text-gray-600 dark:text-gray-200 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold py-6 px-0"
                        :href="route('dashboard')"
                    >
                        <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                            <div class="bg-emerald-500 p-2 rounded-2xl shadow-lg shadow-emerald-500/20 shrink-0 transition-transform duration-300" :class="isSidebarCollapsed ? 'scale-90' : ''">
                                <ApplicationLogo class="h-8 w-8 text-white" />
                            </div>
                            <span
                                v-show="!isSidebarCollapsed"
                                class="text-emerald-600 dark:text-emerald-400 font-black text-2xl tracking-tighter transition-all duration-300 whitespace-nowrap"
                                >OPERRA</span
                            >
                        </div>
                    </Link>

                    <!-- App Switcher (Hanya muncul jika multi-module) -->
                    <Link
                        v-if="
                            page.props.auth.user.company?.enabled_modules
                                ?.length > 1
                        "
                        :href="route('dashboard')"
                        class="mt-1 md:mt-2 mb-3 md:mb-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl flex items-center transition-all border border-gray-100 dark:border-gray-700 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-500/5 group"
                        :class="isSidebarCollapsed ? 'p-2 justify-center mx-1' : 'px-4 py-2.5 justify-between'"
                        :title="isSidebarCollapsed ? 'Switch Application' : ''"
                    >
                        <div class="flex flex-col" v-if="!isSidebarCollapsed">
                            <span
                                class="text-[9px] uppercase font-black text-gray-400 group-hover:text-emerald-500 transition-colors tracking-widest leading-none mb-1.5"
                                >Active Application</span
                            >
                            <span
                                class="text-xs md:text-sm font-black text-gray-800 dark:text-gray-200"
                            >
                                {{ getPortalName(currentPortal) }}
                            </span>
                        </div>
                        <div
                            class="rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900 group-hover:text-emerald-600 transition-all"
                            :class="isSidebarCollapsed ? 'p-2.5' : 'p-2'"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                                ></path>
                            </svg>
                        </div>
                    </Link>
                </div>
                <!-- Collapse -->
                <div
                    class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded"
                    v-bind:class="collapseShow"
                >
                    <!-- Collapse header -->
                    <div
                        class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex flex-wrap items-center">
                            <div class="w-6/12">
                                <Link
                                    class="md:block text-left md:pb-2 text-gray-600 dark:text-gray-200 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
                                    :href="route('dashboard')"
                                >
                                    <div class="flex items-center gap-2">
                                        <ApplicationLogo class="h-6 w-auto" />
                                        <span>OPERRA</span>
                                    </div>
                                </Link>
                            </div>
                            <div class="w-6/12 flex justify-end">
                                <button
                                    type="button"
                                    class="cursor-pointer text-gray-500 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                                    v-on:click="toggleCollapseShow('hidden')"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Divider -->
                    <hr class="my-4 md:min-w-full" />
                    <h6
                        class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                        v-show="!isSidebarCollapsed"
                    >
                        Main Navigation
                    </h6>
                    <ul
                        class="md:flex-col md:min-w-full flex flex-col list-none"
                    >
                        <li class="items-center">
                            <Link
                                :href="
                                    currentPortal
                                        ? route('dashboard', {
                                              portal: currentPortal,
                                          })
                                        : route('dashboard')
                                "
                                class="text-xs uppercase py-3.5 px-4 font-black block transition-all duration-300 rounded-2xl mb-1"
                                :class="
                                    route().current('dashboard')
                                        ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200 dark:shadow-none'
                                        : 'text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600'
                                "
                                :title="isSidebarCollapsed ? 'Dashboard' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        class="w-5 h-5 shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Dashboard</span>
                                </div>
                            </Link>
                        </li>

                        <!-- WA Blast Module Links -->
                        <template v-if="currentPortal === 'wa_blast'">
                            <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                            <h6
                                class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                                v-show="!isSidebarCollapsed"
                            >
                                CRM & Leads
                            </h6>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.wa.inbox')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.wa.inbox')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Chat Inbox' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 4-8-4m8 4v6"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Chat Inbox</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.wa.settings.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.wa.settings.index')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Scan WhatsApp' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Scan WhatsApp</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.wa.leads.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.wa.leads.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Manage Leads' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Manage Leads</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.wa.blast.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.wa.blast.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'WhatsApp Blast' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >WhatsApp Blast</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.wa.auto-reply.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.wa.auto-reply.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Auto Reply' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Auto Reply</span>
                                    </div>
                                </Link>
                            </li>
                            <h6
                                class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                                v-show="!isSidebarCollapsed"
                            >
                                WhatsApp Tools
                            </h6>
                            <li
                                v-if="
                                    hasRole('super-admin') &&
                                    hasMinimumPlan('business-pro')
                                "
                                class="items-center"
                            >
                                <Link
                                    :href="route('crm.wa.external-apps.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.wa.external-apps.*',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'External Apps' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300 text-xs"
                                            >External Apps</span>
                                    </div>
                                </Link>
                            </li>
                            <li
                                v-if="hasRole('super-admin')"
                                class="items-center"
                            >
                                <Link
                                    :href="route('crm.wa.media.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.wa.media.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'WA Media Gallery' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300 text-xs"
                                            >WA Media Gallery</span>
                                    </div>
                                </Link>
                            </li>
                            <li
                                v-if="hasRole('super-admin')"
                                class="items-center"
                            >
                                <Link
                                    :href="
                                        route('crm.wa.customer-statuses.index')
                                    "
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.wa.customer-statuses.*',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Lead Statuses' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300 text-xs"
                                        >Lead Statuses</span>
                                </div>
                            </Link>
                        </li>
                        </template>

                        <!-- Sales CRM Module Links -->
                        <template v-if="currentPortal === 'sales_crm'">
                            <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                            <h6
                                class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                                v-show="!isSidebarCollapsed"
                            >
                                Sales Portal
                            </h6>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.sales.pipeline.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.sales.pipeline.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Pipeline (Kanban)' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Pipeline (Kanban)</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.sales.customers.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.sales.customers.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Manage Leads' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Manage Leads</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.sales.orders.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.sales.orders.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Sales Orders' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Sales Orders</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.sales.products.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.sales.products.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Stok Produk' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Stok Produk</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <a
                                    :href="route('crm.sales.report')"
                                    target="_blank"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1 text-gray-600 dark:text-gray-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600"
                                    :title="isSidebarCollapsed ? 'Laporan (PDF)' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Laporan (PDF)</span>
                                    </div>
                                </a>
                            </li>
                        </template>

                        <!-- Marketing CRM Module Links -->
                        <template v-if="currentPortal === 'marketing_crm'">
                            <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                            <h6
                                class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                                v-show="!isSidebarCollapsed"
                            >
                                Marketing Portal
                            </h6>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.marketing.dashboard')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.marketing.dashboard',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Portal Dashboard' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Portal Dashboard</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.marketing.broadcasts.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.marketing.broadcasts.*',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Broadcasting' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Broadcasting</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.marketing.campaigns.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.marketing.campaigns.*',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Email Marketing' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Email Marketing</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="
                                        route(
                                            'crm.marketing.lead-scoring.index',
                                        )
                                    "
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.marketing.lead-scoring.*',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Lead Scoring' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300 text-xs"
                                            >Lead Scoring</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="
                                        route('crm.marketing.automations.index')
                                    "
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current(
                                            'crm.marketing.automations.*',
                                        )
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Automations' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300 text-xs"
                                            >Automations</span>
                                    </div>
                                </Link>
                            </li>
                        </template>

                        <!-- Support CRM Module Links -->
                        <template v-if="currentPortal === 'customer_service'">
                            <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                            <h6
                                class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                                v-show="!isSidebarCollapsed"
                            >
                                Support Portal
                            </h6>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.support.dashboard')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.support.dashboard')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Support Stats' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Support Stats</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.support.tickets.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.support.tickets.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Ticketing' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Ticketing</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.support.chat-history.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.support.chat-history.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Chat History' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Chat History</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('crm.support.knowledge-base.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('crm.support.knowledge-base.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Knowledge Base' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Knowledge Base</span>
                                    </div>
                                </Link>
                            </li>
                        </template>

                        <!-- Bot Antam Module Links -->
                        <template v-if="currentPortal === 'bot_antam'">
                            <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                            <h6
                                class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                                v-show="!isSidebarCollapsed"
                            >
                                War Antam Portal
                            </h6>
                            <li class="items-center">
                                <Link
                                    :href="route('bot_antam.dashboard')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('bot_antam.dashboard')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Bot Dashboard' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Bot Dashboard</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <Link
                                    :href="route('bot_antam.support.index')"
                                    class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                    :class="
                                        route().current('bot_antam.support.*')
                                            ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                    "
                                    :title="isSidebarCollapsed ? 'Support Messages' : ''"
                                >
                                    <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                            />
                                        </svg>
                                        <span
                                            v-show="!isSidebarCollapsed"
                                            class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                            >Support Messages</span>
                                    </div>
                                </Link>
                            </li>
                            <li class="items-center">
                                <a
                                    href="#"
                                    onclick="return false;"
                                    class="text-xs uppercase py-2 font-bold block text-gray-400 cursor-not-allowed"
                                >
                                    Transaction History (Soon)
                                </a>
                            </li>
                        </template>

                        <template
                            v-if="
                                isModuleEnabled('analytical_crm') &&
                                currentPortal === 'analytical_crm'
                            "
                        >
                            <li class="items-center">
                                <span
                                    class="text-xs uppercase py-2 font-bold block text-gray-400 italic"
                                    >Reports (Coming Soon)</span
                                >
                            </li>
                        </template>

                        <!-- Global Support Link (Komplain) -->
                        <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                        <h6
                            class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                            v-show="!isSidebarCollapsed"
                        >
                            Pusat Bantuan
                        </h6>
                        <li class="items-center">
                            <Link
                                :href="route('support.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('support.*')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Komplain / Bantuan' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 10.606l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Komplain / Bantuan</span>
                                </div>
                            </Link>
                        </li>
                    </ul>

                    <hr class="my-4 md:min-w-full" v-show="!isSidebarCollapsed" />
                    <h6
                        class="px-4 md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest"
                        v-show="!isSidebarCollapsed"
                    >
                        Administrative
                    </h6>
                    <ul
                        class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4"
                    >
                        <!-- System Owner Only -->
                        <li
                            v-if="
                                $page.props.auth.user.company?.is_system_owner
                            "
                            class="items-center"
                        >
                            <Link
                                :href="route('admin.system.companies.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('admin.system.companies.*')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Monitoring Pelanggan' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Monitoring Pelanggan</span>
                                </div>
                            </Link>
                        </li>
                        <li
                            v-if="
                                $page.props.auth.user.company?.is_system_owner
                            "
                            class="items-center"
                        >
                            <Link
                                :href="route('admin.monitoring.webhooks')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('admin.monitoring.webhooks')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Webhook Monitor' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Webhook Monitor</span>
                                </div>
                            </Link>
                        </li>
                        <li
                            v-if="
                                hasRole('super-admin') &&
                                $page.props.auth.user.company?.is_system_owner
                            "
                            class="items-center"
                        >
                            <Link
                                :href="route('admin.leads.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('admin.leads.*')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Leads Request' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300 text-xs"
                                        >Leads Request</span>
                                    <span
                                        v-if="$page.props.newLeadsCount > 0 && !isSidebarCollapsed"
                                        class="ml-auto px-2 py-0.5 bg-emerald-500 text-white rounded-full text-[10px]"
                                    >
                                        {{ $page.props.newLeadsCount }}
                                    </span>
                                </div>
                            </Link>
                        </li>
                        <li
                            v-if="
                                hasRole('super-admin') &&
                                $page.props.auth.user.company?.is_system_owner
                            "
                            class="items-center"
                        >
                            <Link
                                :href="route('admin.tickets.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('admin.tickets.*')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Support Inbox' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 4-8-4m8 4v6"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Support Inbox</span>
                                </div>
                            </Link>
                        </li>
                        <li
                            v-if="
                                hasRole('super-admin') &&
                                $page.props.auth.user.company &&
                                $page.props.auth.user.company.is_system_owner
                            "
                            class="items-center"
                        >
                            <Link
                                :href="route('bot-antam-accounts.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('bot-antam-accounts.*')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Manage Bot Travelers' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Manage Bot Travelers</span>
                                </div>
                            </Link>
                        </li>
                        <li
                            v-if="hasRole('super-admin') || hasRole('manager')"
                            class="items-center"
                        >
                            <Link
                                :href="route('settings.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('settings.index')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Company Settings' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12a3 3 0 106 0 3 3 0 00-6 0z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Company Settings</span>
                                </div>
                            </Link>
                        </li>
                        <li
                            v-if="
                                hasRole('super-admin') &&
                                (hasMinimumPlan('business-pro') ||
                                    $page.props.auth.user.company
                                        ?.is_system_owner)
                            "
                            class="items-center"
                        >
                            <Link
                                :href="route('staff.index')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('staff.*')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Manage Staff' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Manage Staff</span>
                                </div>
                            </Link>
                        </li>
                        <li class="items-center">
                            <Link
                                :href="route('profile.edit')"
                                class="text-xs uppercase py-3 px-4 font-bold block transition-all duration-200 rounded-xl mb-1"
                                :class="
                                    route().current('profile.edit')
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                                "
                                :title="isSidebarCollapsed ? 'Profile Settings' : ''"
                            >
                                <div class="flex items-center transition-all duration-300" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                    <span
                                        v-show="!isSidebarCollapsed"
                                        class="whitespace-nowrap overflow-hidden transition-all duration-300"
                                        >Profile Settings</span>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div
            class="relative bg-gray-100 dark:bg-gray-900 min-h-screen transition-all duration-300"
            :class="isSidebarCollapsed ? 'md:ml-[85px]' : 'md:ml-[280px]'"
        >
            <!-- Notification & Sound Permission Prompt (Floating Banner) -->
            <transition
                enter-active-class="transform transition ease-out duration-300"
                enter-from-class="translate-y-full opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-full opacity-0"
            >
                <div
                    v-if="showPermissionPrompt"
                    class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-[100] w-[90%] max-w-md"
                >
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-operra-500/30 p-5 flex flex-col items-center text-center gap-4"
                    >
                        <div
                            class="h-14 w-14 bg-operra-100 dark:bg-operra-900/30 rounded-full flex items-center justify-center text-operra-600 dark:text-operra-400"
                        >
                            <svg
                                class="w-8 h-8"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            <h4
                                class="text-lg font-bold text-gray-800 dark:text-gray-100"
                            >
                                Aktifkan Notifikasi & Suara
                            </h4>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                Izinkan sistem mengirim notifikasi dan suara
                                agar Anda tidak melewatkan pesan dari customer.
                            </p>
                        </div>
                        <div class="flex gap-3 w-full">
                            <button
                                @click="dismissPermissionPrompt"
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            >
                                Nanti Saja
                            </button>
                            <button
                                @click="requestPermissions"
                                class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-operra-600 hover:bg-operra-700 text-white shadow-lg shadow-operra-600/20 transition-all active:scale-95"
                            >
                                Aktifkan Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Top Navbar -->
            <nav
                class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4"
            >
                <div
                    class="w-full mx-auto items-center flex justify-between md:flex-nowrap flex-wrap md:px-10 px-4"
                >
                    <!-- Brand -->
                    <span
                        class="text-white text-sm uppercase hidden lg:inline-block font-semibold"
                    >
                        <slot name="header" />
                    </span>
                    <!-- User & Theme -->
                    <ul
                        class="flex-row list-none items-center hidden md:flex gap-4"
                    >
                        <li
                            v-if="isModuleEnabled('wa_blast')"
                            class="inline-block relative"
                        >
                            <!-- <Link
                                :href="route('crm.wa.inbox')"
                                class="text-white hover:text-operra-200 transition-colors duration-200 relative block"
                            >
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    ></path>
                                </svg>
                                <span
                                    v-if="
                                        (realTimeUnreadCount ??
                                            $page.props.unreadCount) > 0
                                    "
                                    class="absolute -top-1 -right-1 flex h-4 w-4"
                                >
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                                    ></span>
                                    <span
                                        class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] items-center justify-center font-bold text-white"
                                    >
                                        {{
                                            realTimeUnreadCount ??
                                            $page.props.unreadCount
                                        }}
                                    </span>
                                </span>
                            </Link> -->
                        </li>
                        <li class="inline-block relative">
                            <button
                                @click="toggleTheme"
                                class="text-white hover:text-operra-200 transition-colors duration-200"
                            >
                                <svg
                                    v-if="isDark"
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 3v1m0 18v1m9-9h1m-18 0H2m3.364-7.364l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                    />
                                </svg>
                            </button>
                        </li>
                        <li class="inline-block relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        class="text-white flex items-center gap-2 py-1 px-3 font-semibold hover:text-operra-200 transition-colors duration-200 group"
                                    >
                                        <span class="flex flex-col items-end">
                                            <span
                                                class="text-sm leading-none"
                                                >{{
                                                    $page.props.auth.user.name
                                                }}</span
                                            >
                                            <span
                                                v-if="
                                                    $page.props.auth.user
                                                        .company?.pricing_plan
                                                "
                                                class="text-[10px] font-black uppercase tracking-widest text-cyan-200 group-hover:text-white transition-colors"
                                            >
                                                {{
                                                    $page.props.auth.user
                                                        .company.pricing_plan
                                                        .name
                                                }}
                                                PLAN
                                            </span>
                                            <span
                                                v-if="
                                                    $page.props.auth.user
                                                        .company
                                                        ?.subscription_ends_at
                                                "
                                                class="text-[9px] font-bold text-white group-hover:text-white/90 transition-colors uppercase tracking-wider"
                                            >
                                                EXP:
                                                {{
                                                    new Date(
                                                        $page.props.auth.user
                                                            .company
                                                            .subscription_ends_at,
                                                    ).toLocaleDateString(
                                                        "id-ID",
                                                        {
                                                            day: "numeric",
                                                            month: "short",
                                                            year: "numeric",
                                                        },
                                                    )
                                                }}
                                            </span>
                                        </span>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')"
                                        >Profile</DropdownLink
                                    >
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        >Log Out</DropdownLink
                                    >
                                </template>
                            </Dropdown>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Header / Stats Container -->
            <div
                class="relative bg-operra-600 md:pt-32 pb-32 pt-16 transition-colors duration-300"
            >
                <div class="px-4 md:px-10 mx-auto w-full">
                    <div>
                        <!-- Header Stats Slots -->
                        <slot name="stats" />
                    </div>
                </div>
            </div>

            <!-- Main Page Content -->
            <div class="px-4 md:px-10 mx-auto w-full -mt-24 pb-12">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 rounded-lg"
                >
                    <slot />
                </div>

                <footer class="block py-4">
                    <div class="container mx-auto px-4">
                        <hr
                            class="mb-4 border-b-1 border-gray-200 dark:border-gray-700"
                        />
                        <div
                            class="flex flex-wrap items-center md:justify-between justify-center"
                        >
                            <div class="w-full md:w-4/12 px-4">
                                <div
                                    class="text-sm text-gray-500 dark:text-gray-400 font-semibold py-1 text-center md:text-left"
                                >
                                    © 2026 Operra by
                                    <a
                                        href="https://hasanarofid.site"
                                        class="text-operra-500 hover:text-operra-700"
                                        >hasanarofid</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <OnboardingModal
            :show="!page.props.auth.user.has_completed_onboarding"
        />
        <ExpirationModal />
        <ToastNotification />
    </div>
</template>

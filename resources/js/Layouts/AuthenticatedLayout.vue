<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { database } from '@/firebase';
import { ref as dbRef, onValue } from "firebase/database";

// Global variable to persist across page navigations (Inertia re-mounts)
let globalLastSoundPlayedMessageId = null;
let globalAudio = null;

const collapseShow = ref("hidden");
const isDark = ref(localStorage.getItem('theme') !== 'light'); // Inisialisasi sesuai localStorage
const realTimeUnreadCount = ref(null);
const showPermissionPrompt = ref(false);
let unsubInbox = null;
let unsubNotif = null;

const page = usePage();

const playNotificationSound = () => {
    if (!globalAudio) {
        globalAudio = new Audio('/sound/sound.mp3');
    }
    console.log('[Operra] Memutar suara notifikasi...');
    globalAudio.play().catch(e => {
        console.warn('[Operra] Suara gagal diputar (biasanya karena belum ada interaksi user di halaman ini):', e);
        showPermissionPrompt.value = true;
    });
};

const hasRole = (role) => page.props.auth.user.roles.includes(role);
const hasPermission = (permission) => page.props.auth.user.permissions.includes(permission);
const isModuleEnabled = (module) => page.props.auth.user.company?.enabled_modules?.includes(module);

const currentPortal = computed(() => {
    if (route().current('crm.wa.*')) return 'wa_blast';
    if (route().current('crm.sales.*')) return 'sales_crm';
    if (route().current('crm.marketing.*')) return 'marketing_crm';
    if (route().current('crm.support.*')) return 'customer_service';
    
    // Check query parameter using Inertia URL state
    const portal = page.url.split('portal=')[1]?.split('&')[0];
    if (portal) return portal;

    // Fallback if only 1 module enabled
    const enabled = page.props.auth.user.company?.enabled_modules ?? [];
    return enabled.length === 1 ? enabled[0] : null;
});

const getPortalName = (portalId) => {
    const names = {
        'wa_blast': 'WhatsApp CRM',
        'sales_crm': 'Sales CRM',
        'marketing_crm': 'Marketing CRM',
        'customer_service': 'Customer Support',
        'analytical_crm': 'Analytical CRM'
    };
    return names[portalId] || 'Pilih Aplikasi';
};

const requestPermissions = async () => {
    // 1. Request Notification Permission
    if ("Notification" in window) {
        try {
            const permission = await Notification.requestPermission();
            console.log('[Operra] Notification permission:', permission);
        } catch (e) {
            console.error('[Operra] Error requesting notification permission:', e);
        }
    }

    // 2. Unlock Audio
    // Create audio if not exists
    if (!globalAudio) {
        globalAudio = new Audio('/sound/sound.mp3');
    }
    
    // Play a very short sound to unlock audio context
    globalAudio.muted = true; // Mute first to be safe
    globalAudio.play().then(() => {
        globalAudio.pause();
        globalAudio.muted = false;
        globalAudio.currentTime = 0;
        console.log('[Operra] Audio system unlocked successfully');
        showPermissionPrompt.value = false;
        
        // Optional: Save to session storage that user has dismissed/enabled for this session
        sessionStorage.setItem('operra_audio_unlocked', 'true');
    }).catch(e => {
        console.error('[Operra] Gagal unlock audio:', e);
        // Even if it fails, we close the prompt to not annoy the user, 
        // it will reappear if playNotificationSound fails again later
        showPermissionPrompt.value = false;
    });
};

function toggleCollapseShow(classes) {
  collapseShow.value = classes;
}

function toggleTheme() {
    isDark.value = !isDark.value;
}

watch(isDark, (val) => {
    if (val) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
}, { immediate: true });

onMounted(() => {
    // Check if we should show prompt
    const isAudioUnlocked = sessionStorage.getItem('operra_audio_unlocked');
    
    if (("Notification" in window && Notification.permission !== 'granted') || !isAudioUnlocked) {
        // Delay slightly for better UX
        setTimeout(() => {
            showPermissionPrompt.value = true;
        }, 1500);
    }

    // Global notification sound listener
    if (page.props.auth.user) {
        const userInboxRef = dbRef(database, `inbox/users/${page.props.auth.user.id}`);
        unsubInbox = onValue(userInboxRef, (snapshot) => {
            const data = snapshot.val();
            if (data) {
                const incomingData = Object.values(data);
                
                // Temukan pesan terbaru dari customer untuk play sound
                const latestCustomerMessage = incomingData
                    .map(d => d.message)
                    .filter(m => m.sender_type === 'customer')
                    .sort((a, b) => b.id - a.id)[0];

                if (latestCustomerMessage && latestCustomerMessage.id !== globalLastSoundPlayedMessageId) {
                    // Hanya bunyikan suara jika bukan loading pertama kali (saat web pertama kali dibuka)
                    if (globalLastSoundPlayedMessageId !== null) {
                        playNotificationSound();
                    }
                    globalLastSoundPlayedMessageId = latestCustomerMessage.id;
                }
            }
        });

        // Global unread count listener
        const userNotificationRef = dbRef(database, `notifications/users/${page.props.auth.user.id}`);
        unsubNotif = onValue(userNotificationRef, (snapshot) => {
            const data = snapshot.val();
            if (data && typeof data.unread_count !== 'undefined') {
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
    <nav class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white dark:bg-gray-800 flex flex-wrap items-center justify-between relative md:w-64 z-50 py-2 md:py-4 px-4 md:px-6">
      <div class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto">
        
        <!-- Mobile Header (Visible only on mobile) -->
        <div class="md:hidden flex items-center justify-between w-full mb-3">
          <!-- Left: Toggler -->
          <button
            class="cursor-pointer text-gray-500 p-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
            type="button"
            v-on:click="toggleCollapseShow('bg-white dark:bg-gray-800 shadow-2xl p-6')"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
          </button>

          <!-- Middle: Brand -->
          <Link
            class="flex items-center gap-1 text-gray-600 dark:text-gray-200"
            :href="route('dashboard')"
          >
            <ApplicationLogo class="h-7 w-auto" />
            <span class="text-operra-600 dark:text-operra-400 font-black tracking-tighter text-lg">OPERRA</span>
          </Link>

          <!-- Right: Mobile Quick Actions -->
          <ul class="flex items-center list-none gap-0.5">
            <li v-if="isModuleEnabled('wa_blast')" class="inline-block relative">
               <Link :href="route('crm.wa.inbox')" class="text-gray-400 hover:text-operra-500 block p-2 transition-colors relative">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                  </svg>
                  <span v-if="(realTimeUnreadCount ?? $page.props.unreadCount) > 0" class="absolute top-1 right-1 flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 text-[7px] items-center justify-center font-bold text-white">
                          {{ realTimeUnreadCount ?? $page.props.unreadCount }}
                      </span>
                  </span>
               </Link>
            </li>
            <li>
              <button @click="toggleTheme" class="text-gray-400 hover:text-operra-500 block p-2 transition-colors">
                  <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1m-18 0H2m3.364-7.364l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
              </button>
            </li>
            <li>
              <Dropdown align="right" width="48">
                  <template #trigger>
                      <button class="text-gray-400 hover:text-operra-500 block p-2 transition-colors">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                      </button>
                  </template>
                  <template #content>
                      <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                      <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                  </template>
              </Dropdown>
            </li>
          </ul>
        </div>

        <!-- Desktop Brand & Multi-module App Switcher -->
        <div class="flex flex-col w-full">
            <!-- Brand (Desktop Only) -->
            <Link
              class="hidden md:block text-left md:pb-2 text-gray-600 dark:text-gray-200 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
              :href="route('dashboard')"
            >
              <div class="flex items-center gap-2">
                <ApplicationLogo class="h-8 w-auto" />
                <span class="text-operra-600 dark:text-operra-400 font-black">OPERRA</span>
              </div>
            </Link>

            <!-- App Switcher (Hanya muncul jika multi-module) -->
            <Link 
                v-if="page.props.auth.user.company?.enabled_modules?.length > 1"
                :href="route('dashboard')"
                class="mt-1 md:mt-2 mb-3 md:mb-4 px-3 md:px-4 py-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl flex items-center justify-between group hover:bg-operra-50 dark:hover:bg-operra-900/20 transition-all border border-gray-100 dark:border-gray-700 hover:border-operra-200 shadow-sm md:shadow-none"
            >
                <div class="flex flex-col">
                    <span class="text-[9px] md:text-[10px] uppercase font-black text-gray-400 group-hover:text-operra-400 transition-colors tracking-widest leading-none mb-1">Active App</span>
                    <span class="text-xs md:text-sm font-bold text-gray-700 dark:text-gray-200">
                        {{ getPortalName(currentPortal) }}
                    </span>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-operra-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </Link>
        </div>
        <!-- Collapse -->
        <div
          class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded"
          v-bind:class="collapseShow"
        >
          <!-- Collapse header -->
          <div class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-gray-200 dark:border-gray-700">
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
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
              </div>
            </div>
          </div>
          <!-- Divider -->
          <hr class="my-4 md:min-w-full" />
          <!-- Heading -->
          <h6 class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest">
            Main Navigation
          </h6>
          <!-- Navigation -->
          <ul class="md:flex-col md:min-w-full flex flex-col list-none">
            <li class="items-center">
              <Link
                :href="currentPortal ? route('dashboard', { portal: currentPortal }) : route('dashboard')"
                class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                :class="route().current('dashboard') ? 'text-operra-500 hover:text-operra-600' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'"
              >
                Dashboard
              </Link>
            </li>

            <!-- WA Blast Module Links -->
            <template v-if="currentPortal === 'wa_blast'">
              <hr class="my-4 md:min-w-full" />
              <h6 class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest">
                CRM & Leads
              </h6>
              <li class="items-center">
                  <Link :href="route('crm.wa.inbox')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.wa.inbox') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Chat Inbox
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.wa.leads.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.wa.leads.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Manage Leads
                  </Link>
              </li>
              <li v-if="hasRole('super-admin')" class="items-center">
                  <Link :href="route('crm.wa.settings.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.wa.settings.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      WA Multi-Account
                  </Link>
              </li>
            </template>

            <!-- Sales CRM Module Links -->
            <template v-if="currentPortal === 'sales_crm'">
              <hr class="my-4 md:min-w-full" />
              <h6 class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest">
                Sales Portal
              </h6>
              <li class="items-center">
                  <Link :href="route('crm.sales.customers.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.sales.customers.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Manage Leads
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.sales.orders.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.sales.orders.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Sales Orders
                  </Link>
              </li>
            </template>

            <!-- Marketing CRM Module Links -->
            <template v-if="currentPortal === 'marketing_crm'">
              <hr class="my-4 md:min-w-full" />
              <h6 class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest">
                Marketing Portal
              </h6>
              <li class="items-center">
                  <Link :href="route('crm.marketing.dashboard')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.marketing.dashboard') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Marketing Stats
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.marketing.campaigns.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.marketing.campaigns.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Campaigns
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.marketing.blasts.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.marketing.blasts.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Email/WA Blast
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.marketing.lead-scoring.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.marketing.lead-scoring.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Lead Scoring
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.marketing.automations.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.marketing.automations.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Automations
                  </Link>
              </li>
            </template>

            <!-- Support CRM Module Links -->
            <template v-if="currentPortal === 'customer_service'">
              <hr class="my-4 md:min-w-full" />
              <h6 class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest">
                Support Portal
              </h6>
              <li class="items-center">
                  <Link :href="route('crm.support.dashboard')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.support.dashboard') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Support Stats
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.support.tickets.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.support.tickets.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Ticketing
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.support.chat-history.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.support.chat-history.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Chat History
                  </Link>
              </li>
              <li class="items-center">
                  <Link :href="route('crm.support.knowledge-base.index')" 
                      class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                      :class="route().current('crm.support.knowledge-base.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                      Knowledge Base
                  </Link>
              </li>
            </template>

            <template v-if="isModuleEnabled('analytical_crm') && currentPortal === 'analytical_crm'">
              <hr class="my-4 md:min-w-full" />
              <h6 class="md:min-w-full text-gray-500 text-[10px] uppercase font-black block pt-1 pb-4 no-underline tracking-widest">
                Analytics Portal
              </h6>
              <li class="items-center">
                <span class="text-xs uppercase py-2 font-bold block text-gray-400 italic">Reports (Coming Soon)</span>
              </li>
            </template>
          </ul>

          <!-- Divider -->
          <hr class="my-4 md:min-w-full" />
          <!-- Heading -->
          <h6 class="md:min-w-full text-gray-500 text-xs uppercase font-bold block pt-1 pb-4 no-underline">
            Administrative
          </h6>
          <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
            <!-- System Owner Only -->
            <li v-if="$page.props.auth.user.company?.is_system_owner" class="items-center">
                <Link :href="route('admin.system.companies.index')" 
                    class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                    :class="route().current('admin.system.companies.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                    Monitoring Pelanggan
                </Link>
            </li>
            <li v-if="hasRole('super-admin')" class="items-center">
                <Link :href="route('admin.leads.index')" 
                    class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                    :class="route().current('admin.leads.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'">
                    Leads Request
                    <span v-if="$page.props.newLeadsCount > 0" class="ml-2 px-2 py-0.5 bg-operra-500 text-white rounded-full text-[10px]">
                        {{ $page.props.newLeadsCount }}
                    </span>
                </Link>
            </li>
            <li v-if="hasRole('super-admin') || hasRole('manager')" class="items-center">
              <Link
                :href="route('settings.index')"
                class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                :class="route().current('settings.index') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'"
              >
                Company Settings
              </Link>
            </li>
            <li v-if="hasRole('super-admin')" class="items-center">
              <Link
                :href="route('staff.index')"
                class="text-xs uppercase py-2 font-bold block transition-colors duration-200"
                :class="route().current('staff.*') ? 'text-operra-500' : 'text-gray-700 dark:text-gray-300 hover:text-operra-500'"
              >
                Manage Staff
              </Link>
            </li>
            <li class="items-center">
              <Link
                :href="route('profile.edit')"
                class="text-gray-700 dark:text-gray-300 hover:text-operra-500 text-xs uppercase py-2 font-bold block transition-colors duration-200"
              >
                Profile Settings
              </Link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="relative md:ml-64 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
      
      <!-- Notification & Sound Permission Prompt (Floating Banner) -->
      <transition
        enter-active-class="transform transition ease-out duration-300"
        enter-from-class="translate-y-full opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-full opacity-0"
      >
        <div v-if="showPermissionPrompt" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-[100] w-[90%] max-w-md">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-operra-500/30 p-5 flex flex-col items-center text-center gap-4">
                <div class="h-14 w-14 bg-operra-100 dark:bg-operra-900/30 rounded-full flex items-center justify-center text-operra-600 dark:text-operra-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">Aktifkan Notifikasi & Suara</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Izinkan sistem mengirim notifikasi dan suara agar Anda tidak melewatkan pesan dari customer.
                    </p>
                </div>
                <div class="flex gap-3 w-full">
                    <button @click="showPermissionPrompt = false" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Nanti Saja
                    </button>
                    <button @click="requestPermissions" class="flex-1 py-2.5 rounded-xl text-sm font-semibold bg-operra-600 hover:bg-operra-700 text-white shadow-lg shadow-operra-600/20 transition-all active:scale-95">
                        Aktifkan Sekarang
                    </button>
                </div>
            </div>
        </div>
      </transition>

      <!-- Top Navbar -->
      <nav class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4">
        <div class="w-full mx-auto items-center flex justify-between md:flex-nowrap flex-wrap md:px-10 px-4">
          <!-- Brand -->
          <span class="text-white text-sm uppercase hidden lg:inline-block font-semibold">
            <slot name="header" />
          </span>
          <!-- User & Theme -->
          <ul class="flex-row list-none items-center hidden md:flex gap-4">
             <li v-if="isModuleEnabled('wa_blast')" class="inline-block relative">
                <Link :href="route('crm.wa.inbox')" class="text-white hover:text-operra-200 transition-colors duration-200 relative block">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <!-- Notification Badge -->
                    <span v-if="(realTimeUnreadCount ?? $page.props.unreadCount) > 0" class="absolute -top-1 -right-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] items-center justify-center font-bold text-white">
                            {{ realTimeUnreadCount ?? $page.props.unreadCount }}
                        </span>
                    </span>
                </Link>
             </li>
             <li class="inline-block relative">
                <button @click="toggleTheme" class="text-white hover:text-operra-200 transition-colors duration-200">
                    <svg v-if="isDark" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 18v1m9-9h1m-18 0H2m3.364-7.364l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>
             </li>
             <li class="inline-block relative">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="text-white block py-1 px-3 font-semibold hover:text-operra-200 transition-colors duration-200">
                            {{ $page.props.auth.user.name }}
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                    </template>
                </Dropdown>
              </li>
          </ul>
        </div>
      </nav>

      <!-- Header / Stats Container -->
      <div class="relative bg-operra-600 md:pt-32 pb-32 pt-16 transition-colors duration-300">
        <div class="px-4 md:px-10 mx-auto w-full">
          <div>
            <!-- Header Stats Slots -->
            <slot name="stats" />
          </div>
        </div>
      </div>

      <!-- Main Page Content -->
      <div class="px-4 md:px-10 mx-auto w-full -mt-24 pb-12">
        <div class="relative flex flex-col min-w-0 break-words w-full mb-6 rounded-lg">
          <slot />
        </div>
        
        <footer class="block py-4">
          <div class="container mx-auto px-4">
            <hr class="mb-4 border-b-1 border-gray-200 dark:border-gray-700" />
            <div class="flex flex-wrap items-center md:justify-between justify-center">
              <div class="w-full md:w-4/12 px-4">
                <div class="text-sm text-gray-500 dark:text-gray-400 font-semibold py-1 text-center md:text-left">
                  © 2026 Operra by <a href="https://hasanarofid.site" class="text-operra-500 hover:text-operra-700">hasanarofid</a>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
  </div>
</template>

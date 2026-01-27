<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            User Profile Settings
        </template>

        <div class="flex flex-wrap mt-4">
            <div class="w-full px-4">
                <!-- Subscription Information Card -->
                <div v-if="$page.props.auth.user.company?.pricing_plan" class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded-[20px] border-l-4 border-operra-500 overflow-hidden transition-all hover:shadow-xl">
                    <div class="rounded-t mb-0 px-6 py-4 border-0 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                        <h3 class="font-black text-lg text-gray-800 dark:text-white uppercase tracking-tighter">Subscription Details</h3>
                        <span class="px-3 py-1 rounded-full bg-operra-100 dark:bg-operra-900/30 text-operra-600 dark:text-operra-400 text-[10px] font-black uppercase tracking-widest border border-operra-200 dark:border-operra-700/50">
                            Active Package
                        </span>
                    </div>
                    <div class="p-6 md:p-8 border-t border-gray-100 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="flex items-center gap-5">
                                <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-operra-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-operra-500/20">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Paket Langganan</div>
                                    <h4 class="text-2xl font-black text-gray-800 dark:text-white uppercase tracking-tighter">
                                        {{ $page.props.auth.user.company.pricing_plan.name }}
                                    </h4>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-5">
                                <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Berlaku Hingga</div>
                                    <h4 v-if="$page.props.auth.user.company.subscription_ends_at" class="text-xl font-bold text-gray-800 dark:text-gray-100 italic">
                                        {{ new Date($page.props.auth.user.company.subscription_ends_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                    </h4>
                                    <h4 v-else class="text-xl font-bold text-gray-500 italic">Lifetime / Unlimited</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Account Information</h3>
                    </div>
                    <div class="p-4 sm:p-8 border-t border-gray-100 dark:border-gray-700">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-gray-700 dark:text-gray-200">Security</h3>
                    </div>
                    <div class="p-4 sm:p-8 border-t border-gray-100 dark:border-gray-700">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <!-- <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-gray-800 w-full mb-6 shadow-lg rounded border-l-4 border-red-500">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <h3 class="font-bold text-base text-red-600">Danger Zone</h3>
                    </div>
                    <div class="p-4 sm:p-8 border-t border-gray-100 dark:border-gray-700">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div> -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>

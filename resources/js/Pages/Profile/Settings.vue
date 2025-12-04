<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const { t } = useTranslation();
</script>

<template>
    <ProfileLayout>
        <Head :title="t('settings') || 'Settings'" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Page Title -->
            <div class="text-center mb-8">
                <h1 class="text-[32px] md:text-[45px] font-bold text-[#C7C7C7] mb-2">
                    {{ t('settings') || 'Settings' }}
                </h1>
                <p class="text-gray-400">Manage your account settings and preferences</p>
            </div>

            <!-- Back to Profile Link -->
            <div class="mb-6">
                <Link
                    :href="route('profile.edit')"
                    class="inline-flex items-center gap-2 text-[#F15A2B] hover:text-[#BF3206] transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Back to Profile</span>
                </Link>
            </div>

            <!-- Profile Information Card -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <h2 class="text-xl font-semibold text-[#C7C7C7] mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ t('profile_information') }}
                </h2>
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <!-- Password Update Card -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <h2 class="text-xl font-semibold text-[#C7C7C7] mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ t('settings') || 'Settings' }}
                </h2>
                <UpdatePasswordForm />
            </div>

            <!-- Delete Account Card -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <h2 class="text-xl font-semibold text-[#C7C7C7] mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Account
                </h2>
                <DeleteUserForm />
            </div>
        </div>
    </ProfileLayout>
</template>




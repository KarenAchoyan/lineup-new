<template>
    <AppLayout>
        <Head :title="t('theatre')" />
        <div class="min-h-screen bg-[#232222] py-6 sm:py-8 md:py-12">
            <div class="container mx-auto px-3 sm:px-4">
                <h1 class="text-[24px] sm:text-[32px] md:text-[45px] font-bold mb-4 sm:mb-6 text-center text-[#C7C7C7]">{{ t('theatre') }}</h1>
                <div class="text-center py-12">
                    <p class="text-[#C7C7C7] text-base sm:text-lg">{{ t('no_data') || 'No content available.' }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();
</script>


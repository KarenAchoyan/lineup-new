<template>
    <AppLayout>
        <Head :title="t('return_refund')" />
        <div class="min-h-screen bg-[#232222] py-6 sm:py-8 md:py-12">
            <div class="container mx-auto px-3 sm:px-4">
                <div class="max-w-4xl mx-auto bg-[#4D4C4C] p-4 sm:p-6 md:p-8 lg:p-10 rounded-2xl border-t-2 border-[#BF3206]">
                    <h1 class="text-[24px] sm:text-[32px] md:text-[45px] font-bold mb-4 sm:mb-6 text-center text-[#C7C7C7] mt-20">{{ t('privacy_policy') || t('return_refund') }}</h1>
                    <div class="text-sm sm:text-base md:text-lg text-[#C7C7C7] leading-relaxed space-y-4 prose prose-invert max-w-none" v-html="content || (t('privacy_content') || 'Privacy policy content will be displayed here.')"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    content: String,
});

const { t } = useTranslation();
</script>


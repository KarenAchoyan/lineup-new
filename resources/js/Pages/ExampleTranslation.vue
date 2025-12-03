<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold mb-4">{{ t('welcome_to_lineup') }}</h1>
        
        <div class="mb-6">
            <LanguageSwitcher />
        </div>
        
        <div class="space-y-4">
            <p><strong>{{ t('news') }}:</strong> {{ t('read_more') }}</p>
            <p><strong>{{ t('events') }}:</strong> {{ t('see_more') }}</p>
            <p><strong>{{ t('about_us') }}:</strong> {{ t('our_achievements') }}</p>
            <p><strong>{{ t('sections') }}:</strong> {{ t('dance') }}, {{ t('vocals') }}, {{ t('theatre') }}</p>
        </div>
        
        <div class="mt-8 p-4 bg-gray-100 rounded">
            <h2 class="text-xl font-semibold mb-2">Current Locale: {{ currentLocale }}</h2>
            <p class="text-sm text-gray-600">Available locales: {{ availableLocales.join(', ') }}</p>
        </div>
    </div>
</template>

<script setup>
import LanguageSwitcher from '../Components/LanguageSwitcher.vue';
import { useTranslation } from '../composables/useTranslation';

const { t, currentLocale, availableLocales } = useTranslation();
</script>


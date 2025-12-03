<template>
    <AppLayout>
        <Head :title="t('welcome_to_lineup')" />

        <div class="min-h-screen bg-[#232222]">
            <!-- Banner -->
            <Banner :banner="banner" />

            <!-- News Section -->
            <div class="container mx-auto px-4 sm:px-5 md:px-6 lg:px-8">
                <HomeNews :news="news" :show-title="true" />
            </div>

            <!-- Active Users Carousel -->
            <div class="container mx-auto px-4 sm:px-5 md:px-6 lg:px-8 mt-8 sm:mt-12 md:mt-16">
                <UserCarousel v-if="activeUsers.length > 0" :users="activeUsers" />
            </div>

            <!-- Achievements Gallery -->
            <div class="container mx-auto px-4 sm:px-5 md:px-6 lg:px-8 mt-8 sm:mt-12 md:mt-16">
                <Achievements v-if="galleries.length > 0" :galleries="galleries" :show-all-link="true" />
            </div>

            <!-- Calendar Events -->
            <div class="container mx-auto px-4 sm:px-5 md:px-6 lg:px-8 mt-8 sm:mt-12 md:mt-16">
                <CalendarEvent v-if="events.length > 0" :events="events" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import Banner from '@/Components/Banner.vue';
import HomeNews from '@/Components/HomeNews.vue';
import UserCarousel from '@/Components/UserCarousel.vue';
import Achievements from '@/Components/Achievements.vue';
import CalendarEvent from '@/Components/CalendarEvent.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    banner: {
        type: Object,
        default: null,
    },
    news: Object,
    activeUsers: {
        type: Array,
        default: () => [],
    },
    galleries: {
        type: Array,
        default: () => [],
    },
    events: {
        type: Array,
        default: () => [],
    },
});

const { t } = useTranslation();
</script>

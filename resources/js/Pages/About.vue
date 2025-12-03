<template>
    <AppLayout>
        <Head :title="t('about_us')" />
        
        <div class="min-h-screen bg-[#232222] py-6 sm:py-8 md:py-12">
            <div class="container mx-auto px-3 sm:px-4">
                <!-- About Section -->
                <div class="container m-auto bg-[#4D4C4C] p-4 sm:p-5 md:p-10 rounded-2xl border-t-2 border-[#BF3206] mb-8 sm:mb-10 md:mb-12 overflow-hidden">
                    <h1 class="text-[#C7C7C7] font-bold mb-4 sm:mb-6 text-center text-[24px] sm:text-[30px] md:text-[45px] mt-20">
                        {{ t('about_us') }}
                    </h1>
                    
                    <!-- About Image -->
                    <div v-if="about.image" class="relative w-full h-[250px] sm:h-[350px] md:h-[500px] max-w-[700px] mx-auto mb-6 sm:mb-8 md:mb-10 rounded-2xl overflow-hidden shadow-2xl">
                        <img
                            :src="`/storage/${about.image}`"
                            alt="About"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div v-else class="relative w-full h-[250px] sm:h-[350px] md:h-[500px] max-w-[700px] mx-auto mb-6 sm:mb-8 md:mb-10 rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center">
                        <span class="text-white text-4xl sm:text-5xl md:text-6xl font-bold opacity-50">About</span>
                    </div>

                    <!-- About Content -->
                    <div v-if="aboutContent" class="about-content text-sm sm:text-base md:text-lg lg:text-xl text-white my-6 sm:my-8 md:my-10 text-center max-w-4xl mx-auto leading-relaxed px-2" v-html="aboutContent"></div>
                    <div v-else class="text-sm sm:text-base md:text-lg lg:text-xl text-[#C7C7C7] my-6 sm:my-8 md:my-10 text-center max-w-4xl mx-auto">
                        {{ t('no_data') }}
                    </div>
                </div>

                <!-- Staff Section -->
                <div v-if="staffs.length > 0" class="mb-8 sm:mb-10 md:mb-12">
                    <div class="container m-auto bg-[#4D4C4C] p-4 sm:p-5 md:p-10 rounded-2xl border-t-2 border-[#BF3206]">
                        <h2 class="text-[#C7C7C7] font-bold mb-4 sm:mb-6 text-center text-[24px] sm:text-[30px] md:text-[45px]">
                            {{ t('our_team') || 'Our Team' }}
                        </h2>
                        <div class="flex flex-wrap justify-between gap-3 sm:gap-4">
                            <div
                                v-for="staff in staffs"
                                :key="staff.id"
                                class="w-full sm:w-[48%] lg:w-[31%] bg-[#5D5D5D] rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                            >
                                <!-- Staff Avatar -->
                                <div class="w-full h-[350px] sm:h-[380px] md:h-[480px] overflow-hidden">
                                    <img
                                        v-if="staff.avatar"
                                        :src="`/storage/${staff.avatar}`"
                                        :alt="staff.fullname"
                                        class="w-full h-full object-cover object-top"
                                    />
                                    <div v-else class="w-full h-full bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center">
                                        <span class="text-white text-3xl sm:text-4xl font-bold">{{ staff.fullname.charAt(0) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Staff Info -->
                                <div class="p-4 sm:p-6">
                                    <h3 class="text-lg sm:text-xl font-semibold mb-2 text-[#C7C7C7]">{{ staff.fullname }}</h3>
                                    <p v-if="staff.profession" class="text-sm sm:text-base text-white mb-2">{{ staff.profession }}</p>
                                    <p v-if="staff.phone" class="text-[#C7C7C7] text-xs sm:text-sm break-all">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        {{ staff.phone }}
                                    </p>
                                    <p v-if="staff.email" class="text-[#C7C7C7] text-xs sm:text-sm mt-1 break-all">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ staff.email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 sm:py-12">
                    <p class="text-[#C7C7C7] text-base sm:text-lg">{{ t('no_data') }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    about: {
        type: Object,
        required: true
    },
    staffs: {
        type: Array,
        default: () => []
    }
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

// Get about content based on current locale
const aboutContent = computed(() => {
    if (!props.about) {
        return null;
    }
    
    // Helper function to get content value
    const getContent = (key) => {
        const value = props.about[key];
        if (!value || typeof value !== 'string') {
            return null;
        }
        const trimmed = value.trim();
        return trimmed.length > 0 ? trimmed : null;
    };
    
    // Try current locale first (content_hy, content_ge, content_ru, content_en)
    const localeKey = `content_${locale.value}`;
    const localeContent = getContent(localeKey);
    if (localeContent) {
        return localeContent;
    }
    
    // Fallback to English
    const englishContent = getContent('content_en');
    if (englishContent) {
        return englishContent;
    }
    
    // Fallback to the content field (which should be the current locale from backend)
    const defaultContent = getContent('content');
    if (defaultContent) {
        return defaultContent;
    }
    
    return null;
});
</script>

<style scoped>
/* Ensure content doesn't overflow, especially for Georgian text */
.about-content {
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
    max-width: 100%;
    overflow-x: hidden;
}

/* Ensure all child elements also respect overflow */
.about-content :deep(*) {
    max-width: 100%;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
}

/* Handle images and other media in content */
.about-content :deep(img),
.about-content :deep(video),
.about-content :deep(iframe) {
    max-width: 100%;
    height: auto;
}
</style>

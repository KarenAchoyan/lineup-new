<template>
    <div class="w-full h-[50vh] sm:h-[60vh] md:h-[70vh] lg:h-screen relative bg-[#211d1dfc] overflow-hidden">
        <img 
            :src="bannerImageSrc" 
            class="w-full h-full object-cover opacity-[50%] grayscale object-top transition-all duration-1000 hover:opacity-60"
            alt="Banner Image"
            loading="eager"
        />
        <!-- Gradient overlay for better text readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/60"></div>
        <div class="container  mx-auto px-4 sm:px-6 md:px-8 lg:px-12 z-10 absolute top-0 h-full flex items-end pb-6 sm:pb-10 md:pb-16 lg:pb-20">
            <div class="w-full sm:w-[90%] md:w-[80%] lg:w-[450px] xl:w-[500px] font-bold animate-fade-in">
                <h1 class="text-[22px] sm:text-[28px] md:text-[36px] lg:text-[42px] xl:text-[48px] text-white mb-3 sm:mb-4 md:mb-5 lg:mb-6 drop-shadow-[0_4px_12px_rgba(0,0,0,0.8)] leading-tight sm:leading-snug md:leading-normal">
                    {{ bannerText.aboutDancing }} 
                    <span class="text-[#F15A2B] drop-shadow-[0_0_20px_rgba(241,90,43,0.6)]">{{ bannerText.dancing }}...</span>
                </h1>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    banner: {
        type: Object,
        default: null,
    },
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

// Debug: Log banner data on mount
onMounted(() => {
    if (props.banner) {
        console.log('Banner data received:', props.banner);
        console.log('Current locale:', locale.value);
        console.log('About dancing key:', `about_dancing_${locale.value}`);
        console.log('Dancing key:', `dancing_${locale.value}`);
    } else {
        console.log('No banner data received');
    }
});

// Banner image source
const bannerImageSrc = computed(() => {
    if (props.banner?.image) {
        return `/storage/${props.banner.image}`;
    }
    // Fallback to default image if no banner in database
    return '/banner1.png';
});

// Banner text based on current locale
const bannerText = computed(() => {
    if (!props.banner) {
        // Fallback to translation keys if no banner data
        return {
            aboutDancing: t('about_dancing'),
            dancing: t('dancing'),
        };
    }

    // Helper function to get text value (handles null, undefined, and empty strings)
    const getText = (key) => {
        const value = props.banner[key];
        // Handle null, undefined, or non-string values
        if (value === null || value === undefined) {
            return null;
        }
        // Convert to string if it's not already
        const stringValue = String(value);
        const trimmed = stringValue.trim();
        return trimmed.length > 0 ? trimmed : null;
    };
    
    // Try current locale first, then English, then fallback to translation
    const aboutDancingKey = `about_dancing_${locale.value}`;
    const dancingKey = `dancing_${locale.value}`;
    
    const aboutDancing = getText(aboutDancingKey) || getText('about_dancing_en') || t('about_dancing');
    const dancing = getText(dancingKey) || getText('dancing_en') || t('dancing');
    
    // Debug log
    if (process.env.NODE_ENV === 'development') {
        console.log('Banner text computed:', {
            aboutDancingKey,
            dancingKey,
            aboutDancing,
            dancing,
            bannerData: props.banner
        });
    }
    
    return {
        aboutDancing,
        dancing,
    };
});
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}
</style>

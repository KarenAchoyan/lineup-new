<template>
    <AppLayout>
        <Head :title="activeUser.fullname" />
        
        <div class="w-full bg-[#232222] pt-[160px] min-h-screen">
            <div class="container m-auto bg-[#4D4C4C] min-h-[100vh] rounded-3xl pt-20">
                <h1 class="text-center my-5 text-[32px] md:text-[45px] text-[#C7C7C7] pb-10">
                    {{ t('active_youth') }}
                </h1>
                <div class="w-[80%] m-auto pb-[50px]">
                    <div class="text-center md:text-left">
                        <div class="md:float-left mx-auto w-[300px]">
                            <img 
                                v-if="activeUser.avatar" 
                                :src="`/storage/${activeUser.avatar}`" 
                                :alt="activeUser.fullname"
                                class="w-[250px] h-[250px] rounded-full float-left m-3 mr-5 object-cover border-2 border-[#F15A2B]"
                            />
                        </div>
                        <h2 class="text-[45px] text-[#F15A2B] font-bold mt-4 md:mt-0">
                            {{ displayFullname }}
                        </h2>
                        <div v-if="displayAbout" class="text-[20px] text-white mt-6 about-content" v-html="displayAbout"></div>
                    </div>
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
    activeUser: {
        type: Object,
        required: true,
    },
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

// Get fullname based on current locale
const displayFullname = computed(() => {
    if (!props.activeUser) {
        return '';
    }
    
    // Helper function to get value
    const getValue = (key) => {
        const value = props.activeUser[key];
        if (!value || typeof value !== 'string') {
            return null;
        }
        const trimmed = value.trim();
        return trimmed.length > 0 ? trimmed : null;
    };
    
    // Try current locale first
    const localeKey = `fullname_${locale.value}`;
    const localeFullname = getValue(localeKey);
    if (localeFullname) {
        return localeFullname;
    }
    
    // Fallback to English
    const englishFullname = getValue('fullname_en');
    if (englishFullname) {
        return englishFullname;
    }
    
    // Fallback to the fullname field (which should be the current locale from backend)
    const defaultFullname = getValue('fullname');
    if (defaultFullname) {
        return defaultFullname;
    }
    
    return '';
});

// Get about content based on current locale
const displayAbout = computed(() => {
    if (!props.activeUser) {
        return null;
    }
    
    // Helper function to get content value
    const getContent = (key) => {
        const value = props.activeUser[key];
        if (!value || typeof value !== 'string') {
            return null;
        }
        const trimmed = value.trim();
        return trimmed.length > 0 ? trimmed : null;
    };
    
    // Try current locale first (about_hy, about_ge, about_ru, about_en)
    const localeKey = `about_${locale.value}`;
    const localeAbout = getContent(localeKey);
    if (localeAbout) {
        return localeAbout;
    }
    
    // Fallback to English
    const englishAbout = getContent('about_en');
    if (englishAbout) {
        return englishAbout;
    }
    
    // Fallback to the about field (which should be the current locale from backend)
    const defaultAbout = getContent('about');
    if (defaultAbout) {
        return defaultAbout;
    }
    
    return null;
});
</script>

<style scoped>
/* Style for CKEditor content */
.about-content {
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.6;
}

/* Ensure all child elements respect overflow */
.about-content :deep(*) {
    max-width: 100%;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Handle images in content */
.about-content :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1rem 0;
}

/* Handle paragraphs */
.about-content :deep(p) {
    margin-bottom: 1rem;
}

/* Handle lists */
.about-content :deep(ul),
.about-content :deep(ol) {
    margin: 1rem 0;
    padding-left: 2rem;
}

/* Handle headings */
.about-content :deep(h1),
.about-content :deep(h2),
.about-content :deep(h3),
.about-content :deep(h4),
.about-content :deep(h5),
.about-content :deep(h6) {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: bold;
}

/* Handle links */
.about-content :deep(a) {
    color: #F15A2B;
    text-decoration: underline;
}

.about-content :deep(a:hover) {
    color: #BF3206;
}

/* Handle tables */
.about-content :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.about-content :deep(table td),
.about-content :deep(table th) {
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
}
</style>


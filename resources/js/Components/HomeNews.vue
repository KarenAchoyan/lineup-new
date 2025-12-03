<template>
    <div v-if="news" class="w-full bg-[#232222]">
        <div :class="showTitle ? 'pt-8 sm:pt-12 md:pt-16 lg:pt-20' : 'pt-4 sm:pt-6'">
            <div class="w-full bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-4 sm:p-6 md:p-8 lg:p-10 rounded-xl sm:rounded-2xl border-t-2 border-[#BF3206] shadow-2xl hover:shadow-[0_8px_30px_rgba(191,50,6,0.3)] transition-all duration-300">
                <h2 v-if="showTitle" class="text-[#C7C7C7] font-bold my-4 sm:my-6 md:my-8 text-center text-[22px] sm:text-[28px] md:text-[36px] lg:text-[42px] drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]">
                    {{ title || t('news') }}
                </h2>
                <div class="flex flex-col lg:flex-row items-start gap-5 sm:gap-6 md:gap-8 lg:gap-10">
                    <!-- Image or Video -->
                    <div v-if="!news.video" class="w-full lg:w-1/2 group order-2 lg:order-1">
                        <div class="relative overflow-hidden rounded-lg sm:rounded-xl shadow-xl group-hover:shadow-2xl transition-all duration-500">
                            <img 
                                v-if="news.avatar" 
                                :src="`/storage/${news.avatar}`" 
                                class="w-full h-auto min-h-[200px] sm:min-h-[250px] md:min-h-[300px] object-cover rounded-lg shadow-lg transition-transform duration-700 group-hover:scale-110"
                                alt="News Image"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>
                    <div v-if="news.video && youtubeId" class="w-full lg:w-1/2 group order-2 lg:order-1">
                        <div class="relative overflow-hidden rounded-lg sm:rounded-xl shadow-xl group-hover:shadow-2xl transition-all duration-500">
                            <div class="aspect-video w-full">
                                <iframe
                                    class="w-full h-full rounded-lg transition-transform duration-700 group-hover:scale-105"
                                    :src="`https://www.youtube.com/embed/${youtubeId}`"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerPolicy="strict-origin-when-cross-origin"
                                    allowFullScreen
                                    loading="lazy"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="w-full lg:w-1/2 space-y-3 sm:space-y-4 md:space-y-5 order-1 lg:order-2">
                        <h3 class="text-[18px] sm:text-[22px] md:text-[26px] lg:text-[30px] font-semibold mb-3 sm:mb-4 md:mb-5 text-[#C7C7C7] hover:text-[#F15A2B] transition-colors duration-300 drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)] leading-tight sm:leading-snug">
                            {{ newsTitle }}
                        </h3>
                        <div class="text-[14px] sm:text-[16px] md:text-[18px] lg:text-[20px] font-medium text-white leading-relaxed sm:leading-loose" v-html="truncatedContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import { usePage } from '@inertiajs/vue3';
import { getYouTubeId, truncateText } from '@/utils/utils';

const props = defineProps({
    news: Object,
    showTitle: {
        type: Boolean,
        default: false,
    },
    title: String,
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

const newsTitle = computed(() => {
    if (!props.news) return '';
    return props.news[`title_${locale.value}`] || props.news.title_en || '';
});

const newsContent = computed(() => {
    if (!props.news) return '';
    return props.news[`content_${locale.value}`] || props.news.content_en || '';
});

const truncatedContent = computed(() => {
    return truncateText(newsContent.value, 250);
});

const youtubeId = computed(() => {
    if (!props.news?.video) return null;
    return getYouTubeId(props.news.video);
});
</script>

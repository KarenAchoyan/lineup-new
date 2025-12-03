<template>
    <AppLayout>
        <Head :title="t('archive')" />
        
        <div class="min-h-screen bg-[#232222] py-6 sm:py-8 md:py-12">
            <div class="container mx-auto px-3 sm:px-4">
                <!-- Header -->
                <div class="text-center mb-8 sm:mb-10 md:mb-12">
                    <h1 class="text-[24px] sm:text-[32px] md:text-[45px] font-bold text-[#C7C7C7] mb-3 sm:mb-4">{{ t('archive') }}</h1>
                </div>

                <!-- Year Filter Buttons -->
                <div v-if="years && years.length > 0" class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-6 sm:mb-8">
                    <button
                        @click="filterByYear(null)"
                        :class="[
                            'px-4 sm:px-6 py-2 sm:py-3 rounded-full text-sm sm:text-base font-semibold transition-all duration-300 shadow-md',
                            currentYear === null
                                ? 'bg-[#F15A2B] text-white'
                                : 'bg-[#4D4C4C] text-[#C7C7C7] hover:bg-[#5D5D5D]'
                        ]"
                    >
                        {{ t('all_years') || 'All Years' }}
                    </button>
                    <button
                        v-for="year in years"
                        :key="year"
                        @click="filterByYear(year)"
                        :class="[
                            'px-4 sm:px-6 py-2 sm:py-3 rounded-full text-sm sm:text-base font-semibold transition-all duration-300 shadow-md',
                            currentYear === year
                                ? 'bg-[#F15A2B] text-white'
                                : 'bg-[#4D4C4C] text-[#C7C7C7] hover:bg-[#5D5D5D]'
                        ]"
                    >
                        {{ year }}
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="flex justify-center mb-6 sm:mb-8 overflow-x-auto pb-2">
                    <div class="bg-[#4D4C4C] rounded-full p-1.5 sm:p-2 shadow-lg inline-flex gap-1 sm:gap-2 flex-nowrap">
                        <button
                            @click="activeTab = 'all'"
                            :class="[
                                'px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-full text-xs sm:text-sm md:text-base font-semibold transition-all duration-300 whitespace-nowrap',
                                activeTab === 'all'
                                    ? 'bg-[#F15A2B] text-white shadow-md'
                                    : 'text-[#C7C7C7] hover:text-[#F15A2B]'
                            ]"
                        >
                            {{ t('all') || 'All' }}
                        </button>
                        <button
                            @click="activeTab = 'images'"
                            :class="[
                                'px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-full text-xs sm:text-sm md:text-base font-semibold transition-all duration-300 whitespace-nowrap',
                                activeTab === 'images'
                                    ? 'bg-[#F15A2B] text-white shadow-md'
                                    : 'text-[#C7C7C7] hover:text-[#F15A2B]'
                            ]"
                        >
                            {{ t('photos') }}
                        </button>
                        <button
                            @click="activeTab = 'videos'"
                            :class="[
                                'px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-full text-xs sm:text-sm md:text-base font-semibold transition-all duration-300 whitespace-nowrap',
                                activeTab === 'videos'
                                    ? 'bg-[#F15A2B] text-white shadow-md'
                                    : 'text-[#C7C7C7] hover:text-[#F15A2B]'
                            ]"
                        >
                            {{ t('videos') }}
                        </button>
                        <button
                            @click="activeTab = 'news'"
                            :class="[
                                'px-3 sm:px-4 md:px-6 py-2 sm:py-3 rounded-full text-xs sm:text-sm md:text-base font-semibold transition-all duration-300 whitespace-nowrap',
                                activeTab === 'news'
                                    ? 'bg-[#F15A2B] text-white shadow-md'
                                    : 'text-[#C7C7C7] hover:text-[#F15A2B]'
                            ]"
                        >
                            {{ t('news') }}
                        </button>
                    </div>
                </div>

                <!-- Videos Gallery -->
                <div v-if="activeTab === 'all' || activeTab === 'videos'" class="mb-10 sm:mb-12 md:mb-16" data-section="videos">
                    <div class="container m-auto bg-[#4D4C4C] p-4 sm:p-5 rounded-2xl border-t-2 border-[#BF3206] mb-6 sm:mb-8">
                        <h2 class="text-[#C7C7C7] font-bold mb-4 sm:mb-6 text-center text-[24px] sm:text-[30px] md:text-[45px]">{{ t('videos') }}</h2>
                        <div v-if="displayedVideos.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <div
                                v-for="item in displayedVideos"
                                :key="item.id"
                                @click="openVideoModal(item)"
                                class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer bg-[#5D5D5D]"
                            >
                                <div class="aspect-video relative bg-gray-900">
                                    <div v-if="item.youtube_id" class="w-full h-full relative">
                                        <img
                                            :src="`https://img.youtube.com/vi/${item.youtube_id}/maxresdefault.jpg`"
                                            :alt="item.title"
                                            class="w-full h-full object-cover"
                                        />
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/50 transition-colors">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#F15A2B] rounded-full flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="item.title" class="p-3 sm:p-4 bg-[#4D4C4C]">
                                    <h3 class="text-base sm:text-lg font-bold text-[#C7C7C7]">{{ item.title }}</h3>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 sm:py-12">
                            <p class="text-[#C7C7C7] text-base sm:text-lg">{{ t('no_data') }}</p>
                        </div>
                        <!-- Show All Videos Button -->
                        <div v-if="activeTab === 'all' && videos.length > 3" class="flex justify-center mt-6 sm:mt-8">
                            <button
                                @click="showAllVideos"
                                class="px-6 sm:px-8 py-2 sm:py-3 bg-[#F15A2B] hover:bg-[#BF3206] text-white rounded-full font-semibold text-sm sm:text-base transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                                {{ t('see_all') || 'See All' }} ({{ videos.length }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Images Gallery -->
                <div v-if="activeTab === 'all' || activeTab === 'images'" class="mb-10 sm:mb-12 md:mb-16" data-section="images">
                    <div class="container m-auto bg-[#4D4C4C] p-4 sm:p-5 rounded-2xl border-t-2 border-[#BF3206] mb-6 sm:mb-8">
                        <h2 class="text-[#C7C7C7] font-bold mb-4 sm:mb-6 text-center text-[24px] sm:text-[30px] md:text-[45px]">{{ t('photos') }}</h2>
                        <div v-if="displayedImages.length > 0" class="archive-gallery-grid">
                            <div
                                v-for="(item, index) in displayedImages"
                                :key="item.id"
                                :class="getImageItemClass(index)"
                                class="archive-gallery-item rounded-xl sm:rounded-2xl overflow-hidden group cursor-pointer hover:scale-[1.02] transition-all duration-500 relative shadow-xl hover:shadow-2xl"
                                @click="openImageModal(item)"
                            >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 flex items-end justify-center pb-3 sm:pb-4 md:pb-5">
                                    <span class="text-white text-xs sm:text-sm md:text-base lg:text-lg font-semibold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">{{ item.title || t('view_image') }}</span>
                                </div>
                                <img
                                    :src="item.image ? `/storage/${item.image}` : '/placeholder.jpg'"
                                    :alt="item.title"
                                    class="w-full h-full object-cover group-hover:opacity-90 transition-all duration-500 group-hover:scale-110"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                        <div v-else class="text-center py-8 sm:py-12">
                            <p class="text-[#C7C7C7] text-base sm:text-lg">{{ t('no_data') }}</p>
                        </div>
                        <!-- Show All Images Button -->
                        <div v-if="activeTab === 'all' && images.length > 7" class="flex justify-center mt-6 sm:mt-8">
                            <button
                                @click="showAllImages"
                                class="px-6 sm:px-8 py-2 sm:py-3 bg-[#F15A2B] hover:bg-[#BF3206] text-white rounded-full font-semibold text-sm sm:text-base transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                                {{ t('see_all') || 'See All' }} ({{ images.length }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- News Gallery -->
                <div v-if="activeTab === 'all' || activeTab === 'news'" data-section="news">
                    <div class="container m-auto bg-[#4D4C4C] p-4 sm:p-5 rounded-2xl border-t-2 border-[#BF3206]">
                        <h2 class="text-[#C7C7C7] font-bold mb-4 sm:mb-6 text-center text-[24px] sm:text-[30px] md:text-[45px]">{{ t('news') }}</h2>
                        <div v-if="displayedNews.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <div
                                v-for="item in displayedNews"
                                :key="item.id"
                                class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 bg-[#5D5D5D]"
                            >
                                <div v-if="item.image" class="aspect-video overflow-hidden">
                                    <img
                                        :src="`/storage/${item.image}`"
                                        :alt="item.title"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    />
                                </div>
                                <div class="p-4 sm:p-6">
                                    <h3 class="text-lg sm:text-xl font-bold text-[#C7C7C7] mb-2 sm:mb-3">{{ item.title }}</h3>
                                    <p v-if="item.description" class="text-sm sm:text-base text-white mb-3 sm:mb-4 line-clamp-3">{{ item.description }}</p>
                                    <a
                                        v-if="item.link"
                                        :href="item.link"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 text-xs sm:text-sm text-[#F15A2B] hover:text-[#BF3206] font-semibold"
                                    >
                                        {{ t('read_more') }}
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 sm:py-12">
                            <p class="text-[#C7C7C7] text-base sm:text-lg">{{ t('no_data') }}</p>
                        </div>
                        <!-- Show All News Button -->
                        <div v-if="activeTab === 'all' && news.length > 3" class="flex justify-center mt-6 sm:mt-8">
                            <button
                                @click="showAllNews"
                                class="px-6 sm:px-8 py-2 sm:py-3 bg-[#F15A2B] hover:bg-[#BF3206] text-white rounded-full font-semibold text-sm sm:text-base transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                                {{ t('see_all') || 'See All' }} ({{ news.length }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Video Modal -->
                <Transition name="modal">
                    <div
                        v-if="selectedVideo"
                        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/90 backdrop-blur-sm"
                        @click.self="closeVideoModal"
                    >
                        <div class="relative w-full max-w-5xl bg-[#4D4C4C] rounded-2xl shadow-2xl overflow-hidden border-t-2 border-[#BF3206]">
                            <!-- Close Button -->
                            <button
                                @click="closeVideoModal"
                                class="absolute top-2 right-2 sm:top-4 sm:right-4 z-10 w-8 h-8 sm:w-10 sm:h-10 bg-[#F15A2B] hover:bg-[#BF3206] rounded-full flex items-center justify-center shadow-lg transition-colors"
                            >
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Video Container -->
                            <div class="aspect-video bg-gray-900">
                                <iframe
                                    v-if="selectedVideo.youtube_embed_url"
                                    :src="selectedVideo.youtube_embed_url + '?autoplay=1'"
                                    class="w-full h-full"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </div>

                            <!-- Video Title -->
                            <div v-if="selectedVideo.title" class="p-4 sm:p-6 bg-[#4D4C4C]">
                                <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-[#C7C7C7]">{{ selectedVideo.title }}</h3>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Image Modal -->
                <Transition name="modal">
                    <div
                        v-if="selectedImage"
                        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/90 backdrop-blur-sm"
                        @click.self="closeImageModal"
                    >
                        <div class="relative w-full max-w-6xl bg-[#4D4C4C] rounded-2xl shadow-2xl overflow-hidden border-t-2 border-[#BF3206]">
                            <!-- Close Button -->
                            <button
                                @click="closeImageModal"
                                class="absolute top-2 right-2 sm:top-4 sm:right-4 z-10 w-8 h-8 sm:w-10 sm:h-10 bg-[#F15A2B] hover:bg-[#BF3206] rounded-full flex items-center justify-center shadow-lg transition-colors"
                            >
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Previous Button -->
                            <button
                                v-if="canGoToPreviousImage"
                                @click="goToPreviousImage"
                                class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 sm:w-12 sm:h-12 bg-[#F15A2B]/80 hover:bg-[#F15A2B] rounded-full flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110"
                            >
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- Next Button -->
                            <button
                                v-if="canGoToNextImage"
                                @click="goToNextImage"
                                class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 sm:w-12 sm:h-12 bg-[#F15A2B]/80 hover:bg-[#F15A2B] rounded-full flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110"
                            >
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <!-- Image Container -->
                            <div class="relative">
                                <Transition name="fade" mode="out-in">
                                    <img
                                        :key="currentImageIndex"
                                        :src="selectedImage.image ? `/storage/${selectedImage.image}` : '/placeholder.jpg'"
                                        :alt="selectedImage.title"
                                        class="w-full h-auto max-h-[90vh] object-contain"
                                    />
                                </Transition>
                            </div>

                            <!-- Image Counter -->
                            <div class="absolute bottom-20 sm:bottom-24 left-1/2 -translate-x-1/2 z-10 bg-black/60 px-4 py-2 rounded-full text-white text-sm sm:text-base">
                                {{ currentImageIndex + 1 }} / {{ images.length }}
                            </div>

                            <!-- Image Title and Description -->
                            <div v-if="selectedImage.title || selectedImage.description" class="p-4 sm:p-6 bg-[#4D4C4C]">
                                <h3 v-if="selectedImage.title" class="text-lg sm:text-xl md:text-2xl font-bold text-[#C7C7C7] mb-2">{{ selectedImage.title }}</h3>
                                <p v-if="selectedImage.description" class="text-sm sm:text-base text-white">{{ selectedImage.description }}</p>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    images: {
        type: Array,
        default: () => []
    },
    videos: {
        type: Array,
        default: () => []
    },
    news: {
        type: Array,
        default: () => []
    },
    years: {
        type: Array,
        default: () => []
    },
    selectedYear: {
        type: [Number, String, null],
        default: null
    }
});

const { t } = useTranslation();
const activeTab = ref('all');
const selectedVideo = ref(null);
const selectedImage = ref(null);
const currentImageIndex = ref(0);
const currentYear = ref(props.selectedYear);

// Computed properties for limited display in "all" tab
const displayedVideos = computed(() => {
    if (activeTab.value === 'all') {
        return props.videos.slice(0, 3);
    }
    return props.videos;
});

const displayedImages = computed(() => {
    if (activeTab.value === 'all') {
        return props.images.slice(0, 7);
    }
    return props.images;
});

const displayedNews = computed(() => {
    if (activeTab.value === 'all') {
        return props.news.slice(0, 3);
    }
    return props.news;
});

const filterByYear = (year) => {
    currentYear.value = year;
    router.get(route('archive'), { year: year }, {
        preserveState: true,
        preserveScroll: true,
        only: ['images', 'videos', 'news', 'years', 'selectedYear'],
    });
};

const openVideoModal = (video) => {
    selectedVideo.value = video;
    document.body.style.overflow = 'hidden';
};

const closeVideoModal = () => {
    selectedVideo.value = null;
    document.body.style.overflow = '';
};

const getImageItemClass = (index) => {
    // Create a pattern: big, small, small, small, big, small, small, small, etc.
    const pattern = index % 4;
    if (pattern === 0) {
        // Big item - spans 2 columns and 2 rows
        return 'archive-gallery-item-big';
    } else {
        // Small item - spans 1 column and 1 row
        return 'archive-gallery-item-small';
    }
};

const openImageModal = (image) => {
    // Find the index of the clicked image
    const index = props.images.findIndex(img => img.id === image.id);
    currentImageIndex.value = index >= 0 ? index : 0;
    selectedImage.value = image;
    document.body.style.overflow = 'hidden';
};

const closeImageModal = () => {
    selectedImage.value = null;
    currentImageIndex.value = 0;
    document.body.style.overflow = '';
};

// Computed properties for navigation
const canGoToPreviousImage = computed(() => {
    return props.images.length > 0 && currentImageIndex.value > 0;
});

const canGoToNextImage = computed(() => {
    return props.images.length > 0 && currentImageIndex.value < props.images.length - 1;
});

// Navigation functions
const goToPreviousImage = () => {
    if (canGoToPreviousImage.value) {
        currentImageIndex.value--;
        selectedImage.value = props.images[currentImageIndex.value];
    }
};

const goToNextImage = () => {
    if (canGoToNextImage.value) {
        currentImageIndex.value++;
        selectedImage.value = props.images[currentImageIndex.value];
    }
};

// Functions to show all items
const showAllVideos = () => {
    activeTab.value = 'videos';
    // Scroll to videos section
    setTimeout(() => {
        const videosSection = document.querySelector('[data-section="videos"]');
        if (videosSection) {
            videosSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
};

const showAllImages = () => {
    activeTab.value = 'images';
    // Scroll to images section
    setTimeout(() => {
        const imagesSection = document.querySelector('[data-section="images"]');
        if (imagesSection) {
            imagesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
};

const showAllNews = () => {
    activeTab.value = 'news';
    // Scroll to news section
    setTimeout(() => {
        const newsSection = document.querySelector('[data-section="news"]');
        if (newsSection) {
            newsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
};

// Keyboard navigation
const handleKeyboard = (e) => {
    if (selectedImage.value) {
        if (e.key === 'Escape') {
            closeImageModal();
        } else if (e.key === 'ArrowLeft' && canGoToPreviousImage.value) {
            goToPreviousImage();
        } else if (e.key === 'ArrowRight' && canGoToNextImage.value) {
            goToNextImage();
        }
    } else if (selectedVideo.value) {
        if (e.key === 'Escape') {
            closeVideoModal();
        }
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeyboard);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeyboard);
    document.body.style.overflow = '';
});
</script>

<style scoped>
/* Archive Gallery Grid */
.archive-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    grid-auto-rows: 250px;
    gap: 1rem;
    grid-auto-flow: dense;
}

.archive-gallery-item {
    position: relative;
    overflow: hidden;
}

.archive-gallery-item-big {
    grid-column: span 2;
    grid-row: span 2;
    min-height: 500px;
}

.archive-gallery-item-small {
    grid-column: span 1;
    grid-row: span 1;
    min-height: 250px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .archive-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        grid-auto-rows: 150px;
        gap: 0.75rem;
    }
    
    .archive-gallery-item-big {
        grid-column: span 2;
        grid-row: span 2;
        min-height: 300px;
    }
    
    .archive-gallery-item-small {
        min-height: 150px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .archive-gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        grid-auto-rows: 200px;
    }
    
    .archive-gallery-item-big {
        min-height: 400px;
    }
    
    .archive-gallery-item-small {
        min-height: 200px;
    }
}

/* Modal transition */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.9);
    opacity: 0;
}

/* Fade transition for image changes */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

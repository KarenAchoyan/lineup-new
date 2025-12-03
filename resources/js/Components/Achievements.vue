<template>
    <div class="bg-[#232222] py-8 sm:py-12 md:py-16 lg:py-20">
        <div class="w-full bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-4 sm:p-6 md:p-8 lg:p-10 rounded-xl sm:rounded-2xl border-t-2 border-[#BF3206] shadow-2xl hover:shadow-[0_8px_30px_rgba(191,50,6,0.3)] transition-all duration-300">
            <h1 class="text-[#C7C7C7] font-bold mb-6 sm:mb-8 md:mb-10 text-center text-[22px] sm:text-[28px] md:text-[36px] lg:text-[42px] drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]">
                {{ t('gallery') }}
            </h1>
            <div class="gallery-grid">
                <div 
                    v-for="(gallery, index) in galleries" 
                    :key="gallery.id"
                    :class="getItemClass(index)"
                    class="gallery-item rounded-xl sm:rounded-2xl overflow-hidden group cursor-pointer hover:scale-[1.02] transition-all duration-500 relative shadow-xl hover:shadow-2xl"
                    @click="openLightbox(gallery)"
                >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 flex items-end justify-center pb-3 sm:pb-4 md:pb-5">
                        <span class="text-white text-xs sm:text-sm md:text-base lg:text-lg font-semibold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">Click to view</span>
                    </div>
                    <img 
                        :src="`/storage/${gallery.image}`" 
                        class="w-full h-full object-cover group-hover:opacity-90 transition-all duration-500 group-hover:scale-110"
                        :alt="`Gallery ${gallery.id}`"
                        loading="lazy"
                    />
                </div>
            </div>
            <div v-if="showAllLink" class="flex justify-center mt-6 sm:mt-8 md:mt-10 lg:mt-12">
                <Link :href="route('gallery')" class="text-[#C7C7C7] underline hover:text-[#F15A2B] transition-all duration-300 font-semibold text-sm sm:text-base md:text-lg transform hover:scale-110 inline-block px-4 py-2 rounded-lg hover:bg-white/5">
                    {{ t('see_more') }}
                </Link>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <Transition name="fade">
            <div 
                v-if="selectedImage"
                class="fixed inset-0 bg-black/95 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                @click="closeLightbox"
            >
                <div class="relative max-w-5xl max-h-full group">
                    <button 
                        @click="closeLightbox"
                        class="absolute -top-10 sm:-top-12 right-0 text-white hover:text-[#F15A2B] transition-all duration-300 z-10 w-10 h-10 sm:w-12 sm:h-12 bg-white/10 hover:bg-[#F15A2B] rounded-full flex items-center justify-center shadow-lg hover:scale-110 hover:rotate-90"
                    >
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <img 
                        :src="`/storage/${selectedImage.image}`" 
                        class="max-w-full max-h-[85vh] sm:max-h-[90vh] rounded-xl shadow-2xl transform scale-95 group-hover:scale-100 transition-transform duration-500"
                        alt="Gallery Image"
                        @click.stop
                    />
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    galleries: {
        type: Array,
        default: () => [],
    },
    showAllLink: {
        type: Boolean,
        default: false,
    },
});

const { t } = useTranslation();
const selectedImage = ref(null);

const getItemClass = (index) => {
    // Create a pattern: big, small, small, big, small, small, etc.
    const pattern = index % 4;
    if (pattern === 0) {
        // Big item - spans 2 columns and 2 rows
        return 'gallery-item-big';
    } else {
        // Small item - spans 1 column and 1 row
        return 'gallery-item-small';
    }
};

const openLightbox = (gallery) => {
    selectedImage.value = gallery;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    selectedImage.value = null;
    document.body.style.overflow = '';
};
</script>

<style scoped>
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    grid-auto-rows: 250px;
    gap: 1rem;
    grid-auto-flow: dense;
}

.gallery-item {
    position: relative;
    overflow: hidden;
}

.gallery-item-big {
    grid-column: span 2;
    grid-row: span 2;
    min-height: 500px;
}

.gallery-item-small {
    grid-column: span 1;
    grid-row: span 1;
    min-height: 250px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        grid-auto-rows: 150px;
        gap: 0.75rem;
    }
    
    .gallery-item-big {
        grid-column: span 2;
        grid-row: span 2;
        min-height: 300px;
    }
    
    .gallery-item-small {
        min-height: 150px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        grid-auto-rows: 200px;
    }
    
    .gallery-item-big {
        min-height: 400px;
    }
    
    .gallery-item-small {
        min-height: 200px;
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

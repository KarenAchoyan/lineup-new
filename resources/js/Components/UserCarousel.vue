<template>
    <div class="bg-[#232222] pt-6 sm:pt-8 md:pt-12 lg:pt-16 xl:pt-20">
        <div class="w-full max-w-7xl mx-auto bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-3 sm:p-4 md:p-6 lg:p-8 xl:p-10 rounded-lg sm:rounded-xl md:rounded-2xl border-t-2 border-[#BF3206] min-h-[350px] sm:min-h-[400px] md:min-h-[450px] lg:min-h-[500px] xl:min-h-[550px] shadow-2xl hover:shadow-[0_8px_30px_rgba(191,50,6,0.3)] transition-all duration-300">
            <h2 class="text-[#C7C7C7] font-bold mb-4 sm:mb-6 md:mb-8 lg:mb-10 text-center text-[20px] sm:text-[24px] md:text-[30px] lg:text-[36px] xl:text-[42px] drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)] px-2">
                {{ t('active_youth') }}
            </h2>
            
            <div class="relative px-1 sm:px-2 md:px-4 lg:px-8 xl:px-12 2xl:px-16">
                <!-- Carousel Container -->
                <div 
                    ref="carouselContainer"
                    class="carousel-wrapper overflow-hidden rounded-lg sm:rounded-xl"
                    @touchstart="handleTouchStart" 
                    @touchmove="handleTouchMove" 
                    @touchend="handleTouchEnd"
                >
                    <div 
                        ref="carouselTrack"
                        class="carousel-track flex transition-transform duration-500 ease-in-out"
                        :style="{ transform: `translateX(-${translateX}px)` }"
                    >
                        <div 
                            v-for="(user, index) in users" 
                            :key="user.id || index"
                            class="carousel-slide flex-shrink-0"
                            :style="{ width: slideWidth > 0 ? `${slideWidth}px` : '100%' }"
                        >
                            <div class="flex flex-col items-center justify-center pt-2 sm:pt-4 md:pt-6 lg:pt-8 xl:pt-10 group px-1.5 sm:px-2 md:px-3 lg:px-4">
                                <Link :href="route('active-youth.show', user.id)" class="flex flex-col items-center">
                                    <div class="shadow-[4px_4px_16.9px_7px_#00000040] w-[80px] h-[80px] min-[375px]:w-[100px] min-[375px]:h-[100px] sm:w-[120px] sm:h-[120px] md:w-[150px] md:h-[150px] lg:w-[180px] lg:h-[180px] xl:w-[200px] xl:h-[200px] 2xl:w-[240px] 2xl:h-[240px] rounded-full overflow-hidden border-2 sm:border-t-2 border-[#F15A2B] hover:scale-105 sm:hover:scale-110 hover:border-[#BF3206] transition-all duration-500 cursor-pointer m-auto relative group">
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#F15A2B]/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                                        <img 
                                            :src="`/storage/${user.avatar}`" 
                                            :alt="userName(user)"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                            loading="lazy"
                                        />
                                    </div>
                                    <p class="text-center mt-2 sm:mt-3 md:mt-4 lg:mt-5 text-[10px] min-[375px]:text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl text-[#F5F5F5] font-medium group-hover:text-[#F15A2B] transition-colors duration-300 px-1 sm:px-2 break-words max-w-full">{{ userName(user) }}</p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button
                    v-if="shouldShowNavigation"
                    @click.prevent="prevSlide"
                    :disabled="!canNavigate || currentIndex === 0"
                    class="nav-button-prev absolute left-0 sm:left-[-4px] md:left-[-8px] lg:left-[-16px] xl:left-[-20px] 2xl:left-[-24px] top-1/2 -translate-y-1/2 z-30 w-[32px] h-[32px] sm:w-[40px] sm:h-[40px] md:w-[44px] md:h-[44px] lg:w-[50px] lg:h-[50px] xl:w-[55px] xl:h-[55px] 2xl:w-[60px] 2xl:h-[60px] flex items-center justify-center bg-gradient-to-br from-[#C7C7C7] to-[#a0a0a0] rounded-full hover:border-2 hover:border-[#F15A2B] hover:bg-gradient-to-br hover:from-[#F15A2B] hover:to-[#BF3206] hover:text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-110 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    aria-label="Previous slide"
                    type="button"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-5 md:h-5 lg:w-6 lg:h-6 xl:w-6 xl:h-6 2xl:w-7 2xl:h-7 text-[#F15A2B] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    v-if="shouldShowNavigation"
                    @click.prevent="nextSlide"
                    :disabled="!canNavigate || currentIndex >= maxIndex"
                    class="nav-button-next absolute right-0 sm:right-[-4px] md:right-[-8px] lg:right-[-16px] xl:right-[-20px] 2xl:right-[-24px] top-1/2 -translate-y-1/2 z-30 w-[32px] h-[32px] sm:w-[40px] sm:h-[40px] md:w-[44px] md:h-[44px] lg:w-[50px] lg:h-[50px] xl:w-[55px] xl:h-[55px] 2xl:w-[60px] 2xl:h-[60px] flex items-center justify-center bg-gradient-to-br from-[#C7C7C7] to-[#a0a0a0] rounded-full hover:border-2 hover:border-[#F15A2B] hover:bg-gradient-to-br hover:from-[#F15A2B] hover:to-[#BF3206] hover:text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-110 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    aria-label="Next slide"
                    type="button"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-5 md:h-5 lg:w-6 lg:h-6 xl:w-6 xl:h-6 2xl:w-7 2xl:h-7 text-[#F15A2B] transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Mobile Indicators -->
                <div v-if="shouldShowNavigation && windowWidth < 768" class="flex justify-center gap-1.5 sm:gap-2 mt-4 sm:mt-6">
                    <button
                        v-for="(dot, index) in totalDots"
                        :key="index"
                        @click="goToSlide(index)"
                        :class="[
                            'w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full transition-all duration-300',
                            index === currentDotIndex ? 'bg-[#F15A2B] scale-125' : 'bg-gray-500 hover:bg-gray-400'
                        ]"
                        :aria-label="`Go to slide ${index + 1}`"
                        type="button"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

// Refs
const carouselContainer = ref(null);
const carouselTrack = ref(null);
const currentIndex = ref(0);
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024);
const containerWidth = ref(0);
const slideWidth = ref(0);
const translateX = ref(0);

// Touch states
const touchStartX = ref(0);
const touchEndX = ref(0);
const canNavigate = ref(true);

// Responsive breakpoints
const slidesToShow = computed(() => {
    if (windowWidth.value < 480) return 2;
    if (windowWidth.value < 640) return 2;
    if (windowWidth.value < 768) return 2;
    if (windowWidth.value < 1024) return 3;
    if (windowWidth.value < 1280) return 4;
    return 4;
});

const maxIndex = computed(() => {
    return Math.max(0, props.users.length - slidesToShow.value);
});

const shouldShowNavigation = computed(() => {
    return props.users.length > slidesToShow.value;
});

const totalDots = computed(() => {
    if (props.users.length <= slidesToShow.value) return 0;
    return Math.ceil(props.users.length / slidesToShow.value);
});

const currentDotIndex = computed(() => {
    return Math.floor(currentIndex.value / slidesToShow.value);
});

// Calculate dimensions
const calculateDimensions = () => {
    if (!carouselContainer.value || typeof window === 'undefined') {
        return;
    }
    
    const container = carouselContainer.value;
    if (container && container.offsetWidth > 0) {
        containerWidth.value = container.offsetWidth;
        const calculatedWidth = containerWidth.value / slidesToShow.value;
        slideWidth.value = calculatedWidth > 0 ? calculatedWidth : 200; // fallback
        updateTranslateX();
    }
};

const updateTranslateX = () => {
    if (slideWidth.value > 0) {
        translateX.value = currentIndex.value * slideWidth.value;
    }
};

// Navigation functions
const nextSlide = () => {
    if (!canNavigate.value || currentIndex.value >= maxIndex.value) return;
    
    canNavigate.value = false;
    currentIndex.value = Math.min(currentIndex.value + 1, maxIndex.value);
    updateTranslateX();
    
    setTimeout(() => {
        canNavigate.value = true;
    }, 500);
};

const prevSlide = () => {
    if (!canNavigate.value || currentIndex.value === 0) return;
    
    canNavigate.value = false;
    currentIndex.value = Math.max(currentIndex.value - 1, 0);
    updateTranslateX();
    
    setTimeout(() => {
        canNavigate.value = true;
    }, 500);
};

const goToSlide = (dotIndex) => {
    if (!canNavigate.value) return;
    const targetIndex = dotIndex * slidesToShow.value;
    const clampedIndex = Math.min(targetIndex, maxIndex.value);
    currentIndex.value = clampedIndex;
    updateTranslateX();
};

// Touch handlers
const handleTouchStart = (e) => {
    touchStartX.value = e.touches[0].clientX;
};

const handleTouchMove = (e) => {
    if (!touchStartX.value) return;
    touchEndX.value = e.touches[0].clientX;
    
    const distance = Math.abs(touchStartX.value - touchEndX.value);
    const verticalDistance = Math.abs(e.touches[0].clientY - (e.touches[0].clientY || 0));
    
    if (distance > 10 && distance > verticalDistance) {
        e.preventDefault();
    }
};

const handleTouchEnd = () => {
    if (!touchStartX.value || !touchEndX.value) {
        touchStartX.value = 0;
        touchEndX.value = 0;
        return;
    }
    
    const distance = touchStartX.value - touchEndX.value;
    const minSwipeDistance = 50;
    
    if (Math.abs(distance) > minSwipeDistance && canNavigate.value) {
        if (distance > 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    }
    
    touchStartX.value = 0;
    touchEndX.value = 0;
};

// Resize handler
const handleResize = () => {
    windowWidth.value = window.innerWidth;
    calculateDimensions();
    
    // Reset to valid index if out of bounds
    if (currentIndex.value > maxIndex.value) {
        currentIndex.value = 0;
        updateTranslateX();
    }
};

// Debounced resize
let resizeTimeout = null;
const debouncedResize = () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        handleResize();
    }, 150);
};

// Watch for changes
watch(() => props.users, () => {
    if (props.users.length > 0) {
        nextTick(() => {
            calculateDimensions();
        });
    }
}, { deep: true, immediate: true });

watch(slidesToShow, () => {
    calculateDimensions();
    if (currentIndex.value > maxIndex.value) {
        currentIndex.value = 0;
        updateTranslateX();
    }
});

watch(currentIndex, () => {
    updateTranslateX();
});

// Auto-play
let autoPlayInterval = null;

const userName = (user) => {
    return user[`fullname_${locale.value}`] || user.fullname_en || '';
};

onMounted(() => {
    if (typeof window !== 'undefined') {
        windowWidth.value = window.innerWidth;
        window.addEventListener('resize', debouncedResize);
        
        // Calculate initial dimensions with multiple attempts
        calculateDimensions();
        
        // Try again after a short delay
        setTimeout(() => {
            calculateDimensions();
        }, 100);
        
        // Try with requestAnimationFrame
        requestAnimationFrame(() => {
            calculateDimensions();
        });
        
        // Auto-play if there are more slides than visible
        if (shouldShowNavigation.value) {
            autoPlayInterval = setInterval(() => {
                if (canNavigate.value && shouldShowNavigation.value) {
                    if (currentIndex.value >= maxIndex.value) {
                        currentIndex.value = 0;
                        updateTranslateX();
                    } else {
                        nextSlide();
                    }
                }
            }, 5000);
        }
    }
});

onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('resize', debouncedResize);
    }
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
    if (resizeTimeout) {
        clearTimeout(resizeTimeout);
    }
});
</script>

<style scoped>
.carousel-wrapper {
    position: relative;
    width: 100%;
}

.carousel-track {
    display: flex;
    will-change: transform;
}

.carousel-slide {
    flex-shrink: 0;
}

/* Prevent text selection during drag */
.carousel-track {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* Ensure navigation buttons are always visible */
.nav-button-prev,
.nav-button-next {
    position: absolute;
    z-index: 30;
    cursor: pointer;
}

/* Better touch targets for mobile */
@media (max-width: 768px) {
    .nav-button-prev,
    .nav-button-next {
        min-width: 44px;
        min-height: 44px;
    }
}
</style>

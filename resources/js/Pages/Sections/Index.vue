<template>
    <AppLayout>
        <Head :title="t('sections')" />
        
        <div class="min-h-screen bg-[#232222] py-6 sm:py-8 md:py-12">
            <div class="container mx-auto px-3 sm:px-4">
                <!-- Header -->
                <div class="text-center mb-8 sm:mb-10 md:mb-12">
                    <h1 class="text-[24px] sm:text-[32px] md:text-[45px] lg:text-[55px] font-bold text-[#C7C7C7] mb-3 sm:mb-4">{{ t('sections') }}</h1>
                    <p class="text-sm sm:text-base md:text-lg text-[#C7C7C7] max-w-2xl mx-auto px-2">
                        {{ t('select_a_branch') }}
                    </p>
                </div>

                <!-- Courses Grid -->
                <div v-if="courses.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                    <div
                        v-for="course in courses"
                        :key="course.id"
                        class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 bg-[#4D4C4C]"
                    >
                        <!-- Background Image -->
                        <div 
                            v-if="course.background_image"
                            class="aspect-video relative overflow-hidden"
                            :style="{ backgroundImage: `url(/storage/${course.background_image})`, backgroundSize: 'cover', backgroundPosition: 'center' }"
                        >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                        </div>
                        <div v-else class="aspect-video bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center">
                            <span class="text-white text-3xl sm:text-4xl font-bold">{{ course.name.charAt(0) }}</span>
                        </div>

                        <!-- Content -->
                        <div class="p-4 sm:p-6">
                            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-[#C7C7C7] mb-2 sm:mb-3">{{ course.name }}</h3>
                            <p v-if="course.description" class="text-sm sm:text-base text-gray-300 mb-3 sm:mb-4 line-clamp-2">{{ course.description }}</p>
                            
                            <!-- Price -->
                            <div class="mb-3 sm:mb-4">
                                <span class="text-2xl sm:text-3xl font-bold text-[#F15A2B]">{{ course.price }}</span>
                                <span class="text-sm sm:text-base text-[#C7C7C7] ml-2">GEL</span>
                                <span class="text-xs sm:text-sm text-gray-400 block mt-1">{{ t('every_month') }}</span>
                            </div>

                            <!-- Branches -->
                            <div v-if="course.branches && course.branches.length > 0" class="mb-3 sm:mb-4">
                                <p class="text-xs sm:text-sm text-gray-400 mb-2">{{ t('available_in') || 'Available in' }}:</p>
                                <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                    <span
                                        v-for="branch in course.branches"
                                        :key="branch.id"
                                        class="px-2 sm:px-3 py-1 bg-[#5D5D5D] text-[#C7C7C7] rounded-full text-xs sm:text-sm"
                                    >
                                        {{ branch.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- View Details Button -->
                            <Link
                                :href="route('sections.show', course.slug)"
                                class="block w-full text-center bg-[#F15A2B] hover:bg-[#BF3206] text-white text-sm sm:text-base font-semibold py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg transition-all duration-300 transform hover:scale-105"
                            >
                                {{ t('see_more') }}
                            </Link>
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
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

defineProps({
    courses: {
        type: Array,
        default: () => []
    }
});

const { t } = useTranslation();
</script>


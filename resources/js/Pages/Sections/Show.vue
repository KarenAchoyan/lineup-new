<template>
    <AppLayout>
        <Head :title="course.name" />
        
        <div 
            class="min-h-screen py-8 sm:py-12 md:py-20 relative bg-[#211d1dfc]"
            :style="backgroundStyle"
        >
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/70 to-black/85"></div>
            
            <div class="container mx-auto px-3 sm:px-4 relative z-10">
                <!-- Course Title -->
                <h1 class="text-[24px] sm:text-[32px] md:text-[45px] lg:text-[55px] font-bold text-[#C7C7C7] text-center mb-6 sm:mb-8 md:mb-12 mt-[100px]">
                    {{ course.name }}
                </h1>

                <!-- Main Card -->
                <div class="max-w-2xl mx-auto">
                    <div class="bg-[#4D4C4C] backdrop-blur-sm rounded-2xl border-t-2 border-[#BF3206] shadow-2xl p-5 sm:p-6 md:p-8 lg:p-12">
                        <!-- Price Section -->
                        <div class="text-center mb-6 sm:mb-8">
                            <div class="mb-3 sm:mb-4">
                                <span class="text-4xl sm:text-5xl md:text-6xl font-bold text-[#F15A2B]">{{ course.price }}</span>
                                <span class="text-xl sm:text-2xl text-[#C7C7C7] ml-2">GEL</span>
                            </div>
                            <p class="text-lg sm:text-xl text-white mb-2">{{ t('every_month') }}</p>
                            <p class="text-base sm:text-lg text-[#C7C7C7]">
                                {{ t('valid_for_12_months') }} {{ course.months }} {{ t('months') }}
                            </p>
                        </div>

                        <!-- Branch Selection -->
                        <div class="mb-5 sm:mb-6">
                            <label class="block text-xs sm:text-sm font-medium text-[#C7C7C7] mb-2 sm:mb-3">
                                {{ t('select_a_branch') }}
                            </label>
                            <div class="relative">
                                <button
                                    @click="isDropdownOpen = !isDropdownOpen"
                                    :class="[
                                        'w-full bg-[#5D5D5D] text-white text-base sm:text-lg md:text-xl py-3 sm:py-4 px-4 sm:px-6 rounded-lg shadow-lg transition-all duration-300 flex items-center justify-between hover:bg-[#6D6D6D]',
                                        isDropdownOpen && 'bg-[#F15A2B]'
                                    ]"
                                >
                                    <span class="text-left truncate pr-2">{{ selectedBranch || t('select_a_branch') }}</span>
                                    <svg
                                        class="w-4 h-4 sm:w-5 sm:h-5 transform transition-transform duration-300 flex-shrink-0"
                                        :class="{ 'rotate-180': isDropdownOpen }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <Transition name="dropdown">
                                    <div
                                        v-if="isDropdownOpen"
                                        class="absolute z-50 w-full mt-2 bg-[#4D4C4C] rounded-lg shadow-xl border border-[#BF3206] max-h-60 overflow-y-auto"
                                        @click.stop
                                    >
                                        <ul class="py-2">
                                            <li
                                                v-for="branch in branches"
                                                :key="branch.id"
                                                @click="selectBranch(branch)"
                                                class="px-4 sm:px-6 py-2.5 sm:py-3 text-sm sm:text-base text-[#C7C7C7] hover:bg-[#F15A2B] hover:text-white cursor-pointer transition-colors duration-200"
                                            >
                                                {{ branch.name }}
                                            </li>
                                        </ul>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Register Button -->
                        <button
                            @click="handleRegister"
                            :disabled="!selectedBranchId"
                            :class="[
                                'w-full text-base sm:text-lg md:text-xl font-semibold py-3 sm:py-4 px-4 sm:px-6 rounded-lg shadow-lg transition-all duration-300',
                                selectedBranchId
                                    ? 'bg-[#F15A2B] hover:bg-[#BF3206] text-white cursor-pointer transform hover:scale-105'
                                    : 'bg-[#5D5D5D] text-[#C7C7C7] cursor-not-allowed'
                            ]"
                        >
                            {{ t('register') }}
                        </button>

                        <!-- Description -->
                        <div v-if="course.description" class="mt-6 sm:mt-8 pt-6 sm:pt-8 border-t border-[#5D5D5D]">
                            <h3 class="text-lg sm:text-xl font-bold text-[#C7C7C7] mb-2 sm:mb-3">{{ t('description') || 'Description' }}</h3>
                            <p class="text-sm sm:text-base text-white leading-relaxed">{{ course.description }}</p>
                        </div>
                    </div>
                </div>
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
    course: {
        type: Object,
        required: true
    },
    branches: {
        type: Array,
        default: () => []
    }
});

const { t } = useTranslation();
const isDropdownOpen = ref(false);
const selectedBranch = ref(null);
const selectedBranchId = ref(null);

const backgroundStyle = computed(() => {
    if (props.course.background_image) {
        return {
            backgroundImage: `url(/storage/${props.course.background_image})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            backgroundAttachment: 'fixed'
        };
    }
    return {
        background: 'linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%)'
    };
});

const selectBranch = (branch) => {
    selectedBranch.value = branch.name;
    selectedBranchId.value = branch.id;
    isDropdownOpen.value = false;
};

const handleRegister = () => {
    if (selectedBranchId.value) {
        // Store registration data in session or redirect to registration with params
        router.visit(route('register'), {
            data: {
                course_id: props.course.id,
                branch_id: selectedBranchId.value
            }
        });
    }
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        isDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.3s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>

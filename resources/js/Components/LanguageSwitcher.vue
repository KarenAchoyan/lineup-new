<template>
    <div class="relative" ref="dropdownRef">
        <button
            @click="showDropdown = !showDropdown"
            class="language-switcher-btn relative flex items-center justify-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base font-extrabold text-[#F15A2B] bg-white/15 backdrop-blur-md rounded-xl border border-white/30 hover:bg-white/25 hover:border-[#F15A2B]/60 focus:outline-none focus:ring-2 focus:ring-[#F15A2B] focus:ring-offset-2 focus:ring-offset-[#232222] transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg hover:shadow-[0_0_25px_rgba(241,90,43,0.4)] min-w-[75px] sm:min-w-[85px] md:min-w-[95px] group overflow-visible"
        >
            <!-- Glowing background effect -->
            <div class="absolute -inset-1 rounded-xl bg-[#F15A2B]/20 blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
            
            <!-- Language/Globe Icon -->
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#F15A2B] transition-transform duration-300 group-hover:rotate-12 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5h12M9 3v2m1.196 3.859A1.997 1.997 0 0013 12a1.997 1.997 0 00-.196-.859M3 5l3 3m0 0l3-3m-3 3V3m9 2h-3m-3 0h3m-3 0v2m0-2V3m0 2v2m0-2h3m-3 0H9m3 6h3m-3 0H9m3 0v2m0-2v-2m0 2v2m0-2h3m-3 0H9" />
            </svg>
            
            <!-- Locale Code -->
            <span class="uppercase font-black tracking-wider text-[#F15A2B] drop-shadow-[0_0_10px_rgba(241,90,43,0.6)]">{{ currentLocale.toUpperCase() }}</span>
            
            <!-- Dropdown Chevron -->
            <svg
                class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#F15A2B] transition-transform duration-300 flex-shrink-0"
                :class="{ 'rotate-180': showDropdown }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="3"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95 translate-y-[-10px]"
            enter-to-class="transform opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="transform opacity-100 scale-100 translate-y-0"
            leave-to-class="transform opacity-0 scale-95 translate-y-[-10px]"
        >
            <div
                v-if="showDropdown"
                class="absolute right-0 mt-3 w-48 sm:w-56 rounded-2xl shadow-2xl bg-gradient-to-br from-[#2D2D2D]/95 via-[#232222]/95 to-[#1a1a1a]/95 backdrop-blur-xl ring-2 ring-[#F15A2B]/40 z-50 border border-[#4D4C4C]/70 overflow-hidden"
            >
                <!-- Dropdown Header with gradient -->
                <div class="px-4 py-3 border-b border-[#4D4C4C]/50 bg-gradient-to-r from-[#F15A2B]/15 via-[#F15A2B]/10 to-transparent">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.196 3.859A1.997 1.997 0 0013 12a1.997 1.997 0 00-.196-.859M3 5l3 3m0 0l3-3m-3 3V3m9 2h-3m-3 0h3m-3 0v2m0-2V3m0 2v2m0-2h3m-3 0H9m3 6h3m-3 0H9m3 0v2m0-2v-2m0 2v2m0-2h3m-3 0H9" />
                        </svg>
                        <p class="text-xs font-bold text-gray-300 uppercase tracking-wider">Choose Language</p>
                    </div>
                </div>
                
                <div class="py-2">
                    <button
                        v-for="locale in availableLocales"
                        :key="locale"
                        @click="switchLocale(locale)"
                        :class="[
                            'relative block w-full text-left px-4 py-3.5 text-sm transition-all duration-200 group/item overflow-hidden',
                            locale === currentLocale
                                ? 'bg-gradient-to-r from-[#F15A2B]/25 via-[#F15A2B]/15 to-transparent text-[#F15A2B] font-bold'
                                : 'text-gray-300 hover:bg-[#4D4C4C]/40 hover:text-[#F15A2B]'
                        ]"
                    >
                        <!-- Active indicator gradient bar -->
                        <div 
                            v-if="locale === currentLocale"
                            class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-[#F15A2B] via-[#F15A2B] to-[#BF3206] shadow-[0_0_15px_rgba(241,90,43,0.8)]"
                        ></div>
                        
                        <!-- Hover background effect -->
                        <div class="absolute inset-0 bg-gradient-to-r from-[#F15A2B]/10 to-transparent opacity-0 group-hover/item:opacity-100 transition-opacity duration-200"></div>
                        
                        <span class="flex items-center gap-3 relative z-10">
                            <!-- Status Indicator -->
                            <div class="flex-shrink-0">
                                <div 
                                    v-if="locale === currentLocale"
                                    class="w-3 h-3 rounded-full bg-[#F15A2B] shadow-[0_0_15px_rgba(241,90,43,0.9)] animate-pulse-glow"
                                ></div>
                                <div 
                                    v-else
                                    class="w-3 h-3 rounded-full border-2 border-gray-500 group-hover/item:border-[#F15A2B] group-hover/item:bg-[#F15A2B]/20 transition-all duration-200"
                                ></div>
                            </div>
                            
                            <!-- Locale Code -->
                            <span class="uppercase font-black tracking-wider text-base flex-1">{{ locale.toUpperCase() }}</span>
                            
                            <!-- Language Name -->
                            <span class="text-xs text-gray-400 group-hover/item:text-[#F15A2B] transition-colors font-semibold">
                                {{ getLocaleName(locale) }}
                            </span>
                            
                            <!-- Check Icon for Active -->
                            <svg 
                                v-if="locale === currentLocale"
                                class="w-4 h-4 text-[#F15A2B] flex-shrink-0 animate-scale-in"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useTranslation } from '../composables/useTranslation';

const { currentLocale, availableLocales, getLocaleName } = useTranslation();
const showDropdown = ref(false);
const dropdownRef = ref(null);

const switchLocale = (locale) => {
    showDropdown.value = false;
    
    // Store locale in session via a POST request
    router.post(
        route('locale.switch'),
        { locale },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['translations', 'locale'],
        }
    );
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        showDropdown.value = false;
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
.language-switcher-btn {
    position: relative;
    overflow: visible;
}

/* Enhanced glow effect */
.language-switcher-btn::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: inherit;
    background: radial-gradient(circle, rgba(241, 90, 43, 0.3) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
    pointer-events: none;
}

.language-switcher-btn:hover::after {
    opacity: 1;
}

/* Custom pulse glow animation */
@keyframes pulse-glow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 10px rgba(241, 90, 43, 0.7), 0 0 20px rgba(241, 90, 43, 0.5);
    }
    50% {
        opacity: 0.9;
        transform: scale(1.1);
        box-shadow: 0 0 15px rgba(241, 90, 43, 0.9), 0 0 30px rgba(241, 90, 43, 0.7);
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Scale in animation for check icon */
@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-scale-in {
    animation: scale-in 0.2s ease-out;
}

/* Enhanced backdrop blur */
.backdrop-blur-xl {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

/* Smooth transitions for all interactive elements */
.group\/item:hover {
    transform: translateX(2px);
}
</style>

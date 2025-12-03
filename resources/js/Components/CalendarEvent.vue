<template>
    <div v-if="events.length > 0" class="bg-[#232222] pb-8 sm:pb-12 md:pb-16 lg:pb-20">
        <div class="w-full max-w-7xl mx-auto bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-4 sm:p-6 md:p-8 lg:p-10 rounded-xl sm:rounded-2xl border-t-2 border-[#BF3206] shadow-2xl">
            <h1 class="text-[#C7C7C7] font-bold mb-6 sm:mb-8 md:mb-10 text-center text-[22px] sm:text-[28px] md:text-[36px] lg:text-[42px] drop-shadow-[0_2px_8px_rgba(0,0,0,0.5)]">
                {{ t('events') }}
            </h1>
            <div class="w-full flex flex-col xl:flex-row gap-4 sm:gap-6 md:gap-8 lg:gap-10 text-white">
                <!-- Calendar -->
                <div class="w-full xl:w-[340px] xl:flex-shrink-0 h-auto xl:h-[700px] p-3 sm:p-4 md:p-5 rounded-xl border-[3px] sm:border-[4px] md:border-[5px] bg-[#4d5457] border-[#434343] shadow-lg">
                    <CustomCalendar :dates="eventDates" @dateChange="onDateChange" />
                </div>

                <!-- Event Details -->
                <div class="flex-1 p-4 sm:p-5 md:p-6 lg:p-8 rounded-xl border-[#434343] bg-[#4d5457] border-[3px] sm:border-[4px] md:border-[5px] shadow-lg min-h-[400px] sm:min-h-[500px] md:min-h-[600px] xl:min-h-[700px] flex flex-col overflow-hidden">
                    <div v-if="selectedEvent" class="flex-1 flex flex-col overflow-hidden">
                        <!-- Image Container -->
                        <div v-if="selectedEvent.avatar" class="w-full mb-4 sm:mb-5 md:mb-6 flex-shrink-0 overflow-hidden rounded-xl border-2 border-[#434343]/50">
                            <img
                                :src="`/storage/${selectedEvent.avatar}`"
                                alt="Event"
                                class="w-full h-auto max-h-[180px] sm:max-h-[200px] md:max-h-[220px] lg:max-h-[240px] xl:max-h-[260px] object-cover rounded-xl"
                                loading="lazy"
                            />
                        </div>
                        <!-- Date -->
                        <h3 class="text-base sm:text-lg md:text-xl lg:text-2xl font-semibold mb-3 sm:mb-4 md:mb-5 text-[#F15A2B] flex-shrink-0">{{ formattedEventDate }}</h3>
                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto pr-2">
                            <div class="event-content text-xs sm:text-sm md:text-base lg:text-lg text-gray-300 leading-relaxed sm:leading-loose max-w-full break-words" v-html="eventContent"></div>
                        </div>
                    </div>
                    <div v-else class="flex-1 flex items-center justify-center">
                        <p class="text-gray-400 text-center text-xs sm:text-sm md:text-base lg:text-lg">Select a date to view event details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';
import CustomCalendar from './CustomCalendar.vue';

const props = defineProps({
    events: {
        type: Array,
        default: () => [],
    },
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

const selectedEvent = ref(null);
const selectedDate = ref(null);

const eventDates = computed(() => {
    return props.events.map(event => {
        const date = new Date(event.event_date);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    });
});

const formattedEventDate = computed(() => {
    if (!selectedEvent.value) return '';
    const date = new Date(selectedEvent.value.event_date);
    return date.toLocaleDateString('en-GB', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
});

const eventContent = computed(() => {
    if (!selectedEvent.value) return '';
    const content = selectedEvent.value[`content_${locale.value}`] || selectedEvent.value.content_en || '';
    return content || '';
});

const onDateChange = (dateStr) => {
    selectedDate.value = dateStr;
    const event = props.events.find(e => {
        const eventDate = new Date(e.event_date).toISOString().split('T')[0];
        return eventDate === dateStr;
    });
    selectedEvent.value = event || null;
};

onMounted(() => {
    if (props.events.length > 0) {
        const firstEvent = props.events[0];
        selectedEvent.value = firstEvent;
        selectedDate.value = new Date(firstEvent.event_date).toISOString().split('T')[0];
    }
});

watch(() => props.events, (newEvents) => {
    if (newEvents.length > 0 && !selectedEvent.value) {
        const firstEvent = newEvents[0];
        selectedEvent.value = firstEvent;
        selectedDate.value = new Date(firstEvent.event_date).toISOString().split('T')[0];
    }
}, { immediate: true });
</script>

<style scoped>
.event-content {
    max-width: 100%;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}

/* Style for links in event content */
.event-content :deep(a) {
    color: #F15A2B;
    text-decoration: underline;
    transition: color 0.2s ease;
    font-weight: 500;
}

.event-content :deep(a:hover) {
    color: #BF3206;
    text-decoration: underline;
}

/* Style for paragraphs */
.event-content :deep(p) {
    margin-bottom: 1rem;
    line-height: 1.6;
}

/* Style for headings */
.event-content :deep(h1),
.event-content :deep(h2),
.event-content :deep(h3),
.event-content :deep(h4),
.event-content :deep(h5),
.event-content :deep(h6) {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: bold;
    color: #C7C7C7;
}

/* Style for lists */
.event-content :deep(ul),
.event-content :deep(ol) {
    margin: 1rem 0;
    padding-left: 2rem;
}

.event-content :deep(li) {
    margin-bottom: 0.5rem;
}

/* Style for images */
.event-content :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1rem 0;
}

/* Style for bold and italic text */
.event-content :deep(strong),
.event-content :deep(b) {
    font-weight: 600;
    color: #E5E5E5;
}

.event-content :deep(em),
.event-content :deep(i) {
    font-style: italic;
}
</style>


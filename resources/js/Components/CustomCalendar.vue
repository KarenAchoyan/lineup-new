<template>
    <div class="p-2 sm:p-3 md:p-4 rounded-xl w-full max-w-[280px] sm:max-w-[300px] md:max-w-[320px] lg:max-w-[340px] border-0 mx-auto">
        <div class="flex justify-between items-center mb-4 sm:mb-5 md:mb-6">
            <button 
                @click="prevMonth" 
                class="text-[#82181a] hover:text-orange-700 text-lg sm:text-xl md:text-2xl font-bold transition-all duration-300 hover:scale-110 active:scale-95 p-1 sm:p-2 rounded-lg hover:bg-white/10"
                aria-label="Previous month"
            >
                ◀
            </button>
            <h2 class="text-white text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-center px-2 sm:px-3 md:px-4">{{ currentMonthYear }}</h2>
            <button 
                @click="nextMonth" 
                class="text-[#82181a] hover:text-orange-700 text-lg sm:text-xl md:text-2xl font-bold transition-all duration-300 hover:scale-110 active:scale-95 p-1 sm:p-2 rounded-lg hover:bg-white/10"
                aria-label="Next month"
            >
                ▶
            </button>
        </div>
        <div class="grid grid-cols-7 gap-1.5 sm:gap-2 md:gap-2.5 text-center">
            <div v-for="day in weekDays" :key="day" class="font-bold text-[10px] sm:text-xs md:text-sm text-white/80 py-1 sm:py-2">
                {{ day }}
            </div>
            <div v-for="(day, index) in calendarDays" :key="index" class="flex items-center justify-center">
                <div
                    v-if="day"
                    :class="[
                        'text-white flex items-center justify-center cursor-pointer w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 lg:w-10 lg:h-10 rounded-full mx-auto text-[10px] sm:text-xs md:text-sm lg:text-base transition-all duration-300',
                        day.isSelected ? 'bg-[#82181a] text-white scale-110 shadow-lg' : 
                        day.isEvent ? 'bg-[#F15A2B] text-white hover:bg-[#BF3206] hover:scale-110' : 
                        'hover:bg-gray-600 hover:scale-105'
                    ]"
                    @click="handleDateClick(day.date)"
                >
                    {{ day.day }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    dates: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['dateChange']);

const weekDays = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
const currentDate = ref(new Date());
const selectedDate = ref(new Date());

const currentMonthYear = computed(() => {
    return currentDate.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = firstDay.getDay();

    const days = [];

    // Add empty slots before the first day
    for (let i = 0; i < startingDayOfWeek; i++) {
        days.push(null);
    }

    // Generate actual days
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const dateStr = formatDate(date);
        const isSelected = isSameDay(date, selectedDate.value);
        const isEvent = props.dates.some(eventDate => {
            const eventDateObj = parseDate(eventDate);
            return eventDateObj && isSameDay(date, eventDateObj);
        });

        days.push({
            day,
            date,
            dateStr,
            isSelected,
            isEvent,
        });
    }

    return days;
});

const formatDate = (date) => {
    return date.toISOString().split('T')[0];
};

const parseDate = (dateStr) => {
    // Handle DD-MM-YYYY format
    if (dateStr.includes('-')) {
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }
    }
    // Handle YYYY-MM-DD format
    return new Date(dateStr);
};

const isSameDay = (date1, date2) => {
    if (!date1 || !date2) return false;
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
};

const handleDateClick = (date) => {
    const dateStr = formatDate(date);
    const isEvent = props.dates.some(eventDate => {
        const eventDateObj = parseDate(eventDate);
        return eventDateObj && isSameDay(date, eventDateObj);
    });

    if (isEvent) {
        selectedDate.value = date;
        emit('dateChange', dateStr);
    }
};

const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};
</script>


<template>
    <AuthenticatedLayout>
        <Head :title="`Attendance - ${group.name}`" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ group.name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">{{ group.course_name }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                @click="openBulkAttendanceDaysModal"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            >
                                Set Attendance Days for All
                            </button>
                            <Link
                                :href="route('admin.attendance.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            >
                                Back to Groups
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Week Navigation -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <button
                            @click="previousWeek"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            ← Previous Week
                        </button>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ formatWeekRange(weekStart, weekEnd) }}
                            </h3>
                            <button
                                @click="goToToday"
                                class="text-sm text-[#F15A2B] hover:text-[#BF3206] mt-1"
                            >
                                Go to Today
                            </button>
                        </div>
                        <button
                            @click="nextWeek"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Next Week →
                        </button>
                    </div>
                </div>

                <!-- Week Calendar -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-7 gap-2">
                        <div
                            v-for="day in weekDays"
                            :key="day.date"
                            class="text-center p-3 rounded-lg border-2"
                            :class="[
                                day.is_today ? 'border-[#F15A2B] bg-orange-50' : 'border-gray-200',
                                day.is_attendance_day ? 'bg-blue-50' : ''
                            ]"
                        >
                            <div class="text-xs font-medium text-gray-500 mb-1">{{ day.day_short }}</div>
                            <div class="text-sm font-semibold text-gray-900">{{ day.day_formatted }}</div>
                            <button
                                v-if="hasAnyStudentForDay(day)"
                                @click="selectedDate = day.date; markAllForDate(day.date)"
                                class="mt-2 px-2 py-1 text-xs bg-[#F15A2B] text-white rounded hover:bg-[#BF3206]"
                            >
                                Mark All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Students List -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Students ({{ students.length }})</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Days</th>
                                    <th
                                        v-for="day in weekDays"
                                        :key="day.date"
                                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        :class="day.is_attendance_day ? 'bg-blue-50' : ''"
                                    >
                                        <div>{{ day.day_short }}</div>
                                        <div class="text-xs font-normal">{{ day.day_formatted }}</div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statistics</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="student in students" :key="student.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ student.name }}</div>
                                            <div class="text-xs text-gray-500" v-if="student.biradi">Biradi: {{ student.biradi }}</div>
                                            <div class="text-xs text-gray-500">{{ student.user_name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            @click="openAttendanceDaysModal(student)"
                                            class="text-xs text-[#F15A2B] hover:text-[#BF3206] underline"
                                        >
                                            {{ formatAttendanceDays(student.attendance_days) }}
                                        </button>
                                    </td>
                                    <td
                                        v-for="day in weekDays"
                                        :key="`${student.id}-${day.date}`"
                                        class="px-4 py-4 whitespace-nowrap text-center"
                                        :class="isStudentAttendanceDay(student, day) ? 'bg-blue-50' : ''"
                                    >
                                        <select
                                            v-if="isStudentAttendanceDay(student, day)"
                                            :value="getAttendanceStatus(student, day.date)"
                                            @change="updateAttendance(student.id, day.date, $event.target.value)"
                                            class="text-xs rounded border-gray-300 focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                        >
                                            <option value="present">✓ Present</option>
                                            <option value="absent">✗ Absent</option>
                                            <option value="excused">~ Excused</option>
                                        </select>
                                        <span v-else class="text-gray-300">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-xs">
                                            <div class="text-green-600 font-semibold">✓ {{ student.statistics.present_count }}</div>
                                            <div class="text-red-600 font-semibold">✗ {{ student.statistics.absent_count }}</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Days Modal (Individual Student) -->
        <Transition name="fade">
            <div
                v-if="showAttendanceDaysModal"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                @click="closeAttendanceDaysModal"
            >
                <div
                    class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
                    @click.stop
                >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Set Attendance Days for {{ selectedStudent?.name }}
                        </h3>
                        <button
                            @click="closeAttendanceDaysModal"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <label
                            v-for="day in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']"
                            :key="day"
                            class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                            :class="selectedAttendanceDays.includes(day) ? 'border-[#F15A2B] bg-orange-50' : 'border-gray-300'"
                        >
                            <input
                                type="checkbox"
                                :value="day"
                                v-model="selectedAttendanceDays"
                                class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]"
                            />
                            <span class="ml-3 text-sm font-medium text-gray-700 capitalize">{{ day }}</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button
                            @click="closeAttendanceDaysModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveAttendanceDays"
                            :disabled="savingAttendanceDays"
                            class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50"
                        >
                            {{ savingAttendanceDays ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Bulk Attendance Days Modal -->
        <Transition name="fade">
            <div
                v-if="showBulkAttendanceDaysModal"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                @click="closeBulkAttendanceDaysModal"
            >
                <div
                    class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
                    @click.stop
                >
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Set Attendance Days for All Students
                        </h3>
                        <button
                            @click="closeBulkAttendanceDaysModal"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-800">
                            This will set the same attendance days for all {{ students.length }} students in this group.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label
                            v-for="day in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']"
                            :key="day"
                            class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                            :class="bulkAttendanceDays.includes(day) ? 'border-[#F15A2B] bg-orange-50' : 'border-gray-300'"
                        >
                            <input
                                type="checkbox"
                                :value="day"
                                v-model="bulkAttendanceDays"
                                class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]"
                            />
                            <span class="ml-3 text-sm font-medium text-gray-700 capitalize">{{ day }}</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button
                            @click="closeBulkAttendanceDaysModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveBulkAttendanceDays"
                            :disabled="savingBulkAttendanceDays || bulkAttendanceDays.length === 0"
                            class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50"
                        >
                            {{ savingBulkAttendanceDays ? 'Saving...' : 'Save for All' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    group: Object,
    students: Array,
    weekDays: Array,
    selectedDate: String,
    weekStart: String,
    weekEnd: String,
});

const currentDate = ref(props.selectedDate);
const showAttendanceDaysModal = ref(false);
const selectedStudent = ref(null);
const selectedAttendanceDays = ref([]);
const savingAttendanceDays = ref(false);

const showBulkAttendanceDaysModal = ref(false);
const bulkAttendanceDays = ref([]);
const savingBulkAttendanceDays = ref(false);

const getAttendanceStatus = (student, date) => {
    const attendance = student.attendances.find(a => a.date === date);
    return attendance ? attendance.status : 'absent';
};

const isStudentAttendanceDay = (student, day) => {
    const attendanceDays = student.attendance_days || [];
    if (attendanceDays.length === 0) return false;
    
    // Get day name in lowercase (monday, tuesday, etc.)
    const dayName = day.day_name.toLowerCase();
    return attendanceDays.includes(dayName);
};

const hasAnyStudentForDay = (day) => {
    return props.students.some(student => isStudentAttendanceDay(student, day));
};

const updateAttendance = async (studentId, date, status) => {
    try {
        await axios.post(route('admin.attendance.store'), {
            student_id: studentId,
            group_id: props.group.id,
            date: date,
            status: status,
        });
        
        // Reload to get updated data
        router.reload({ only: ['students', 'weekDays'] });
    } catch (error) {
        console.error('Error updating attendance:', error);
        alert('Failed to update attendance. Please try again.');
    }
};

const markAllForDate = async (date) => {
    if (!confirm(`Mark all students as present for ${new Date(date).toLocaleDateString()}?`)) {
        return;
    }

    try {
        // Get the day of week for the selected date
        const selectedDay = props.weekDays.find(d => d.date === date);
        if (!selectedDay) return;

        const attendances = props.students
            .filter(s => {
                const attendanceDays = s.attendance_days || [];
                const dayName = selectedDay.day_name.toLowerCase();
                return attendanceDays.includes(dayName);
            })
            .map(student => ({
                student_id: student.id,
                status: 'present',
            }));

        if (attendances.length === 0) {
            alert('No students have this day set as an attendance day.');
            return;
        }

        await axios.post(route('admin.attendance.bulk-store'), {
            group_id: props.group.id,
            date: date,
            attendances: attendances,
        });

        router.reload({ only: ['students', 'weekDays'] });
    } catch (error) {
        console.error('Error marking all:', error);
        alert('Failed to mark all. Please try again.');
    }
};

const previousWeek = () => {
    const date = new Date(props.weekStart);
    date.setDate(date.getDate() - 7);
    router.get(route('admin.attendance.show', props.group.id), { date: date.toISOString().split('T')[0] });
};

const nextWeek = () => {
    const date = new Date(props.weekStart);
    date.setDate(date.getDate() + 7);
    router.get(route('admin.attendance.show', props.group.id), { date: date.toISOString().split('T')[0] });
};

const goToToday = () => {
    router.get(route('admin.attendance.show', props.group.id), { date: new Date().toISOString().split('T')[0] });
};

const formatWeekRange = (start, end) => {
    const startDate = new Date(start);
    const endDate = new Date(end);
    return `${startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
};

const formatAttendanceDays = (days) => {
    if (!days || days.length === 0) return 'Not set';
    return days.map(d => d.charAt(0).toUpperCase() + d.slice(1)).join(', ');
};

const openAttendanceDaysModal = (student) => {
    selectedStudent.value = student;
    selectedAttendanceDays.value = [...(student.attendance_days || [])];
    showAttendanceDaysModal.value = true;
};

const closeAttendanceDaysModal = () => {
    showAttendanceDaysModal.value = false;
    selectedStudent.value = null;
    selectedAttendanceDays.value = [];
};

const saveAttendanceDays = async () => {
    if (!selectedStudent.value) return;

    savingAttendanceDays.value = true;
    try {
        await axios.post(route('admin.attendance.update-attendance-days', selectedStudent.value.id), {
            attendance_days: selectedAttendanceDays.value,
        });

        closeAttendanceDaysModal();
        router.reload({ only: ['students'] });
    } catch (error) {
        console.error('Error saving attendance days:', error);
        alert('Failed to save attendance days. Please try again.');
    } finally {
        savingAttendanceDays.value = false;
    }
};

const openBulkAttendanceDaysModal = () => {
    // Get the most common attendance days from students (if any)
    const allDays = props.students.flatMap(s => s.attendance_days || []);
    const dayCounts = {};
    allDays.forEach(day => {
        dayCounts[day] = (dayCounts[day] || 0) + 1;
    });
    
    // Set default to most common days, or empty if none
    bulkAttendanceDays.value = Object.keys(dayCounts).filter(day => 
        dayCounts[day] === props.students.length
    );
    
    showBulkAttendanceDaysModal.value = true;
};

const closeBulkAttendanceDaysModal = () => {
    showBulkAttendanceDaysModal.value = false;
    bulkAttendanceDays.value = [];
};

const saveBulkAttendanceDays = async () => {
    if (bulkAttendanceDays.value.length === 0) {
        alert('Please select at least one day.');
        return;
    }

    if (!confirm(`Set attendance days to ${bulkAttendanceDays.value.map(d => d.charAt(0).toUpperCase() + d.slice(1)).join(', ')} for all ${props.students.length} students?`)) {
        return;
    }

    savingBulkAttendanceDays.value = true;
    try {
        await axios.post(route('admin.attendance.bulk-update-attendance-days', props.group.id), {
            attendance_days: bulkAttendanceDays.value,
        });

        closeBulkAttendanceDaysModal();
        router.reload({ only: ['students'] });
    } catch (error) {
        console.error('Error saving bulk attendance days:', error);
        alert('Failed to save attendance days. Please try again.');
    } finally {
        savingBulkAttendanceDays.value = false;
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>


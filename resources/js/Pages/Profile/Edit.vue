<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    students: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    messages: {
        type: Array,
        default: () => [],
    },
    currentMonth: {
        type: Number,
        default: () => new Date().getMonth() + 1,
    },
    currentYear: {
        type: Number,
        default: () => new Date().getFullYear(),
    },
});

const { t } = useTranslation();
const showStudentForm = ref(false);
const editingStudent = ref(null);
const selectedCourseId = ref(null);
const selectedAttendanceTab = ref(null);
const selectedMonth = ref(props.currentMonth);
const selectedYear = ref(props.currentYear);
const attendanceData = ref(null);
const loadingAttendance = ref(false);
const showMessages = ref(false);
const selectedMessage = ref(null);
const paymentLoading = ref(false);

const studentForm = useForm({
    name: '',
    biradi: '',
    course_id: '',
    branch_id: '',
    birthday: '',
});

const availableBranches = computed(() => {
    if (!selectedCourseId.value) return [];
    const course = props.courses.find(c => c.id === selectedCourseId.value);
    return course ? course.branches : [];
});

const openStudentForm = (student = null) => {
    editingStudent.value = student;
    if (student) {
        studentForm.name = student.name;
        studentForm.biradi = student.biradi || '';
        studentForm.course_id = student.course_id;
        studentForm.branch_id = student.branch_id;
        studentForm.birthday = student.birthday || '';
        selectedCourseId.value = student.course_id;
    } else {
        studentForm.reset();
        selectedCourseId.value = null;
    }
    showStudentForm.value = true;
};

const closeStudentForm = () => {
    showStudentForm.value = false;
    editingStudent.value = null;
    studentForm.reset();
    selectedCourseId.value = null;
};

const submitStudent = () => {
    if (editingStudent.value) {
        studentForm.patch(route('profile.students.update', editingStudent.value.id), {
            onSuccess: () => closeStudentForm(),
        });
    } else {
        studentForm.post(route('profile.students.store'), {
            onSuccess: () => closeStudentForm(),
        });
    }
};

const deleteStudent = (studentId) => {
    if (confirm('Are you sure you want to delete this student profile?')) {
        router.delete(route('profile.students.destroy', studentId));
    }
};

// Payment function
const makePayment = async (studentId, groupId) => {
    if (paymentLoading.value) return;
    
    paymentLoading.value = true;

    // Get CSRF token
    const getCsrfToken = () => {
        const metaToken = document.head.querySelector('meta[name="csrf-token"]');
        if (metaToken && metaToken.content) {
            return metaToken.content;
        }
        const getCookie = (name) => {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        };
        const xsrfToken = getCookie('XSRF-TOKEN');
        if (xsrfToken) {
            return decodeURIComponent(xsrfToken);
        }
        return null;
    };

    const csrfToken = getCsrfToken();

    try {
        const response = await axios.post(
            route('profile.payments.create', studentId),
            { 
                amount: 30,
                group_id: groupId 
            },
            {
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-XSRF-TOKEN': csrfToken || '',
                },
            }
        );

        if (response.data.success && response.data.checkout_url) {
            // Redirect to Flitt payment page
            window.location.href = response.data.checkout_url;
        } else {
            alert(response.data.message || 'Failed to create payment');
            paymentLoading.value = false;
        }
    } catch (error) {
        console.error('Payment error:', error);
        const errorMessage = error.response?.data?.message || error.message || 'An error occurred while processing payment';
        alert(errorMessage);
        paymentLoading.value = false;
    }
};

const openMessage = (message) => {
    selectedMessage.value = message;
    showMessages.value = true;
    // Mark as read
    if (!message.is_read) {
        router.post(route('profile.messages.mark-read', message.id), {}, {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

const closeMessage = () => {
    showMessages.value = false;
    selectedMessage.value = null;
};

const unreadCount = computed(() => {
    return props.messages.filter(msg => !msg.is_read).length;
});

// Students with groups (for attendance section)
const studentsWithGroups = computed(() => {
    return props.students.filter(student => student.groups && student.groups.length > 0);
});

// Attendance students (from loaded data or props)
const attendanceStudents = computed(() => {
    if (attendanceData.value && attendanceData.value.students) {
        return attendanceData.value.students.filter(student => student.groups && student.groups.length > 0);
    }
    return studentsWithGroups.value;
});

// Available years (current year and previous 2 years)
const availableYears = computed(() => {
    const currentYear = new Date().getFullYear();
    return [currentYear, currentYear - 1, currentYear - 2];
});

// Check if selected month is current month
const isCurrentMonth = computed(() => {
    const now = new Date();
    return selectedMonth.value === now.getMonth() + 1 && selectedYear.value === now.getFullYear();
});

// Get month name
const getMonthName = (month) => {
    const months = [
        'Հունվար', 'Փետրվար', 'Մարտ', 'Ապրիլ', 'Մայիս', 'Հունիս',
        'Հուլիս', 'Օգոստոս', 'Սեպտեմբեր', 'Հոկտեմբեր', 'Նոյեմբեր', 'Դեկտեմբեր'
    ];
    return months[month - 1] || month;
};

// Load attendance data
const loadAttendanceData = async () => {
    loadingAttendance.value = true;
    try {
        const response = await axios.get(route('profile.attendance-data'), {
            params: {
                month: selectedMonth.value,
                year: selectedYear.value,
            },
        });
        attendanceData.value = response.data;
        
        // Set default selected tab if not set
        if (!selectedAttendanceTab.value && response.data.students.length > 0) {
            const firstStudent = response.data.students.find(s => s.groups && s.groups.length > 0);
            if (firstStudent) {
                selectedAttendanceTab.value = firstStudent.id;
            }
        }
    } catch (error) {
        console.error('Error loading attendance data:', error);
        alert('Failed to load attendance data.');
    } finally {
        loadingAttendance.value = false;
    }
};

// Navigate months
const previousMonth = () => {
    if (selectedMonth.value === 1) {
        selectedMonth.value = 12;
        selectedYear.value -= 1;
    } else {
        selectedMonth.value -= 1;
    }
    loadAttendanceData();
};

const nextMonth = () => {
    if (!isCurrentMonth.value) {
        if (selectedMonth.value === 12) {
            selectedMonth.value = 1;
            selectedYear.value += 1;
        } else {
            selectedMonth.value += 1;
        }
        loadAttendanceData();
    }
};

const goToCurrentMonth = () => {
    const now = new Date();
    selectedMonth.value = now.getMonth() + 1;
    selectedYear.value = now.getFullYear();
    loadAttendanceData();
};

// Initial load - use props data
attendanceData.value = {
    students: props.students,
    month: props.currentMonth,
    year: props.currentYear,
};

// Set default selected tab
watch(() => props.students, (newStudents) => {
    if (newStudents && newStudents.length > 0 && !selectedAttendanceTab.value) {
        const firstStudentWithGroups = studentsWithGroups.value[0];
        if (firstStudentWithGroups) {
            selectedAttendanceTab.value = firstStudentWithGroups.id;
        }
    }
}, { immediate: true });
</script>

<template>
    <ProfileLayout>
        <Head :title="t('profile_information')" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Page Title -->
            <div class="text-center mb-8">
                <h1 class="text-[32px] md:text-[45px] font-bold text-[#C7C7C7] mb-2">
                    {{ t('profile_information') }}
                </h1>
            </div>

            <!-- Messages Section -->
            <div v-if="messages.length > 0" class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-[#C7C7C7] flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Նամակներ
                        <span v-if="unreadCount > 0" class="ml-2 px-2 py-1 bg-red-600 text-white text-xs font-bold rounded-full">
                            {{ unreadCount }}
                        </span>
                    </h2>
                </div>
                
                <div class="space-y-3">
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        @click="openMessage(message)"
                        class="bg-[#5D5D5D] rounded-lg p-4 border border-[#4D4C4C] cursor-pointer hover:border-[#F15A2B] transition-all"
                        :class="{ 'border-[#F15A2B] bg-[#6D6D6D]': !message.is_read }"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-[#C7C7C7]" :class="{ 'text-[#F15A2B]': !message.is_read }">
                                        {{ message.subject }}
                                    </h3>
                                    <span v-if="!message.is_read" class="w-2 h-2 bg-[#F15A2B] rounded-full"></span>
                                </div>
                                <p class="text-sm text-gray-400 mb-2">{{ message.latest_message_snippet }}</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>From: {{ message.from_user_name }}</span>
                                    <span>{{ message.created_at_human }}</span>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Detail Modal -->
            <Transition name="fade">
                <div
                    v-if="showMessages && selectedMessage"
                    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                    @click.self="closeMessage"
                >
                    <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] rounded-2xl border-t-2 border-[#BF3206] shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-[#C7C7C7]">{{ selectedMessage.subject }}</h2>
                                <button
                                    @click="closeMessage"
                                    class="text-gray-400 hover:text-white text-2xl font-bold"
                                >
                                    &times;
                                </button>
                            </div>
                            
                            <div class="mb-4 pb-4 border-b border-[#4D4C4C]">
                                <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                                    <span>From: <strong class="text-[#C7C7C7]">{{ selectedMessage.from_user_name }}</strong></span>
                                    <span>•</span>
                                    <span>{{ selectedMessage.created_at_human }}</span>
                                </div>
                            </div>
                            
                            <div class="prose prose-invert max-w-none mb-6">
                                <p class="text-[#C7C7C7] whitespace-pre-wrap">{{ selectedMessage.message }}</p>
                            </div>

                            <div v-if="selectedMessage.has_students" class="mt-6 p-4 bg-[#5D5D5D] rounded-lg border border-[#4D4C4C]">
                                <h4 class="text-sm font-semibold text-[#C7C7C7] mb-2">Students included in this message:</h4>
                                <p class="text-xs text-gray-400">{{ selectedMessage.student_ids.length }} student(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Settings Link Card -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <Link
                    :href="route('profile.settings')"
                    class="flex items-center justify-between p-4 bg-[#5D5D5D] rounded-lg border border-[#4D4C4C] hover:border-[#F15A2B] transition-all group"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-[#C7C7C7] group-hover:text-[#F15A2B] transition-colors">
                                {{ t('settings') || 'Settings' }}
                            </h3>
                            <p class="text-sm text-gray-400">Manage your profile information, password, and account settings</p>
                        </div>
                    </div>
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-[#F15A2B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>

            <!-- Student Sub-Profiles Section -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-[#C7C7C7]">{{ t('student_name') }} {{ t('groups') }}</h2>
                    <button
                        @click="openStudentForm()"
                        class="bg-[#F15A2B] hover:bg-[#BF3206] text-white font-bold py-2 px-4 rounded-lg transition-colors"
                    >
                        {{ t('add') || 'Add' }} {{ t('student_name') }}
                    </button>
                </div>

                <div v-if="students.length === 0" class="text-[#C7C7C7] text-center py-8">
                    No students added yet.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="student in students"
                        :key="student.id"
                        class="bg-[#5D5D5D] border border-[#4D4C4C] rounded-lg p-4 hover:shadow-lg hover:border-[#F15A2B] transition-all"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-semibold text-[#C7C7C7] text-lg">{{ student.name }}</h4>
                            <div class="flex gap-2" @click.stop>
                                <button
                                    @click="openStudentForm(student)"
                                    class="text-[#F15A2B] hover:text-[#BF3206] text-sm font-medium"
                                >
                                    {{ t('edit') || 'Edit' }}
                                </button>
                                <button
                                    @click="deleteStudent(student.id)"
                                    class="text-red-400 hover:text-red-600 text-sm font-medium"
                                >
                                    {{ t('delete') || 'Delete' }}
                                </button>
                            </div>
                        </div>
                        <div class="text-sm text-[#C7C7C7] space-y-1">
                            <p v-if="student.biradi"><span class="font-medium text-[#F15A2B]">Biradi:</span> {{ student.biradi }}</p>
                            <p><span class="font-medium text-[#F15A2B]">{{ t('sections') }}:</span> {{ student.course_name }}</p>
                            <p><span class="font-medium text-[#F15A2B]">{{ t('find_us') || 'Branch' }}:</span> {{ student.branch_name }}</p>
                            <p v-if="student.birthday">
                                <span class="font-medium text-[#F15A2B]">{{ t('student_birthday') }}:</span> {{ new Date(student.birthday).toLocaleDateString() }}
                            </p>
                            <!-- Payment Status -->
                            <div class="mt-3 pt-3 border-t border-[#4D4C4C]">
                                <p v-if="student.has_paid_this_month" class="text-green-400 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ t('already_paid') || 'Already Paid' }}
                                </p>
                                <p v-else class="text-yellow-400 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ t('payment_required') || 'Payment Required' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Section -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-[#C7C7C7] flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ներկա/Բացակա
                    </h2>
                    
                    <!-- Month/Year Selector -->
                    <div class="flex items-center gap-3">
                        <button
                            @click="previousMonth"
                            class="p-2 rounded-lg bg-[#5D5D5D] hover:bg-[#6D6D6D] text-[#C7C7C7] transition-colors"
                            title="Previous Month"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div class="flex items-center gap-2">
                            <select
                                v-model="selectedMonth"
                                @change="loadAttendanceData"
                                class="bg-[#5D5D5D] border border-[#4D4C4C] text-[#C7C7C7] rounded-lg px-3 py-2 text-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                            >
                                <option v-for="month in 12" :key="month" :value="month">
                                    {{ getMonthName(month) }}
                                </option>
                            </select>
                            <select
                                v-model="selectedYear"
                                @change="loadAttendanceData"
                                class="bg-[#5D5D5D] border border-[#4D4C4C] text-[#C7C7C7] rounded-lg px-3 py-2 text-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                            >
                                <option v-for="year in availableYears" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                        <button
                            @click="nextMonth"
                            :disabled="isCurrentMonth"
                            class="p-2 rounded-lg bg-[#5D5D5D] hover:bg-[#6D6D6D] text-[#C7C7C7] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Next Month"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button
                            v-if="!isCurrentMonth"
                            @click="goToCurrentMonth"
                            class="px-3 py-2 text-sm bg-[#F15A2B] hover:bg-[#BF3206] text-white rounded-lg transition-colors"
                        >
                            Այս Ամիս
                        </button>
                    </div>
                </div>

                <div v-if="studentsWithGroups.length === 0" class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p>No groups assigned to students yet.</p>
                </div>

                <div v-if="loadingAttendance" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#F15A2B]"></div>
                    <p class="mt-4 text-gray-400">Loading attendance data...</p>
                </div>

                <div v-else-if="attendanceStudents.length === 0" class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p>No groups assigned to students yet.</p>
                </div>

                <div v-else>
                    <!-- Tabs -->
                    <div class="border-b border-[#4D4C4C] mb-6">
                        <div class="flex space-x-1 overflow-x-auto">
                            <button
                                v-for="student in attendanceStudents"
                                :key="student.id"
                                @click="selectedAttendanceTab = student.id"
                                :class="[
                                    'px-4 py-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap',
                                    selectedAttendanceTab === student.id
                                        ? 'border-[#F15A2B] text-[#F15A2B]'
                                        : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-500'
                                ]"
                            >
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ student.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <span>{{ student.name }}</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div v-for="student in attendanceStudents" :key="student.id">
                        <div v-if="selectedAttendanceTab === student.id" class="space-y-4">
                            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[#4D4C4C]">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ student.name.charAt(0).toUpperCase() }}</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-[#C7C7C7] text-lg">{{ student.name }}</h3>
                                    <p class="text-sm text-gray-400">{{ student.course_name }} - {{ student.branch_name }}</p>
                                </div>
                            </div>

                            <div v-if="student.groups && student.groups.length > 0" class="space-y-4">
                                <div
                                    v-for="group in student.groups"
                                    :key="group.id"
                                    class="bg-[#5D5D5D] rounded-lg p-5 border border-[#4D4C4C]"
                                >
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <h4 class="font-semibold text-[#C7C7C7] text-lg">{{ group.name }}</h4>
                                            <span class="text-xs text-gray-400 bg-[#4D4C4C] px-3 py-1 rounded-full">{{ group.course_name }}</span>
                                            <span 
                                                :class="[
                                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                                    group.has_paid_this_month ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30'
                                                ]"
                                            >
                                                {{ group.has_paid_this_month ? 'Վճարված' : 'Չի վճարված' }}
                                            </span>
                                        </div>
                                        <button
                                            v-if="!group.has_paid_this_month"
                                            @click="makePayment(student.id, group.id)"
                                            :disabled="paymentLoading"
                                            class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                        >
                                            <svg v-if="!paymentLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>{{ paymentLoading ? 'Վճարում...' : 'Վճարել' }}</span>
                                        </button>
                                    </div>

                                    <!-- Attendance Statistics -->
                                    <div class="grid grid-cols-3 gap-3 mb-4">
                                        <div class="text-center bg-green-500/10 rounded-lg p-4 border border-green-500/20">
                                            <div class="text-3xl font-bold text-green-400">{{ group.present_count }}</div>
                                            <div class="text-sm text-gray-400 mt-2">Ներկա</div>
                                        </div>
                                        <div class="text-center bg-red-500/10 rounded-lg p-4 border border-red-500/20">
                                            <div class="text-3xl font-bold text-red-400">{{ group.absent_count }}</div>
                                            <div class="text-sm text-gray-400 mt-2">Բացակա</div>
                                        </div>
                                        <div class="text-center bg-yellow-500/10 rounded-lg p-4 border border-yellow-500/20">
                                            <div class="text-3xl font-bold text-yellow-400">{{ group.excused_count }}</div>
                                            <div class="text-sm text-gray-400 mt-2">Արտոնված</div>
                                        </div>
                                    </div>

                                    <!-- Attendance Percentage -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between text-sm mb-2">
                                            <span class="text-gray-400 font-medium">Հաճախելիություն</span>
                                            <span class="font-bold text-xl" :class="group.attendance_percentage >= 80 ? 'text-green-400' : group.attendance_percentage >= 60 ? 'text-yellow-400' : 'text-red-400'">
                                                {{ group.attendance_percentage }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-[#3a3a3a] rounded-full h-3">
                                            <div
                                                class="h-3 rounded-full transition-all"
                                                :class="group.attendance_percentage >= 80 ? 'bg-green-400' : group.attendance_percentage >= 60 ? 'bg-yellow-400' : 'bg-red-400'"
                                                :style="{ width: group.attendance_percentage + '%' }"
                                            ></div>
                                        </div>
                                    </div>

                                <!-- Monthly Attendance -->
                                <div v-if="group.recent_attendance && group.recent_attendance.length > 0" class="pt-4 border-t border-[#4D4C4C]">
                                    <div class="text-sm font-semibold text-gray-400 mb-3">Ամսվա ներկայություն</div>
                                    <div class="flex flex-wrap gap-2">
                                        <div
                                            v-for="attendance in group.recent_attendance"
                                            :key="attendance.date"
                                            class="px-3 py-2 rounded-lg text-sm font-medium border"
                                            :class="{
                                                'bg-green-500/20 text-green-400 border-green-500/30': attendance.status === 'present',
                                                'bg-red-500/20 text-red-400 border-red-500/30': attendance.status === 'absent',
                                                'bg-yellow-500/20 text-yellow-400 border-yellow-500/30': attendance.status === 'excused'
                                            }"
                                            :title="attendance.date"
                                        >
                                            <div class="font-semibold">{{ attendance.date_human }}</div>
                                            <div class="text-xs mt-0.5">
                                                {{ attendance.status === 'present' ? 'Ներկա' : attendance.status === 'absent' ? 'Բացակա' : 'Արտոնված' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="pt-4 border-t border-[#4D4C4C] text-center text-gray-400 text-sm">
                                    No attendance records for this month.
                                </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-400">
                                <p>No groups assigned to this student.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Student Form Modal -->
            <Transition name="fade">
                <div
                    v-if="showStudentForm"
                    class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                    @click="closeStudentForm"
                >
                    <div
                        class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] rounded-2xl border-t-2 border-[#BF3206] shadow-2xl max-w-md w-full p-6"
                        @click.stop
                    >
                        <h3 class="text-xl font-semibold text-[#C7C7C7] mb-6">
                            {{ editingStudent ? (t('edit') || 'Edit') + ' ' + (t('student_name') || 'Student') : (t('add') || 'Add') + ' ' + (t('student_name') || 'Student') }}
                        </h3>
                        <form @submit.prevent="submitStudent" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-[#C7C7C7] mb-2">
                                    {{ t('student_name') }} *
                                </label>
                                <input
                                    v-model="studentForm.name"
                                    type="text"
                                    required
                                    class="w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                                    placeholder="Enter student name"
                                />
                                <div v-if="studentForm.errors.name" class="mt-1 text-sm text-red-400">
                                    {{ studentForm.errors.name }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#C7C7C7] mb-2">
                                    Biradi (ID Number)
                                </label>
                                <input
                                    v-model="studentForm.biradi"
                                    type="text"
                                    class="w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                                    placeholder="Enter biradi (passport number)"
                                />
                                <div v-if="studentForm.errors.biradi" class="mt-1 text-sm text-red-400">
                                    {{ studentForm.errors.biradi }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#C7C7C7] mb-2">
                                    {{ t('sections') }} *
                                </label>
                                <select
                                    v-model="studentForm.course_id"
                                    @change="selectedCourseId = studentForm.course_id; studentForm.branch_id = ''"
                                    required
                                    class="w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                                >
                                    <option value="">{{ t('select_a_branch') || 'Select a course' }}</option>
                                    <option
                                        v-for="course in courses"
                                        :key="course.id"
                                        :value="course.id"
                                    >
                                        {{ course.name }}
                                    </option>
                                </select>
                                <div v-if="studentForm.errors.course_id" class="mt-1 text-sm text-red-400">
                                    {{ studentForm.errors.course_id }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#C7C7C7] mb-2">
                                    {{ t('find_us') || 'Branch' }} *
                                </label>
                                <select
                                    v-model="studentForm.branch_id"
                                    :disabled="!selectedCourseId || availableBranches.length === 0"
                                    required
                                    class="w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] disabled:bg-[#3a3a3a] disabled:text-gray-500 px-3 py-2"
                                >
                                    <option value="">{{ t('select_a_branch') }}</option>
                                    <option
                                        v-for="branch in availableBranches"
                                        :key="branch.id"
                                        :value="branch.id"
                                    >
                                        {{ branch.name }}
                                    </option>
                                </select>
                                <div v-if="studentForm.errors.branch_id" class="mt-1 text-sm text-red-400">
                                    {{ studentForm.errors.branch_id }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#C7C7C7] mb-2">
                                    {{ t('student_birthday') }}
                                </label>
                                <input
                                    v-model="studentForm.birthday"
                                    type="date"
                                    class="w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                                />
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    @click="closeStudentForm"
                                    class="px-4 py-2 border border-[#4D4C4C] rounded-lg text-[#C7C7C7] hover:bg-[#5D5D5D] transition-colors"
                                >
                                    {{ t('cancel') || 'Cancel' }}
                                </button>
                                <button
                                    type="submit"
                                    :disabled="studentForm.processing"
                                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50 transition-colors"
                                >
                                    {{ studentForm.processing ? (t('sending') || 'Saving...') : (editingStudent ? (t('update') || 'Update') : (t('create') || 'Create')) }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </div>
    </ProfileLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

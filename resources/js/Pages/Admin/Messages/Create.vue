<template>
    <AuthenticatedLayout>
        <Head title="Send Message to Teacher" />

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8">
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900">Send Message to Teacher</h2>
                            <p class="text-sm text-gray-600 mt-2">Compose and send a message to a teacher. You can optionally include students for group creation.</p>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                {{ $page.props.flash.success }}
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Teacher Selection -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Select Teacher *
                                    </span>
                                </label>
                                <select
                                    v-model="form.to_user_id"
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] text-base"
                                >
                                    <option value="">Choose a teacher...</option>
                                    <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                        {{ teacher.name }} ({{ teacher.email }})
                                    </option>
                                </select>
                                <div v-if="form.errors.to_user_id" class="mt-2 text-sm text-red-600">{{ form.errors.to_user_id }}</div>
                            </div>

                            <!-- Subject -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        Subject *
                                    </span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.subject"
                                    placeholder="Enter message subject..."
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] text-base"
                                />
                                <div v-if="form.errors.subject" class="mt-2 text-sm text-red-600">{{ form.errors.subject }}</div>
                            </div>

                            <!-- Message -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <label class="block text-sm font-semibold text-gray-900 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Message *
                                    </span>
                                </label>
                                <textarea
                                    v-model="form.message"
                                    rows="8"
                                    placeholder="Write your message here..."
                                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] text-base"
                                ></textarea>
                                <div v-if="form.errors.message" class="mt-2 text-sm text-red-600">{{ form.errors.message }}</div>
                            </div>

                            <!-- Students Selection -->
                            <div class="bg-gray-50 rounded-lg p-6 border-2 border-dashed border-gray-300">
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            Select Students (Optional)
                                        </span>
                                    </h3>
                                    <p class="text-sm text-gray-600">Select students to include in the message. The teacher can create a group with these students.</p>
                                </div>

                                <!-- Filters -->
                                <div class="mb-6 bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-semibold text-gray-900">Filters</h4>
                                        <button
                                            type="button"
                                            @click="clearFilters"
                                            class="text-xs text-gray-600 hover:text-gray-900 underline"
                                        >
                                            Clear All
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Search -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                                            <input
                                                v-model="studentFilters.search"
                                                type="text"
                                                placeholder="Student name, biradi, parent..."
                                                class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                            />
                                        </div>
                                        <!-- Course Filter -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Course</label>
                                            <select
                                                v-model="studentFilters.course_id"
                                                class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                            >
                                                <option value="">All Courses</option>
                                                <option v-for="course in courses" :key="course.id" :value="course.id">
                                                    {{ course.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <!-- Branch Filter -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                                            <select
                                                v-model="studentFilters.branch_id"
                                                class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                            >
                                                <option value="">All Branches</option>
                                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                                    {{ branch.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Users List -->
                                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                                    <template v-if="filteredUsers.length === 0">
                                        <div class="text-center py-8 text-gray-500">
                                            <p>No students match the selected filters.</p>
                                            <button
                                                type="button"
                                                @click="clearFilters"
                                                class="mt-2 text-sm text-[#F15A2B] hover:text-[#BF3206] underline"
                                            >
                                                Clear filters
                                            </button>
                                        </div>
                                    </template>
                                    <div
                                        v-for="user in filteredUsers"
                                        :key="user.id"
                                        class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
                                    >
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ user.name }}</h4>
                                                <p class="text-sm text-gray-600">{{ user.email }}</p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="toggleUserSelection(user.id)"
                                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                                                :class="isUserSelected(user.id) ? 'bg-[#F15A2B] text-white hover:bg-[#BF3206]' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                                            >
                                                {{ isUserSelected(user.id) ? 'Deselect All' : 'Select All' }}
                                            </button>
                                        </div>
                                        
                                        <!-- Students List -->
                                        <div class="space-y-2">
                                            <label
                                                v-for="student in user.filteredStudents"
                                                :key="student.id"
                                                class="flex items-center p-3 rounded-lg border cursor-pointer transition-all hover:bg-gray-50"
                                                :class="isStudentSelected(student.id) ? 'border-[#F15A2B] bg-orange-50' : 'border-gray-200'"
                                            >
                                                <input
                                                    type="checkbox"
                                                    :value="student.id"
                                                    v-model="form.student_ids"
                                                    class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B] w-5 h-5"
                                                />
                                                <div class="ml-3 flex-1">
                                                    <div class="text-sm font-medium text-gray-900">{{ student.name }}</div>
                                                    <div class="text-xs text-gray-600 mt-1">
                                                        <span v-if="student.biradi" class="mr-2">Biradi: {{ student.biradi }}</span>
                                                        {{ student.course_name }} - {{ student.branch_name }}
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Students Summary -->
                                <div v-if="selectedStudentsCount > 0 || filteredStudentsCount > 0" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-sm font-semibold text-blue-900">
                                                {{ selectedStudentsCount }} {{ selectedStudentsCount === 1 ? 'student' : 'students' }} selected
                                                <span v-if="filteredStudentsCount > 0" class="text-gray-600 ml-2">
                                                    ({{ filteredStudentsCount }} {{ filteredStudentsCount === 1 ? 'student' : 'students' }} match filters)
                                                </span>
                                            </p>
                                        </div>
                                        <button
                                            v-if="filteredStudentsCount > 0"
                                            type="button"
                                            @click="selectAllFiltered"
                                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                        >
                                            Select All Filtered
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-3 bg-[#F15A2B] text-white font-semibold rounded-lg hover:bg-[#BF3206] disabled:opacity-50 transition-colors shadow-md hover:shadow-lg flex items-center"
                                >
                                    <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span v-else class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></span>
                                    {{ form.processing ? 'Sending...' : 'Send Message' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    teachers: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    branches: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    to_user_id: '',
    subject: '',
    message: '',
    student_ids: [],
});

const studentFilters = ref({
    search: '',
    course_id: '',
    branch_id: '',
});

const selectedStudentsCount = computed(() => form.student_ids.length);

// Count filtered students
const filteredStudentsCount = computed(() => {
    return filteredUsers.value.reduce((count, user) => count + user.filteredStudents.length, 0);
});

// Filter users and students based on filters
const filteredUsers = computed(() => {
    return props.users.map(user => {
        let filteredStudents = [...user.students]; // Create a copy to avoid mutating original

        // Apply search filter
        if (studentFilters.value.search && studentFilters.value.search.trim()) {
            const search = studentFilters.value.search.toLowerCase().trim();
            filteredStudents = filteredStudents.filter(student => {
                return (
                    student.name.toLowerCase().includes(search) ||
                    (student.biradi && student.biradi.toLowerCase().includes(search)) ||
                    user.name.toLowerCase().includes(search) ||
                    user.email.toLowerCase().includes(search)
                );
            });
        }

        // Apply course filter
        if (studentFilters.value.course_id) {
            const courseId = Number(studentFilters.value.course_id);
            filteredStudents = filteredStudents.filter(student => {
                const studentCourseId = Number(student.course_id);
                return studentCourseId === courseId;
            });
        }

        // Apply branch filter
        if (studentFilters.value.branch_id) {
            const branchId = Number(studentFilters.value.branch_id);
            filteredStudents = filteredStudents.filter(student => {
                const studentBranchId = Number(student.branch_id);
                return studentBranchId === branchId;
            });
        }

        return {
            ...user,
            filteredStudents: filteredStudents,
        };
    }).filter(user => user.filteredStudents.length > 0);
});

const clearFilters = () => {
    studentFilters.value = {
        search: '',
        course_id: '',
        branch_id: '',
    };
};

const selectAllFiltered = () => {
    filteredUsers.value.forEach(user => {
        user.filteredStudents.forEach(student => {
            if (!isStudentSelected(student.id)) {
                form.student_ids.push(student.id);
            }
        });
    });
};

const isStudentSelected = (studentId) => {
    return form.student_ids.includes(studentId);
};

const isUserSelected = (userId) => {
    const user = filteredUsers.value.find(u => u.id === userId);
    if (!user) return false;
    return user.filteredStudents.every(student => isStudentSelected(student.id));
};

const toggleUserSelection = (userId) => {
    const user = filteredUsers.value.find(u => u.id === userId);
    if (!user) return;

    const allSelected = isUserSelected(userId);
    
    if (allSelected) {
        // Deselect all filtered students from this user
        user.filteredStudents.forEach(student => {
            const index = form.student_ids.indexOf(student.id);
            if (index > -1) {
                form.student_ids.splice(index, 1);
            }
        });
    } else {
        // Select all filtered students from this user
        user.filteredStudents.forEach(student => {
            if (!isStudentSelected(student.id)) {
                form.student_ids.push(student.id);
            }
        });
    }
};

const submit = () => {
    form.post(route('admin.messages.store'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Edit Group" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Group</h2>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_en"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_en" class="mt-1 text-sm text-red-600">{{ form.errors.name_en }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Armenian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_hy"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_hy" class="mt-1 text-sm text-red-600">{{ form.errors.name_hy }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Georgian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_ge"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_ge" class="mt-1 text-sm text-red-600">{{ form.errors.name_ge }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Russian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_ru"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_ru" class="mt-1 text-sm text-red-600">{{ form.errors.name_ru }}</div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select
                                    v-model="form.course_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                >
                                    <option value="">Select a course</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.course_id" class="mt-1 text-sm text-red-600">{{ form.errors.course_id }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (English)</label>
                                    <textarea
                                        v-model="form.description_en"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Armenian)</label>
                                    <textarea
                                        v-model="form.description_hy"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Georgian)</label>
                                    <textarea
                                        v-model="form.description_ge"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Russian)</label>
                                    <textarea
                                        v-model="form.description_ru"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                                <input
                                    type="number"
                                    v-model="form.order"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                            </div>

                            <div class="mb-6">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>

                            <!-- Students Selection -->
                            <div class="mb-6 border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Students in Group</h3>
                                
                                <!-- Search for Parent User -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Search by Parent Name, Email, Student Name, or Biradi</label>
                                    <input
                                        type="text"
                                        v-model="studentSearch"
                                        @input="searchStudents"
                                        placeholder="Type parent name, email, student name, or biradi..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>

                                <!-- Search Results -->
                                <div v-if="searchResults.length > 0" class="mb-4 border rounded-lg p-4 bg-gray-50 max-h-64 overflow-y-auto">
                                    <div
                                        v-for="student in searchResults"
                                        :key="student.id"
                                        class="flex items-center justify-between p-3 mb-2 bg-white rounded border hover:bg-gray-50"
                                    >
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">{{ student.name }}</div>
                                            <div class="text-sm text-gray-600">
                                                <span v-if="student.biradi">Biradi: {{ student.biradi }} | </span>
                                                Parent: {{ student.user_name }} ({{ student.user_email }}) | 
                                                {{ student.course_name }} - {{ student.branch_name }}
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addStudent(student)"
                                            :disabled="isStudentSelected(student.id)"
                                            class="ml-4 px-3 py-1 bg-[#F15A2B] text-white rounded hover:bg-[#BF3206] disabled:bg-gray-400 disabled:cursor-not-allowed text-sm"
                                        >
                                            {{ isStudentSelected(student.id) ? 'Added' : 'Add' }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Selected Students -->
                                <div v-if="selectedStudents.length > 0" class="mt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Selected Students ({{ selectedStudents.length }})</h4>
                                    <div class="space-y-2">
                                        <div
                                            v-for="student in selectedStudents"
                                            :key="student.id"
                                            class="flex items-center justify-between p-3 bg-blue-50 rounded border border-blue-200"
                                        >
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">{{ student.name }}</div>
                                                <div class="text-sm text-gray-600">
                                                    <span v-if="student.biradi">Biradi: {{ student.biradi }} | </span>
                                                    Parent: {{ student.user_name }}
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                @click="removeStudent(student.id)"
                                                class="ml-4 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('teacher.groups.index')"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50"
                                >
                                    Update
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    group: Object,
    courses: Array,
});

const form = useForm({
    name_en: props.group.name_en,
    name_hy: props.group.name_hy,
    name_ge: props.group.name_ge,
    name_ru: props.group.name_ru,
    course_id: props.group.course_id,
    description_en: props.group.description_en || '',
    description_hy: props.group.description_hy || '',
    description_ge: props.group.description_ge || '',
    description_ru: props.group.description_ru || '',
    order: props.group.order,
    is_active: props.group.is_active,
    students: props.group.students.map(s => s.id),
});

const studentSearch = ref('');
const searchResults = ref([]);
const selectedStudents = ref([...props.group.students]);

let searchTimeout = null;

onMounted(() => {
    // Initialize selected students with existing group students
    selectedStudents.value = props.group.students.map(s => ({
        id: s.id,
        name: s.name,
        biradi: s.biradi,
        user_id: s.user_id,
        user_name: s.user_name,
        user_email: s.user_email,
    }));
});

const searchStudents = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        if (studentSearch.value.length < 2) {
            searchResults.value = [];
            return;
        }

        try {
            const response = await axios.get(route('teacher.groups.search-students'), {
                params: { search: studentSearch.value }
            });
            searchResults.value = response.data.students;
        } catch (error) {
            console.error('Error searching students:', error);
            searchResults.value = [];
        }
    }, 300);
};

const addStudent = (student) => {
    if (!isStudentSelected(student.id)) {
        selectedStudents.value.push(student);
        form.students = selectedStudents.value.map(s => s.id);
    }
};

const removeStudent = (studentId) => {
    selectedStudents.value = selectedStudents.value.filter(s => s.id !== studentId);
    form.students = selectedStudents.value.map(s => s.id);
};

const isStudentSelected = (studentId) => {
    return selectedStudents.value.some(s => s.id === studentId);
};

const submit = () => {
    form.put(route('teacher.groups.update', props.group.id));
};
</script>


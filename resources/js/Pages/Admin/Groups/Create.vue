<template>
    <AuthenticatedLayout>
        <Head title="Create Group" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Group</h2>

                        <form @submit.prevent="submit">
                            <div class="mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Անուն *</label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
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

                            <div class="mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Նկարագրություն</label>
                                    <textarea
                                        v-model="form.description"
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Students to Group</h3>
                                
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
                                    :href="route('admin.groups.index')"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50"
                                >
                                    Create
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
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    courses: Array,
});

const form = useForm({
    name: '',
    course_id: '',
    description: '',
    order: 0,
    is_active: true,
    students: [],
});

const studentSearch = ref('');
const searchResults = ref([]);
const selectedStudents = ref([]);

let searchTimeout = null;

const searchStudents = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        if (studentSearch.value.length < 2) {
            searchResults.value = [];
            return;
        }

        try {
            const response = await axios.get(route('admin.groups.search-students'), {
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
    form.post(route('admin.groups.store'));
};
</script>


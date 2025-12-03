<template>
    <AuthenticatedLayout>
        <Head title="Attendance Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Attendance Management</h2>
                                <p class="text-sm text-gray-600 mt-1">Select a group to manage attendance</p>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold">{{ groups.length }}</span> {{ groups.length === 1 ? 'group' : 'groups' }} found
                            </div>
                        </div>

                        <!-- Filters Section -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                                <button
                                    @click="clearFilters"
                                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                                >
                                    Clear All
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Search -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        placeholder="Group name..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Course Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select
                                        v-model="filters.course_id"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Courses</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Branch Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                                    <select
                                        v-model="filters.branch_id"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Branches</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div v-if="groups.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p>No groups found. Create a group first.</p>
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <Link
                                v-for="group in groups"
                                :key="group.id"
                                :href="route('admin.attendance.show', group.id)"
                                class="group relative bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-[#F15A2B] hover:shadow-lg transition-all"
                            >
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-[#F15A2B] transition-colors">
                                            {{ group.name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ group.course_name }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                        {{ group.students_count }} {{ group.students_count === 1 ? 'Student' : 'Students' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-[#F15A2B] text-sm font-medium">
                                    <span>Manage Attendance</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    groups: {
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
    filters: {
        type: Object,
        default: () => ({
            search: '',
            course_id: '',
            branch_id: '',
        }),
    },
});

const filters = ref({ ...props.filters });

// Debounce search input
let searchTimeout = null;
watch(() => filters.value.search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const applyFilters = () => {
    router.get(route('admin.attendance.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = {
        search: '',
        course_id: '',
        branch_id: '',
    };
    applyFilters();
};
</script>


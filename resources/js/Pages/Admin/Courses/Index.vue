<template>
    <AuthenticatedLayout>
        <Head title="Courses Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Courses Management</h2>
                            <Link
                                :href="route('admin.courses.create')"
                                class="bg-[#F15A2B] hover:bg-[#BF3206] text-white font-bold py-2 px-4 rounded-lg transition-colors"
                            >
                                Add New Course
                            </Link>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branches</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="course in courses" :key="course.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ course.name_en }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ course.slug }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ course.price }} GEL</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    v-for="branch in course.branches"
                                                    :key="branch.id"
                                                    class="px-2 py-1 bg-gray-100 rounded text-xs"
                                                >
                                                    {{ branch.name_en }}
                                                </span>
                                                <span v-if="course.branches.length === 0" class="text-gray-400">No branches</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                course.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ course.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                :href="route('admin.courses.edit', course.id)"
                                                class="text-[#F15A2B] hover:text-[#BF3206] mr-4"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteCourse(course.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="courses.length === 0" class="text-center py-12 text-gray-500">
                                No courses found. Create your first course!
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    courses: Array,
});

const deleteCourse = (id) => {
    if (confirm('Are you sure you want to delete this course?')) {
        router.delete(route('admin.courses.destroy', id));
    }
};
</script>


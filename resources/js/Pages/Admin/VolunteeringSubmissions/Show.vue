<template>
    <AuthenticatedLayout>
        <Head title="Volunteering Submission Details" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Submission Details</h2>
                            <Link :href="route('admin.volunteering-submissions.index')" class="text-gray-600 hover:text-gray-900">
                                ← Back to List
                            </Link>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ submission.subject }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ submission.email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <p class="mt-1 text-sm text-gray-900">{{ submission.phone }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ submission.message }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Submitted</label>
                                <p class="mt-1 text-sm text-gray-900">{{ new Date(submission.created_at).toLocaleString() }}</p>
                            </div>

                            <div class="flex items-center space-x-4 pt-4 border-t">
                                <span :class="submission.is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-3 py-1 text-sm font-semibold rounded-full">
                                    {{ submission.is_read ? 'Read' : 'New' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    submission: Object,
});
</script>


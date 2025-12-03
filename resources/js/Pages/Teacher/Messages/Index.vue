<template>
    <AuthenticatedLayout>
        <Head title="Messages" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900">Messages</h2>
                    <p class="text-sm text-gray-600 mt-2">View and manage your conversations</p>
                </div>

                <div v-if="messages.length === 0" class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 text-lg">No messages yet.</p>
                </div>

                <div v-else class="space-y-4">
                    <Link
                        v-for="message in messages"
                        :key="message.id"
                        :href="route('teacher.messages.show', message.id)"
                        class="block bg-white rounded-lg shadow-sm border-2 transition-all hover:shadow-md"
                        :class="message.has_unread ? 'border-[#F15A2B] bg-orange-50' : 'border-gray-200 hover:border-gray-300'"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ message.subject }}</h3>
                                        <span
                                            v-if="message.has_unread"
                                            class="flex-shrink-0 px-2 py-1 bg-[#F15A2B] text-white text-xs font-semibold rounded-full"
                                        >
                                            New
                                        </span>
                                        <span
                                            v-if="message.replies_count > 0"
                                            class="flex-shrink-0 px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full"
                                        >
                                            {{ message.replies_count }} {{ message.replies_count === 1 ? 'reply' : 'replies' }}
                                        </span>
                                        <span
                                            v-if="message.has_students"
                                            class="flex-shrink-0 px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full"
                                        >
                                            {{ message.student_ids.length }} {{ message.student_ids.length === 1 ? 'Student' : 'Students' }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ message.message }}</p>
                                    
                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ message.is_from_me ? 'To' : 'From' }}: {{ message.is_from_me ? message.to_user_name : message.from_user_name }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ message.created_at_human }}
                                        </span>
                                    </div>
                                </div>
                                
                                <svg class="w-6 h-6 text-gray-400 flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    messages: {
        type: Array,
        default: () => [],
    },
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

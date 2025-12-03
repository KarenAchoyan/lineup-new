<template>
    <AuthenticatedLayout>
        <Head :title="rootMessage.subject" />

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link
                        :href="route('admin.messages.index')"
                        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Messages
                    </Link>
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">{{ rootMessage.subject }}</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                Conversation with <span class="font-semibold">{{ otherUser.name }}</span> ({{ otherUser.email }})
                            </p>
                        </div>
                        <Link
                            :href="route('admin.messages.create')"
                            class="px-4 py-2 bg-[#F15A2B] text-white font-semibold rounded-lg hover:bg-[#BF3206] transition-colors flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Message
                        </Link>
                    </div>
                </div>

                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Chat Messages Container -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-[#F15A2B] rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ otherUser.name.charAt(0).toUpperCase() }}
                                </div>
                                <div class="ml-3">
                                    <div class="font-semibold text-gray-900">{{ otherUser.name }}</div>
                                    <div class="text-xs text-gray-600">{{ otherUser.email }}</div>
                                </div>
                            </div>
                            <div v-if="rootMessage.has_students" class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                {{ rootMessage.student_ids.length }} {{ rootMessage.student_ids.length === 1 ? 'Student' : 'Students' }}
                            </div>
                        </div>
                    </div>

                    <!-- Messages Thread -->
                    <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                        <div
                            v-for="(msg, index) in messages"
                            :key="msg.id"
                            class="flex"
                            :class="msg.from_user_id === currentUserId ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                class="max-w-[75%] rounded-2xl px-4 py-3 shadow-sm"
                                :class="msg.from_user_id === currentUserId 
                                    ? 'bg-[#F15A2B] text-white' 
                                    : 'bg-gray-100 text-gray-900'"
                            >
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold opacity-90">
                                        {{ msg.from_user_id === currentUserId ? 'You' : msg.from_user_name }}
                                    </span>
                                    <span class="text-xs opacity-75">
                                        {{ msg.created_at_human }}
                                    </span>
                                </div>
                                <div class="text-sm whitespace-pre-wrap break-words">{{ msg.message }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                        <form @submit.prevent="sendReply" class="flex gap-3">
                            <textarea
                                v-model="replyForm.message"
                                rows="2"
                                placeholder="Type your reply..."
                                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] resize-none"
                                @keydown.enter.exact.prevent="sendReply"
                                @keydown.shift.enter.exact="true"
                            ></textarea>
                            <button
                                type="submit"
                                :disabled="replyForm.processing || !replyForm.message.trim()"
                                class="px-6 py-3 bg-[#F15A2B] text-white font-semibold rounded-lg hover:bg-[#BF3206] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                            >
                                <svg v-if="!replyForm.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span v-else class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white"></span>
                            </button>
                        </form>
                        <div v-if="replyForm.errors.message" class="mt-2 text-sm text-red-600">{{ replyForm.errors.message }}</div>
                    </div>
                </div>

                <!-- Students Section (only for root message) -->
                <div v-if="rootMessage.has_students" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Students ({{ students.length }})
                        </span>
                    </h3>

                    <!-- Students List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="student in students"
                            :key="student.id"
                            class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition-colors"
                        >
                            <div class="font-semibold text-gray-900">{{ student.name }}</div>
                            <div class="text-sm text-gray-600 mt-2 space-y-1">
                                <div v-if="student.biradi" class="flex items-center">
                                    <span class="font-medium mr-2">Biradi:</span>
                                    <span>{{ student.biradi }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">Parent:</span>
                                    <span>{{ student.user_name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">Course:</span>
                                    <span>{{ student.course_name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">Branch:</span>
                                    <span>{{ student.branch_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    rootMessage: Object,
    otherUser: Object,
    messages: {
        type: Array,
        default: () => [],
    },
    students: {
        type: Array,
        default: () => [],
    },
    currentUserId: Number,
});

const replyForm = useForm({
    message: '',
});

const messagesContainer = ref(null);

const sendReply = () => {
    if (!replyForm.message.trim()) return;
    
    replyForm.post(route('admin.messages.reply', props.rootMessage.id), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset();
            // Scroll to bottom after reply
            nextTick(() => {
                const container = document.querySelector('.overflow-y-auto');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
    });
};
</script>


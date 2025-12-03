<template>
    <AuthenticatedLayout>
        <Head :title="rootMessage.subject" />

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <Link
                        :href="route('teacher.messages.index')"
                        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Messages
                    </Link>
                    <h2 class="text-3xl font-bold text-gray-900">{{ rootMessage.subject }}</h2>
                </div>

                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Conversation Thread -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6 space-y-6">
                        <div
                            v-for="(msg, index) in messages"
                            :key="msg.id"
                            class="border-l-4 pl-6"
                            :class="msg.from_user_id === currentUserId ? 'border-blue-500' : 'border-[#F15A2B]'"
                        >
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-semibold text-gray-900">
                                            {{ msg.from_user_id === currentUserId ? 'You' : msg.from_user_name }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ msg.from_user_email }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mb-3">{{ msg.created_at_human }}</div>
                                </div>
                                <span
                                    v-if="msg.from_user_id === currentUserId"
                                    class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full"
                                >
                                    Sent
                                </span>
                            </div>
                            <div class="prose max-w-none text-gray-700 whitespace-pre-wrap bg-gray-50 rounded-lg p-4">
                                {{ msg.message }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reply</h3>
                    <form @submit.prevent="sendReply">
                        <textarea
                            v-model="replyForm.message"
                            rows="6"
                            placeholder="Type your reply here..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] text-base"
                        ></textarea>
                        <div v-if="replyForm.errors.message" class="mt-2 text-sm text-red-600">{{ replyForm.errors.message }}</div>
                        <div class="flex items-center justify-end gap-4 mt-4">
                            <button
                                type="submit"
                                :disabled="replyForm.processing"
                                class="px-6 py-2 bg-[#F15A2B] text-white font-semibold rounded-lg hover:bg-[#BF3206] disabled:opacity-50 transition-colors flex items-center"
                            >
                                <svg v-if="!replyForm.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span v-else class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></span>
                                {{ replyForm.processing ? 'Sending...' : 'Send Reply' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Students Section (only for root message) -->
                <div v-if="rootMessage.has_students" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Students ({{ students.length }})
                            </span>
                        </h3>
                        <button
                            @click="showGroupForm = !showGroupForm"
                            class="px-4 py-2 bg-[#F15A2B] text-white font-semibold rounded-lg hover:bg-[#BF3206] transition-colors flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ showGroupForm ? 'Cancel' : 'Create Group' }}
                        </button>
                    </div>

                    <!-- Students List -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
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

                    <!-- Group Creation Form -->
                    <div v-if="showGroupForm" class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Create New Group</h4>
                        
                        <form @submit.prevent="createGroup">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) *</label>
                                    <input
                                        type="text"
                                        v-model="groupForm.name_en"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="groupForm.errors.name_en" class="mt-1 text-sm text-red-600">{{ groupForm.errors.name_en }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Armenian) *</label>
                                    <input
                                        type="text"
                                        v-model="groupForm.name_hy"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="groupForm.errors.name_hy" class="mt-1 text-sm text-red-600">{{ groupForm.errors.name_hy }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Georgian) *</label>
                                    <input
                                        type="text"
                                        v-model="groupForm.name_ge"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="groupForm.errors.name_ge" class="mt-1 text-sm text-red-600">{{ groupForm.errors.name_ge }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Russian) *</label>
                                    <input
                                        type="text"
                                        v-model="groupForm.name_ru"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="groupForm.errors.name_ru" class="mt-1 text-sm text-red-600">{{ groupForm.errors.name_ru }}</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course *</label>
                                <select
                                    v-model="groupForm.course_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                >
                                    <option value="">Select a course</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.name }}
                                    </option>
                                </select>
                                <div v-if="groupForm.errors.course_id" class="mt-1 text-sm text-red-600">{{ groupForm.errors.course_id }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (English)</label>
                                    <textarea
                                        v-model="groupForm.description_en"
                                        rows="3"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Armenian)</label>
                                    <textarea
                                        v-model="groupForm.description_hy"
                                        rows="3"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Georgian)</label>
                                    <textarea
                                        v-model="groupForm.description_ge"
                                        rows="3"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Russian)</label>
                                    <textarea
                                        v-model="groupForm.description_ru"
                                        rows="3"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="groupForm.is_active"
                                        class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <button
                                    type="button"
                                    @click="showGroupForm = false"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="groupForm.processing"
                                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50"
                                >
                                    Create Group
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

const props = defineProps({
    rootMessage: Object,
    messages: {
        type: Array,
        default: () => [],
    },
    students: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    currentUserId: Number,
});

const showGroupForm = ref(false);

const replyForm = useForm({
    message: '',
});

const groupForm = useForm({
    name_en: '',
    name_hy: '',
    name_ge: '',
    name_ru: '',
    course_id: '',
    description_en: '',
    description_hy: '',
    description_ge: '',
    description_ru: '',
    is_active: true,
});

const sendReply = () => {
    replyForm.post(route('teacher.messages.reply', props.rootMessage.id), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset();
        },
    });
};

const createGroup = () => {
    groupForm.post(route('teacher.messages.create-group', props.rootMessage.id));
};
</script>

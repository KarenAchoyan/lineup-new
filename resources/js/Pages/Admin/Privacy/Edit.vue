<template>
    <AuthenticatedLayout>
        <Head title="Edit Privacy Policy" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Privacy Policy Management</h1>
                            <p class="mt-2 text-sm text-gray-600">Edit the privacy policy content</p>
                        </div>
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Dashboard
                        </Link>
                    </div>
                </div>

                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-green-700 font-medium">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-red-700 font-medium">{{ $page.props.flash.error }}</p>
                    </div>
                </div>

                <!-- Error Messages -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-red-700 font-medium mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                                <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Main Form Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-xl font-semibold text-gray-900">Privacy Policy Content</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-8">
                        <!-- Content Editors Section with Tabs -->
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl border border-gray-200 shadow-sm">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                    </svg>
                                    Privacy Policy Content (Multi-language)
                                </h3>
                                
                                <!-- Tabs Navigation -->
                                <div class="border-b border-gray-200 mb-6">
                                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                        <button
                                            @click="activeTab = 'en'"
                                            :class="[
                                                activeTab === 'en'
                                                    ? 'border-[#F15A2B] text-[#F15A2B]'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center'
                                            ]"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29 1.354.465 2.742.53 4.155a1.01 1.01 0 01-.903 1.065 1 1 0 01-1.065-.903A21.293 21.293 0 015.34 6.533 2.251 2.251 0 003 8.574v1.966a2.25 2.25 0 001.34 2.033c.652.204 1.337.22 1.997.04a21.375 21.375 0 001.87-5.916 19.226 19.226 0 00.33-4.894 1 1 0 00-1-1H8zm-5 8a.75.75 0 100-1.5.75.75 0 000 1.5zm15 0a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                                            </svg>
                                            English
                                        </button>
                                        <button
                                            @click="activeTab = 'hy'"
                                            :class="[
                                                activeTab === 'hy'
                                                    ? 'border-[#F15A2B] text-[#F15A2B]'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center'
                                            ]"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                            </svg>
                                            Հայերեն
                                        </button>
                                        <button
                                            @click="activeTab = 'ge'"
                                            :class="[
                                                activeTab === 'ge'
                                                    ? 'border-[#F15A2B] text-[#F15A2B]'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center'
                                            ]"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                            </svg>
                                            ქართული
                                        </button>
                                        <button
                                            @click="activeTab = 'ru'"
                                            :class="[
                                                activeTab === 'ru'
                                                    ? 'border-[#F15A2B] text-[#F15A2B]'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 flex items-center'
                                            ]"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                            </svg>
                                            Русский
                                        </button>
                                    </nav>
                                </div>

                                <!-- Tab Content -->
                                <div class="relative min-h-[400px]">
                                    <!-- English Tab -->
                                    <div v-show="activeTab === 'en'" class="transition-all duration-300">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-sm font-semibold text-gray-700">
                                                    English Content
                                                </label>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">EN</span>
                                            </div>
                                            <div class="border-2 border-gray-200 rounded-lg overflow-hidden shadow-sm hover:border-[#F15A2B] transition-colors">
                                                <RichTextEditor
                                                    v-model="form.content_en"
                                                    :editor-id="'privacy-en-editor'"
                                                />
                                            </div>
                                            <div v-if="form.errors.content_en" class="text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ form.errors.content_en }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Armenian Tab -->
                                    <div v-show="activeTab === 'hy'" class="transition-all duration-300">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-sm font-semibold text-gray-700">
                                                    Armenian Content
                                                </label>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">HY</span>
                                            </div>
                                            <div class="border-2 border-gray-200 rounded-lg overflow-hidden shadow-sm hover:border-[#F15A2B] transition-colors">
                                                <RichTextEditor
                                                    v-model="form.content_hy"
                                                    :editor-id="'privacy-hy-editor'"
                                                />
                                            </div>
                                            <div v-if="form.errors.content_hy" class="text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ form.errors.content_hy }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Georgian Tab -->
                                    <div v-show="activeTab === 'ge'" class="transition-all duration-300">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-sm font-semibold text-gray-700">
                                                    Georgian Content
                                                </label>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">GE</span>
                                            </div>
                                            <div class="border-2 border-gray-200 rounded-lg overflow-hidden shadow-sm hover:border-[#F15A2B] transition-colors">
                                                <RichTextEditor
                                                    v-model="form.content_ge"
                                                    :editor-id="'privacy-ge-editor'"
                                                />
                                            </div>
                                            <div v-if="form.errors.content_ge" class="text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ form.errors.content_ge }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Russian Tab -->
                                    <div v-show="activeTab === 'ru'" class="transition-all duration-300">
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-sm font-semibold text-gray-700">
                                                    Russian Content
                                                </label>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">RU</span>
                                            </div>
                                            <div class="border-2 border-gray-200 rounded-lg overflow-hidden shadow-sm hover:border-[#F15A2B] transition-colors">
                                                <RichTextEditor
                                                    v-model="form.content_ru"
                                                    :editor-id="'privacy-ru-editor'"
                                                />
                                            </div>
                                            <div v-if="form.errors.content_ru" class="text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                {{ form.errors.content_ru }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                            <Link
                                :href="route('dashboard')"
                                class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2.5 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50 disabled:cursor-not-allowed font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Updating...' : 'Update Privacy Policy' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    privacy: Object,
});

// Active tab state
const activeTab = ref('en');

const form = useForm({
    content_en: props.privacy.content_en || '',
    content_hy: props.privacy.content_hy || '',
    content_ge: props.privacy.content_ge || '',
    content_ru: props.privacy.content_ru || '',
});

const submit = () => {
    // Get latest content from all CKEditor instances before submission
    if (window.CKEDITOR) {
        const enEditor = window.CKEDITOR.instances['privacy-en-editor'];
        const hyEditor = window.CKEDITOR.instances['privacy-hy-editor'];
        const geEditor = window.CKEDITOR.instances['privacy-ge-editor'];
        const ruEditor = window.CKEDITOR.instances['privacy-ru-editor'];
        
        // Update form data with editor content
        if (enEditor) {
            form.content_en = enEditor.getData();
        }
        if (hyEditor) {
            form.content_hy = hyEditor.getData();
        }
        if (geEditor) {
            form.content_ge = geEditor.getData();
        }
        if (ruEditor) {
            form.content_ru = ruEditor.getData();
        }
    }
    
    form.post(route('admin.privacy.update'), {
        preserveScroll: true,
        onSuccess: (page) => {
            if (page.props.privacy) {
                form.content_en = page.props.privacy.content_en || '';
                form.content_hy = page.props.privacy.content_hy || '';
                form.content_ge = page.props.privacy.content_ge || '';
                form.content_ru = page.props.privacy.content_ru || '';
                
                // Update CKEditor instances with new data
                if (window.CKEDITOR) {
                    const enEditor = window.CKEDITOR.instances['privacy-en-editor'];
                    const hyEditor = window.CKEDITOR.instances['privacy-hy-editor'];
                    const geEditor = window.CKEDITOR.instances['privacy-ge-editor'];
                    const ruEditor = window.CKEDITOR.instances['privacy-ru-editor'];
                    
                    if (enEditor) enEditor.setData(form.content_en);
                    if (hyEditor) hyEditor.setData(form.content_hy);
                    if (geEditor) geEditor.setData(form.content_ge);
                    if (ruEditor) ruEditor.setData(form.content_ru);
                }
            }
        },
    });
};
</script>

<style scoped>
/* Additional custom styles if needed */
</style>


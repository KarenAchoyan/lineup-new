<template>
    <AuthenticatedLayout>
        <Head title="Edit Banner" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Banner Management</h1>
                            <p class="mt-2 text-sm text-gray-600">Edit the banner image and text content</p>
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
                        <h2 class="text-xl font-semibold text-gray-900">Banner Content</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-8">
                        <!-- Banner Image Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Banner Image
                            </h3>
                            
                            <div v-if="banner?.image" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Current Banner Image</label>
                                <div class="relative inline-block">
                                    <img :src="`/storage/${banner.image}`" alt="Current Banner Image" class="w-full max-w-2xl h-auto object-cover rounded-lg shadow-md border-2 border-gray-200" />
                                    <div class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-lg">
                                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ banner?.image ? 'Replace Banner Image' : 'Upload Banner Image' }}
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-[#F15A2B] transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label class="relative cursor-pointer bg-white rounded-md font-medium text-[#F15A2B] hover:text-[#BF3206] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#F15A2B]">
                                                <span>Upload a file</span>
                                                <input 
                                                    type="file" 
                                                    @change="handleImageChange"
                                                    accept="image/*"
                                                    class="sr-only"
                                                />
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                                <div v-if="form.image" class="mt-3">
                                    <p class="text-sm text-gray-600">Selected: {{ form.image.name }}</p>
                                </div>
                                <div v-if="form.errors.image" class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ form.errors.image }}
                                </div>
                            </div>
                        </div>

                        <!-- Banner Text Content Section -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-[#F15A2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Banner Text Content
                            </h3>

                            <!-- Language Tabs -->
                            <div class="space-y-6">
                                <div class="border-b border-gray-200">
                                    <nav class="-mb-px flex space-x-8">
                                        <button
                                            type="button"
                                            @click="activeTab = 'en'"
                                            :class="[
                                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                                activeTab === 'en' 
                                                    ? 'border-[#F15A2B] text-[#F15A2B]' 
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            ]"
                                        >
                                            English
                                        </button>
                                        <button
                                            type="button"
                                            @click="activeTab = 'hy'"
                                            :class="[
                                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                                activeTab === 'hy' 
                                                    ? 'border-[#F15A2B] text-[#F15A2B]' 
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            ]"
                                        >
                                            Armenian
                                        </button>
                                        <button
                                            type="button"
                                            @click="activeTab = 'ge'"
                                            :class="[
                                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                                activeTab === 'ge' 
                                                    ? 'border-[#F15A2B] text-[#F15A2B]' 
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            ]"
                                        >
                                            Georgian
                                        </button>
                                        <button
                                            type="button"
                                            @click="activeTab = 'ru'"
                                            :class="[
                                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
                                                activeTab === 'ru' 
                                                    ? 'border-[#F15A2B] text-[#F15A2B]' 
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                            ]"
                                        >
                                            Russian
                                        </button>
                                    </nav>
                                </div>

                                <!-- English Tab -->
                                <div v-show="activeTab === 'en'" class="transition-all duration-300">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                About Dancing Text (EN)
                                            </label>
                                            <input
                                                v-model="form.about_dancing_en"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'About Dancing' text in English"
                                            />
                                            <div v-if="form.errors.about_dancing_en" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.about_dancing_en }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Dancing Text (EN)
                                            </label>
                                            <input
                                                v-model="form.dancing_en"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'Dancing' text in English"
                                            />
                                            <div v-if="form.errors.dancing_en" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.dancing_en }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Armenian Tab -->
                                <div v-show="activeTab === 'hy'" class="transition-all duration-300">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                About Dancing Text (HY)
                                            </label>
                                            <input
                                                v-model="form.about_dancing_hy"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'About Dancing' text in Armenian"
                                            />
                                            <div v-if="form.errors.about_dancing_hy" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.about_dancing_hy }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Dancing Text (HY)
                                            </label>
                                            <input
                                                v-model="form.dancing_hy"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'Dancing' text in Armenian"
                                            />
                                            <div v-if="form.errors.dancing_hy" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.dancing_hy }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Georgian Tab -->
                                <div v-show="activeTab === 'ge'" class="transition-all duration-300">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                About Dancing Text (GE)
                                            </label>
                                            <input
                                                v-model="form.about_dancing_ge"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'About Dancing' text in Georgian"
                                            />
                                            <div v-if="form.errors.about_dancing_ge" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.about_dancing_ge }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Dancing Text (GE)
                                            </label>
                                            <input
                                                v-model="form.dancing_ge"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'Dancing' text in Georgian"
                                            />
                                            <div v-if="form.errors.dancing_ge" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.dancing_ge }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Russian Tab -->
                                <div v-show="activeTab === 'ru'" class="transition-all duration-300">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                About Dancing Text (RU)
                                            </label>
                                            <input
                                                v-model="form.about_dancing_ru"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'About Dancing' text in Russian"
                                            />
                                            <div v-if="form.errors.about_dancing_ru" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.about_dancing_ru }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Dancing Text (RU)
                                            </label>
                                            <input
                                                v-model="form.dancing_ru"
                                                type="text"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#F15A2B] focus:border-[#F15A2B] transition-colors"
                                                placeholder="Enter 'Dancing' text in Russian"
                                            />
                                            <div v-if="form.errors.dancing_ru" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.dancing_ru }}
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
                                class="px-6 py-2.5 bg-[#F15A2B] text-white rounded-lg font-medium hover:bg-[#BF3206] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
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

const props = defineProps({
    banner: {
        type: Object,
        default: null,
    },
});

const activeTab = ref('en');

const form = useForm({
    image: null,
    about_dancing_en: props.banner?.about_dancing_en || '',
    dancing_en: props.banner?.dancing_en || '',
    about_dancing_hy: props.banner?.about_dancing_hy || '',
    dancing_hy: props.banner?.dancing_hy || '',
    about_dancing_ge: props.banner?.about_dancing_ge || '',
    dancing_ge: props.banner?.dancing_ge || '',
    about_dancing_ru: props.banner?.about_dancing_ru || '',
    dancing_ru: props.banner?.dancing_ru || '',
});

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
    }
};

const submit = () => {
    form.post(route('admin.banner.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>








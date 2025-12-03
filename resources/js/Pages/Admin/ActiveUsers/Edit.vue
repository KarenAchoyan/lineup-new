<template>
    <AuthenticatedLayout>
        <Head title="Edit Active User" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-xl font-semibold text-gray-900">Edit Active User</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Names -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (English) *</label>
                                <input type="text" v-model="form.fullname_en" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (Armenian)</label>
                                <input type="text" v-model="form.fullname_hy" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (Georgian)</label>
                                <input type="text" v-model="form.fullname_ge" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (Russian)</label>
                                <input type="text" v-model="form.fullname_ru" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                        </div>

                        <!-- About with Tabs -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">About</h3>
                            <div class="border-b border-gray-200 mb-4">
                                <nav class="-mb-px flex space-x-4">
                                    <button type="button" @click="activeTab = 'en'" :class="activeTab === 'en' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">English</button>
                                    <button type="button" @click="activeTab = 'hy'" :class="activeTab === 'hy' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">Armenian</button>
                                    <button type="button" @click="activeTab = 'ge'" :class="activeTab === 'ge' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">Georgian</button>
                                    <button type="button" @click="activeTab = 'ru'" :class="activeTab === 'ru' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">Russian</button>
                                </nav>
                            </div>
                            <div>
                                <RichTextEditor v-if="activeTab === 'en'" v-model="form.about_en" :editor-id="'about-en-editor'" />
                                <RichTextEditor v-if="activeTab === 'hy'" v-model="form.about_hy" :editor-id="'about-hy-editor'" />
                                <RichTextEditor v-if="activeTab === 'ge'" v-model="form.about_ge" :editor-id="'about-ge-editor'" />
                                <RichTextEditor v-if="activeTab === 'ru'" v-model="form.about_ru" :editor-id="'about-ru-editor'" />
                            </div>
                        </div>

                        <!-- Current Avatar -->
                        <div v-if="activeUser.avatar">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Avatar</label>
                            <img :src="`/storage/${activeUser.avatar}`" class="w-32 h-32 rounded-full object-cover border-4 border-[#F15A2B]" />
                        </div>

                        <!-- New Avatar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ activeUser.avatar ? 'Replace Avatar' : 'Avatar' }}</label>
                            <input type="file" @input="form.avatar = $event.target.files[0]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#F15A2B] file:text-white hover:file:bg-[#BF3206]" />
                        </div>

                        <!-- Order & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                                <input type="number" v-model="form.order" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]" />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t">
                            <Link :href="route('admin.active-users.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50">
                                {{ form.processing ? 'Updating...' : 'Update Active User' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    activeUser: Object,
});

const activeTab = ref('en');

const form = useForm({
    fullname_en: props.activeUser.fullname_en || '',
    fullname_hy: props.activeUser.fullname_hy || '',
    fullname_ge: props.activeUser.fullname_ge || '',
    fullname_ru: props.activeUser.fullname_ru || '',
    about_en: props.activeUser.about_en || '',
    about_hy: props.activeUser.about_hy || '',
    about_ge: props.activeUser.about_ge || '',
    about_ru: props.activeUser.about_ru || '',
    avatar: null,
    order: props.activeUser.order || 0,
    is_active: props.activeUser.is_active ?? true,
});

// Save editor content when switching tabs
const saveCurrentEditorContent = () => {
    if (window.CKEDITOR) {
        const editorMap = {
            'en': 'about-en-editor',
            'hy': 'about-hy-editor',
            'ge': 'about-ge-editor',
            'ru': 'about-ru-editor',
        };
        
        const currentEditorId = editorMap[activeTab.value];
        if (currentEditorId) {
            const editor = window.CKEDITOR.instances[currentEditorId];
            if (editor) {
                const data = editor.getData();
                const formFieldMap = {
                    'en': 'about_en',
                    'hy': 'about_hy',
                    'ge': 'about_ge',
                    'ru': 'about_ru',
                };
                const fieldName = formFieldMap[activeTab.value];
                if (fieldName) {
                    form[fieldName] = data;
                }
            }
        }
    }
};

// Watch for tab changes and save content before switching
let previousTab = activeTab.value;

watch(activeTab, (newTab) => {
    // Save the previous tab's editor content before switching
    if (previousTab && previousTab !== newTab) {
        const editorMap = {
            'en': 'about-en-editor',
            'hy': 'about-hy-editor',
            'ge': 'about-ge-editor',
            'ru': 'about-ru-editor',
        };
        
        const previousEditorId = editorMap[previousTab];
        if (previousEditorId && window.CKEDITOR) {
            const editor = window.CKEDITOR.instances[previousEditorId];
            if (editor) {
                const data = editor.getData();
                const formFieldMap = {
                    'en': 'about_en',
                    'hy': 'about_hy',
                    'ge': 'about_ge',
                    'ru': 'about_ru',
                };
                const fieldName = formFieldMap[previousTab];
                if (fieldName) {
                    form[fieldName] = data;
                }
            }
        }
    }
    
    // Update previous tab
    previousTab = newTab;
    
    // Wait for the new editor to be created, then set its content
    nextTick(() => {
        setTimeout(() => {
            if (window.CKEDITOR) {
                const editorMap = {
                    'en': 'about-en-editor',
                    'hy': 'about-hy-editor',
                    'ge': 'about-ge-editor',
                    'ru': 'about-ru-editor',
                };
                
                const newEditorId = editorMap[newTab];
                if (newEditorId) {
                    const editor = window.CKEDITOR.instances[newEditorId];
                    if (editor) {
                        const formFieldMap = {
                            'en': 'about_en',
                            'hy': 'about_hy',
                            'ge': 'about_ge',
                            'ru': 'about_ru',
                        };
                        const fieldName = formFieldMap[newTab];
                        if (fieldName && form[fieldName]) {
                            editor.setData(form[fieldName]);
                        }
                    }
                }
            }
        }, 300);
    });
});

const submit = () => {
    // Get latest content from all CKEditor instances before submission
    saveCurrentEditorContent();
    
    if (window.CKEDITOR) {
        const enEditor = window.CKEDITOR.instances['about-en-editor'];
        const hyEditor = window.CKEDITOR.instances['about-hy-editor'];
        const geEditor = window.CKEDITOR.instances['about-ge-editor'];
        const ruEditor = window.CKEDITOR.instances['about-ru-editor'];
        
        if (enEditor) form.about_en = enEditor.getData();
        if (hyEditor) form.about_hy = hyEditor.getData();
        if (geEditor) form.about_ge = geEditor.getData();
        if (ruEditor) form.about_ru = ruEditor.getData();
    }
    
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.active-users.update', props.activeUser.id), {
        forceFormData: true,
    });
};
</script>




<template>
    <AuthenticatedLayout>
        <Head title="Create News" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-xl font-semibold text-gray-900">Create News</h2>
                    </div>

                    <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                        <!-- Titles -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title (English) *</label>
                                <input type="text" v-model="form.title_en" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                                <div v-if="form.errors.title_en" class="mt-1 text-sm text-red-600">{{ form.errors.title_en }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title (Armenian)</label>
                                <input type="text" v-model="form.title_hy" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title (Georgian)</label>
                                <input type="text" v-model="form.title_ge" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title (Russian)</label>
                                <input type="text" v-model="form.title_ru" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            </div>
                        </div>

                        <!-- Content with Tabs -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="border-b border-gray-200 mb-4">
                                <nav class="-mb-px flex space-x-4">
                                    <button type="button" @click="activeTab = 'en'" :class="activeTab === 'en' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">English</button>
                                    <button type="button" @click="activeTab = 'hy'" :class="activeTab === 'hy' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">Armenian</button>
                                    <button type="button" @click="activeTab = 'ge'" :class="activeTab === 'ge' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">Georgian</button>
                                    <button type="button" @click="activeTab = 'ru'" :class="activeTab === 'ru' ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500'" class="py-2 px-1 border-b-2 font-medium text-sm">Russian</button>
                                </nav>
                            </div>
                            <div>
                                <RichTextEditor 
                                    v-if="activeTab === 'en'" 
                                    :model-value="editorContent.content_en" 
                                    :editor-id="'content-en-editor'"
                                    @content-change="editorContent.content_en = $event"
                                />
                                <RichTextEditor 
                                    v-if="activeTab === 'hy'" 
                                    :model-value="editorContent.content_hy" 
                                    :editor-id="'content-hy-editor'"
                                    @content-change="editorContent.content_hy = $event"
                                />
                                <RichTextEditor 
                                    v-if="activeTab === 'ge'" 
                                    :model-value="editorContent.content_ge" 
                                    :editor-id="'content-ge-editor'"
                                    @content-change="editorContent.content_ge = $event"
                                />
                                <RichTextEditor 
                                    v-if="activeTab === 'ru'" 
                                    :model-value="editorContent.content_ru" 
                                    :editor-id="'content-ru-editor'"
                                    @content-change="editorContent.content_ru = $event"
                                />
                            </div>
                        </div>

                        <!-- Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <input type="file" @input="form.avatar = $event.target.files[0]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#F15A2B] file:text-white hover:file:bg-[#BF3206]" />
                            <div v-if="form.errors.avatar" class="mt-1 text-sm text-red-600">{{ form.errors.avatar }}</div>
                        </div>

                        <!-- Video -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">YouTube Video URL</label>
                            <input type="url" v-model="form.video" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]" />
                            <div v-if="form.errors.video" class="mt-1 text-sm text-red-600">{{ form.errors.video }}</div>
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
                            <Link :href="route('admin.news.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50">
                                {{ form.processing ? 'Creating...' : 'Create News' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const activeTab = ref('en');

// Local state for editor content - not bound to form to prevent auto-submit
const editorContent = ref({
    content_en: '',
    content_hy: '',
    content_ge: '',
    content_ru: '',
});

// Function to save editor content periodically
const saveEditorContent = () => {
    if (window.CKEDITOR) {
        const editorMap = {
            'en': 'content-en-editor',
            'hy': 'content-hy-editor',
            'ge': 'content-ge-editor',
            'ru': 'content-ru-editor',
        };
        
        // Save all existing editors' content
        Object.keys(editorMap).forEach(lang => {
            const editorId = editorMap[lang];
            const editor = window.CKEDITOR.instances[editorId];
            if (editor) {
                const contentKey = `content_${lang}`;
                editorContent.value[contentKey] = editor.getData();
            }
        });
    }
};

// Set up interval to save editor content every 2 seconds
let saveInterval = null;

onMounted(() => {
    // Save editor content periodically
    saveInterval = setInterval(saveEditorContent, 2000);
});

onUnmounted(() => {
    if (saveInterval) {
        clearInterval(saveInterval);
    }
});

const form = useForm({
    title_en: '',
    title_hy: '',
    title_ge: '',
    title_ru: '',
    content_en: '',
    content_hy: '',
    content_ge: '',
    content_ru: '',
    avatar: null,
    video: '',
    order: 0,
    is_active: true,
});

// Watch for tab changes to save/restore editor content
// This only updates editorContent ref, NOT the form - so no request is sent
watch(activeTab, async (newTab, oldTab) => {
    if (oldTab && window.CKEDITOR) {
        // Save content from previous tab's editor to editorContent
        const editorMap = {
            'en': 'content-en-editor',
            'hy': 'content-hy-editor',
            'ge': 'content-ge-editor',
            'ru': 'content-ru-editor',
        };
        
        const previousEditorId = editorMap[oldTab];
        if (previousEditorId) {
            const editor = window.CKEDITOR.instances[previousEditorId];
            if (editor) {
                const contentKey = `content_${oldTab}`;
                editorContent.value[contentKey] = editor.getData();
            }
        }
    }
    
    // Wait for new editor to be ready, then restore its content from editorContent
    await nextTick();
    setTimeout(() => {
        if (window.CKEDITOR) {
            const editorMap = {
                'en': 'content-en-editor',
                'hy': 'content-hy-editor',
                'ge': 'content-ge-editor',
                'ru': 'content-ru-editor',
            };
            
            const newEditorId = editorMap[newTab];
            if (newEditorId) {
                const editor = window.CKEDITOR.instances[newEditorId];
                if (editor) {
                    const contentKey = `content_${newTab}`;
                    const content = editorContent.value[contentKey] || '';
                    // Set data without triggering any events
                    editor.setData(content, { callback: () => {
                        // Content restored, no action needed
                    }});
                }
            }
        }
    }, 300);
});

const handleSubmit = (e) => {
    e.preventDefault();
    e.stopPropagation();
    
    // Save content from currently visible editor to editorContent before submission
    if (window.CKEDITOR) {
        const editorMap = {
            'en': 'content-en-editor',
            'hy': 'content-hy-editor',
            'ge': 'content-ge-editor',
            'ru': 'content-ru-editor',
        };
        
        // Save current tab's editor content
        const currentEditorId = editorMap[activeTab.value];
        if (currentEditorId) {
            const currentEditor = window.CKEDITOR.instances[currentEditorId];
            if (currentEditor) {
                const contentKey = `content_${activeTab.value}`;
                editorContent.value[contentKey] = currentEditor.getData();
            }
        }
        
        // Try to get content from all editors (if they exist)
        // But also use editorContent as fallback for unmounted editors
        const enEditor = window.CKEDITOR.instances['content-en-editor'];
        const hyEditor = window.CKEDITOR.instances['content-hy-editor'];
        const geEditor = window.CKEDITOR.instances['content-ge-editor'];
        const ruEditor = window.CKEDITOR.instances['content-ru-editor'];
        
        // Update form with editor content - use editor instance if available, otherwise use editorContent
        form.content_en = enEditor ? enEditor.getData() : (editorContent.value.content_en || '');
        form.content_hy = hyEditor ? hyEditor.getData() : (editorContent.value.content_hy || '');
        form.content_ge = geEditor ? geEditor.getData() : (editorContent.value.content_ge || '');
        form.content_ru = ruEditor ? ruEditor.getData() : (editorContent.value.content_ru || '');
    } else {
        // If CKEditor is not available, use editorContent directly
        form.content_en = editorContent.value.content_en || '';
        form.content_hy = editorContent.value.content_hy || '';
        form.content_ge = editorContent.value.content_ge || '';
        form.content_ru = editorContent.value.content_ru || '';
    }
    
    form.post(route('admin.news.store'), {
        forceFormData: true,
    });
};
</script>







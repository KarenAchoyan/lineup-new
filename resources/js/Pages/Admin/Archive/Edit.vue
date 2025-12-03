<template>
    <AuthenticatedLayout>
        <Head title="Edit Archive Item" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Archive Item</h2>

                        <form @submit.prevent="submit">
                            <!-- Type Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.type"
                                            value="image"
                                            class="mr-2"
                                        />
                                        <span>Image</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.type"
                                            value="video"
                                            class="mr-2"
                                        />
                                        <span>Video</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.type"
                                            value="news"
                                            class="mr-2"
                                        />
                                        <span>News</span>
                                    </label>
                                </div>
                                <div v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</div>
                            </div>

                            <!-- Year -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                <input
                                    type="number"
                                    v-model="form.year"
                                    min="2000"
                                    :max="new Date().getFullYear() + 1"
                                    placeholder="e.g., 2024"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                                <div v-if="form.errors.year" class="mt-1 text-sm text-red-600">{{ form.errors.year }}</div>
                            </div>

                            <!-- Current Image Preview (for image and news types) -->
                            <div v-if="(form.type === 'image' || form.type === 'news') && item.image" class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                                <img :src="`/storage/${item.image}`" alt="Current" class="w-32 h-32 object-cover rounded-lg" />
                            </div>

                            <!-- Image Upload (for image and news types) -->
                            <div v-if="form.type === 'image' || form.type === 'news'" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Image (leave empty to keep current)</label>
                                <input
                                    type="file"
                                    @input="form.image = $event.target.files[0]"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#F15A2B] file:text-white hover:file:bg-[#BF3206]"
                                />
                                <div v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</div>
                            </div>

                            <!-- YouTube Link (for video type) -->
                            <div v-if="form.type === 'video'" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">YouTube Link *</label>
                                <input
                                    type="url"
                                    v-model="form.youtube_link"
                                    placeholder="https://www.youtube.com/watch?v=..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                                <div v-if="form.errors.youtube_link" class="mt-1 text-sm text-red-600">{{ form.errors.youtube_link }}</div>
                            </div>

                            <!-- Link (for image and news types) -->
                            <div v-if="form.type === 'image' || form.type === 'news'" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Link (Optional)</label>
                                <input
                                    type="url"
                                    v-model="form.link"
                                    placeholder="https://example.com"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                                <div v-if="form.errors.link" class="mt-1 text-sm text-red-600">{{ form.errors.link }}</div>
                            </div>

                            <!-- Titles -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title (English)</label>
                                    <input
                                        type="text"
                                        v-model="form.title_en"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title (Armenian)</label>
                                    <input
                                        type="text"
                                        v-model="form.title_hy"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title (Georgian)</label>
                                    <input
                                        type="text"
                                        v-model="form.title_ge"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title (Russian)</label>
                                    <input
                                        type="text"
                                        v-model="form.title_ru"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                            </div>

                            <!-- Descriptions (for news type) -->
                            <div v-if="form.type === 'news'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (English)</label>
                                    <textarea
                                        v-model="form.description_en"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Armenian)</label>
                                    <textarea
                                        v-model="form.description_hy"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Georgian)</label>
                                    <textarea
                                        v-model="form.description_ge"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description (Russian)</label>
                                    <textarea
                                        v-model="form.description_ru"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Order -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                                <input
                                    type="number"
                                    v-model="form.order"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                            </div>

                            <!-- Active Status -->
                            <div class="mb-6">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('admin.archive.index')"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50"
                                >
                                    Update
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
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    item: Object,
});

const form = useForm({
    type: props.item.type || 'image',
    year: props.item.year || new Date().getFullYear(),
    image: null,
    youtube_link: props.item.youtube_link || '',
    link: props.item.link || '',
    title_en: props.item.title_en || '',
    title_hy: props.item.title_hy || '',
    title_ge: props.item.title_ge || '',
    title_ru: props.item.title_ru || '',
    description_en: props.item.description_en || '',
    description_hy: props.item.description_hy || '',
    description_ge: props.item.description_ge || '',
    description_ru: props.item.description_ru || '',
    order: props.item.order || 0,
    is_active: props.item.is_active ?? true,
});

const submit = () => {
    form.post(route('admin.archive.update', props.item.id), {
        forceFormData: true,
    });
};
</script>


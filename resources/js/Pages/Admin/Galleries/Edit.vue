<template>
    <AuthenticatedLayout>
        <Head title="Edit Gallery Item" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-xl font-semibold text-gray-900">Edit Gallery Item</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Current Image -->
                        <div v-if="gallery.image">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                            <img :src="`/storage/${gallery.image}`" class="w-full max-w-md h-auto rounded-lg shadow-md" />
                        </div>

                        <!-- New Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ gallery.image ? 'Replace Image' : 'Image' }}</label>
                            <input type="file" @input="form.image = $event.target.files[0]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#F15A2B] file:text-white hover:file:bg-[#BF3206]" />
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
                            <Link :href="route('admin.galleries.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50">
                                {{ form.processing ? 'Updating...' : 'Update Gallery Item' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    gallery: Object,
});

const form = useForm({
    image: null,
    order: props.gallery.order || 0,
    is_active: props.gallery.is_active ?? true,
});

const submit = () => {
    form.post(route('admin.galleries.update', props.gallery.id), {
        forceFormData: true,
        _method: 'PUT',
    });
};
</script>









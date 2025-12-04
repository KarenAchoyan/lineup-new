<template>
    <AuthenticatedLayout>
        <Head title="Gallery Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Gallery Management</h2>
                            <Link
                                :href="route('admin.galleries.create')"
                                class="bg-[#F15A2B] hover:bg-[#BF3206] text-white font-bold py-2 px-4 rounded-lg transition-colors"
                            >
                                Add New Image
                            </Link>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div v-for="gallery in galleries" :key="gallery.id" class="group relative bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden">
                                <div class="aspect-square overflow-hidden">
                                    <img :src="`/storage/${gallery.image}`" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" />
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span :class="gallery.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ gallery.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <span class="text-sm text-gray-500">Order: {{ gallery.order }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link :href="route('admin.galleries.edit', gallery.id)" class="flex-1 text-center px-3 py-2 bg-[#F15A2B] text-white rounded hover:bg-[#BF3206] transition-colors text-sm">Edit</Link>
                                        <button @click="deleteItem(gallery.id)" class="flex-1 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="galleries.length === 0" class="text-center py-12 text-gray-500">No gallery items found.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    galleries: Array,
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this image?')) {
        router.delete(route('admin.galleries.destroy', id));
    }
};
</script>









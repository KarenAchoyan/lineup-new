<template>
    <AuthenticatedLayout>
        <Head title="Archive Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Archive Management</h2>
                            <Link
                                :href="route('admin.archive.create')"
                                class="bg-[#F15A2B] hover:bg-[#BF3206] text-white font-bold py-2 px-4 rounded-lg transition-colors"
                            >
                                Add New Item
                            </Link>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                item.type === 'image' ? 'bg-blue-100 text-blue-800' : 
                                                item.type === 'video' ? 'bg-red-100 text-red-800' : 
                                                'bg-green-100 text-green-800'
                                            ]">
                                                {{ item.type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ item.year || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div v-if="(item.type === 'image' || item.type === 'news') && item.image" class="w-16 h-16 rounded-lg overflow-hidden">
                                                <img :src="`/storage/${item.image}`" :alt="item.title_en" class="w-full h-full object-cover" />
                                            </div>
                                            <div v-else-if="item.type === 'video' && item.youtube_id" class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                </svg>
                                            </div>
                                            <span v-else class="text-gray-400">No preview</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ item.title_en || 'No title' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ item.order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                item.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                            ]">
                                                {{ item.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                :href="route('admin.archive.edit', item.id)"
                                                class="text-[#F15A2B] hover:text-[#BF3206] mr-4"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteItem(item.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="items.length === 0" class="text-center py-12 text-gray-500">
                                No archive items found. Create your first item!
                            </div>
                        </div>
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
    items: Array,
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this item?')) {
        router.delete(route('admin.archive.destroy', id));
    }
};
</script>


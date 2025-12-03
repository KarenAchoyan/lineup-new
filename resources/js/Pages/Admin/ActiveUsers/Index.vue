<template>
    <AuthenticatedLayout>
        <Head title="Active Users Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Active Users Management</h2>
                            <Link
                                :href="route('admin.active-users.create')"
                                class="bg-[#F15A2B] hover:bg-[#BF3206] text-white font-bold py-2 px-4 rounded-lg transition-colors"
                            >
                                Add New User
                            </Link>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div v-for="user in activeUsers" :key="user.id" class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow overflow-hidden border border-gray-200">
                                <div class="aspect-square overflow-hidden bg-gray-100">
                                    <img :src="`/storage/${user.avatar}`" class="w-full h-full object-cover" :alt="user.fullname_en" />
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ user.fullname_en || 'No name' }}</h3>
                                    <div class="flex items-center justify-between mb-3">
                                        <span :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ user.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <span class="text-xs text-gray-500">Order: {{ user.order }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <Link :href="route('admin.active-users.edit', user.id)" class="flex-1 text-center px-3 py-2 bg-[#F15A2B] text-white rounded hover:bg-[#BF3206] transition-colors text-sm">Edit</Link>
                                        <button @click="deleteItem(user.id)" class="flex-1 px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-sm">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="activeUsers.length === 0" class="text-center py-12 text-gray-500">No active users found.</div>
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
    activeUsers: Array,
});

const deleteItem = (id) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.active-users.destroy', id));
    }
};
</script>








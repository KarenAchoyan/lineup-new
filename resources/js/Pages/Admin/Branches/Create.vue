<template>
    <AuthenticatedLayout>
        <Head title="Create Branch" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Branch</h2>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (English) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_en"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_en" class="mt-1 text-sm text-red-600">{{ form.errors.name_en }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Armenian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_hy"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_hy" class="mt-1 text-sm text-red-600">{{ form.errors.name_hy }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Georgian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_ge"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_ge" class="mt-1 text-sm text-red-600">{{ form.errors.name_ge }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Name (Russian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.name_ru"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.name_ru" class="mt-1 text-sm text-red-600">{{ form.errors.name_ru }}</div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                                <input
                                    type="number"
                                    v-model="form.order"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                            </div>

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

                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('admin.branches.index')"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50"
                                >
                                    Create
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

const form = useForm({
    name_en: '',
    name_hy: '',
    name_ge: '',
    name_ru: '',
    order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.branches.store'));
};
</script>


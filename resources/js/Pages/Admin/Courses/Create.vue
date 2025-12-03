<template>
    <AuthenticatedLayout>
        <Head title="Create Course" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Course</h2>

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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Slug (auto-generated if empty)</label>
                                <input
                                    type="text"
                                    v-model="form.slug"
                                    placeholder="e.g., dance, vocals, guitar"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                />
                                <div v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (GEL) *</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        v-model="form.price"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Months (Validity) *</label>
                                    <input
                                        type="number"
                                        v-model="form.months"
                                        min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.months" class="mt-1 text-sm text-red-600">{{ form.errors.months }}</div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Background Image</label>
                                <input
                                    type="file"
                                    @input="form.background_image = $event.target.files[0]"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#F15A2B] file:text-white hover:file:bg-[#BF3206]"
                                />
                                <div v-if="form.errors.background_image" class="mt-1 text-sm text-red-600">{{ form.errors.background_image }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Branches</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                    <label
                                        v-for="branch in branches"
                                        :key="branch.id"
                                        class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                        :class="form.branches.includes(branch.id) ? 'border-[#F15A2B] bg-orange-50' : 'border-gray-300'"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="branch.id"
                                            v-model="form.branches"
                                            class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B]"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">{{ branch.name_en }}</span>
                                    </label>
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
                                    :href="route('admin.courses.index')"
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

const props = defineProps({
    branches: Array,
});

const form = useForm({
    name_en: '',
    name_hy: '',
    name_ge: '',
    name_ru: '',
    slug: '',
    price: 0,
    months: 12,
    background_image: null,
    description_en: '',
    description_hy: '',
    description_ge: '',
    description_ru: '',
    branches: [],
    order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.courses.store'), {
        forceFormData: true,
    });
};
</script>


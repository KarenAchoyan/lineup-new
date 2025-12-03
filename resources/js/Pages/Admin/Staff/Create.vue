<template>
    <AuthenticatedLayout>
        <Head title="Create Staff Member" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Staff Member</h2>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (English) *</label>
                                    <input
                                        type="text"
                                        v-model="form.fullname_en"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.fullname_en" class="mt-1 text-sm text-red-600">{{ form.errors.fullname_en }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (Armenian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.fullname_hy"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.fullname_hy" class="mt-1 text-sm text-red-600">{{ form.errors.fullname_hy }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (Georgian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.fullname_ge"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.fullname_ge" class="mt-1 text-sm text-red-600">{{ form.errors.fullname_ge }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name (Russian) *</label>
                                    <input
                                        type="text"
                                        v-model="form.fullname_ru"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                    <div v-if="form.errors.fullname_ru" class="mt-1 text-sm text-red-600">{{ form.errors.fullname_ru }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Profession (English)</label>
                                    <input
                                        type="text"
                                        v-model="form.profession_en"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Profession (Armenian)</label>
                                    <input
                                        type="text"
                                        v-model="form.profession_hy"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Profession (Georgian)</label>
                                    <input
                                        type="text"
                                        v-model="form.profession_ge"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Profession (Russian)</label>
                                    <input
                                        type="text"
                                        v-model="form.profession_ru"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input
                                        type="text"
                                        v-model="form.phone"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B]"
                                    />
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Avatar</label>
                                <input
                                    type="file"
                                    @input="form.avatar = $event.target.files[0]"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#F15A2B] file:text-white hover:file:bg-[#BF3206]"
                                />
                                <div v-if="form.errors.avatar" class="mt-1 text-sm text-red-600">{{ form.errors.avatar }}</div>
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
                                    :href="route('admin.staff.index')"
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
    fullname_en: '',
    fullname_hy: '',
    fullname_ge: '',
    fullname_ru: '',
    profession_en: '',
    profession_hy: '',
    profession_ge: '',
    profession_ru: '',
    phone: '',
    email: '',
    avatar: null,
    order: 0,
    is_active: true,
});

const submit = () => {
    form.post(route('admin.staff.store'), {
        forceFormData: true,
    });
};
</script>


<template>
    <AppLayout>
        <Head :title="t('volunteering')" />

        <div class="min-h-screen bg-[#211d1dfc] py-12 sm:py-16 md:py-20 lg:py-32">
            <div class="container mx-auto px-3 sm:px-4">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-[#4d4c4c2b] backdrop-blur-sm rounded-xl shadow-2xl p-5 sm:p-6 md:p-8 lg:p-12">
                        <div v-if="submitted" class="text-center py-10 sm:py-12 md:py-16">
                            <div class="mb-6 sm:mb-8">
                                <svg class="mx-auto h-16 w-16 sm:h-20 sm:w-20 text-[#F15A2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h2 class="text-3xl sm:text-4xl md:text-5xl text-[#F15A2B] mb-3 sm:mb-4 font-bold">
                                {{ t('thank_you') }}
                            </h2>
                            <p class="text-[#C7C7C7] text-base sm:text-lg md:text-xl lg:text-2xl px-2">
                                {{ t('thank_you_for_registration') }}
                            </p>
                        </div>

                        <div v-else>
                            <h1 class="text-2xl sm:text-3xl md:text-4xl text-[#C7C7C7] text-center mb-6 sm:mb-8 font-bold">
                                {{ t('volunteering') }}
                            </h1>

                            <form @submit.prevent="submit" class="max-w-md mx-auto space-y-4 sm:space-y-6">
                                <div v-if="form.errors.general" class="bg-red-50 border-l-4 border-red-400 p-3 sm:p-4 rounded">
                                    <p class="text-sm sm:text-base text-red-700">{{ form.errors.general }}</p>
                                </div>

                                <div>
                                    <label class="block text-[#C7C7C7] text-xs sm:text-sm font-medium mb-1.5 sm:mb-2">
                                        {{ t('full_name') }}
                                    </label>
                                    <input
                                        v-model="form.subject"
                                        type="text"
                                        required
                                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white/10 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F15A2B] focus:border-transparent transition-all"
                                        :placeholder="t('full_name') + '...'"
                                    />
                                    <div v-if="form.errors.subject" class="mt-1 text-xs sm:text-sm text-red-400">
                                        {{ form.errors.subject }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[#C7C7C7] text-xs sm:text-sm font-medium mb-1.5 sm:mb-2">
                                        {{ t('phone_number') }}
                                    </label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white/10 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F15A2B] focus:border-transparent transition-all"
                                        :placeholder="t('phone_number') + '...'"
                                    />
                                    <div v-if="form.errors.phone" class="mt-1 text-xs sm:text-sm text-red-400">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[#C7C7C7] text-xs sm:text-sm font-medium mb-1.5 sm:mb-2">
                                        {{ t('email_address_c') }}
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white/10 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F15A2B] focus:border-transparent transition-all"
                                        :placeholder="t('email_address_c') + '...'"
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-xs sm:text-sm text-red-400">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[#C7C7C7] text-xs sm:text-sm font-medium mb-1.5 sm:mb-2">
                                        {{ t('cover_letter') }}
                                    </label>
                                    <textarea
                                        v-model="form.message"
                                        required
                                        rows="5"
                                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base bg-white/10 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F15A2B] focus:border-transparent transition-all resize-none"
                                        :placeholder="t('cover_letter') + '...'"
                                    ></textarea>
                                    <div v-if="form.errors.message" class="mt-1 text-xs sm:text-sm text-red-400">
                                        {{ form.errors.message }}
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full bg-[#F15A2B] hover:bg-[#BF3206] text-white text-base sm:text-lg md:text-xl font-semibold py-3 sm:py-4 px-4 sm:px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="form.processing">{{ t('sending') }}...</span>
                                    <span v-else>{{ t('button_text_col') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();

const props = defineProps({
    success: Boolean,
});

const submitted = ref(props.success || false);

const form = useForm({
    subject: '',
    phone: '',
    email: '',
    message: '',
});

const submit = () => {
    form.post(route('volunteering.store'), {
        onSuccess: () => {
            submitted.value = true;
        },
    });
};
</script>


<template>
    <AppLayout>
        <Head :title="t('volunteering')" />

        <div class="min-h-screen bg-[#211d1dfc] py-12 sm:py-16 md:py-20 lg:py-32">
            <div class="container mx-auto px-3 sm:px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-[#4d4c4c2b] backdrop-blur-sm rounded-xl shadow-2xl p-5 sm:p-6 md:p-8 lg:p-12">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-[#C7C7C7] text-center mb-4 sm:mb-6 font-bold">
                            {{ t('volunteering') }}
                        </h1>
                        <h3 class="text-lg sm:text-xl md:text-2xl lg:text-3xl text-[#C7C7C7] text-center mb-6 sm:mb-8">
                            {{ t('prof_photography_videography') }}
                        </h3>
                        <div class="mt-6 sm:mt-8 text-white text-sm sm:text-base md:text-lg lg:text-2xl text-left leading-relaxed">
                            <p>
                                {{ t('prof_photography_videography_2') }}
                            </p>
                        </div>
                        <div class="flex justify-center mt-8 sm:mt-10 md:mt-12">
                            <Link
                                :href="route('volunteering.registration')"
                                class="bg-[#F15A2B] hover:bg-[#BF3206] text-white text-base sm:text-lg md:text-xl lg:text-2xl font-semibold rounded-lg px-6 sm:px-8 py-3 sm:py-4 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                            >
                                {{ t('fill_out_the_application_2') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();
</script>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

defineProps({
    status: String,
});

const { t } = useTranslation();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AppLayout>
        <Head :title="t('forgot_password') || 'Forgot Password'" />

        <div class="min-h-screen flex items-center justify-center bg-[#232222] py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-96 h-96 bg-[#F15A2B]/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#BF3206]/5 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-md w-full space-y-8 relative z-10">
                <div class="text-center">
                    <h2 class="text-4xl md:text-5xl font-bold text-[#C7C7C7] mb-2 drop-shadow-[0_2px_8px_rgba(241,90,43,0.3)]">
                        {{ t('forgot_password') || 'Forgot Password' }}
                    </h2>
                    <p class="text-lg text-gray-400">
                        {{ t('forgot_password_description') || 'No problem. Just let us know your email address and we will email you a password reset link.' }}
                    </p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-400 text-center bg-green-500/10 border border-green-500/30 rounded-lg p-3">
                    {{ status }}
                </div>

                <div class="bg-[#4D4C4C] backdrop-blur-sm rounded-2xl border-t-2 border-[#BF3206] shadow-2xl p-8 md:p-10">
                    <form class="space-y-6" @submit.prevent="submit">
                        <div>
                            <InputLabel for="email" :value="t('email')" class="text-[#C7C7C7]" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                :placeholder="t('email_address')"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <PrimaryButton class="w-full bg-gradient-to-r from-[#F15A2B] to-[#BF3206] hover:from-[#BF3206] hover:to-[#F15A2B] text-white font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ form.processing ? (t('sending') || 'Sending...') : (t('send_reset_link') || 'Email Password Reset Link') }}
                            </PrimaryButton>
                        </div>

                        <div class="text-center pt-4 border-t border-[#5D5D5D]">
                            <Link :href="route('login')" class="text-sm font-medium text-[#F15A2B] hover:text-[#BF3206] transition-colors duration-300">
                                {{ t('back_to_login') || 'Back to login' }}
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

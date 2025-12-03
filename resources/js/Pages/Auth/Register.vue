<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="t('sign_up')" />

        <div class="min-h-screen flex items-center justify-center bg-[#232222] py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Background decorative elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-0 right-0 w-96 h-96 bg-[#F15A2B]/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#BF3206]/5 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-md w-full space-y-8 relative z-10">
                <div class="text-center">
                    <h2 class="text-4xl md:text-5xl font-bold text-[#C7C7C7] mb-2 drop-shadow-[0_2px_8px_rgba(241,90,43,0.3)]">
                        {{ t('register') }}
                    </h2>
                    <p class="text-lg text-gray-400">
                        {{ t('dont_have_an_account_sign_up') }}
                    </p>
                </div>

                <div class="bg-[#4D4C4C] backdrop-blur-sm rounded-2xl border-t-2 border-[#BF3206] shadow-2xl p-8 md:p-10">
                    <form class="space-y-6" @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" :value="t('full_name')" class="text-[#C7C7C7]" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                :placeholder="t('full_name')"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" :value="t('email')" class="text-[#C7C7C7]" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                :placeholder="t('email_address')"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" :value="t('password')" class="text-[#C7C7C7]" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                :placeholder="t('password')"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" :value="t('confirm_password')" class="text-[#C7C7C7]" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                :placeholder="t('confirm_password')"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-center">
                            <Checkbox name="terms" v-model:checked="form.terms" />
                            <label class="ml-2 block text-sm text-[#C7C7C7]">
                                {{ t('terms_and_conditions') }}
                            </label>
                        </div>

                        <div>
                            <PrimaryButton class="w-full bg-gradient-to-r from-[#F15A2B] to-[#BF3206] hover:from-[#BF3206] hover:to-[#F15A2B] text-white font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]" :class="{ 'opacity-25': form.processing || !form.terms }" :disabled="form.processing || !form.terms">
                                {{ form.processing ? t('signing_up') || 'Signing up...' : t('sign_up') }}
                            </PrimaryButton>
                        </div>

                        <div class="text-center pt-4 border-t border-[#5D5D5D]">
                            <p class="text-sm text-gray-400">
                                {{ t('already_have_an_account_sign_in') }}
                                <Link :href="route('login')" class="font-medium text-[#F15A2B] hover:text-[#BF3206] transition-colors duration-300">
                                    {{ t('sign_in') }}
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

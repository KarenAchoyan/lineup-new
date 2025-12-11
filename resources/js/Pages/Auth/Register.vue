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
    phone: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

// Filter name input to allow only Armenian letters and spaces
const filterArmenianName = (event) => {
    const input = event.target;
    // Armenian letters range: \u0531-\u0587 (uppercase) and \u0561-\u0587 (lowercase)
    // Also allow spaces
    const armenianRegex = /^[\u0531-\u0587\u0561-\u0587\s]*$/;
    const value = input.value;
    
    // Filter out any non-Armenian characters
    const filteredValue = value.split('').filter(char => {
        return /[\u0531-\u0587\u0561-\u0587\s]/.test(char);
    }).join('');
    
    if (value !== filteredValue) {
        form.name = filteredValue;
        // Force update the input value
        input.value = filteredValue;
    }
};

// Filter phone input to allow only Georgian phone format
const filterGeorgianPhone = (event) => {
    const input = event.target;
    let value = input.value;
    
    // Remove all non-digit and non-plus characters
    let cleaned = value.replace(/[^\d+]/g, '');
    
    // If starts with +995, allow up to 13 characters (+995 + 9 digits)
    if (cleaned.startsWith('+995')) {
        // Limit to 13 characters total
        cleaned = cleaned.substring(0, 13);
        // Ensure format is +995 followed by max 9 digits
        const match = cleaned.match(/^(\+995)(\d{0,9})/);
        if (match) {
            cleaned = match[1] + match[2];
        }
    } 
    // If starts with +, allow typing +995 (don't restrict while typing)
    else if (cleaned.startsWith('+')) {
        // Allow +, +9, +99, +995, +9955, etc. while typing
        // But limit to max 13 characters for +995 format
        if (cleaned.length > 13) {
            cleaned = cleaned.substring(0, 13);
        }
        // If user types +5, convert to local format (5XXXXXXXX)
        const digits = cleaned.substring(1);
        if (digits.length > 0 && digits[0] === '5' && !digits.startsWith('995')) {
            cleaned = digits.substring(0, 9);
        }
    }
    // If starts with 5 (local format), allow up to 9 digits
    else if (cleaned.startsWith('5')) {
        // Limit to 9 digits
        cleaned = cleaned.substring(0, 9);
        // Ensure it starts with 5 and has only digits
        const match = cleaned.match(/^(5\d{0,8})/);
        if (match) {
            cleaned = match[1];
        }
    }
    // If starts with digit but not 5, clear it
    else if (cleaned.length > 0 && !cleaned.startsWith('5')) {
        cleaned = '';
    }
    // If empty, allow it
    
    if (value !== cleaned) {
        form.phone = cleaned;
        input.value = cleaned;
    }
};

// Filter email input to prevent Armenian characters
const filterEmail = (event) => {
    const input = event.target;
    const value = input.value;
    // Allow only Latin letters, numbers, @, ., -, _, and spaces (but we'll remove spaces)
    // Armenian Unicode range: \u0531-\u0587 (uppercase) and \u0561-\u0587 (lowercase)
    const armenianRegex = /[\u0531-\u0587\u0561-\u0587]/;
    
    // Remove Armenian characters and spaces
    let cleaned = value.split('').filter(char => {
        return !armenianRegex.test(char) && char !== ' ';
    }).join('');
    
    if (value !== cleaned) {
        form.email = cleaned;
        input.value = cleaned;
    }
};

// Filter password input to prevent Armenian characters
const filterPassword = (event) => {
    const input = event.target;
    const value = input.value;
    // Allow only Latin letters, numbers, and common special characters
    // Armenian Unicode range: \u0531-\u0587 (uppercase) and \u0561-\u0587 (lowercase)
    const armenianRegex = /[\u0531-\u0587\u0561-\u0587]/;
    
    // Remove Armenian characters
    let cleaned = value.split('').filter(char => {
        return !armenianRegex.test(char);
    }).join('');
    
    if (value !== cleaned) {
        // Update both password and password_confirmation if they match
        if (input.id === 'password') {
            form.password = cleaned;
        } else if (input.id === 'password_confirmation') {
            form.password_confirmation = cleaned;
        }
        input.value = cleaned;
    }
};

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
                            <InputLabel for="name" :value="t('full_name')" class="text-white" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.name"
                                @input="filterArmenianName"
                                @paste="filterArmenianName"
                                required
                                autofocus
                                autocomplete="name"
                                :placeholder="t('full_name')"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" :value="t('email')" class="text-white" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.email"
                                @input="filterEmail"
                                @paste="filterEmail"
                                required
                                autocomplete="username"
                                :placeholder="t('email_address')"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="phone" :value="t('phone_number') || t('phone') || 'Հեռախոսահամար'" class="text-white" />
                            <TextInput
                                id="phone"
                                type="tel"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.phone"
                                @input="filterGeorgianPhone"
                                @paste="filterGeorgianPhone"
                                autocomplete="tel"
                                placeholder="+995593662895 կամ 593662895"
                            />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>

                        <div>
                            <InputLabel for="password" :value="t('password')" class="text-white" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.password"
                                @input="filterPassword"
                                @paste="filterPassword"
                                required
                                autocomplete="new-password"
                                :placeholder="t('password')"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" :value="t('confirm_password')" class="text-white" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-2 block w-full bg-[#5D5D5D] border border-[#6D6D6D] text-white placeholder-gray-400 focus:border-[#F15A2B] focus:ring-2 focus:ring-[#F15A2B]/20 rounded-lg transition-all duration-300"
                                v-model="form.password_confirmation"
                                @input="filterPassword"
                                @paste="filterPassword"
                                required
                                autocomplete="new-password"
                                :placeholder="t('confirm_password')"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-start">
                            <Checkbox name="terms" v-model:checked="form.terms" class="mt-1" />
                            <label class="ml-2 block text-sm text-[#C7C7C7]">
                                {{ t('terms_and_conditions') }}
                                <Link :href="route('privacy')" target="_blank" class="text-[#F15A2B] hover:text-[#BF3206] underline ml-1">
                                    {{ t('privacy_policy') || 'Privacy Policy' }}
                                </Link>
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

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const { t } = useTranslation();
const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
            <div>
                <InputLabel for="name" :value="t('full_name')" class="text-white" />

                <input
                    id="name"
                    type="text"
                    class="mt-2 block w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" :value="t('email')" class="text-white" />

                <input
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="props.mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-[#C7C7C7]">
                    {{ t('email_not_verified') || 'Your email address is unverified.' }}
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-[#F15A2B] hover:text-[#BF3206] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F15A2B]"
                    >
                        {{ t('resend_verification') || 'Click here to re-send the verification email.' }}
                    </Link>
                </p>

                <div
                    v-show="props.status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-400"
                >
                    {{ t('verification_sent') || 'A new verification link has been sent to your email address.' }}
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-[#F15A2B] text-white rounded-lg hover:bg-[#BF3206] disabled:opacity-50 transition-colors"
                >
                    {{ t('save') || 'Save' }}
                </button>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-400">{{ t('saved') || 'Saved.' }}</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

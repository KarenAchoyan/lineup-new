<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <form @submit.prevent="updatePassword" class="space-y-6">
            <div>
                <InputLabel for="current_password" :value="t('current_password') || 'Current Password'" class="text-[#C7C7C7]" />

                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-2 block w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                    autocomplete="current-password"
                />

                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password" :value="t('new_password') || 'New Password'" class="text-[#C7C7C7]" />

                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-2 block w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password_confirmation" :value="t('confirm_password')" class="text-[#C7C7C7]" />

                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-2 block w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password_confirmation" class="mt-2" />
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

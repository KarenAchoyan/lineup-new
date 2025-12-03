<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

const { t } = useTranslation();
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-[#C7C7C7]">{{ t('delete_account') || 'Delete Account' }}</h2>

            <p class="mt-1 text-sm text-[#C7C7C7]">
                {{ t('delete_account_warning') || 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.' }}
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
        >
            {{ t('delete_account') || 'Delete Account' }}
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a]">
                <h2 class="text-lg font-medium text-[#C7C7C7]">
                    {{ t('are_you_sure_you_want_to_log_out') || 'Are you sure you want to delete your account?' }}
                </h2>

                <p class="mt-1 text-sm text-[#C7C7C7]">
                    {{ t('delete_account_confirmation') || 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.' }}
                </p>

                <div class="mt-6">
                    <InputLabel for="password" :value="t('password')" class="sr-only" />

                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full rounded-md bg-[#5D5D5D] border border-[#4D4C4C] text-white shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] px-3 py-2"
                        :placeholder="t('password')"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="closeModal"
                        class="px-4 py-2 border border-[#4D4C4C] rounded-lg text-[#C7C7C7] hover:bg-[#5D5D5D] transition-colors"
                    >
                        {{ t('cancel') || 'Cancel' }}
                    </button>

                    <button
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ t('delete_account') || 'Delete Account' }}
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>

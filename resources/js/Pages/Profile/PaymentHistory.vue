<script setup>
import ProfileLayout from '@/Layouts/ProfileLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

const props = defineProps({
    student: {
        type: Object,
        required: true,
    },
    payments: {
        type: Array,
        default: () => [],
    },
    hasPaidThisMonth: {
        type: Boolean,
        default: false,
    },
});

const { t } = useTranslation();
const paymentLoading = ref(false);
const showPayment = ref(false);

const makePayment = async () => {
    if (paymentLoading.value) return;
    
    paymentLoading.value = true;
    showPayment.value = true;

    // Get CSRF token
    const getCsrfToken = () => {
        // Try meta tag first
        const metaToken = document.head.querySelector('meta[name="csrf-token"]');
        if (metaToken && metaToken.content) {
            return metaToken.content;
        }
        // Try cookie
        const getCookie = (name) => {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        };
        const xsrfToken = getCookie('XSRF-TOKEN');
        if (xsrfToken) {
            return decodeURIComponent(xsrfToken);
        }
        return null;
    };

    const csrfToken = getCsrfToken();

    try {
        const response = await window.axios.post(
            route('profile.payments.create', props.student.id),
            { amount: 30 },
            {
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-XSRF-TOKEN': csrfToken || '',
                },
            }
        );

        if (response.data.success && response.data.checkout_data) {
            // Submit POST form to Flitt
            submitFlittForm(response.data.checkout_data);
        } else {
            alert(response.data.message || 'Failed to create payment');
            paymentLoading.value = false;
            showPayment.value = false;
        }
    } catch (error) {
        console.error('Payment error:', error);
        console.error('Error response:', error.response);
        const errorMessage = error.response?.data?.message || error.message || 'An error occurred while processing payment';
        alert(errorMessage);
        paymentLoading.value = false;
        showPayment.value = false;
    }
};

const cancelPayment = () => {
    showPayment.value = false;
    paymentLoading.value = false;
};

// Submit POST form to Flitt
const submitFlittForm = (checkoutData) => {
    // Create a form element
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = checkoutData.url;
    form.style.display = 'none';

    // Add all parameters as hidden inputs
    Object.keys(checkoutData.params).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = checkoutData.params[key];
        form.appendChild(input);
    });

    // Append form to body and submit
    document.body.appendChild(form);
    form.submit();
};

const getStatusColor = (status) => {
    switch (status) {
        case 'success':
            return 'text-green-400';
        case 'pending':
            return 'text-yellow-400';
        case 'failed':
        case 'cancelled':
            return 'text-red-400';
        default:
            return 'text-gray-400';
    }
};

const getStatusText = (status) => {
    switch (status) {
        case 'success':
            return t('paid') || 'Paid';
        case 'pending':
            return t('pending') || 'Pending';
        case 'failed':
            return t('failed') || 'Failed';
        case 'cancelled':
            return t('cancelled') || 'Cancelled';
        default:
            return status;
    }
};
</script>

<template>
    <ProfileLayout>
        <Head :title="t('payment_history') || 'Payment History'" />

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Page Title -->
            <div class="text-center mb-8">
                <h1 class="text-[32px] md:text-[45px] font-bold text-[#C7C7C7] mb-2">
                    {{ t('payment_history') || 'Payment History' }}
                </h1>
                <p class="text-[#C7C7C7] text-lg">{{ props.student.name }}</p>
                <p class="text-[#C7C7C7] text-sm">{{ props.student.course_name }} - {{ props.student.branch_name }}</p>
            </div>

            <!-- Payment Status Card -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <h2 class="text-xl font-semibold text-[#C7C7C7] mb-6">{{ t('payment_status') || 'Payment Status' }}</h2>
                
                <div v-if="hasPaidThisMonth" class="bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-semibold">{{ t('already_paid') || 'Payment completed successfully for this month!' }}</p>
                    </div>
                </div>

                <div v-else class="mb-4">
                    <div class="bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 px-4 py-3 rounded-lg mb-4">
                        <p>{{ t('payment_required_message') || 'Payment is required to access all features.' }}</p>
                    </div>
                    
                    <div v-if="!showPayment">
                        <button
                            @click="makePayment"
                            :disabled="paymentLoading"
                            class="w-full bg-[#F15A2B] hover:bg-[#BF3206] text-white px-6 py-4 rounded-lg transition-colors flex items-center justify-center text-lg font-semibold shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="paymentLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            {{ paymentLoading ? (t('processing') || 'Processing...') : (t('make_payment') || 'Make Payment') + ' (30 GEL)' }}
                        </button>
                    </div>
                    <div v-else class="w-full">
                        <div class="bg-blue-500/10 border border-blue-500/30 text-blue-400 px-4 py-3 rounded-lg mb-4">
                            <p class="font-semibold">{{ t('redirecting_to_payment') || 'Redirecting to Payment' }}</p>
                            <p class="text-sm">{{ t('redirecting_message') || 'You will be redirected to Flitt\'s secure payment page.' }}</p>
                        </div>
                        
                        <button
                            @click="cancelPayment"
                            class="mt-4 w-full bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition-colors font-medium"
                        >
                            {{ t('cancel_payment') || 'Cancel Payment' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Payment History Card -->
            <div class="bg-gradient-to-br from-[#4D4C4C] to-[#3a3a3a] p-6 sm:p-8 rounded-2xl border-t-2 border-[#BF3206] shadow-xl">
                <h2 class="text-xl font-semibold text-[#C7C7C7] mb-6">{{ t('payment_history') || 'Payment History' }}</h2>
                
                <div v-if="payments.length === 0" class="text-center py-12 text-[#C7C7C7]">
                    <p>{{ t('no_payments') || 'No payment history found.' }}</p>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="payment in payments"
                        :key="payment.id"
                        class="bg-[#5D5D5D] border border-[#4D4C4C] rounded-lg p-4 hover:shadow-lg transition-all"
                    >
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-semibold text-[#C7C7C7]">{{ t('order') || 'Order' }}: {{ payment.order_id }}</h4>
                                <p class="text-sm text-[#C7C7C7]">{{ t('amount') || 'Amount' }}: {{ payment.amount }} {{ payment.currency }}</p>
                            </div>
                            <span :class="['font-semibold', getStatusColor(payment.status)]">
                                {{ getStatusText(payment.status) }}
                            </span>
                        </div>
                        <div class="text-xs text-[#C7C7C7] space-y-1">
                            <p v-if="payment.paid_at">
                                {{ t('paid_at') || 'Paid at' }}: {{ new Date(payment.paid_at).toLocaleString() }}
                            </p>
                            <p>{{ t('created_at') || 'Created' }}: {{ new Date(payment.created_at).toLocaleString() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
                <button
                    @click="router.visit(route('profile.edit'))"
                    class="bg-[#4D4C4C] hover:bg-[#5D5D5D] text-[#C7C7C7] px-6 py-3 rounded-lg transition-colors font-medium"
                >
                    {{ t('back_to_profile') || 'Back to Profile' }}
                </button>
            </div>
        </div>
    </ProfileLayout>
</template>


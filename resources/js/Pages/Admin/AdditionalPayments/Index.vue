<template>
    <AuthenticatedLayout>
        <Head title="Կողմնակի վճարումներ" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div>
                                <h2 class="text-3xl font-bold bg-gradient-to-r from-[#F15A2B] to-[#BF3206] bg-clip-text text-transparent">
                                    Կողմնակի վճարումներ
                                </h2>
                                <p class="text-gray-600 mt-1">Կառավարեք կողմնակի վճարումները</p>
                            </div>
                            <button
                                @click="showAddModal = true"
                                class="bg-gradient-to-r from-[#F15A2B] to-[#BF3206] hover:from-[#BF3206] hover:to-[#8B2404] text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Ավելացնել կողմնակի վճարում</span>
                            </button>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ $page.props.flash.success }}</span>
                            </div>
                        </div>

                        <!-- Payments Table -->
                        <div v-if="payments.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ամսաթիվ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Աշակերտ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ծնող</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Դասընթաց</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Մասնաճյուղ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Գումար</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Մեկնաբանություն</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(payment.paid_at || payment.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payment.student_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ payment.user_name }}<br>
                                            <span class="text-xs text-gray-500">{{ payment.user_email }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payment.course_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payment.branch_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ payment.amount }} {{ payment.currency }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-md">
                                            {{ payment.order_desc }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                            {{ payment.order_id }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>Կողմնակի վճարումներ չկան</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Payment Modal -->
        <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Ավելացնել կողմնակի վճարում</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitPayment">
                        <!-- Student Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Աշակերտ *</label>
                            <div class="relative">
                                <input
                                    ref="studentSearchInput"
                                    type="text"
                                    v-model="studentSearchQuery"
                                    @input="filterStudents"
                                    @focus="showStudentDropdown = true"
                                    @blur="handleStudentBlur"
                                    placeholder="Փնտրել աշակերտի անունով, ծնողի անունով կամ email-ով..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': !paymentForm.student_id && paymentForm.errors?.student_id }"
                                />
                                <div v-if="paymentForm.student_id && selectedStudent" class="absolute right-10 top-1/2 transform -translate-y-1/2">
                                    <button
                                        type="button"
                                        @click="clearStudentSelection"
                                        class="text-gray-400 hover:text-gray-600"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div v-if="showStudentDropdown && filteredStudents.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                    <div
                                        v-for="student in filteredStudents"
                                        :key="student.id"
                                        @mousedown.prevent="selectStudent(student)"
                                        class="px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                                    >
                                        <div class="font-semibold text-gray-900">{{ student.name }}</div>
                                        <div class="text-sm text-gray-600">
                                            Ծնող: {{ student.user_name }} ({{ student.user_email }})
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ student.course_name }} - {{ student.branch_name }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="showStudentDropdown && filteredStudents.length === 0 && studentSearchQuery" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-4 text-center text-gray-500">
                                    Աշակերտ չի գտնվել
                                </div>
                            </div>
                            <!-- Selected Student Display -->
                            <div v-if="paymentForm.student_id && selectedStudent" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ selectedStudent.name }}</div>
                                        <div class="text-sm text-gray-600">
                                            Ծնող: {{ selectedStudent.user_name }} ({{ selectedStudent.user_email }})
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ selectedStudent.course_name }} - {{ selectedStudent.branch_name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Գումար *</label>
                            <input
                                v-model.number="paymentForm.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                required
                                placeholder="30.00"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Currency -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Արժույթ</label>
                            <select
                                v-model="paymentForm.currency"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="GEL">GEL</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>

                        <!-- Description/Comment -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Մեկնաբանություն *</label>
                            <textarea
                                v-model="paymentForm.order_desc"
                                rows="4"
                                required
                                placeholder="Նշեք, թե ինչի համար է վճարումը..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <!-- Payment Date -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Վճարման ամսաթիվ</label>
                            <input
                                v-model="paymentForm.paid_at"
                                type="datetime-local"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Error Messages -->
                        <div v-if="paymentForm.errors" class="mb-4 text-sm text-red-600">
                            <div v-for="(error, key) in paymentForm.errors" :key="key">
                                {{ error }}
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                            >
                                Չեղարկել
                            </button>
                            <button
                                type="submit"
                                :disabled="paymentForm.processing"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
                            >
                                <span v-if="paymentForm.processing">Պահպանվում է...</span>
                                <span v-else>Պահպանել</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    students: {
        type: Array,
        default: () => [],
    },
    payments: {
        type: Array,
        default: () => [],
    },
});

const showAddModal = ref(false);
const studentSearchQuery = ref('');
const showStudentDropdown = ref(false);
const selectedStudent = ref(null);
const studentSearchInput = ref(null);
const filteredStudents = ref([]);

const paymentForm = useForm({
    student_id: '',
    amount: 30,
    currency: 'GEL',
    order_desc: '',
    paid_at: new Date().toISOString().slice(0, 16),
});

// Watch for modal opening to initialize filtered students
watch(showAddModal, (newValue) => {
    if (newValue) {
        filteredStudents.value = props.students;
        nextTick(() => {
            if (studentSearchInput.value) {
                studentSearchInput.value.focus();
            }
        });
    }
});

// Filter students based on search query
const filterStudents = () => {
    if (!studentSearchQuery.value.trim()) {
        filteredStudents.value = props.students;
        return;
    }

    const query = studentSearchQuery.value.toLowerCase();
    filteredStudents.value = props.students.filter(student => {
        return (
            student.name.toLowerCase().includes(query) ||
            student.user_name.toLowerCase().includes(query) ||
            student.user_email.toLowerCase().includes(query) ||
            student.course_name.toLowerCase().includes(query) ||
            student.branch_name.toLowerCase().includes(query)
        );
    });
};

// Select student
const selectStudent = (student) => {
    selectedStudent.value = student;
    paymentForm.student_id = student.id;
    studentSearchQuery.value = student.name;
    showStudentDropdown.value = false;
};

// Clear student selection
const clearStudentSelection = () => {
    selectedStudent.value = null;
    paymentForm.student_id = '';
    studentSearchQuery.value = '';
    filteredStudents.value = props.students;
};

// Handle blur with delay to allow click
const handleStudentBlur = () => {
    setTimeout(() => {
        showStudentDropdown.value = false;
    }, 200);
};

// Close modal
const closeModal = () => {
    showAddModal.value = false;
    selectedStudent.value = null;
    paymentForm.reset();
    paymentForm.clearErrors();
    studentSearchQuery.value = '';
    filteredStudents.value = [];
};

// Submit payment
const submitPayment = () => {
    if (!paymentForm.student_id) {
        paymentForm.setError('student_id', 'Խնդրում ենք ընտրել աշակերտ');
        return;
    }
    
    paymentForm.post(route('admin.additional-payments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    });
};

// Format date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('hy-AM', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>


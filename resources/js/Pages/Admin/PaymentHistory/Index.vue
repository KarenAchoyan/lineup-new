<template>
    <AuthenticatedLayout>
        <Head title="Payment History" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Payment History</h2>
                            <div class="flex items-center gap-4">
                                <div class="text-sm text-gray-600">
                                    <span v-if="!filters.unpaid_this_month" class="font-semibold">{{ payments.length }}</span>
                                    <span v-else class="font-semibold">{{ unpaidStudents.length }}</span>
                                    <span v-if="!filters.unpaid_this_month">{{ payments.length === 1 ? ' payment' : ' payments' }} found</span>
                                    <span v-else>{{ unpaidStudents.length === 1 ? ' student' : ' students' }} haven't paid</span>
                                </div>
                                <button
                                    @click="showCashPaymentModal = true"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Cash Payment
                                </button>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div v-if="!filters.unpaid_this_month" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-6">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="text-sm text-green-600 font-medium">Ընդամենը</div>
                                <div class="text-2xl font-bold text-green-900">{{ formatCurrency(statistics.total_amount) }}</div>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="text-sm text-blue-600 font-medium">Հաջող</div>
                                <div class="text-2xl font-bold text-blue-900">{{ statistics.success_count }}</div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="text-sm text-yellow-600 font-medium">Սպասվող</div>
                                <div class="text-2xl font-bold text-yellow-900">{{ statistics.pending_count }}</div>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="text-sm text-red-600 font-medium">Ձախողված</div>
                                <div class="text-2xl font-bold text-red-900">{{ statistics.failed_count }}</div>
                            </div>
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                <div class="text-sm text-indigo-600 font-medium">Վարձեր</div>
                                <div class="text-2xl font-bold text-indigo-900">{{ statistics.regular_count || 0 }}</div>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <div class="text-sm text-purple-600 font-medium">Կողմնակի</div>
                                <div class="text-2xl font-bold text-purple-900">{{ statistics.additional_count || 0 }}</div>
                            </div>
                        </div>

                        <!-- Unpaid This Month Alert -->
                        <div v-if="filters.unpaid_this_month" class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <div class="text-lg font-semibold text-red-900">{{ statistics.unpaid_count }} students haven't paid this month</div>
                                    <div class="text-sm text-red-700">Showing students who haven't made a successful payment in {{ getCurrentMonthName() }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters Section -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                                <button
                                    @click="clearFilters"
                                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                                >
                                    Clear All
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                <!-- Month Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                                    <input
                                        v-model="filters.month"
                                        @change="applyFilters"
                                        type="month"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Branch Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                                    <select
                                        v-model="filters.branch_id"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Branches</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Course Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                    <select
                                        v-model="filters.course_id"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Courses</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div v-if="!filters.unpaid_this_month">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select
                                        v-model="filters.status"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Statuses</option>
                                        <option value="success">Success</option>
                                        <option value="pending">Pending</option>
                                        <option value="failed">Failed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <!-- Payment Type Filter -->
                                <div v-if="!filters.unpaid_this_month">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                                    <select
                                        v-model="filters.payment_type"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Types</option>
                                        <option value="cash">Cash</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>

                                <!-- Payment Category Filter -->
                                <div v-if="!filters.unpaid_this_month">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Վճարման կատեգորիա</label>
                                    <select
                                        v-model="filters.payment_category"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Բոլորը</option>
                                        <option value="regular">Վարձեր</option>
                                        <option value="additional">Կողմնակի վճարումներ</option>
                                    </select>
                                </div>

                                <!-- Unpaid This Month Toggle -->
                                <div class="flex items-end">
                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            v-model="filters.unpaid_this_month"
                                            @change="applyFilters"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm font-medium text-gray-700">Unpaid This Month</span>
                                    </label>
                                </div>

                                <!-- Search -->
                                <div class="xl:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        placeholder="Student name, parent name, email, or order ID..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Payments Table -->
                        <div v-if="!filters.unpaid_this_month && payments.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Մեկնաբանություն</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="payment in payments" :key="payment.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(payment.paid_at || payment.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payment.student.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div>{{ payment.student.user_name }}</div>
                                            <div class="text-xs text-gray-500">{{ payment.student.user_email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payment.student.course_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ payment.student.branch_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span v-if="payment.group_name" class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                                {{ payment.group_name }}
                                            </span>
                                            <span v-else class="text-gray-400 text-xs">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                            {{ payment.order_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ formatCurrency(payment.amount) }} {{ payment.currency }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="payment.payment_type === 'cash'" class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Cash
                                            </span>
                                            <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Online
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 max-w-md">
                                            <div v-if="payment.order_desc" class="truncate" :title="payment.order_desc">
                                                {{ payment.order_desc }}
                                            </div>
                                            <span v-else class="text-gray-400 text-xs">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusClass(payment.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ getStatusText(payment.status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Unpaid Students Table -->
                        <div v-if="filters.unpaid_this_month && unpaidStudents.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payment</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="student in unpaidStudents" :key="student.id" class="hover:bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ student.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div>{{ student.user_name }}</div>
                                            <div class="text-xs text-gray-500">{{ student.user_email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ student.course_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ student.branch_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span v-if="student.last_payment" class="text-gray-900">{{ formatDate(student.last_payment) }}</span>
                                            <span v-else class="text-red-600 font-semibold">Never</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="(!filters.unpaid_this_month && payments.length === 0) || (filters.unpaid_this_month && unpaidStudents.length === 0)" class="text-center py-12 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">
                                <span v-if="!filters.unpaid_this_month">No payments found for the selected filters.</span>
                                <span v-else>All students have paid this month! 🎉</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Payment Modal -->
        <div v-if="showCashPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add Cash Payment</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCashPayment">
                        <!-- Student Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student *</label>
                            <div class="relative">
                                <input
                                    ref="studentSearchInput"
                                    type="text"
                                    v-model="studentSearchQuery"
                                    @input="filterStudents"
                                    @focus="showStudentDropdown = true"
                                    @blur="handleStudentBlur"
                                    placeholder="Search by student name, parent name, or email..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': !cashPaymentForm.student_id && cashPaymentForm.errors?.student_id }"
                                />
                                <div v-if="cashPaymentForm.student_id && selectedStudent" class="absolute right-10 top-1/2 transform -translate-y-1/2">
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
                                            Parent: {{ student.user_name }} ({{ student.user_email }})
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ student.course_name }} - {{ student.branch_name }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="showStudentDropdown && filteredStudents.length === 0 && studentSearchQuery" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-4 text-center text-gray-500">
                                    No students found
                                </div>
                            </div>
                            <!-- Selected Student Display -->
                            <div v-if="cashPaymentForm.student_id && selectedStudent" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ selectedStudent.name }}</div>
                                        <div class="text-sm text-gray-600">
                                            Parent: {{ selectedStudent.user_name }} ({{ selectedStudent.user_email }})
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                            <input
                                v-model.number="cashPaymentForm.amount"
                                type="number"
                                step="0.01"
                                min="0.01"
                                required
                                placeholder="0.00"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Currency -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <select
                                v-model="cashPaymentForm.currency"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="GEL">GEL</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>

                        <!-- Payment Date -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                            <input
                                v-model="cashPaymentForm.paid_at"
                                type="datetime-local"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea
                                v-model="cashPaymentForm.order_desc"
                                rows="3"
                                placeholder="Optional description..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <!-- Error Messages -->
                        <div v-if="cashPaymentForm.errors" class="mb-4 text-sm text-red-600">
                            <div v-for="(error, key) in cashPaymentForm.errors" :key="key">
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
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="cashPaymentForm.processing"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
                            >
                                <span v-if="cashPaymentForm.processing">Saving...</span>
                                <span v-else>Save Payment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    payments: {
        type: Array,
        default: () => [],
    },
    unpaidStudents: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    branches: {
        type: Array,
        default: () => [],
    },
    students: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            month: new Date().toISOString().slice(0, 7),
            branch_id: '',
            course_id: '',
            status: '',
            payment_type: '',
            unpaid_this_month: false,
            search: '',
        }),
    },
    statistics: {
        type: Object,
        default: () => ({
            total_amount: 0,
            success_count: 0,
            pending_count: 0,
            failed_count: 0,
            cancelled_count: 0,
            cash_count: 0,
            online_count: 0,
            unpaid_count: 0,
        }),
    },
});

const filters = ref({ ...props.filters });
const showCashPaymentModal = ref(false);
const studentSearchQuery = ref('');
const showStudentDropdown = ref(false);
const filteredStudents = ref([]);
const selectedStudent = ref(null);
const studentSearchInput = ref(null);

// Cash payment form
const cashPaymentForm = useForm({
    student_id: '',
    amount: '',
    currency: 'GEL',
    paid_at: new Date().toISOString().slice(0, 16),
    order_desc: '',
});

// Watch for modal opening to initialize student search
watch(showCashPaymentModal, async (isOpen) => {
    if (isOpen) {
        // Initialize with first 10 students
        filteredStudents.value = props.students.slice(0, 10);
        showStudentDropdown.value = false;
        // Focus on search input after modal opens
        await nextTick();
        if (studentSearchInput.value) {
            studentSearchInput.value.focus();
        }
    }
});

// Debounce search input
let searchTimeout = null;
watch(() => filters.value.search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const applyFilters = () => {
    router.get(route('admin.payment-history.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = {
        month: new Date().toISOString().slice(0, 7),
        branch_id: '',
        course_id: '',
        status: '',
        payment_type: '',
        payment_category: '',
        unpaid_this_month: false,
        search: '',
    };
    applyFilters();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusClass = (status) => {
    const classes = {
        success: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        failed: 'bg-red-100 text-red-800',
        cancelled: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
    const texts = {
        success: 'Success',
        pending: 'Pending',
        failed: 'Failed',
        cancelled: 'Cancelled',
    };
    return texts[status] || status;
};

const getCurrentMonthName = () => {
    return new Date().toLocaleString('en-US', { month: 'long', year: 'numeric' });
};

const filterStudents = () => {
    const query = studentSearchQuery.value.toLowerCase().trim();
    if (!query) {
        filteredStudents.value = props.students.slice(0, 10); // Show first 10 if no search
    } else {
        filteredStudents.value = props.students.filter(student => {
            return (
                student.name.toLowerCase().includes(query) ||
                student.user_name.toLowerCase().includes(query) ||
                student.user_email.toLowerCase().includes(query) ||
                student.course_name.toLowerCase().includes(query) ||
                student.branch_name.toLowerCase().includes(query)
            );
        }).slice(0, 20); // Limit to 20 results
    }
};

const selectStudent = (student) => {
    cashPaymentForm.student_id = student.id;
    selectedStudent.value = student;
    studentSearchQuery.value = `${student.name} - ${student.user_name}`;
    showStudentDropdown.value = false;
};

const clearStudentSelection = () => {
    cashPaymentForm.student_id = '';
    selectedStudent.value = null;
    studentSearchQuery.value = '';
    showStudentDropdown.value = false;
};

const handleStudentBlur = () => {
    // Delay to allow click event on dropdown items
    setTimeout(() => {
        showStudentDropdown.value = false;
    }, 200);
};

const closeModal = () => {
    showCashPaymentModal.value = false;
    cashPaymentForm.reset();
    cashPaymentForm.clearErrors();
    clearStudentSelection();
};

const submitCashPayment = () => {
    if (!cashPaymentForm.student_id) {
        cashPaymentForm.setError('student_id', 'Please select a student');
        return;
    }
    
    cashPaymentForm.post(route('admin.payment-history.cash.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            applyFilters();
        },
    });
};
</script>


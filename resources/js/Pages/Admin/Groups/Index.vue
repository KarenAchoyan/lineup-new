<template>
    <AuthenticatedLayout>
        <Head title="Groups Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                            <div>
                                <h2 class="text-3xl font-bold bg-gradient-to-r from-[#F15A2B] to-[#BF3206] bg-clip-text text-transparent">
                                    Խմբերի կառավարում
                                </h2>
                                <p class="text-gray-600 mt-1">Կառավարեք աշակերտների խմբերը</p>
                            </div>
                            <Link
                                :href="route('admin.groups.create')"
                                class="bg-gradient-to-r from-[#F15A2B] to-[#BF3206] hover:from-[#BF3206] hover:to-[#8B2404] text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Ավելացնել նոր խումբ</span>
                            </Link>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ $page.props.flash.success }}</span>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span>Ֆիլտրներ</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Որոնում</label>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        placeholder="Խմբի անվանում..."
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Դասընթաց</label>
                                    <select
                                        v-model="filters.course_id"
                                        @change="applyFilters"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                    >
                                        <option value="">Բոլոր դասընթացները</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Անվանում</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Դասընթաց</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Աշակերտներ</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Կարգավիճակ</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Գործողություններ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="group in groups" :key="group.id" class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ group.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ group.course_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 rounded-full text-xs font-semibold shadow-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                {{ group.students_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full',
                                                group.is_active 
                                                    ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 shadow-sm' 
                                                    : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 shadow-sm'
                                            ]">
                                                <span :class="[
                                                    'w-2 h-2 rounded-full mr-2',
                                                    group.is_active ? 'bg-green-500' : 'bg-gray-400'
                                                ]"></span>
                                                {{ group.is_active ? 'Ակտիվ' : 'Անակտիվ' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-3">
                                                <button
                                                    @click="viewClass(group.id)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg font-medium transition-colors text-xs"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Դիտել
                                                </button>
                                                <Link
                                                    :href="route('admin.groups.edit', group.id)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#F15A2B] rounded-lg font-medium transition-colors text-xs"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Խմբագրել
                                                </Link>
                                                <button
                                                    @click="deleteGroup(group.id)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg font-medium transition-colors text-xs"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Ջնջել
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="groups.length === 0" class="text-center py-16">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Խմբեր չեն գտնվել</p>
                                <p class="text-gray-400 text-sm mt-2">Ստեղծեք ձեր առաջին խումբը</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Class Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="closeModal"
        >
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-[#F15A2B] to-[#BF3206] bg-clip-text text-transparent">
                                {{ selectedGroup?.name || 'Դասարանի աշակերտներ' }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Ընդամենը: <span class="font-semibold text-[#F15A2B]">{{ modalStudents.length }}</span> {{ modalStudents.length === 1 ? 'աշակերտ' : 'աշակերտ' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button
                                @click="exportToExcel"
                                class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl flex items-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Excel-ում արտահանել
                            </button>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 text-2xl font-bold"
                            >
                                &times;
                            </button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <div class="flex space-x-8">
                            <button
                                @click="showMessagesSection = false"
                                :class="[
                                    'py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                                    !showMessagesSection ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Աշակերտներ
                            </button>
                            <button
                                @click="showMessagesSection = true"
                                :class="[
                                    'py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors',
                                    showMessagesSection ? 'border-[#F15A2B] text-[#F15A2B]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Հաղորդագրություններ
                                <span v-if="modalMessages.length > 0" class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    {{ modalMessages.length }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Messages Section -->
                    <div v-if="showMessagesSection" class="mb-6">
                        <div v-if="modalMessages.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <p>Այս խմբի ծնողներին դեռ հաղորդագրություն չի ուղարկվել:</p>
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="message in modalMessages"
                                :key="message.id"
                                class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-[#F15A2B] transition-all"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-semibold text-gray-900">{{ message.subject }}</h4>
                                            <span v-if="message.has_students" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                                {{ message.student_ids.length }} student(s)
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ message.message }}</p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span>From: {{ message.from_user_name }}</span>
                                            <span>To: {{ message.to_user_name }}</span>
                                            <span>{{ message.created_at_human }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div v-if="!showMessagesSection" class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900">Ֆիլտրներ</h4>
                            <button
                                @click="clearStudentFilters"
                                class="text-sm text-gray-600 hover:text-[#F15A2B] underline transition-colors"
                            >
                                Մաքրել բոլորը
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Որոնում</label>
                                <input
                                    v-model="studentFilters.search"
                                    type="text"
                                    placeholder="Աշակերտի անուն, բիրադի, ծնող..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                />
                            </div>

                            <!-- Course Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Դասընթաց</label>
                                <select
                                    v-model="studentFilters.course_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                >
                                    <option value="">Բոլոր դասընթացները</option>
                                    <option v-for="course in modalCourses" :key="course.id" :value="course.id">
                                        {{ course.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Branch Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Մասնաճյուղ</label>
                                <select
                                    v-model="studentFilters.branch_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                >
                                    <option value="">Բոլոր մասնաճյուղերը</option>
                                    <option v-for="branch in modalBranches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Payment Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Վճարում</label>
                                <select
                                    v-model="studentFilters.payment_status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Բոլորը</option>
                                    <option value="paid">Վճարված</option>
                                    <option value="unpaid">Չի վճարված</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div v-if="!showMessagesSection" class="overflow-x-auto max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Աշակերտի անուն</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Բիրադի</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ծնողի անուն</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ծնողի էլ. փոստ</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Դասընթաց</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Մասնաճյուղ</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Վճարում</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ներկա/Բացակա</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(student, index) in filteredStudents" :key="student.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ student.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.biradi || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.user_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.user_email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.course_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ student.branch_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <span :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                student.has_paid_this_month ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            ]">
                                                {{ student.has_paid_this_month ? 'Վճարված' : 'Չի վճարված' }}
                                            </span>
                                            <button
                                                @click="openCashPaymentModal(student)"
                                                class="inline-flex items-center px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition-colors"
                                                title="Add Cash Payment"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        -
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="filteredStudents.length === 0" class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p>Ֆիլտրներին համապատասխանող աշակերտներ չեն գտնվել:</p>
                        </div>
                    </div>

                    <!-- Send Message to Unpaid Section -->
                    <div v-if="!showMessagesSection && !showMessageForm" class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Ուղարկել հաղորդագրություն չվճարած ծնողներին</h4>
                                <p class="text-sm text-gray-600 mt-1">Ընտրեք չվճարած աշակերտներին և ուղարկեք հաղորդագրություն նրանց ծնողներին</p>
                            </div>
                            <button
                                @click="openMessageForm"
                                :disabled="unpaidStudents.length === 0"
                                class="px-4 py-2 bg-gradient-to-r from-[#F15A2B] to-[#BF3206] text-white font-semibold rounded-lg hover:from-[#BF3206] hover:to-[#8B2404] disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl flex items-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Ուղարկել հաղորդագրություն ({{ selectedUnpaidUsers.length }})
                            </button>
                        </div>
                        
                        <div v-if="unpaidStudents.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                <label
                                    v-for="student in unpaidStudents"
                                    :key="student.id"
                                    class="flex items-center p-2 rounded border cursor-pointer hover:bg-yellow-100 transition-all"
                                    :class="isUnpaidSelected(student.user_id) ? 'border-[#F15A2B] bg-yellow-100' : 'border-yellow-200'"
                                >
                                    <input
                                        type="checkbox"
                                        :value="student.user_id"
                                        v-model="selectedUnpaidUsers"
                                        class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B] w-4 h-4"
                                    />
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ student.user_name }}</div>
                                        <div class="text-xs text-gray-600">{{ student.user_email }} - {{ student.name }}</div>
                                    </div>
                                </label>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <button
                                    @click="selectAllUnpaid"
                                    class="text-sm text-[#F15A2B] hover:text-[#BF3206] underline transition-colors"
                                >
                                    Ընտրել բոլորը
                                </button>
                                <button
                                    @click="selectedUnpaidUsers = []"
                                    class="text-sm text-gray-600 hover:text-gray-900 underline transition-colors"
                                >
                                    Մաքրել ընտրությունը
                                </button>
                            </div>
                        </div>
                        <div v-else class="text-center py-4 text-gray-500 text-sm">
                            <svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Բոլոր աշակերտները վճարել են այս ամիս:
                        </div>
                    </div>

                    <!-- Message Form -->
                    <div v-if="showMessageForm" class="mt-6 pt-6 border-t border-gray-200">
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-900">
                                <strong>{{ selectedUnpaidUsers.length }}</strong> ծնող(ներ) կստանան այս հաղորդագրությունը
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Թեմա *</label>
                                <input
                                    v-model="messageForm.subject"
                                    type="text"
                                    placeholder="Մուտքագրեք հաղորդագրության թեման..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Հաղորդագրություն *</label>
                                <textarea
                                    v-model="messageForm.message"
                                    rows="6"
                                    placeholder="Գրեք ձեր հաղորդագրությունը այստեղ..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#F15A2B] focus:ring-[#F15A2B] transition-colors"
                                ></textarea>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button
                                    @click="showMessageForm = false"
                                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors font-medium"
                                >
                                    Չեղարկել
                                </button>
                                <button
                                    @click="sendMessageToUnpaid"
                                    :disabled="!messageForm.subject || !messageForm.message || selectedUnpaidUsers.length === 0"
                                    class="px-4 py-2 bg-gradient-to-r from-[#F15A2B] to-[#BF3206] text-white font-semibold rounded-lg hover:from-[#BF3206] hover:to-[#8B2404] disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
                                >
                                    Ուղարկել հաղորդագրություն
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="mt-6 flex justify-end">
                        <button
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors font-medium"
                        >
                            Փակել
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Payment Modal -->
        <div v-if="showCashPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="closeCashPaymentModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add Cash Payment</h3>
                        <button @click="closeCashPaymentModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCashPayment">
                        <!-- Student Info (read-only) -->
                        <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-md">
                            <div class="text-sm font-semibold text-gray-900">{{ selectedStudentForPayment?.name }}</div>
                            <div class="text-xs text-gray-600 mt-1">
                                Parent: {{ selectedStudentForPayment?.user_name }} ({{ selectedStudentForPayment?.user_email }})
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ selectedStudentForPayment?.course_name }} - {{ selectedStudentForPayment?.branch_name }}
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
                                @click="closeCashPaymentModal"
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
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            course_id: '',
        }),
    },
});

const filters = ref({ ...props.filters });

let searchTimeout = null;
watch(() => filters.value.search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const applyFilters = () => {
    router.get(route('admin.groups.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const deleteGroup = (id) => {
    if (confirm('Դուք համոզված եք, որ ցանկանում եք ջնջել այս խումբը?')) {
        router.delete(route('admin.groups.destroy', id));
    }
};

// Modal state
const showModal = ref(false);
const selectedGroup = ref(null);
const modalStudents = ref([]);
const modalCourses = ref([]);
const modalBranches = ref([]);
const modalMessages = ref([]);
const showMessagesSection = ref(false);
const showMessageForm = ref(false);
const selectedUnpaidUsers = ref([]);
const messageForm = ref({
    subject: '',
    message: '',
});
const studentFilters = ref({
    search: '',
    course_id: '',
    branch_id: '',
    payment_status: '',
});

// Cash Payment Modal
const showCashPaymentModal = ref(false);
const selectedStudentForPayment = ref(null);
const cashPaymentForm = useForm({
    student_id: '',
    group_id: '',
    amount: '',
    currency: 'GEL',
    paid_at: new Date().toISOString().slice(0, 16),
    order_desc: '',
});

const viewClass = async (groupId) => {
    try {
        // Load all students without filters (we'll filter client-side)
        const response = await axios.get(route('admin.groups.show-students', groupId));
        
        selectedGroup.value = response.data.group;
        modalStudents.value = response.data.students;
        modalCourses.value = response.data.courses;
        modalBranches.value = response.data.branches;
        modalMessages.value = response.data.messages || [];
        showModal.value = true;
    } catch (error) {
        console.error('Error loading group students:', error);
        alert('Խմբի աշակերտները բեռնելը ձախողվեց:');
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedGroup.value = null;
    modalStudents.value = [];
    modalMessages.value = [];
    showMessageForm.value = false;
    selectedUnpaidUsers.value = [];
    messageForm.value = {
        subject: '',
        message: '',
    };
    studentFilters.value = {
        search: '',
        course_id: '',
        branch_id: '',
        payment_status: '',
    };
};

// Cash Payment Functions
const openCashPaymentModal = (student) => {
    selectedStudentForPayment.value = student;
    cashPaymentForm.student_id = student.id;
    cashPaymentForm.group_id = selectedGroup.value?.id || '';
    cashPaymentForm.amount = '';
    cashPaymentForm.currency = 'GEL';
    cashPaymentForm.paid_at = new Date().toISOString().slice(0, 16);
    cashPaymentForm.order_desc = '';
    cashPaymentForm.clearErrors();
    showCashPaymentModal.value = true;
};

const closeCashPaymentModal = () => {
    showCashPaymentModal.value = false;
    selectedStudentForPayment.value = null;
    cashPaymentForm.reset();
    cashPaymentForm.clearErrors();
};

const submitCashPayment = () => {
    if (!cashPaymentForm.student_id) {
        cashPaymentForm.setError('student_id', 'Please select a student');
        return;
    }
    
    cashPaymentForm.post(route('admin.payment-history.cash.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeCashPaymentModal();
            // Reload the group students to update payment status
            if (selectedGroup.value) {
                viewClass(selectedGroup.value.id);
            }
        },
    });
};

// Unpaid students
const unpaidStudents = computed(() => {
    const unpaid = filteredStudents.value.filter(student => !student.has_paid_this_month);
    // Get unique users (one per parent)
    const uniqueUsers = new Map();
    unpaid.forEach(student => {
        if (!uniqueUsers.has(student.user_id)) {
            uniqueUsers.set(student.user_id, student);
        }
    });
    return Array.from(uniqueUsers.values());
});

const isUnpaidSelected = (userId) => {
    return selectedUnpaidUsers.value.includes(userId);
};

const selectAllUnpaid = () => {
    selectedUnpaidUsers.value = unpaidStudents.value.map(student => student.user_id);
};

const openMessageForm = () => {
    if (selectedUnpaidUsers.value.length === 0) {
        selectAllUnpaid();
    }
    showMessageForm.value = true;
};

const sendMessageToUnpaid = async () => {
    if (!messageForm.value.subject || !messageForm.value.message || selectedUnpaidUsers.value.length === 0) {
        return;
    }

    try {
        const response = await axios.post(route('admin.messages.send-to-users'), {
            user_ids: selectedUnpaidUsers.value,
            subject: messageForm.value.subject,
            message: messageForm.value.message,
        });

        if (response.data.success) {
            alert(`Հաղորդագրությունը հաջողությամբ ուղարկվել է ${selectedUnpaidUsers.value.length} ծնող(ներ)ին!`);
            showMessageForm.value = false;
            selectedUnpaidUsers.value = [];
            messageForm.value = {
                subject: '',
                message: '',
            };
            // Reload messages
            if (selectedGroup.value) {
                viewClass(selectedGroup.value.id);
            }
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Հաղորդագրությունը ուղարկելը ձախողվեց: Խնդրում ենք կրկին փորձել:');
    }
};

// Watch student filters - no need to reload, we filter client-side

// Filter students in modal
const filteredStudents = computed(() => {
    let filtered = [...modalStudents.value];

    if (studentFilters.value.search) {
        const search = studentFilters.value.search.toLowerCase();
        filtered = filtered.filter(student => 
            student.name.toLowerCase().includes(search) ||
            (student.biradi && student.biradi.toLowerCase().includes(search)) ||
            student.user_name.toLowerCase().includes(search) ||
            student.user_email.toLowerCase().includes(search)
        );
    }

    if (studentFilters.value.course_id) {
        const courseId = Number(studentFilters.value.course_id);
        filtered = filtered.filter(student => Number(student.course_id) === courseId);
    }

    if (studentFilters.value.branch_id) {
        const branchId = Number(studentFilters.value.branch_id);
        filtered = filtered.filter(student => Number(student.branch_id) === branchId);
    }

    if (studentFilters.value.payment_status) {
        if (studentFilters.value.payment_status === 'paid') {
            filtered = filtered.filter(student => student.has_paid_this_month === true);
        } else if (studentFilters.value.payment_status === 'unpaid') {
            filtered = filtered.filter(student => student.has_paid_this_month === false);
        }
    }

    return filtered;
});

const clearStudentFilters = () => {
    studentFilters.value = {
        search: '',
        course_id: '',
        branch_id: '',
        payment_status: '',
    };
};

const exportToExcel = () => {
    // Prepare data for export
    const data = filteredStudents.value.map((student, index) => ({
        '#': index + 1,
        'Student Name': student.name,
        'Biradi': student.biradi || '',
        'Parent Name': student.user_name,
        'Parent Email': student.user_email,
        'Course': student.course_name,
        'Branch': student.branch_name,
        'Վճարում': student.has_paid_this_month ? 'Վճարված' : 'Չի վճարված',
        'Ներկա/Բացակա': '',
    }));

    // Convert to CSV
    const headers = Object.keys(data[0] || {});
    const csvContent = [
        headers.join(','),
        ...data.map(row => 
            headers.map(header => {
                const value = row[header] || '';
                // Escape commas and quotes
                return `"${String(value).replace(/"/g, '""')}"`;
            }).join(',')
        )
    ].join('\n');

    // Add BOM for UTF-8
    const BOM = '\uFEFF';
    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
    
    // Create download link
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `${selectedGroup.value?.name || 'class'}_students_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>


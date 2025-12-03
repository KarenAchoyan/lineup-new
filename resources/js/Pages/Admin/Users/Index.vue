<template>
    <AuthenticatedLayout>
        <Head title="Օգտատերերի կառավարում" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Գրանցված օգտատերեր և աշակերտների պրոֆիլներ</h2>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold">{{ users.length }}</span> {{ users.length === 1 ? 'օգտատեր' : 'օգտատեր' }} գտնված
                            </div>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <!-- Filters Section -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Ֆիլտրներ</h3>
                                <button
                                    @click="clearFilters"
                                    class="text-sm text-gray-600 hover:text-gray-900 underline"
                                >
                                    Մաքրել բոլորը
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Search -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Որոնում</label>
                                    <input
                                        v-model="filters.search"
                                        type="text"
                                        placeholder="Անուն կամ Email..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Course Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Դասընթաց</label>
                                    <select
                                        v-model="filters.course_id"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Բոլոր դասընթացները</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Branch Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Մասնաճյուղ</label>
                                    <select
                                        v-model="filters.branch_id"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Բոլոր մասնաճյուղերը</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Has Students Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ունի աշակերտներ</label>
                                    <select
                                        v-model="filters.has_students"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Բոլորը</option>
                                        <option value="yes">Ունի աշակերտներ</option>
                                        <option value="no">Չունի աշակերտներ</option>
                                    </select>
                                </div>

                                <!-- Student Type Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Սաների տեսակ</label>
                                    <select
                                        v-model="filters.student_type"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Բոլորը</option>
                                        <option value="new">Նոր սաներ</option>
                                        <option value="old">Հին սաներ</option>
                                    </select>
                                </div>

                                <!-- Date From -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ամսաթիվից</label>
                                    <input
                                        v-model="filters.date_from"
                                        @change="applyFilters"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Date To -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ամսաթիվը</label>
                                    <input
                                        v-model="filters.date_to"
                                        @change="applyFilters"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Sort By -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Տեսակավորել ըստ</label>
                                    <select
                                        v-model="filters.sort_by"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="created_at">Գրանցման ամսաթիվ</option>
                                        <option value="name">Անուն</option>
                                        <option value="email">Email</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Կարգ</label>
                                    <select
                                        v-model="filters.sort_order"
                                        @change="applyFilters"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="desc">Նվազման</option>
                                        <option value="asc">Աճման</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div
                                v-for="user in users"
                                :key="user.id"
                                class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                            >
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ user.name }}</h3>
                                            <span
                                                v-if="user.is_teacher"
                                                class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full"
                                            >
                                                Ուսուցիչ
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ user.email }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Գրանցված: {{ user.created_at }}</p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                            {{ user.students.length }} {{ user.students.length === 1 ? 'Աշակերտ' : 'Աշակերտ' }}
                                        </span>
                                        <button
                                            v-if="!user.is_teacher && user.students.length === 0"
                                            @click="assignTeacherRole(user)"
                                            :disabled="processingUser === user.id"
                                            class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 disabled:opacity-50 transition-colors"
                                        >
                                            {{ processingUser === user.id ? 'Մշակվում է...' : 'Նշանակել որպես ուսուցիչ' }}
                                        </button>
                                        <button
                                            v-else-if="user.is_teacher"
                                            @click="removeTeacherRole(user)"
                                            :disabled="processingUser === user.id"
                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 disabled:opacity-50 transition-colors"
                                        >
                                            {{ processingUser === user.id ? 'Մշակվում է...' : 'Հեռացնել ուսուցչի դերը' }}
                                        </button>
                                        <span
                                            v-else-if="user.students.length > 0"
                                            class="px-3 py-1 bg-gray-200 text-gray-600 text-xs rounded cursor-not-allowed"
                                            title="Չի կարելի նշանակել ուսուցչի դեր օգտատերերին, որոնք ունեն աշակերտներ"
                                        >
                                            Ունի աշակերտներ
                                        </span>
                                    </div>
                                </div>

                                <div v-if="user.students.length === 0" class="text-gray-500 text-sm italic">
                                    Դեռ աշակերտների պրոֆիլներ չկան:
                                </div>

                                <div v-else class="mt-4 space-y-3">
                                    <div
                                        v-for="student in user.students"
                                        :key="student.id"
                                        :class="[
                                            'rounded-lg p-4 border',
                                            student.has_groups 
                                                ? 'bg-gray-50 border-gray-100' 
                                                : 'bg-yellow-50 border-yellow-200'
                                        ]"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <h4 class="font-semibold text-gray-900">{{ student.name }}</h4>
                                                    <span
                                                        v-if="!student.has_groups"
                                                        class="px-2 py-1 bg-yellow-200 text-yellow-800 text-xs font-semibold rounded-full"
                                                        title="Այս սանը դեռ ոչ մի խմբում չէ"
                                                    >
                                                        Նոր սան
                                                    </span>
                                                    <span
                                                        v-else
                                                        class="px-2 py-1 bg-green-200 text-green-800 text-xs font-semibold rounded-full"
                                                        title="Այս սանը խմբ(եր)ում է"
                                                    >
                                                        {{ student.groups_count }} խումբ
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                                                    <div>
                                                        <span class="font-medium">Դասընթաց:</span> {{ student.course_name }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Մասնաճյուղ:</span> {{ student.branch_name }}
                                                    </div>
                                                    <div v-if="student.birthday" class="md:col-span-2">
                                                        <span class="font-medium">Ծննդյան օր:</span> {{ new Date(student.birthday).toLocaleDateString('hy-AM') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                @click="openGroupsModal(student)"
                                                class="ml-4 px-3 py-1 bg-[#F15A2B] text-white text-sm rounded hover:bg-[#BF3206] transition-colors"
                                            >
                                                Կառավարել խմբերը
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="users.length === 0" class="text-center py-12 text-gray-500">
                            Գրանցված օգտատերեր չեն գտնվել:
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groups Management Modal -->
        <Transition name="fade">
            <div
                v-if="showGroupsModal"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                @click="closeGroupsModal"
            >
                <div
                    class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Կառավարել {{ selectedStudent?.name }}-ի խմբերը
                            </h3>
                            <button
                                @click="closeGroupsModal"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto flex-1">
                        <div v-if="loadingGroups" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#F15A2B]"></div>
                            <p class="mt-2 text-gray-600">Բեռնվում են խմբերը...</p>
                        </div>

                        <div v-else-if="groups.length === 0" class="text-center py-8 text-gray-500">
                            Խմբեր չեն գտնվել:
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="group in groups"
                                :key="group.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                :class="group.is_selected ? 'bg-blue-50 border-blue-300' : ''"
                            >
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">{{ group.name }}</div>
                                    <div class="text-sm text-gray-600">{{ group.course_name }}</div>
                                </div>
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        :checked="group.is_selected"
                                        @change="toggleGroup(group.id)"
                                        class="rounded border-gray-300 text-[#F15A2B] focus:ring-[#F15A2B] w-5 h-5"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">
                                        {{ group.is_selected ? 'Խմբում է' : 'Ավելացնել խմբին' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-6 border-t border-gray-200 flex justify-end gap-4">
                        <button
                            @click="closeGroupsModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Չեղարկել
                        </button>
                        <button
                            @click="saveGroups"
                            :disabled="savingGroups"
                            class="px-4 py-2 bg-[#F15A2B] text-white rounded-md hover:bg-[#BF3206] disabled:opacity-50 transition-colors"
                        >
                            {{ savingGroups ? 'Պահպանվում է...' : 'Պահպանել փոփոխությունները' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const props = defineProps({
    users: {
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
    filters: {
        type: Object,
        default: () => ({
            search: '',
            course_id: '',
            branch_id: '',
            has_students: '',
            date_from: '',
            date_to: '',
            sort_by: 'created_at',
            sort_order: 'desc',
        }),
    },
});

const filters = ref({ ...props.filters });

// Debounce search input
let searchTimeout = null;
watch(() => filters.value.search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const applyFilters = () => {
    router.get(route('admin.users.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = {
        search: '',
        course_id: '',
        branch_id: '',
        has_students: '',
        student_type: '',
        date_from: '',
        date_to: '',
        sort_by: 'created_at',
        sort_order: 'desc',
    };
    applyFilters();
};

// Groups Modal
const showGroupsModal = ref(false);
const selectedStudent = ref(null);
const groups = ref([]);
const loadingGroups = ref(false);
const savingGroups = ref(false);

// Teacher Role Management
const processingUser = ref(null);

const openGroupsModal = async (student) => {
    selectedStudent.value = student;
    showGroupsModal.value = true;
    loadingGroups.value = true;
    groups.value = [];

    try {
        const response = await axios.get(route('admin.users.students.groups', student.id));
        groups.value = response.data.groups;
        selectedStudent.value = response.data.student;
    } catch (error) {
        console.error('Error loading groups:', error);
        alert('Խմբերը բեռնելը ձախողվեց: Խնդրում ենք կրկին փորձել:');
    } finally {
        loadingGroups.value = false;
    }
};

const closeGroupsModal = () => {
    showGroupsModal.value = false;
    selectedStudent.value = null;
    groups.value = [];
};

const toggleGroup = (groupId) => {
    const group = groups.value.find(g => g.id === groupId);
    if (group) {
        group.is_selected = !group.is_selected;
    }
};

const saveGroups = async () => {
    if (!selectedStudent.value) return;

    savingGroups.value = true;
    const selectedGroupIds = groups.value
        .filter(g => g.is_selected)
        .map(g => g.id);

    try {
        await axios.post(route('admin.users.students.groups.update', selectedStudent.value.id), {
            groups: selectedGroupIds,
        });
        
        closeGroupsModal();
        // Refresh the page to show updated data
        router.reload({ only: ['users'] });
    } catch (error) {
        console.error('Error saving groups:', error);
        alert('Խմբերը պահպանելը ձախողվեց: Խնդրում ենք կրկին փորձել:');
    } finally {
        savingGroups.value = false;
    }
};

const assignTeacherRole = async (user) => {
    if (!confirm(`Դուք համոզված եք, որ ցանկանում եք նշանակել ուսուցչի դեր ${user.name}-ին?\n\nՆշում: Օգտատերը չպետք է ունենա աշակերտներ:`)) {
        return;
    }

    processingUser.value = user.id;
    try {
        const response = await axios.post(route('admin.users.assign-teacher-role', user.id));
        
        if (response.data.success) {
            // Reload the page to get updated data
            router.reload({ only: ['users'] });
        } else {
            alert(response.data.message || 'Ուսուցչի դեր նշանակելը ձախողվեց:');
            processingUser.value = null;
        }
    } catch (error) {
        console.error('Error assigning teacher role:', error);
        const message = error.response?.data?.message || 'Ուսուցչի դեր նշանակելը ձախողվեց: Խնդրում ենք կրկին փորձել:';
        alert(message);
        processingUser.value = null;
    }
};

const removeTeacherRole = async (user) => {
    if (!confirm(`Դուք համոզված եք, որ ցանկանում եք հեռացնել ուսուցչի դերը ${user.name}-ից?`)) {
        return;
    }

    processingUser.value = user.id;
    try {
        const response = await axios.post(route('admin.users.remove-teacher-role', user.id));
        
        if (response.data.success) {
            // Reload the page to get updated data
            router.reload({ only: ['users'] });
        } else {
            alert(response.data.message || 'Ուսուցչի դերը հեռացնելը ձախողվեց:');
            processingUser.value = null;
        }
    } catch (error) {
        console.error('Error removing teacher role:', error);
        const message = error.response?.data?.message || 'Ուսուցչի դերը հեռացնելը ձախողվեց: Խնդրում ենք կրկին փորձել:';
        alert(message);
        processingUser.value = null;
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>


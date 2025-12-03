<template>
    <AuthenticatedLayout>
        <Head title="Events Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-3xl font-bold bg-gradient-to-r from-[#F15A2B] to-[#BF3206] bg-clip-text text-transparent">Միջոցառումների կառավարում</h2>
                                <p class="text-gray-600 mt-1">Կառավարեք միջոցառումները</p>
                            </div>
                            <Link
                                :href="route('admin.events.create')"
                                class="bg-gradient-to-r from-[#F15A2B] to-[#BF3206] hover:from-[#BF3206] hover:to-[#8B2404] text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Ավելացնել նոր միջոցառում</span>
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

                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Նկար</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ամսաթիվ</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Հերթականություն</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Կարգավիճակ</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Գործողություններ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="event in events" :key="event.id" class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-purple-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div v-if="event.avatar" class="w-16 h-16 rounded-lg overflow-hidden shadow-sm">
                                                <img :src="`/storage/${event.avatar}`" alt="Event" class="w-full h-full object-cover" />
                                            </div>
                                            <span v-else class="text-gray-400 text-sm">Նկար չկա</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ new Date(event.event_date).toLocaleDateString('hy-AM') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ event.order }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full',
                                                event.is_active 
                                                    ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 shadow-sm' 
                                                    : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 shadow-sm'
                                            ]">
                                                <span :class="[
                                                    'w-2 h-2 rounded-full mr-2',
                                                    event.is_active ? 'bg-green-500' : 'bg-gray-400'
                                                ]"></span>
                                                {{ event.is_active ? 'Ակտիվ' : 'Անակտիվ' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-3">
                                                <Link 
                                                    :href="route('admin.events.edit', event.id)" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#F15A2B] rounded-lg font-medium transition-colors text-xs"
                                                >
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Խմբագրել
                                                </Link>
                                                <button 
                                                    @click="deleteItem(event.id)" 
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
                            <div v-if="events.length === 0" class="text-center py-16">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500 text-lg font-medium">Միջոցառումներ չեն գտնվել</p>
                                <p class="text-gray-400 text-sm mt-2">Ստեղծեք ձեր առաջին միջոցառումը</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    events: Array,
});

const deleteItem = (id) => {
    if (confirm('Դուք համոզված եք, որ ցանկանում եք ջնջել այս միջոցառումը?')) {
        router.delete(route('admin.events.destroy', id));
    }
};
</script>







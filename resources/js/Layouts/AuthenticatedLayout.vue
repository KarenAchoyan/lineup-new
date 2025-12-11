<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);

const isActive = (routeName) => {
    return route().current(routeName);
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 flex">
        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed top-0 left-0 z-40 h-screen transition-transform duration-300 ease-in-out',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0 lg:relative lg:z-auto lg:h-screen lg:flex-shrink-0'
            ]"
            class="w-64 bg-gradient-to-b from-[#1a1a2e] via-[#16213e] to-[#0f3460] shadow-2xl"
        >
            <div class="flex flex-col h-full lg:h-screen">
                <!-- Logo -->
                <div class="flex items-center justify-between p-6 border-b border-white/10">
                    <Link :href="route('dashboard')" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#F15A2B] to-[#BF3206] rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
    <div>
                            <h1 class="text-xl font-bold text-white">Ադմին Պանել</h1>
                            <p class="text-xs text-white/70">Կառավարման համակարգ</p>
                        </div>
                                </Link>
                    <button 
                        @click="sidebarOpen = false"
                        class="lg:hidden text-white/70 hover:text-white transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                            </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                    <!-- Dashboard -->
                    <Link 
                        :href="route('dashboard')" 
                        :class="[
                            'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                            isActive('dashboard') 
                                ? 'bg-gradient-to-r from-[#F15A2B] to-[#BF3206] text-white shadow-lg shadow-[#F15A2B]/30' 
                                : 'text-white/80 hover:bg-white/10 hover:text-white'
                        ]"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium">Գլխավոր</span>
                    </Link>
                                
                                <!-- Admin Navigation -->
                                <template v-if="$page.props.auth.user?.is_admin">
                        <div class="pt-4 pb-2">
                            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Կառավարում</p>
                        </div>
                        
                        <Link 
                            :href="route('admin.archive.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.archive.*') 
                                    ? 'bg-gradient-to-r from-purple-500 to-purple-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span class="font-medium">Արխիվ</span>
                        </Link>

                        <Link 
                            :href="route('admin.branches.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.branches.*') 
                                    ? 'bg-gradient-to-r from-blue-500 to-blue-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium">Մասնաճյուղեր</span>
                        </Link>

                        <Link 
                            :href="route('admin.courses.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.courses.*') 
                                    ? 'bg-gradient-to-r from-green-500 to-green-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="font-medium">Դասընթացներ</span>
                        </Link>

                        <Link 
                            :href="route('admin.groups.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.groups.*') 
                                    ? 'bg-gradient-to-r from-lime-500 to-lime-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">Խմբեր</span>
                        </Link>

                        <Link 
                            :href="route('admin.users.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.users.*') 
                                    ? 'bg-gradient-to-r from-indigo-500 to-indigo-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">Օգտատերեր</span>
                        </Link>

                        <Link 
                            :href="route('admin.staff.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.staff.*') 
                                    ? 'bg-gradient-to-r from-pink-500 to-pink-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">Աշխատակազմ</span>
                        </Link>

                        <div class="pt-4 pb-2">
                            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Բովանդակություն</p>
                        </div>

                        <Link 
                            :href="route('admin.news.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.news.*') 
                                    ? 'bg-gradient-to-r from-indigo-500 to-indigo-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span class="font-medium">Լուրեր</span>
                        </Link>

                        <Link 
                            :href="route('admin.events.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.events.*') 
                                    ? 'bg-gradient-to-r from-red-500 to-red-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Միջոցառումներ</span>
                        </Link>

                        <Link 
                            :href="route('admin.galleries.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.galleries.*') 
                                    ? 'bg-gradient-to-r from-teal-500 to-teal-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Պատկերասրահ</span>
                        </Link>

                        <Link 
                            :href="route('admin.about.edit')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.about.*') 
                                    ? 'bg-gradient-to-r from-orange-500 to-orange-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">Մեր մասին</span>
                        </Link>

                        <Link 
                            :href="route('admin.privacy.edit')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.privacy.*') 
                                    ? 'bg-gradient-to-r from-purple-500 to-purple-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span class="font-medium">Գաղտնիության քաղաքականություն</span>
                        </Link>

                        <Link 
                            :href="route('admin.banner.edit')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.banner.*') 
                                    ? 'bg-gradient-to-r from-amber-500 to-amber-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Բաններ</span>
                        </Link>

                        <div class="pt-4 pb-2">
                            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Ծառայություններ</p>
                        </div>

                        <Link 
                            :href="route('admin.attendance.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.attendance.*') 
                                    ? 'bg-gradient-to-r from-sky-500 to-sky-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span class="font-medium">Ներկայություն</span>
                        </Link>

                        <Link 
                            :href="route('admin.messages.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.messages.*') 
                                    ? 'bg-gradient-to-r from-cyan-500 to-cyan-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Հաղորդագրություններ</span>
                        </Link>

                        <Link 
                            :href="route('admin.payment-history.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.payment-history.*') 
                                    ? 'bg-gradient-to-r from-emerald-500 to-emerald-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">Վճարումներ</span>
                        </Link>

                        <Link 
                            :href="route('admin.additional-payments.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.additional-payments.*') 
                                    ? 'bg-gradient-to-r from-purple-500 to-purple-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">Կողմնակի վճարումներ</span>
                        </Link>

                        <Link 
                            :href="route('admin.active-users.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.active-users.*') 
                                    ? 'bg-gradient-to-r from-yellow-500 to-yellow-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="font-medium">Ակտիվ երիտասարդներ</span>
                        </Link>

                        <Link 
                            :href="route('admin.volunteering-submissions.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.volunteering-submissions.*') 
                                    ? 'bg-gradient-to-r from-cyan-500 to-cyan-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">Կամավորություն</span>
                        </Link>

                        <Link 
                            :href="route('admin.collaboration-submissions.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('admin.collaboration-submissions.*') 
                                    ? 'bg-gradient-to-r from-violet-500 to-violet-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">Համագործակցություն</span>
                        </Link>
                                </template>
                                
                                <!-- Teacher Navigation -->
                                <template v-if="$page.props.auth.user?.is_teacher">
                        <div class="pt-4 pb-2">
                            <p class="px-4 text-xs font-semibold text-white/50 uppercase tracking-wider">Դասավանդող</p>
                        </div>

                        <Link 
                            :href="route('teacher.groups.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('teacher.groups.*') 
                                    ? 'bg-gradient-to-r from-[#F15A2B] to-[#BF3206] text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">Իմ խմբերը</span>
                        </Link>

                        <Link 
                            :href="route('teacher.attendance.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('teacher.attendance.*') 
                                    ? 'bg-gradient-to-r from-sky-500 to-sky-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span class="font-medium">Ներկայություն</span>
                        </Link>

                        <Link 
                            :href="route('teacher.messages.index')" 
                            :class="[
                                'flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200',
                                isActive('teacher.messages.*') 
                                    ? 'bg-gradient-to-r from-cyan-500 to-cyan-700 text-white shadow-lg' 
                                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                            ]"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                            <span class="font-medium">Հաղորդագրություններ</span>
                        </Link>
                                    </template>
                </nav>

                <!-- User Profile -->
                <div class="p-4 border-t border-white/10">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all duration-200">
                                <div class="w-10 h-10 bg-gradient-to-br from-[#F15A2B] to-[#BF3206] rounded-full flex items-center justify-center font-semibold text-white shadow-lg">
                                    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                                <div class="flex-1 text-left">
                                    <p class="text-sm font-medium">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-white/60">{{ $page.props.auth.user.email }}</p>
                        </div>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">Իմ պրոֆիլը</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Դուրս գալ</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
                    </div>
        </aside>

        <!-- Overlay for mobile -->
        <div 
            v-if="sidebarOpen" 
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-lg border-b border-gray-200 shadow-sm flex-shrink-0">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <button 
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    
                    <div class="flex-1"></div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:block text-sm text-gray-600">
                            <span class="font-medium">{{ $page.props.auth.user.name }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-[#F15A2B] to-[#BF3206] bg-clip-text text-transparent mb-2">
                        Ադմինիստրատորի վահանակ
                    </h1>
                    <p class="text-gray-600 text-lg">Կառավարեք կայքի բովանդակությունը և կարգավորումները</p>
                </div>

                <!-- Admin Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Archive -->
                    <Link
                        :href="route('admin.archive.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-purple-600 group-hover:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Արխիվ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել լուսանկարները, տեսանյութերը և լուրերը</p>
                        </div>
                    </Link>

                    <!-- Branches -->
                    <Link
                        :href="route('admin.branches.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Մասնաճյուղեր</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել մասնաճյուղերի գտնվելու վայրերը</p>
                        </div>
                    </Link>

                    <!-- Courses -->
                    <Link
                        :href="route('admin.courses.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-green-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Դասընթացներ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել դասընթացները և բաժինները</p>
                        </div>
                    </Link>

                    <!-- About Page -->
                    <Link
                        :href="route('admin.about.edit')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-orange-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-orange-600 group-hover:text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Մեր մասին</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Խմբագրել մեր մասին բաժնի բովանդակությունը</p>
                        </div>
                    </Link>

                    <!-- Banner -->
                    <Link
                        :href="route('admin.banner.edit')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-amber-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-amber-600 group-hover:text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Բաններ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Խմբագրել բանների պատկերը և տեքստը</p>
                        </div>
                    </Link>

                    <!-- Staff -->
                    <Link
                        :href="route('admin.staff.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-500 to-pink-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-pink-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-pink-600 group-hover:text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Աշխատակազմ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել աշխատակազմի անդամներին</p>
                        </div>
                    </Link>

                    <!-- News -->
                    <Link
                        :href="route('admin.news.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-indigo-600 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Լուրեր</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել լրատվական հոդվածները</p>
                        </div>
                    </Link>

                    <!-- Events -->
                    <Link
                        :href="route('admin.events.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-red-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-red-600 group-hover:text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Միջոցառումներ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել օրացույցի միջոցառումները</p>
                        </div>
                    </Link>

                    <!-- Galleries -->
                    <Link
                        :href="route('admin.galleries.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-teal-500 to-teal-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-teal-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-teal-600 group-hover:text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Պատկերասրահ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել նվաճումների պատկերները</p>
                        </div>
                    </Link>

                    <!-- Active Users -->
                    <Link
                        :href="route('admin.active-users.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-yellow-500 to-yellow-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-yellow-600 group-hover:text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Ակտիվ երիտասարդներ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել ակտիվ երիտասարդներին</p>
                        </div>
                    </Link>

                    <!-- Registered Users & Students -->
                    <Link
                        :href="route('admin.users.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-indigo-600 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Օգտատերեր</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Դիտել գրանցված օգտատերերին և պրոֆիլները</p>
                        </div>
                    </Link>

                    <!-- Payment History -->
                    <Link
                        :href="route('admin.payment-history.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-emerald-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-emerald-600 group-hover:text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Վճարումներ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Դիտել վճարումները և հետևել չվճարվածներին</p>
                        </div>
                    </Link>

                    <!-- Additional Payments -->
                    <Link
                        :href="route('admin.additional-payments.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-purple-600 group-hover:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Կողմնակի վճարումներ</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել կողմնակի վճարումները</p>
                        </div>
                    </Link>

                    <!-- Volunteering Submissions -->
                    <Link
                        :href="route('admin.volunteering-submissions.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-cyan-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-cyan-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-cyan-600 group-hover:text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Կամավորություն</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Դիտել հայտերը</p>
                        </div>
                    </Link>

                    <!-- Collaboration Submissions -->
                    <Link
                        :href="route('admin.collaboration-submissions.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-violet-500 to-violet-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-violet-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-violet-600 group-hover:text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Համագործակցություն</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Դիտել հայտերը</p>
                        </div>
                    </Link>

                    <!-- Groups -->
                    <Link
                        :href="route('admin.groups.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-lime-500 to-lime-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-lime-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-lime-600 group-hover:text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Խմբեր</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Կառավարել աշակերտների խմբերը</p>
                        </div>
                    </Link>

                    <!-- Attendance -->
                    <Link
                        :href="route('admin.attendance.index')"
                        class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-sky-500 to-sky-700 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative p-8">
                            <div class="w-16 h-16 bg-sky-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-white transition-colors">
                                <svg class="w-8 h-8 text-sky-600 group-hover:text-sky-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-white transition-colors">Ներկայություն</h3>
                            <p class="text-gray-600 text-sm group-hover:text-white/90 transition-colors">Նշել ներկա/բացակա</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

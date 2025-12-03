<template>
    <div class="min-h-screen flex flex-col bg-[#232222]">
        <!-- Header -->
        <header class="absolute top-0 left-0 w-full z-40 backdrop-blur-md bg-[#232222]/80">
            <div class="container m-auto h-[80px] sm:h-[100px] md:h-[136px] flex items-center justify-between px-3 sm:px-4 md:px-6">
                <!-- Logo -->
                <div class="logo-parent flex-shrink-0">
                    <Link :href="route('home')" class="block transition-all duration-300 hover:scale-110 hover:drop-shadow-[0_0_15px_rgba(241,90,43,0.5)]">
                        <img src="/logo.png" alt="Logo" class="h-[50px] sm:h-[70px] md:h-[111px] w-auto object-contain transition-all duration-300" />
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <nav class="menu-parent hidden lg:flex flex-1 justify-center mx-4">
                    <div class="w-[95%] m-auto h-[55px] bg-gradient-to-r from-[#434343] via-[#4D4C4C] to-[#434343] rounded-full relative shadow-[0_4px_20px_rgba(0,0,0,0.3)] border border-[#5D5D5D]/50">
                        <ul class="flex h-full items-center justify-evenly relative">
                            <li class="text-[20px] text-white hover:text-[#F15A2B] hover:cursor-pointer relative group">
                                <Link :href="route('about')" class="transition-all duration-300 relative px-3 py-2 rounded-lg group-hover:bg-white/5">
                                    {{ t('about_us') }}
                                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#F15A2B] transition-all duration-300 group-hover:w-full"></span>
                                </Link>
                            </li>
                            <li class="text-[20px] text-white hover:text-[#F15A2B] hover:cursor-pointer relative group">
                                <Link :href="route('archive')" class="transition-all duration-300 relative px-3 py-2 rounded-lg group-hover:bg-white/5">
                                    {{ t('archive') }}
                                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#F15A2B] transition-all duration-300 group-hover:w-full"></span>
                                </Link>
                            </li>
                            <li class="text-[20px] text-white hover:text-[#F15A2B] hover:cursor-pointer relative dropdown group">
                                <span class="cursor-pointer flex items-center gap-1 transition-all duration-300 relative px-3 py-2 rounded-lg group-hover:bg-white/5">
                                    {{ t('sections') }}
                                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#F15A2B] transition-all duration-300 group-hover:w-full"></span>
                                </span>
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-56 bg-[#211d1dfc] backdrop-blur-md text-white rounded-2xl shadow-2xl p-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 origin-top border border-[#4D4C4C]/50">
                                    <ul class="space-y-1">
                                        <li v-for="course in courses" :key="course.id">
                                            <Link :href="route('sections.show', course.slug)" class="block px-4 py-2.5 text-sm rounded-lg hover:bg-[#4D4C4C] hover:text-[#F15A2B] transition-all duration-200 transform hover:translate-x-1">
                                                {{ course.name }}
                                            </Link>
                                        </li>
                                        <li v-if="courses.length === 0" class="px-4 py-2.5 text-sm text-gray-400">
                                            {{ t('no_data') }}
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="text-[20px] text-white hover:text-[#F15A2B] hover:cursor-pointer relative dropdown group">
                                <span class="cursor-pointer flex items-center gap-1 transition-all duration-300 relative px-3 py-2 rounded-lg group-hover:bg-white/5">
                                    {{ t('support_us') }}
                                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#F15A2B] transition-all duration-300 group-hover:w-full"></span>
                                </span>
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-56 bg-[#211d1dfc] backdrop-blur-md text-white rounded-2xl shadow-2xl p-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 origin-top border border-[#4D4C4C]/50">
                                    <ul class="space-y-1">
                                        <li>
                                            <Link :href="route('volunteering')" class="block px-4 py-2.5 text-sm rounded-lg hover:bg-[#4D4C4C] hover:text-[#F15A2B] transition-all duration-200 transform hover:translate-x-1">
                                                {{ t('volunteering') }}
                                            </Link>
                                        </li>
                                        <li>
                                            <Link :href="route('collaboration')" class="block px-4 py-2.5 text-sm rounded-lg hover:bg-[#4D4C4C] hover:text-[#F15A2B] transition-all duration-200 transform hover:translate-x-1">
                                                {{ t('collaboration') }}
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Actions (Login/Logout, User Icon, Language Switcher, Mobile Menu) -->
                <div class="actions-parent flex items-center gap-2 sm:gap-3 md:gap-4">
                    <!-- Login Button (if not authenticated) -->
                    <Link 
                        v-if="!$page.props.auth?.user"
                        :href="route('login')"
                        class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#F15A2B] to-[#BF3206] text-white rounded-full shadow-[0_4px_15px_rgba(241,90,43,0.3)] hover:shadow-[0_6px_25px_rgba(241,90,43,0.5)] transition-all duration-300 hover:scale-105 font-medium text-sm sm:text-base border border-[#F15A2B]/50"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        {{ t('login') }}
                    </Link>

                    <!-- Logout Button (if authenticated) -->
                    <Link 
                        v-if="$page.props.auth?.user"
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#434343] to-[#3a3a3a] text-white rounded-full shadow-[0_4px_15px_rgba(0,0,0,0.3)] hover:shadow-[0_6px_25px_rgba(241,90,43,0.4)] transition-all duration-300 hover:scale-105 hover:bg-gradient-to-r hover:from-[#F15A2B] hover:to-[#BF3206] font-medium text-sm sm:text-base border border-[#5D5D5D]/50 hover:border-[#F15A2B]/50"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {{ t('logout') }}
                    </Link>

                    <!-- User Icon (if authenticated) -->
                    <div v-if="$page.props.auth?.user" class="relative w-[45px] h-[45px] sm:w-[55px] sm:h-[55px] md:w-[65px] md:h-[65px] bg-gradient-to-br from-[#434343] to-[#3a3a3a] rounded-full flex items-center justify-center shadow-[0_4px_15px_rgba(0,0,0,0.3)] hover:shadow-[0_6px_25px_rgba(241,90,43,0.4)] transition-all duration-300 hover:scale-110 hover:bg-gradient-to-br hover:from-[#F15A2B] hover:to-[#BF3206] group border border-[#5D5D5D]/50">
                        <Link :href="route('profile.edit')" class="text-white group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Language Switcher -->
                        <LanguageSwitcher />

                    <!-- Mobile Menu Button -->
                    <button 
                        @click.stop="mobileMenuOpen = !mobileMenuOpen" 
                        class="lg:hidden relative w-[45px] h-[45px] sm:w-[55px] sm:h-[55px] md:w-[65px] md:h-[65px] bg-gradient-to-br from-[#434343] to-[#3a3a3a] rounded-full flex items-center justify-center shadow-[0_4px_15px_rgba(0,0,0,0.3)] hover:shadow-[0_6px_25px_rgba(241,90,43,0.4)] transition-all duration-300 hover:scale-110 hover:bg-gradient-to-br hover:from-[#F15A2B] hover:to-[#BF3206] border border-[#5D5D5D]/50 active:scale-95 z-50"
                        aria-label="Toggle menu"
                        type="button"
                    >
                        <svg v-if="!mobileMenuOpen" class="w-5 h-5 sm:w-6 sm:h-6 text-white transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="w-5 h-5 sm:w-6 sm:h-6 text-white transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Mobile Menu Drawer -->
        <Transition name="slide">
            <div v-if="mobileMenuOpen" class="fixed inset-0 z-[9999] lg:hidden">
                <!-- Backdrop -->
                <div 
                    class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity duration-300" 
                    @click="mobileMenuOpen = false"
                ></div>
                
                <!-- Menu Panel -->
                <div 
                    class="fixed right-0 top-0 h-full w-[320px] max-w-[85vw] bg-gradient-to-b from-[#2D2D2D] via-[#232222] to-[#1a1a1a] shadow-2xl border-l-2 border-[#BF3206] overflow-hidden z-[10000]"
                    @click.stop
                >
                    <div class="h-full flex flex-col overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-[#4D4C4C]/50 bg-[#2D2D2D]/50">
                            <Link :href="route('home')" @click="mobileMenuOpen = false" class="flex-shrink-0">
                                <img src="/logo.png" alt="Logo" class="h-10 sm:h-12 transition-transform hover:scale-105" />
                            </Link>
                            <button 
                                @click="mobileMenuOpen = false" 
                                class="text-white hover:text-[#F15A2B] transition-all duration-300 w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 active:scale-95"
                                aria-label="Close menu"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- User Profile Section (if authenticated) -->
                        <div v-if="$page.props.auth?.user" class="p-4 border-b border-[#4D4C4C]/50 bg-[#2D2D2D]/30">
                            <Link 
                                :href="route('profile.edit')" 
                                @click="mobileMenuOpen = false"
                                class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-all duration-300 group"
                            >
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#F15A2B] to-[#BF3206] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold text-sm truncate group-hover:text-[#F15A2B] transition-colors">
                                        {{ $page.props.auth.user.name || 'Profile' }}
                                    </p>
                                    <p class="text-gray-400 text-xs truncate">View Profile</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-[#F15A2B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>

                        <!-- Navigation Menu -->
                        <nav class="flex-1 overflow-y-auto py-4 px-3">
                            <div class="space-y-1">
                                <Link 
                                    :href="route('about')" 
                                    @click="mobileMenuOpen = false"
                                    class="flex items-center gap-3 text-base text-white hover:text-[#F15A2B] hover:bg-white/10 py-3 px-4 rounded-lg transition-all duration-300 font-medium group active:scale-95"
                                >
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#F15A2B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ t('about_us') }}</span>
                                </Link>
                                
                                <Link 
                                    :href="route('archive')" 
                                    @click="mobileMenuOpen = false"
                                    class="flex items-center gap-3 text-base text-white hover:text-[#F15A2B] hover:bg-white/10 py-3 px-4 rounded-lg transition-all duration-300 font-medium group active:scale-95"
                                >
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-[#F15A2B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span>{{ t('archive') }}</span>
                                </Link>
                                
                                <!-- Sections Dropdown -->
                                <div>
                                    <button 
                                        @click="sectionsOpen = !sectionsOpen"
                                        class="w-full flex items-center justify-between gap-3 text-base text-white hover:text-[#F15A2B] hover:bg-white/10 py-3 px-4 rounded-lg transition-all duration-300 font-medium group active:scale-95"
                                    >
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#F15A2B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span>{{ t('sections') }}</span>
                                        </div>
                                        <svg 
                                            class="w-5 h-5 text-gray-400 transition-transform duration-300" 
                                            :class="{ 'rotate-180': sectionsOpen }" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <Transition name="expand">
                                        <div v-if="sectionsOpen" class="pl-4 ml-4 mt-1 space-y-1 border-l-2 border-[#F15A2B]/30">
                                            <Link
                                                v-for="course in courses"
                                                :key="course.id"
                                                :href="route('sections.show', course.slug)"
                                                @click="mobileMenuOpen = false"
                                                class="block text-sm text-gray-300 hover:text-[#F15A2B] hover:bg-white/10 py-2.5 px-4 rounded-lg transition-all duration-300 active:scale-95"
                                            >
                                                {{ course.name }}
                                            </Link>
                                            <div v-if="courses.length === 0" class="block text-sm text-gray-400 py-2.5 px-4">
                                                {{ t('no_data') }}
                                            </div>
                                        </div>
                                    </Transition>
                                </div>
                                
                                <!-- Support Us Dropdown -->
                                <div>
                                    <button 
                                        @click="supportsOpen = !supportsOpen"
                                        class="w-full flex items-center justify-between gap-3 text-base text-white hover:text-[#F15A2B] hover:bg-white/10 py-3 px-4 rounded-lg transition-all duration-300 font-medium group active:scale-95"
                                    >
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-[#F15A2B] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <span>{{ t('support_us') }}</span>
                                        </div>
                                        <svg 
                                            class="w-5 h-5 text-gray-400 transition-transform duration-300" 
                                            :class="{ 'rotate-180': supportsOpen }" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <Transition name="expand">
                                        <div v-if="supportsOpen" class="pl-4 ml-4 mt-1 space-y-1 border-l-2 border-[#F15A2B]/30">
                                            <Link 
                                                :href="route('volunteering')" 
                                                @click="mobileMenuOpen = false"
                                                class="block text-sm text-gray-300 hover:text-[#F15A2B] hover:bg-white/10 py-2.5 px-4 rounded-lg transition-all duration-300 active:scale-95"
                                            >
                                                {{ t('volunteering') }}
                                            </Link>
                                            <Link 
                                                :href="route('collaboration')" 
                                                @click="mobileMenuOpen = false"
                                                class="block text-sm text-gray-300 hover:text-[#F15A2B] hover:bg-white/10 py-2.5 px-4 rounded-lg transition-all duration-300 active:scale-95"
                                            >
                                                {{ t('collaboration') }}
                                            </Link>
                                        </div>
                                    </Transition>
                                </div>
                            </div>
                        </nav>

                        <!-- Login/Logout Buttons -->
                        <div class="p-4 border-t border-[#4D4C4C]/50">
                            <!-- Login Button (if not authenticated) -->
                            <Link 
                                v-if="!$page.props.auth?.user"
                                :href="route('login')"
                                @click="mobileMenuOpen = false"
                                class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-[#F15A2B] to-[#BF3206] text-white rounded-lg shadow-[0_4px_15px_rgba(241,90,43,0.3)] hover:shadow-[0_6px_25px_rgba(241,90,43,0.5)] transition-all duration-300 font-medium active:scale-95 mb-3"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                {{ t('login') }}
                            </Link>

                            <!-- Logout Button (if authenticated) -->
                            <Link 
                                v-if="$page.props.auth?.user"
                                :href="route('logout')"
                                method="post"
                                as="button"
                                @click="mobileMenuOpen = false"
                                class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-[#434343] to-[#3a3a3a] text-white rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.3)] hover:shadow-[0_6px_25px_rgba(241,90,43,0.4)] transition-all duration-300 hover:bg-gradient-to-r hover:from-[#F15A2B] hover:to-[#BF3206] font-medium active:scale-95 mb-3"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ t('logout') }}
                            </Link>

                            <!-- Footer with Language Switcher -->
                            <div class="flex items-center justify-between pt-3 border-t border-[#4D4C4C]/50">
                                <span class="text-sm text-gray-400">{{ t('language') || 'Language' }}</span>
                                <div class="flex-shrink-0">
                                    <LanguageSwitcher />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="relative w-full bg-gradient-to-b from-[#2D2D2D] via-[#232222] to-[#1a1a1a] border-t-2 border-[#BF3206] pt-[50px] pb-6 overflow-hidden">
            <!-- Decorative gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#F15A2B]/5 to-transparent pointer-events-none"></div>
            
            <div class="container mx-auto flex flex-col md:flex-row items-center md:items-start text-center md:text-left px-4 sm:px-6 relative z-10">
                <!-- Logo & Social Icons -->
                <div class="w-full md:w-1/3 mb-6 md:mb-0 flex flex-col items-center md:items-start">
                    <Link :href="route('home')" class="block mb-4 transition-all duration-300 hover:scale-110 hover:drop-shadow-[0_0_15px_rgba(241,90,43,0.5)]">
                        <img src="/logo.png" alt="Logo" class="h-12 sm:h-16 md:h-24 w-auto" />
                    </Link>
                    <ul class="flex space-x-3 sm:space-x-4 mt-2">
                        <li>
                            <a href="https://www.facebook.com/share/16T2PFUeuZ/?mibextid=wwXIfr" target="_blank" class="block w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 hover:bg-[#F15A2B] flex items-center justify-center transition-all duration-300 hover:scale-125 hover:rotate-12 shadow-lg hover:shadow-[#F15A2B]/50">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/lineup_akhalkalaki?igsh=MTN4MHd3ZjltNHBoNg==" target="_blank" class="block w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 hover:bg-[#F15A2B] flex items-center justify-center transition-all duration-300 hover:scale-125 hover:rotate-12 shadow-lg hover:shadow-[#F15A2B]/50">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://youtube.com/@lineup3927?si=pd_gZredsOgxYzpl" target="_blank" class="block w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/10 hover:bg-[#F15A2B] flex items-center justify-center transition-all duration-300 hover:scale-125 hover:rotate-12 shadow-lg hover:shadow-[#F15A2B]/50">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="w-full md:w-1/3 mb-6 md:mb-0">
                    <h3 class="text-lg sm:text-xl md:text-2xl text-[#F15A2B] font-bold mb-4 sm:mb-6 drop-shadow-[0_2px_4px_rgba(241,90,43,0.3)]">{{ t('find_us') }}</h3>
                    <ul class="space-y-3 sm:space-y-4">
                        <li class="flex items-center justify-center md:justify-start gap-2 sm:gap-3 text-xs sm:text-sm md:text-base text-gray-300 hover:text-white transition-all duration-300 group">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#F15A2B]/20 group-hover:bg-[#F15A2B]/40 flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#F15A2B] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <a href="mailto:lineup2606@gmail.com" class="hover:underline hover:text-[#F15A2B] transition-colors break-all">lineup2606@gmail.com</a>
                        </li>
                        <li class="flex items-center justify-center md:justify-start gap-2 sm:gap-3 text-xs sm:text-sm md:text-base text-gray-300 hover:text-white transition-all duration-300 group">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#F15A2B]/20 group-hover:bg-[#F15A2B]/40 flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#F15A2B] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <a href="tel:+995592777743" class="hover:underline hover:text-[#F15A2B] transition-colors">+995 (592) 777 743</a>
                        </li>
                        <li class="flex items-center justify-center md:justify-start gap-2 sm:gap-3 text-xs sm:text-sm md:text-base text-gray-300 hover:text-white transition-all duration-300 group">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#F15A2B]/20 group-hover:bg-[#F15A2B]/40 flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#F15A2B] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <a href="tel:+995574515075" class="hover:underline hover:text-[#F15A2B] transition-colors">+995 (574) 515 075</a>
                        </li>
                        <li class="flex items-center justify-center md:justify-start gap-2 sm:gap-3 text-xs sm:text-sm md:text-base text-gray-300 hover:text-white transition-all duration-300 group">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#F15A2B]/20 group-hover:bg-[#F15A2B]/40 flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#F15A2B] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="hover:text-[#F15A2B] transition-colors text-center md:text-left">Akhalkalaki city, Azatutyan 87/3</span>
                        </li>
                    </ul>
                </div>

                <!-- Customers Section -->
                <div class="w-full md:w-1/3">
                    <h3 class="text-lg sm:text-xl md:text-2xl text-[#F15A2B] font-bold mb-4 sm:mb-6 drop-shadow-[0_2px_4px_rgba(241,90,43,0.3)]">{{ t('customers') }}</h3>
                    <ul class="space-y-2 sm:space-y-3">
                        <li>
                            <Link :href="route('archive')" class="text-xs sm:text-sm md:text-base text-gray-300 hover:text-[#F15A2B] transition-all duration-300 inline-block transform hover:translate-x-2">
                                {{ t('archive') }}
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('privacy')" class="text-xs sm:text-sm md:text-base text-gray-300 hover:text-[#F15A2B] transition-all duration-300 inline-block transform hover:translate-x-2">
                                {{ t('return_refund') }}
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('news')" class="text-xs sm:text-sm md:text-base text-gray-300 hover:text-[#F15A2B] transition-all duration-300 inline-block transform hover:translate-x-2">
                                {{ t('news') }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="container mx-auto text-center mt-6 sm:mt-8 md:mt-12 pt-4 sm:pt-6 border-t border-gray-700/50 text-gray-400 text-xs sm:text-sm md:text-base relative z-10 px-4">
                <p class="mb-1 sm:mb-2">Copyright ©2025</p>
                <p class="mb-1 sm:mb-2">All rights reserved</p>
                <p class="text-[#F15A2B] font-semibold">The website is made by GeekLab Company</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import LanguageSwitcher from '../Components/LanguageSwitcher.vue';
import { useTranslation } from '../composables/useTranslation';

const { t } = useTranslation();
const page = usePage();
const mobileMenuOpen = ref(false);
const sectionsOpen = ref(false);
const supportsOpen = ref(false);

const courses = computed(() => (page.props.courses || []).filter(course => course.slug));

// Lock body scroll when mobile menu is open
watch(mobileMenuOpen, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

// Cleanup on unmount
onUnmounted(() => {
    document.body.style.overflow = '';
});
</script>

<style scoped>
/* Slide transition for mobile menu */
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.slide-enter-to {
    transform: translateX(0);
    opacity: 1;
}

.slide-leave-from {
    transform: translateX(0);
    opacity: 1;
}

.slide-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

/* Expand transition for dropdowns */
.expand-enter-active,
.expand-leave-active {
    transition: all 0.3s ease-out;
    overflow: hidden;
}

.expand-enter-from {
    max-height: 0;
    opacity: 0;
    transform: translateY(-10px);
}

.expand-enter-to {
    max-height: 500px;
    opacity: 1;
    transform: translateY(0);
}

.expand-leave-from {
    max-height: 500px;
    opacity: 1;
    transform: translateY(0);
}

.expand-leave-to {
    max-height: 0;
    opacity: 0;
    transform: translateY(-10px);
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar for mobile menu */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #F15A2B, #BF3206);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #BF3206, #F15A2B);
}

/* Logo and menu parent widths matching old project */
.logo-parent {
    width: auto;
    min-width: 100px;
}

@media (min-width: 640px) {
    .logo-parent {
        width: 150px;
    }
}

@media (min-width: 1024px) {
    .logo-parent {
        width: 200px;
    }
}

.menu-parent {
    width: calc(100% - 250px - 250px);
}

.actions-parent {
    width: auto;
    min-width: fit-content;
}

@media (min-width: 1024px) {
    .actions-parent {
        width: 250px;
    }
}

.actions-parent > div {
    width: auto;
}

@media (min-width: 1024px) {
    .actions-parent > div {
        width: 65px;
    }
}

/* Enhanced dropdown animations */
.dropdown {
    position: relative;
}

/* Glow effect on hover */
.group:hover .group-hover\:drop-shadow-\[0_0_15px_rgba\(241\,90\,43\,0\.5\)\] {
    filter: drop-shadow(0 0 15px rgba(241, 90, 43, 0.5));
}
</style>

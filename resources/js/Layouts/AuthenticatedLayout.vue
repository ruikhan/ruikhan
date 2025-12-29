<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NotificationCenter from '@/Components/NotificationCenter.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const showingNavigationDropdown = ref(false);
const showingMobileMenu = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const logoUrl = '/images/enotpili.png'; 
const isLoading = ref(false);
const isMobileView = ref(false);

// Check if user is a business owner with active subscription
const isBusinessOwner = computed(() => {
    return user.value.role === 'business_owner' || user.value.has_business;
});

// Debounced viewport check for better performance
let resizeTimeout;
const checkMobileView = () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        isMobileView.value = window.innerWidth < 1024;
    }, 150);
};

onMounted(() => {
    checkMobileView();
    window.addEventListener('resize', checkMobileView, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('resize', checkMobileView);
    clearTimeout(resizeTimeout);
});

// Avatar gradient generator with Apple-inspired colors
const getAvatarGradient = (name) => {
    const gradients = [
        'from-blue-400 via-blue-500 to-blue-600', 
        'from-purple-400 via-purple-500 to-purple-600', 
        'from-pink-400 via-pink-500 to-pink-600',
        'from-orange-400 via-orange-500 to-orange-600',
        'from-green-400 via-green-500 to-green-600',
    ];
    return gradients[(name?.length || 0) % gradients.length];
};

// Optimized transition logic with RAF
let minLoadTime = 400;
let start = 0;
let rafId = null;

router.on('start', () => { 
    start = Date.now();
    isLoading.value = true;
    showingMobileMenu.value = false;
});

router.on('finish', () => { 
    const elapsed = Date.now() - start;
    const remaining = Math.max(0, minLoadTime - elapsed);
    
    if (rafId) cancelAnimationFrame(rafId);
    
    setTimeout(() => {
        rafId = requestAnimationFrame(() => {
            isLoading.value = false;
        });
    }, remaining);
});

// Navigation items for mobile bottom bar
const mobileNavItems = computed(() => {
    if (user.value.role === 'admin') {
        return [
            { name: 'Dashboard', route: 'dashboard', icon: '🏠', current: 'dashboard' },
            { name: 'Command', route: 'admin.analytics', icon: '📊', current: 'admin.analytics' },
            { name: 'Approvals', route: 'admin.documents.index', icon: '✅', current: 'admin.documents.*' },
            { name: 'Market', route: 'marketplace.index', icon: '🏪', current: 'marketplace.*' },
        ];
    } else {
        return [
            { name: 'Home', route: 'dashboard', icon: '🏠', current: 'dashboard' },
            { name: 'Market', route: 'marketplace.index', icon: '🏪', current: 'marketplace.*' },
            { name: 'Docs', route: 'services.index', icon: '📄', current: 'services.*' },
            { name: 'Emergency', route: 'emergency.index', icon: '🚨', current: 'emergency.*', danger: true },
        ];
    }
});
</script>

<template>
    <div class="min-h-screen bg-[#000000] text-slate-100 font-sans relative selection:bg-blue-500/30 selection:text-blue-200">
        
        <!-- Apple-Style Mobile Phone Frame -->
        <div class="lg:hidden fixed inset-0 pointer-events-none z-[200] overflow-hidden">
            <!-- Dynamic Island / Notch with subtle glow -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[140px] h-[32px] bg-black rounded-b-[24px] shadow-xl">
                <div class="absolute inset-0 bg-gradient-to-b from-gray-900 to-black rounded-b-[24px] opacity-80"></div>
            </div>
            
            <!-- Premium Phone Frame Border with subtle shine -->
            <div class="absolute inset-0 rounded-[48px] border-[3px] border-white/[0.08] shadow-2xl">
                <!-- Inner border for depth -->
                <div class="absolute inset-[3px] rounded-[45px] border border-white/[0.03]"></div>
            </div>

            <!-- Corner camera lenses (iPhone style) -->
            <div class="absolute top-[52px] left-6 w-2 h-2 bg-gradient-to-br from-blue-900/40 to-purple-900/40 rounded-full blur-[1px]"></div>
        </div>

        <!-- Mobile Content Container with Apple-style backdrop -->
        <div class="lg:hidden fixed inset-[3px] rounded-[45px] overflow-hidden z-[1] bg-gradient-to-b from-[#0a0a0a] to-[#000000]">
            <!-- Sophisticated Background Effects -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <!-- Refined grain texture -->
                <div class="absolute inset-0 opacity-[0.015] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] mix-blend-overlay"></div>
                
                <!-- Apple-style ambient glows -->
                <div class="absolute top-[5%] left-[30%] w-[280px] h-[280px] bg-gradient-to-br from-blue-500/[0.08] to-purple-500/[0.06] rounded-full blur-[80px] animate-float-slow"></div>
                <div class="absolute bottom-[10%] right-[25%] w-[320px] h-[320px] bg-gradient-to-tl from-pink-500/[0.06] to-orange-500/[0.05] rounded-full blur-[90px] animate-float-slower"></div>
                
                <!-- Subtle mesh gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/[0.02] via-transparent to-purple-500/[0.02]"></div>
            </div>
        </div>

        <!-- Desktop Background Effects -->
        <div class="hidden lg:block fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 opacity-[0.02] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] mix-blend-overlay"></div>
            <div class="absolute top-[-15%] left-[25%] w-[500px] h-[500px] bg-gradient-to-br from-blue-500/10 to-purple-500/8 rounded-full blur-[100px] animate-float-slow"></div>
            <div class="absolute bottom-[-10%] right-[15%] w-[600px] h-[600px] bg-gradient-to-tl from-pink-500/8 to-orange-500/6 rounded-full blur-[120px] animate-float-slower"></div>
        </div>

        <!-- Desktop Navigation -->
        <nav 
            class="hidden lg:flex fixed top-6 left-0 right-0 z-40 justify-center px-4 transition-all duration-700 ease-out"
            :class="{ '-translate-y-32 opacity-0': isLoading }"
            role="navigation"
            aria-label="Main navigation"
        >
            <div class="group relative flex items-center justify-between gap-6 px-3 py-2.5 bg-[#1a1a1a]/60 backdrop-blur-2xl border border-white/[0.08] rounded-full shadow-2xl ring-1 ring-white/[0.03] transition-all duration-500 hover:scale-[1.01] hover:bg-[#1a1a1a]/70 hover:shadow-3xl">
                
                <!-- Subtle inner glow -->
                <div class="absolute inset-[1px] rounded-full bg-gradient-to-b from-white/[0.03] to-transparent pointer-events-none"></div>
                
                <!-- Logo -->
                <Link 
                    :href="route('dashboard')" 
                    class="pl-4 pr-2 flex items-center gap-3 shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-full transition-transform duration-300 hover:scale-105"
                    aria-label="Go to dashboard"
                >
                    <img :src="logoUrl" alt="E-Pili Logo" class="h-16 w-auto drop-shadow-lg" />
                </Link>

                <!-- Elegant Divider -->
                <div class="w-px h-5 bg-gradient-to-b from-transparent via-white/10 to-transparent"></div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-1" role="menubar">
                    <Link 
                        :href="route('dashboard')" 
                        :class="['nav-pill', route().current('dashboard') ? 'active' : '']"
                        role="menuitem"
                    >
                        🏠 Dashboard
                    </Link>

                    <template v-if="user.role === 'admin'">
                        <Link 
                            :href="route('admin.analytics')" 
                            :class="['nav-pill', route().current('admin.analytics') ? 'active-danger' : '']"
                            role="menuitem"
                        >
                            📊 Command
                        </Link>
                        <Link 
                            :href="route('admin.documents.index')" 
                            :class="['nav-pill', route().current('admin.documents.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            ✅ Approvals
                        </Link>
                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-pill', route().current('marketplace.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            🏪 Marketplace
                        </Link>
                    </template>

                    <template v-else>
                        <Link 
                            :href="route('marketplace.index')" 
                            :class="['nav-pill', route().current('marketplace.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            🏪 Marketplace
                        </Link>

                        <Link 
                            v-if="isBusinessOwner" 
                            :href="route('business.dashboard')" 
                            :class="['nav-pill nav-pill-special', route().current('business.dashboard') ? 'active-special' : '']"
                            role="menuitem"
                        >
                            📊 My Business
                        </Link>

                        <Link 
                            v-else
                            :href="route('business.register')" 
                            :class="['nav-pill nav-pill-cta', route().current('business.register') ? 'active' : '']"
                            role="menuitem"
                        >
                            🚀 Start Business
                        </Link>

                        <Link 
                            :href="route('services.index')" 
                            :class="['nav-pill', route().current('services.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            📄 My Docs
                        </Link>
                        
                        <Link 
                            :href="route('market.index')" 
                            :class="['nav-pill', route().current('market.*') ? 'active' : '']"
                            role="menuitem"
                        >
                            🌾 Market
                        </Link>
                        
                        <Link 
                            :href="route('emergency.index')" 
                            :class="['nav-pill text-red-400 hover:text-red-300', route().current('emergency.*') ? 'active-danger' : '']"
                            role="menuitem"
                        >
                            🚨 Emergency
                        </Link>
                    </template>
                </div>

                <!-- User Dropdown -->
                <div class="flex items-center gap-3 pl-2 pr-1">
                    <!-- Notification Center -->
                    <NotificationCenter />
                    
                    <!-- User Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button 
                                class="relative flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-gray-700 to-gray-900 ring-2 ring-black/50 hover:ring-white/20 transition-all duration-300 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-lg hover:shadow-xl"
                                aria-label="User menu"
                            >
                                <div :class="`absolute inset-0 rounded-full bg-gradient-to-br ${getAvatarGradient(user.name)} opacity-90 blur-sm`"></div>
                                <span class="relative z-10 text-sm font-bold text-white drop-shadow-lg">{{ user.name.charAt(0) }}</span>
                            </button>
                        </template>
                        <template #content>
                            <div class="p-1 bg-[#1a1a1a]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl">
                                <div class="px-4 py-3 border-b border-white/10">
                                    <p class="text-sm font-semibold text-white truncate">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ user.email }}</p>
                                    <span v-if="user.role === 'admin'" class="inline-block mt-2 px-2 py-1 text-xs font-bold rounded-full bg-red-500/20 text-red-300 border border-red-500/30">👑 Admin</span>
                                </div>
                                <DropdownLink :href="route('profile.edit')" class="rounded-xl text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
                                    ⚙️ Profile Settings
                                </DropdownLink>
                                <DropdownLink :href="route('proposal')" class="rounded-xl text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
                                    📖 Barangay Proposal
                                </DropdownLink>
                                <DropdownLink :href="route('simulation')" class="rounded-xl text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
                                    📖 System Handbook
                                </DropdownLink>
                                <DropdownLink :href="route('training')" class="rounded-xl text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
                                    🖧 Training Assessment
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="rounded-xl text-red-400 hover:bg-red-500/10 w-full text-left transition-all duration-200">
                                    🚪 Log Out
                                </DropdownLink>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>

        <!-- Hidden Mobile Top Bar - Only Status Bar for Dynamic Island -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-40 pointer-events-none">
            <!-- Status Bar with Dynamic Island space -->
            <div class="h-[32px] bg-gradient-to-b from-black to-transparent"></div>
        </div>

        <!-- Premium iOS-Style Bottom Navigation -->
        <nav 
            class="lg:hidden fixed bottom-0 left-0 right-0 z-40 transition-all duration-500 ease-out"
            :class="{ 'translate-y-full opacity-0': isLoading }"
            role="navigation"
        >
            <div class="relative">
                <!-- Floating Center Button - More Menu -->
                <div class="absolute -top-8 left-1/2 -translate-x-1/2 z-10">
                    <button
                        @click="showingMobileMenu = !showingMobileMenu"
                        class="relative group"
                    >
                        <!-- Pulse rings -->
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 animate-ping opacity-20" :class="{'opacity-0': showingMobileMenu}"></div>
                        
                        <!-- Shadow glow -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full blur-xl opacity-40 group-hover:opacity-60 transition-opacity"></div>
                        
                        <!-- Button container -->
                        <div class="relative w-16 h-16 bg-gradient-to-br from-purple-500 via-fuchsia-500 to-pink-500 rounded-full shadow-2xl shadow-purple-500/40 flex items-center justify-center transition-all duration-300 group-active:scale-90"
                             :class="{'scale-110 rotate-180': showingMobileMenu}">
                            <!-- Inner shine -->
                            <div class="absolute inset-[2px] bg-gradient-to-b from-white/30 to-transparent rounded-full"></div>
                            
                            <!-- Icon -->
                            <span class="relative text-2xl filter drop-shadow-xl transition-transform duration-500"
                                  :class="{'rotate-45 scale-110': showingMobileMenu}">
                                ✨
                            </span>
                        </div>
                    </button>
                </div>

                <!-- Main Navigation Bar -->
                <div class="bg-[#1c1c1e]/95 backdrop-blur-3xl border-t border-white/[0.08] shadow-2xl">
                    <!-- Top highlight line -->
                    <div class="h-px bg-gradient-to-r from-transparent via-white/[0.08] to-transparent"></div>
                    
                    <!-- Navigation Items -->
                    <div class="flex items-end justify-around px-4 pt-2 pb-1 safe-area-inset-bottom">
                        <Link 
                            v-for="item in mobileNavItems" 
                            :key="item.route"
                            :href="route(item.route)"
                            class="nav-item group relative flex flex-col items-center gap-1 py-2.5 px-3 min-w-[70px] rounded-2xl transition-all duration-300 active:scale-95"
                            :class="{'is-active': route().current(item.current)}"
                        >
                            <!-- Active background -->
                            <div 
                                class="absolute inset-0 bg-white/[0.08] rounded-2xl scale-90 opacity-0 transition-all duration-300"
                                :class="{
                                    'scale-100 opacity-100': route().current(item.current),
                                    '!bg-red-500/15': item.danger && route().current(item.current)
                                }"
                            ></div>
                            
                            <!-- Icon container -->
                            <div class="relative mb-0.5">
                                <!-- Active glow -->
                                <div 
                                    v-if="route().current(item.current)"
                                    class="absolute inset-0 blur-lg opacity-50 scale-110"
                                    :class="item.danger ? 'text-red-400' : 'text-blue-400'"
                                >
                                    {{ item.icon }}
                                </div>
                                
                                <!-- Icon -->
                                <span 
                                    class="relative block text-[26px] transition-all duration-300 filter drop-shadow-lg"
                                    :class="{
                                        'scale-110': route().current(item.current),
                                        'scale-100 opacity-60': !route().current(item.current)
                                    }"
                                >
                                    {{ item.icon }}
                                </span>
                            </div>
                            
                            <!-- Label -->
                            <span 
                                class="relative text-[10px] font-bold tracking-wide transition-all duration-300"
                                :class="{
                                    'text-white': route().current(item.current),
                                    'text-gray-500': !route().current(item.current) && !item.danger,
                                    'text-red-400': item.danger
                                }"
                            >
                                {{ item.name }}
                            </span>
                            
                            <!-- Active dot indicator -->
                            <div 
                                v-if="route().current(item.current)"
                                class="absolute top-1 w-1 h-1 rounded-full transition-all duration-300"
                                :class="item.danger ? 'bg-red-400' : 'bg-blue-400'"
                            ></div>
                        </Link>
                    </div>
                    
                    <!-- Home Indicator -->
                    <div class="h-5 flex items-center justify-center pt-1">
                        <div class="w-32 h-1 bg-white/20 rounded-full"></div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Enhanced Mobile More Menu with Card Style -->
        <Transition
            enter-active-class="transition-all duration-400 ease-out"
            enter-from-class="opacity-0 translate-y-12 scale-90"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-12 scale-90"
        >
            <div 
                v-show="showingMobileMenu" 
                class="lg:hidden fixed bottom-[110px] left-4 right-4 z-50"
            >
                <!-- User Profile Card -->
                <div class="mb-3 bg-gradient-to-br from-[#1a1a1a]/95 to-[#0f0f0f]/95 backdrop-blur-3xl border border-white/10 rounded-3xl p-4 shadow-2xl shadow-purple-500/20">
                    <div class="absolute inset-[1px] bg-gradient-to-b from-white/[0.05] to-transparent rounded-3xl pointer-events-none"></div>
                    
                    <div class="flex items-center gap-4 mb-3">
                        <!-- User Avatar -->
                        <div class="relative flex-shrink-0">
                            <div :class="`w-14 h-14 rounded-full bg-gradient-to-br ${getAvatarGradient(user.name)} flex items-center justify-center shadow-lg`">
                                <span class="text-xl font-bold text-white">{{ user.name.charAt(0) }}</span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-[#1a1a1a] flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                        </div>
                        
                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ user.name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ user.email }}</p>
                            <div class="flex gap-2 mt-1">
                                <span v-if="user.role === 'admin'" class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500/20 text-red-300 border border-red-500/30">👑 Admin</span>
                                <span v-else class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">👤 {{ isBusinessOwner ? 'Business' : 'Citizen' }}</span>
                            </div>
                        </div>
                        
                        <!-- Notification Bell -->
                        <NotificationCenter />
                    </div>
                    
                    <!-- Quick Profile Actions -->
                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-white/10">
                        <Link 
                            :href="route('profile.edit')"
                            class="flex items-center justify-center gap-2 px-3 py-2 bg-white/[0.05] hover:bg-white/[0.1] rounded-xl transition-all active:scale-95 text-xs font-semibold text-gray-300 hover:text-white"
                        >
                            <span>⚙️</span>
                            <span>Profile</span>
                        </Link>
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button"
                            class="flex items-center justify-center gap-2 px-3 py-2 bg-red-500/10 hover:bg-red-500/20 rounded-xl transition-all active:scale-95 text-xs font-semibold text-red-400 hover:text-red-300"
                        >
                            <span>🚪</span>
                            <span>Logout</span>
                        </Link>
                    </div>
                </div>

                <!-- Menu Cards -->
                <div class="bg-gradient-to-br from-[#1a1a1a]/95 to-[#0f0f0f]/95 backdrop-blur-3xl border border-white/10 rounded-3xl p-4 shadow-2xl shadow-purple-500/20">
                    <!-- Subtle inner highlight -->
                    <div class="absolute inset-[1px] bg-gradient-to-b from-white/[0.05] to-transparent rounded-3xl pointer-events-none"></div>
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-3 px-2">
                        <h3 class="text-sm font-bold text-white/80 tracking-wide">Quick Actions</h3>
                        <div class="flex gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 animate-pulse"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-blue-400 to-cyan-400 animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-green-400 to-emerald-400 animate-pulse" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                    
                    <div class="space-y-2 relative">
                        <template v-if="user.role !== 'admin'">
                            <Link 
                                v-if="isBusinessOwner" 
                                :href="route('business.dashboard')" 
                                class="mobile-more-card-inline group"
                                :class="{'!bg-blue-500/15 !border-blue-500/30': route().current('business.dashboard')}"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="text-2xl drop-shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">📊</span>
                                <div class="flex-1 min-w-0 text-left">
                                    <span class="text-sm font-bold relative z-10 block">My Business</span>
                                    <span class="text-xs text-gray-400 relative z-10 block">Dashboard & Analytics</span>
                                </div>
                            </Link>
                            <Link 
                                v-else
                                :href="route('business.register')" 
                                class="mobile-more-card-inline group"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-emerald-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="text-2xl drop-shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">🚀</span>
                                <div class="flex-1 min-w-0 text-left">
                                    <span class="text-sm font-bold relative z-10 block">Start Business</span>
                                    <span class="text-xs text-gray-400 relative z-10 block">Register Your Business</span>
                                </div>
                            </Link>
                            <Link 
                                :href="route('market.index')" 
                                class="mobile-more-card-inline group"
                                :class="{'!bg-green-500/15 !border-green-500/30': route().current('market.*')}"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-lime-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="text-2xl drop-shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">🌾</span>
                                <div class="flex-1 min-w-0 text-left">
                                    <span class="text-sm font-bold relative z-10 block">Market Prices</span>
                                    <span class="text-xs text-gray-400 relative z-10 block">Live Commodity Rates</span>
                                </div>
                            </Link>
                        </template>
                        
                        <!-- Additional Options -->
                        <Link 
                            :href="route('proposal')" 
                            class="mobile-more-card-inline group"
                        >
                            <span class="text-2xl drop-shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">📖</span>
                            <div class="flex-1 min-w-0 text-left">
                                <span class="text-sm font-bold relative z-10 block">Barangay Proposal</span>
                                <span class="text-xs text-gray-400 relative z-10 block">View Documents</span>
                            </div>
                        </Link>
                        
                        <Link 
                            :href="route('simulation')" 
                            class="mobile-more-card-inline group"
                        >
                            <span class="text-2xl drop-shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">📚</span>
                            <div class="flex-1 min-w-0 text-left">
                                <span class="text-sm font-bold relative z-10 block">System Handbook</span>
                                <span class="text-xs text-gray-400 relative z-10 block">User Guide</span>
                            </div>
                        </Link>
                        
                        <Link 
                            :href="route('training')" 
                            class="mobile-more-card-inline group"
                        >
                            <span class="text-2xl drop-shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">🎓</span>
                            <div class="flex-1 min-w-0 text-left">
                                <span class="text-sm font-bold relative z-10 block">Training Assessment</span>
                                <span class="text-xs text-gray-400 relative z-10 block">Test Your Knowledge</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop with enhanced blur -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-show="showingMobileMenu" 
                @click="showingMobileMenu = false"
                class="lg:hidden fixed inset-0 bg-black/70 backdrop-blur-xl z-40"
            ></div>
        </Transition>

        <!-- Main Content -->
        <main 
            class="relative z-10 pt-10 lg:pt-32 pb-32 lg:pb-20 px-4 transition-all duration-700 ease-out"
            :class="{ 'scale-[0.97] opacity-40 blur-md pointer-events-none': isLoading }"
        >
            <slot />
        </main>

        <!-- Chat Assistant -->
        <div class="relative z-50">
             <ChatAssistant />
        </div>

        <!-- Apple-Style Loading Screen -->
        <Transition name="fade-scale-apple">
            <div 
                v-if="isLoading" 
                class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-[#000000]/98 backdrop-blur-2xl"
            >
                <div class="relative flex flex-col items-center">
                    <!-- Ambient glow -->
                    <div class="absolute w-40 h-40 bg-gradient-to-br from-blue-500/20 via-purple-500/15 to-pink-500/10 rounded-full blur-3xl animate-pulse-elegant"></div>
                    
                    <!-- Logo with refined animation -->
                    <img 
                        :src="logoUrl" 
                        class="h-28 w-auto relative animate-float-elegant drop-shadow-2xl" 
                        alt="Loading" 
                    />

                    <!-- Elegant loading dots -->
                    <div class="mt-10 flex gap-2.5">
                        <div class="w-2.5 h-2.5 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full animate-dot-wave-elegant shadow-lg" style="animation-delay: 0ms"></div>
                        <div class="w-2.5 h-2.5 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full animate-dot-wave-elegant shadow-lg" style="animation-delay: 150ms"></div>
                        <div class="w-2.5 h-2.5 bg-gradient-to-br from-pink-400 to-pink-600 rounded-full animate-dot-wave-elegant shadow-lg" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* Desktop NAV PILLS with enhanced styling */
.nav-pill {
    @apply px-4 py-2 rounded-full text-xs font-semibold text-white/50 hover:text-white hover:bg-white/[0.08] transition-all duration-300 whitespace-nowrap cursor-pointer backdrop-blur-xl;
    @apply focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent;
    @apply active:scale-95 shadow-sm hover:shadow-lg;
}

.nav-pill.active {
    @apply bg-white/[0.12] text-white border border-white/[0.08] shadow-lg;
}

.nav-pill.active-danger {
    @apply bg-red-500/20 text-red-200 border border-red-500/30 shadow-red-500/20 shadow-lg;
}

.nav-pill-special {
    @apply relative overflow-hidden;
}

.nav-pill-special::before {
    content: '';
    @apply absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 transition-opacity duration-300;
}

.nav-pill-special:hover::before {
    @apply opacity-100;
}

.nav-pill-special.active-special {
    @apply bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-blue-200 border border-blue-500/30 shadow-lg;
}

.nav-pill-cta {
    @apply bg-gradient-to-r from-green-500/20 to-emerald-500/20 text-green-300 border border-green-500/30 shadow-lg;
}

.nav-pill-cta:hover {
    @apply from-green-500/30 to-emerald-500/30 scale-105 shadow-xl;
}

/* Enhanced Mobile Navigation */
.nav-item {
    @apply relative cursor-pointer;
    @apply focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

.nav-item.is-active {
    @apply transform;
}

.mobile-more-card {
    @apply relative flex flex-col items-center justify-center gap-2 p-4 rounded-2xl text-gray-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.05] transition-all duration-300 cursor-pointer backdrop-blur-xl;
    @apply active:scale-[0.95] shadow-lg hover:shadow-xl;
    @apply focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
    min-height: 100px;
}

.mobile-more-card-inline {
    @apply relative flex items-center gap-3 p-3 rounded-2xl text-gray-300 hover:text-white bg-white/[0.03] hover:bg-white/[0.08] border border-white/[0.05] transition-all duration-300 cursor-pointer backdrop-blur-xl;
    @apply active:scale-[0.98] shadow-lg hover:shadow-xl;
    @apply focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}

/* Apple-inspired Loading Transitions */
.fade-scale-apple-enter-active,
.fade-scale-apple-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-scale-apple-enter-from { 
    opacity: 0; 
    transform: scale(1.05); 
}

.fade-scale-apple-enter-to { 
    opacity: 1; 
    transform: scale(1); 
}

.fade-scale-apple-leave-from { 
    opacity: 1; 
    transform: scale(1); 
}

.fade-scale-apple-leave-to { 
    opacity: 0; 
    transform: scale(0.95); 
}

/* Refined Animations */
@keyframes pulse-elegant {
    0%, 100% { 
        opacity: 0.15; 
        transform: scale(1);
    }
    50% { 
        opacity: 0.25; 
        transform: scale(1.05);
    }
}

.animate-pulse-elegant {
    animation: pulse-elegant 4s ease-in-out infinite;
}

@keyframes float-elegant {
    0%, 100% { 
        transform: translateY(0px); 
    }
    50% { 
        transform: translateY(-12px); 
    }
}

.animate-float-elegant {
    animation: float-elegant 2s ease-in-out infinite;
}

@keyframes dot-wave-elegant {
    0%, 60%, 100% { 
        transform: translateY(0) scale(1); 
        opacity: 0.4; 
    }
    30% { 
        transform: translateY(-10px) scale(1.1); 
        opacity: 1; 
    }
}

.animate-dot-wave-elegant {
    animation: dot-wave-elegant 1.4s ease-in-out infinite;
}

@keyframes float-slow {
    0%, 100% { 
        transform: translate(0, 0) scale(1); 
    }
    33% { 
        transform: translate(30px, -30px) scale(1.1); 
    }
    66% { 
        transform: translate(-20px, 20px) scale(0.9); 
    }
}

.animate-float-slow {
    animation: float-slow 20s ease-in-out infinite;
}

@keyframes float-slower {
    0%, 100% { 
        transform: translate(0, 0) scale(1); 
    }
    33% { 
        transform: translate(-40px, 30px) scale(1.15); 
    }
    66% { 
        transform: translate(30px, -25px) scale(0.85); 
    }
}

.animate-float-slower {
    animation: float-slower 25s ease-in-out infinite;
}

/* Safe area support for notched devices */
.safe-area-inset-bottom {
    padding-bottom: env(safe-area-inset-bottom, 0);
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Touch optimization for mobile devices */
@media (hover: none) and (pointer: coarse) {
    button:active,
    a:active {
        transform: scale(0.96);
    }
    
    .nav-pill:active {
        transform: scale(0.94);
    }
}

/* Webkit scrollbar styling for consistency */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.15);
}
</style>
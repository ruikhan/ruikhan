<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
const user = computed(() => page.props.auth.user);
const logoUrl = '/images/enotpili.png'; 
const isLoading = ref(false);

// Check if user is a business owner with active subscription
const isBusinessOwner = computed(() => {
    return user.value.role === 'business_owner' || user.value.has_business;
});

// --- AVATAR GRADIENT GENERATOR ---
const getAvatarGradient = (name) => {
    const gradients = [
        'from-cyan-500 to-blue-500', 
        'from-violet-500 to-purple-500', 
        'from-fuchsia-500 to-pink-500',
        'from-amber-500 to-orange-500',
        'from-green-500 to-emerald-500',
    ];
    return gradients[name.length % gradients.length];
};

// --- CINEMATIC TRANSITION LOGIC ---
let minLoadTime = 800;
let start = 0;

router.on('start', () => { 
    start = Date.now();
    isLoading.value = true;
});

router.on('finish', () => { 
    const elapsed = Date.now() - start;
    const remaining = Math.max(0, minLoadTime - elapsed);
    
    setTimeout(() => {
        isLoading.value = false;
    }, remaining);
});
</script>

<template>
    <div class="min-h-screen bg-[#000000] text-slate-100 font-sans relative selection:bg-blue-500/30 selection:text-blue-200">
        
        <!-- Background Effects -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 opacity-[0.05] bg-[url('https://grainy-gradients.vercel.app/noise.svg')] mix-blend-overlay"></div>
            <div class="absolute top-[-20%] left-[20%] w-[500px] h-[500px] bg-blue-900/20 rounded-full blur-[120px] mix-blend-screen animate-pulse-slow"></div>
            <div class="absolute bottom-[-10%] right-[10%] w-[600px] h-[600px] bg-indigo-900/10 rounded-full blur-[100px] mix-blend-screen"></div>
        </div>

        <!-- Navigation Bar -->
        <nav class="fixed top-6 left-0 right-0 z-40 flex justify-center px-4 transition-all duration-700 cubic-bezier(0.16, 1, 0.3, 1)"
             :class="{ '-translate-y-32 opacity-0': isLoading }">
             
            <div class="group relative flex items-center gap-6 px-3 py-2.5 bg-[#121212]/70 backdrop-blur-2xl border border-white/10 rounded-full shadow-[0_20px_40px_-10px_rgba(0,0,0,0.5)] ring-1 ring-white/5 transition-all duration-300 hover:scale-[1.01] hover:bg-[#121212]/80">
                
                <!-- Top Shine Effect -->
                <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

                <!-- Logo -->
                <Link :href="route('dashboard')" class="pl-4 pr-2 flex items-center gap-3">
                    <img :src="logoUrl" alt="E-Pili Logo" class="h-16 w-auto drop-shadow-[0_0_10px_rgba(59,130,246,0.5)]" />
                </Link>

                <!-- Divider -->
                <div class="w-px h-5 bg-white/10 hidden md:block"></div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-1">
                    <!-- Dashboard -->
                    <Link :href="route('dashboard')" :class="['nav-pill', route().current('dashboard') ? 'active' : '']">
                        🏠 Dashboard
                    </Link>

                    <!-- Admin Navigation -->
                    <template v-if="user.role === 'admin'">
                        <Link :href="route('admin.analytics')" :class="['nav-pill', route().current('admin.analytics') ? 'active-danger' : '']">
                            📊 Command
                        </Link>
                        <Link :href="route('admin.documents.index')" :class="['nav-pill', route().current('admin.documents.*') ? 'active' : '']">
                            ✅ Approvals
                        </Link>
                        <Link :href="route('marketplace.index')" :class="['nav-pill', route().current('marketplace.*') ? 'active' : '']">
                            🏪 Marketplace
                        </Link>
                    </template>

                    <!-- Resident/Business Owner Navigation -->
                    <template v-else>
                        <!-- Marketplace (Available to all authenticated users) -->
                        <Link :href="route('marketplace.index')" :class="['nav-pill', route().current('marketplace.*') ? 'active' : '']">
                            🏪 Marketplace
                        </Link>

                        <!-- Business Dashboard (Only for business owners) -->
                        <Link 
                            v-if="isBusinessOwner" 
                            :href="route('business.dashboard')" 
                            :class="['nav-pill nav-pill-special', route().current('business.dashboard') ? 'active-special' : '']"
                        >
                            📊 My Business
                        </Link>

                        <!-- Start Business Button (Only for residents without business) -->
                        <Link 
                            v-else
                            :href="route('business.register')" 
                            :class="['nav-pill nav-pill-cta', route().current('business.register') ? 'active' : '']"
                        >
                            🚀 Start Business
                        </Link>

                        <!-- Other Resident Links -->
                        <Link :href="route('documents.index')" :class="['nav-pill', route().current('documents.*') ? 'active' : '']">
                            📄 Docs
                        </Link>
                        <Link :href="route('market.index')" :class="['nav-pill', route().current('market.*') ? 'active' : '']">
                            🌾 Market
                        </Link>
                        <Link :href="route('establishments.index')" :class="['nav-pill', route().current('establishments.*') ? 'active' : '']">
                            📍 Places
                        </Link>
                    </template>
                </div>

                <!-- User Dropdown -->
                <div class="pl-2 pr-1">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="relative flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-gray-700 to-gray-900 ring-2 ring-black hover:ring-white/20 transition-all active:scale-95">
                                <div :class="`absolute inset-0 rounded-full bg-gradient-to-br ${getAvatarGradient(user.name)} opacity-80 blur-sm`"></div>
                                <span class="relative z-10 text-xs font-bold text-white">{{ user.name.charAt(0) }}</span>
                            </button>
                        </template>
                        <template #content>
                            <div class="p-1 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-xl backdrop-blur-xl">
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-white/10">
                                    <p class="text-sm font-semibold text-white">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400">{{ user.email }}</p>
                                    <span v-if="user.role === 'admin'" class="inline-block mt-2 px-2 py-1 text-xs font-bold rounded-full bg-red-500/20 text-red-300 border border-red-500/30">
                                        👑 Admin
                                    </span>
                                    <span v-else-if="isBusinessOwner" class="inline-block mt-2 px-2 py-1 text-xs font-bold rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        💼 Business Owner
                                    </span>
                                    <span v-else class="inline-block mt-2 px-2 py-1 text-xs font-bold rounded-full bg-gray-500/20 text-gray-300 border border-gray-500/30">
                                        👤 Resident
                                    </span>
                                </div>

                                <!-- Profile Link -->
                                <DropdownLink :href="route('profile.edit')" class="rounded-lg text-gray-300 hover:text-white hover:bg-white/10">
                                    ⚙️ Profile Settings
                                </DropdownLink>

                                <!-- Business Dashboard (if owner) -->
                                <DropdownLink 
                                    v-if="isBusinessOwner" 
                                    :href="route('business.dashboard')" 
                                    class="rounded-lg text-blue-300 hover:text-blue-200 hover:bg-blue-500/10"
                                >
                                    📊 Business Dashboard
                                </DropdownLink>

                                <!-- Start Business (if not owner) -->
                                <DropdownLink 
                                    v-else
                                    :href="route('business.register')" 
                                    class="rounded-lg text-green-300 hover:text-green-200 hover:bg-green-500/10"
                                >
                                    🚀 Start Your Business
                                </DropdownLink>

                                <!-- Logout -->
                                <DropdownLink 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button" 
                                    class="rounded-lg text-red-400 hover:bg-red-500/10 w-full text-left"
                                >
                                    🚪 Log Out
                                </DropdownLink>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 pt-32 pb-20 px-4 transition-transform duration-700"
              :class="{ 'scale-95 opacity-50 blur-[2px]': isLoading }">
            <slot />
        </main>

        <!-- Chat Assistant -->
        <ChatAssistant />

        <!-- Loading Shutter -->
        <Transition name="shutter">
            <div v-if="isLoading" class="fixed inset-0 z-[100] flex flex-col items-center justify-center">
                <div class="absolute inset-0 bg-gradient-to-b from-[#000000]/80 via-[#050505]/85 to-[#000000]/90 backdrop-blur-[60px] shutter-backdrop"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_30%,#000000_95%)] opacity-90"></div>

                <div class="relative z-20 flex flex-col items-center animate-content-reveal">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-56 h-56 bg-blue-500/20 rounded-full blur-[80px] animate-glow-pulse"></div>
                    
                    <img :src="logoUrl" 
                         class="h-29 w-auto relative drop-shadow-[0_10px_40px_rgba(59,130,246,0.4)] animate-logo-float" 
                         alt="E-Pili Loading" />

                    <div class="mt-10 flex gap-2">
                        <div class="w-2 h-2 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full animate-dot-wave shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full animate-dot-wave shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full animate-dot-wave shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
/* NAV PILLS */
.nav-pill {
    @apply px-4 py-1.5 rounded-full text-xs font-medium text-white/50 hover:text-white hover:bg-white/5 transition-all duration-300;
}

.nav-pill.active {
    @apply bg-white/10 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.1)] border border-white/5;
}

.nav-pill.active-danger {
    @apply bg-red-500/20 text-red-200 border border-red-500/20;
}

/* Special styling for Business Dashboard link */
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
    @apply bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-blue-200 border border-blue-500/30 shadow-[0_0_20px_rgba(59,130,246,0.3)];
}

/* CTA Button for "Start Business" */
.nav-pill-cta {
    @apply bg-gradient-to-r from-green-500/20 to-emerald-500/20 text-green-300 border border-green-500/30;
}

.nav-pill-cta:hover {
    @apply from-green-500/30 to-emerald-500/30 shadow-[0_0_20px_rgba(34,197,94,0.3)] scale-105;
}

/* PREMIUM APPLE-STYLE TRANSITIONS */
.shutter-enter-active,
.shutter-leave-active {
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.shutter-enter-from {
    opacity: 0;
    transform: scale(1.03);
}

.shutter-enter-to {
    opacity: 1;
    transform: scale(1);
}

.shutter-leave-from {
    opacity: 1;
    transform: scale(1);
}

.shutter-leave-to {
    opacity: 0;
    transform: scale(0.98);
}

.shutter-backdrop {
    animation: backdrop-fade-in 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

@keyframes backdrop-fade-in {
    0% {
        opacity: 0;
        backdrop-filter: blur(0px);
    }
    100% {
        opacity: 1;
        backdrop-filter: blur(60px);
    }
}

@keyframes content-reveal {
    0% {
        opacity: 0;
        transform: translateY(20px) scale(0.96);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-content-reveal {
    animation: content-reveal 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.2s backwards;
}

@keyframes logo-float {
    0% {
        transform: translateY(0px) scale(0.95);
        opacity: 0;
    }
    50% {
        transform: translateY(-3px) scale(1);
        opacity: 1;
    }
    100% {
        transform: translateY(0px) scale(1);
        opacity: 1;
    }
}

.animate-logo-float {
    animation: logo-float 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s backwards,
               logo-breathe 4s cubic-bezier(0.45, 0.05, 0.55, 0.95) 1.5s infinite;
}

@keyframes logo-breathe {
    0%, 100% {
        transform: translateY(0px) scale(1);
    }
    50% {
        transform: translateY(-2px) scale(1.01);
    }
}

@keyframes glow-pulse {
    0%, 100% {
        opacity: 0.15;
        transform: scale(0.95);
    }
    50% {
        opacity: 0.25;
        transform: scale(1.05);
    }
}

.animate-glow-pulse {
    animation: glow-pulse 3s cubic-bezier(0.45, 0.05, 0.55, 0.95) infinite;
}

@keyframes dot-wave {
    0%, 60%, 100% {
        transform: translateY(0px) scale(1);
        opacity: 0.5;
    }
    30% {
        transform: translateY(-12px) scale(1.1);
        opacity: 1;
    }
}

.animate-dot-wave {
    animation: dot-wave 1.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) infinite;
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

.animate-pulse-slow {
    animation: pulse-slow 6s ease-in-out infinite;
}
</style>
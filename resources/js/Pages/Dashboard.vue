<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import DashboardMap from '@/Components/DashboardMap.vue';
import IssueWizard from '@/Components/IssueWizard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const user = usePage().props.auth.user;
const page = usePage();
const showWizard = ref(false);

const isBusinessOwner = computed(() => {
    return user.role === 'business_owner' || user.has_business;
});

// Toast notification
const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

// Live clock
const time = ref('');
const date = ref('');
const fullDate = ref('');
const greeting = ref('Good Morning');

const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'long' });
    fullDate.value = now.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
    
    const hour = now.getHours();
    if (hour < 12) greeting.value = 'Good Morning';
    else if (hour < 18) greeting.value = 'Good Afternoon';
    else greeting.value = 'Good Evening';
};

// Mobile app shortcuts with enhanced styling
const mobileApps = [
    // Row 1
    { icon: '⚡', name: 'Services', link: route('services.landing'), gradient: 'from-blue-500 via-blue-600 to-indigo-600', glow: 'shadow-blue-500/50' },
    { icon: '💳', name: 'Bills', link: route('bills.create'), gradient: 'from-green-500 via-emerald-600 to-teal-600', glow: 'shadow-green-500/50' },
    { icon: '🩺', name: 'Health', link: route('health.index'), gradient: 'from-red-500 via-rose-600 to-pink-600', glow: 'shadow-red-500/50' },
    { icon: '📍', name: 'Places', link: route('establishments.index'), gradient: 'from-purple-500 via-violet-600 to-fuchsia-600', glow: 'shadow-purple-500/50' },
    
    // Row 2
    { icon: '🏪', name: 'Market', link: route('marketplace.index'), gradient: 'from-orange-500 via-amber-600 to-yellow-600', glow: 'shadow-orange-500/50' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: 'from-indigo-500 via-blue-600 to-cyan-600', glow: 'shadow-indigo-500/50' },
    { icon: '💼', name: 'Jobs', link: route('jobs.index'), gradient: 'from-yellow-500 via-orange-600 to-amber-600', glow: 'shadow-yellow-500/50' },
    { icon: '📈', name: 'Prices', link: route('market.index'), gradient: 'from-cyan-500 via-teal-600 to-emerald-600', glow: 'shadow-cyan-500/50' },
    
    // Row 3
    { icon: '♻️', name: 'Green', link: route('environment.index'), gradient: 'from-emerald-500 via-green-600 to-lime-600', glow: 'shadow-emerald-500/50' },
    { icon: '🤝', name: 'Social', link: route('social.index'), gradient: 'from-violet-500 via-purple-600 to-indigo-600', glow: 'shadow-violet-500/50' },
    { icon: '🚨', name: 'Report', link: null, action: 'wizard', gradient: 'from-red-600 via-rose-700 to-pink-700', glow: 'shadow-red-600/60', pulse: true },
    { icon: '🗳️', name: 'Polls', link: route('polls.index'), gradient: 'from-amber-500 via-yellow-600 to-orange-600', glow: 'shadow-amber-500/50' },
];

// Quick stats for mobile
const quickStats = [
    { label: 'Active Docs', value: '3', icon: '📄', color: 'from-blue-500 to-indigo-600' },
    { label: 'Pending', value: '1', icon: '⏳', color: 'from-amber-500 to-orange-600' },
    { label: 'Completed', value: '12', icon: '✅', color: 'from-green-500 to-emerald-600' },
];

// Desktop features (original design)
const desktopFeatures = [
    { 
        title: 'Smart Services',
        subtitle: 'Permits & Clearances', 
        icon: '⚡',
        link: route('services.landing'),
        historyLink: route('services.index'),
        gradient: 'from-blue-500 to-indigo-600',
        size: 'large'
    },
    { 
        title: 'Bills & Fees', 
        subtitle: 'Secure Payments', 
        icon: '💳', 
        link: route('bills.create'),
        gradient: 'from-teal-500 to-emerald-600',
        size: 'medium'
    },
    { 
        title: 'E-Health', 
        subtitle: 'Telemed & Booking', 
        icon: '🩺', 
        link: route('health.index'),
        gradient: 'from-sky-500 to-cyan-600',
        size: 'medium'
    },
    { 
        title: 'Issue Report', 
        subtitle: 'Quick Response', 
        icon: '🚨', 
        link: null,
        action: 'wizard',
        gradient: 'from-red-500 to-pink-600',
        size: 'medium',
        special: true
    },
    { 
        title: 'Green Guard', 
        subtitle: 'Waste & Air', 
        icon: '♻️', 
        link: route('environment.index'),
        gradient: 'from-green-500 to-emerald-600',
        size: 'medium'
    },
    { 
        title: 'Social Aid', 
        subtitle: 'Support Programs', 
        icon: '🤝', 
        link: route('social.index'),
        gradient: 'from-violet-500 to-purple-600',
        size: 'medium'
    },
    { 
        title: 'Marketplace', 
        subtitle: 'Local Businesses', 
        icon: '🌐', 
        link: route('marketplace.index'),
        gradient: 'from-purple-500 to-fuchsia-600',
        size: 'large',
        featured: true
    },
    { 
        title: isBusinessOwner.value ? 'My Business' : 'Business Hub', 
        subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', 
        icon: isBusinessOwner.value ? '📊' : '🏪', 
        link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), 
        gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600',
        size: 'medium',
        special: !isBusinessOwner.value
    },
    { 
        title: 'Market Prices', 
        subtitle: 'Live Rates', 
        icon: '📈', 
        link: route('market.index'),
        gradient: 'from-cyan-500 to-blue-600',
        size: 'medium'
    },
    { 
        title: 'Places', 
        subtitle: 'Directory', 
        icon: '🏨', 
        link: route('establishments.index'),
        gradient: 'from-lime-500 to-green-600',
        size: 'medium'
    },
    { 
        title: 'Local Jobs', 
        subtitle: 'Opportunities', 
        icon: '💼', 
        link: route('jobs.index'),
        gradient: 'from-yellow-500 to-orange-600',
        size: 'medium'
    },
];

// Timers
let clockTimer;

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);

    if (flashSuccess.value) {
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 4000);
    }
});

onUnmounted(() => {
    clearInterval(clockTimer);
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        
        <!-- Toast Notification with enhanced styling -->
        <Transition name="slide-fade">
            <div v-if="showFlash && flashSuccess" 
                 class="fixed top-12 lg:top-24 left-1/2 -translate-x-1/2 z-50 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 text-white px-5 py-3.5 rounded-3xl shadow-2xl shadow-green-500/40 flex items-center gap-3 max-w-sm backdrop-blur-xl border border-white/20">
                <div class="p-2.5 bg-white/20 rounded-2xl flex-shrink-0 animate-bounce-subtle">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold truncate">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="p-2 hover:bg-white/20 rounded-xl transition-colors flex-shrink-0 active:scale-90">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </Transition>

        <!-- MOBILE LAYOUT (Enhanced iOS/Android Native Style) - Only < lg -->
        <div class="lg:hidden w-full max-w-md mx-auto px-4 pb-8 pt-8">
            
                        <div class="mb-6">
                <div class="relative overflow-hidden bg-gradient-to-br from-[#1c1c1e] to-[#2c2c2e] rounded-[2rem] p-5 border border-white/[0.08] shadow-xl">
                    <!-- Subtle animated gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/[0.05] via-purple-500/[0.05] to-pink-500/[0.05] animate-gradient-shift"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <!-- Time -->
                        <div class="flex-1">
                            <p class="text-gray-400 text-xs font-medium mb-1 tracking-wide">{{ date }}</p>
                            <h2 class="text-4xl font-bold text-white tracking-tight">{{ time }}</h2>
                        </div>
                        
                        <!-- Date badge -->
                        <div class="flex flex-col items-end">
                            <div class="px-4 py-2 bg-white/[0.08] backdrop-blur-xl rounded-2xl border border-white/[0.08] shadow-lg">
                                <p class="text-2xl font-bold text-white text-center leading-none mb-0.5">{{ new Date().getDate() }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ fullDate.split(' ')[1] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Greeting Section -->
            <!-- <div class="mb-6">
                <p class="text-gray-400 text-sm font-medium mb-1">{{ greeting }},</p>
                <h1 class="text-3xl font-bold text-white">{{ user.name.split(' ')[0] }} 👋</h1>
            </div> -->

            <!-- Premium Stats Cards - Apple Style -->
            <div class="mb-6 grid grid-cols-3 gap-3">
                <div 
                    v-for="stat in quickStats" 
                    :key="stat.label"
                    class="relative group"
                >
                    <!-- Card -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-[#1c1c1e] to-[#2c2c2e] rounded-[1.75rem] p-5 shadow-xl hover:shadow-2xl transition-all duration-500 border border-white/[0.08] hover:scale-[1.03] active:scale-95">
                        <!-- Ambient glow -->
                        <div :class="`absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br ${stat.color} opacity-20 blur-2xl rounded-full group-hover:opacity-30 transition-opacity duration-500`"></div>
                        
                        <!-- Top icon badge -->
                        <div class="relative mb-3">
                            <div :class="`w-12 h-12 rounded-2xl bg-gradient-to-br ${stat.color} flex items-center justify-center shadow-lg shadow-black/20`">
                                <span class="text-2xl filter drop-shadow-lg">{{ stat.icon }}</span>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="relative">
                            <p class="text-3xl font-bold text-white mb-0.5 tracking-tight">{{ stat.value }}</p>
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest">{{ stat.label }}</p>
                        </div>
                        
                        <!-- Bottom highlight -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-white/[0.05] to-transparent"></div>
                    </div>
                </div>
            </div>

            <!-- Apple-Style Search Bar -->
            <div class="mb-6">
                <div class="relative group">
                    <!-- Glow effect on focus -->
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-[2rem] opacity-0 group-focus-within:opacity-20 blur-xl transition-all duration-500"></div>
                    
                    <!-- Search container -->
                    <div class="relative bg-[#1c1c1e] rounded-[2rem] border border-white/[0.08] shadow-xl overflow-hidden">
                        <!-- Inner highlight -->
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/[0.08] to-transparent"></div>
                        
                        <!-- Search icon -->
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Input -->
                        <input 
                            type="text" 
                            placeholder="Search services, documents..."
                            class="w-full px-5 py-4 pl-14 bg-transparent text-white placeholder-gray-500 text-[15px] focus:outline-none"
                        >
                        
                        <!-- Mic button -->
                        <button class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-xl bg-white/[0.08] hover:bg-white/[0.12] active:scale-90 transition-all duration-200">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Time Widget - Minimal Card -->
            

            <!-- iOS-Style App Grid -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="text-lg font-bold text-white">Quick Access</h3>
                    <button class="text-xs font-semibold text-blue-400 hover:text-blue-300 active:scale-95 transition-all">
                        View All
                    </button>
                </div>
                
                <div class="grid grid-cols-4 gap-4">
                    <component 
                        :is="app.link ? Link : 'button'"
                        v-for="(app, index) in mobileApps" 
                        :key="app.name"
                        :href="app.link"
                        @click="app.action === 'wizard' ? showWizard = true : null"
                        class="group flex flex-col items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-3xl p-2 active:scale-90 transition-all duration-300"
                        :style="`animation-delay: ${index * 30}ms`"
                    >
                        <!-- App Icon Container -->
                        <div class="relative w-full aspect-square">
                            <!-- Glow effect -->
                            <div 
                                v-if="app.pulse"
                                class="absolute inset-0 bg-red-500 rounded-[1.3rem] animate-ping opacity-30"
                            ></div>
                            
                            <div :class="`absolute -inset-1 bg-gradient-to-br ${app.gradient} rounded-[1.4rem] opacity-0 group-hover:opacity-60 blur-md transition-all duration-300 ${app.glow}`"></div>
                            
                            <!-- Icon -->
                            <div :class="`relative w-full h-full rounded-[1.3rem] bg-gradient-to-br ${app.gradient} shadow-xl flex items-center justify-center transition-all duration-300 group-hover:scale-105 group-active:scale-95`">
                                <!-- Inner shine -->
                                <div class="absolute inset-[1px] bg-gradient-to-b from-white/25 via-white/10 to-transparent rounded-[1.2rem] opacity-80"></div>
                                
                                <!-- Reflection effect -->
                                <div class="absolute inset-0 bg-gradient-to-b from-white/20 via-transparent to-transparent rounded-[1.3rem]"></div>
                                
                                <!-- Icon emoji -->
                                <span class="relative text-[32px] filter drop-shadow-xl">{{ app.icon }}</span>
                            </div>
                        </div>
                        
                        <!-- App Name -->
                        <span class="text-[11px] font-semibold text-gray-300 text-center leading-tight w-full px-1">
                            {{ app.name }}
                        </span>
                    </component>
                </div>
            </div>

            <!-- Featured Actions - Apple Card Style -->
            <div class="space-y-4 mb-6">
                <!-- Section Header -->
                <div class="flex items-center justify-between px-1">
                    <h3 class="text-lg font-bold text-white">Featured</h3>
                    <div class="flex gap-1.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-purple-400"></div>
                    </div>
                </div>

                <!-- Quick Action Cards -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- Pay Bills Card -->
                    <Link 
                        :href="route('bills.create')"
                        class="group relative overflow-hidden bg-gradient-to-br from-[#1c1c1e] to-[#2c2c2e] rounded-[1.75rem] p-4 border border-white/[0.08] shadow-xl hover:shadow-2xl active:scale-[0.97] transition-all duration-300"
                    >
                        <!-- Glow -->
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-green-500 to-emerald-500 opacity-20 blur-2xl rounded-full group-hover:opacity-30 transition-opacity"></div>
                        
                        <!-- Icon -->
                        <div class="mb-3 w-12 h-12 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-500/30">
                            <span class="text-2xl">💳</span>
                        </div>
                        
                        <!-- Content -->
                        <div class="relative">
                            <p class="text-sm font-bold text-white mb-1">Pay Bills</p>
                            <p class="text-xs text-gray-400 leading-relaxed">Quick & secure payments</p>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="absolute bottom-4 right-4 w-6 h-6 rounded-full bg-white/[0.08] flex items-center justify-center group-hover:bg-white/[0.12] transition-colors">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </Link>
                    
                    <!-- Report Issue Card -->
                    <button
                        @click="showWizard = true"
                        class="group relative overflow-hidden bg-gradient-to-br from-[#1c1c1e] to-[#2c2c2e] rounded-[1.75rem] p-4 border border-white/[0.08] shadow-xl hover:shadow-2xl active:scale-[0.97] transition-all duration-300"
                    >
                        <!-- Glow -->
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-gradient-to-br from-red-500 to-pink-500 opacity-20 blur-2xl rounded-full group-hover:opacity-30 transition-opacity"></div>
                        
                        <!-- Pulse indicator -->
                        <div class="absolute top-3 right-3 w-2 h-2 bg-red-400 rounded-full animate-pulse shadow-lg shadow-red-400/50"></div>
                        
                        <!-- Icon -->
                        <div class="mb-3 w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center shadow-lg shadow-red-500/30">
                            <span class="text-2xl animate-bounce-subtle">🚨</span>
                        </div>
                        
                        <!-- Content -->
                        <div class="relative">
                            <p class="text-sm font-bold text-white mb-1">Report</p>
                            <p class="text-xs text-gray-400 leading-relaxed">Issue or emergency</p>
                        </div>
                        
                        <!-- Arrow -->
                        <div class="absolute bottom-4 right-4 w-6 h-6 rounded-full bg-white/[0.08] flex items-center justify-center group-hover:bg-white/[0.12] transition-colors">
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>
                </div>

                <!-- Map Preview Card - Apple Maps Style -->
                <div class="relative overflow-hidden bg-gradient-to-br from-[#1c1c1e] to-[#2c2c2e] rounded-[1.75rem] border border-white/[0.08] shadow-xl">
                    <!-- Map Container -->
                    <div class="relative h-52 overflow-hidden group">
                        <!-- Badge -->
                        <div class="absolute top-4 left-4 z-10 flex items-center gap-2 bg-black/60 backdrop-blur-2xl px-3 py-2 rounded-full border border-white/[0.08] shadow-lg">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                            </span>
                            <span class="text-xs font-bold text-white">Live Map</span>
                        </div>
                        
                        <!-- Map -->
                        <div class="w-full h-full">
                            <DashboardMap />
                        </div>
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1c1c1e] via-transparent to-transparent pointer-events-none"></div>
                        
                        <!-- Bottom info bar -->
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent backdrop-blur-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-white">Barangay Overview</p>
                                    <p class="text-xs text-gray-300">View all locations & alerts</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white/[0.15] flex items-center justify-center group-hover:bg-white/[0.2] transition-colors">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apple-Style Floating Emergency Button -->
            <Link 
                :href="route('emergency.index')"
                class="fixed bottom-36 right-6 z-40 group"
            >
                <div class="relative">
                    <!-- Multiple pulse rings -->
                    <div class="absolute inset-0 rounded-full bg-red-400 animate-ping opacity-20"></div>
                    <div class="absolute inset-0 rounded-full bg-red-500 animate-ping opacity-30" style="animation-delay: 0.5s"></div>
                    
                    <!-- Shadow glow -->
                    <div class="absolute inset-0 bg-red-500 rounded-full blur-xl opacity-50 group-hover:opacity-70 transition-opacity"></div>
                    
                    <!-- Button -->
                    <div class="relative w-16 h-16 bg-gradient-to-br from-red-500 via-red-600 to-rose-700 rounded-full shadow-2xl flex items-center justify-center active:scale-90 transition-all duration-300 group-hover:scale-110 border-4 border-black/20">
                        <!-- Inner shine -->
                        <div class="absolute inset-[2px] bg-gradient-to-b from-white/30 to-transparent rounded-full"></div>
                        
                        <!-- Icon -->
                        <span class="relative text-3xl animate-bounce-subtle filter drop-shadow-xl">🚨</span>
                    </div>
                    
                    <!-- Label -->
                    <div class="absolute -bottom-7 left-1/2 -translate-x-1/2 whitespace-nowrap">
                        <div class="px-3 py-1 bg-black/60 backdrop-blur-xl rounded-full border border-white/10">
                            <p class="text-[10px] font-bold text-white">Emergency</p>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT (Original Dark Design) - lg and above -->
        <div class="hidden lg:block w-full max-w-7xl mx-auto px-4 pb-20">
            
            <!-- Header -->
            <header class="mb-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4 mb-6">
                    <!-- Greeting -->
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                            </span>
                            <p class="text-blue-300/70 text-[10px] font-bold tracking-widest uppercase">System Operational</p>
                        </div>
                        <h1 class="text-5xl font-extrabold text-white tracking-tight">
                            {{ greeting }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">{{ user.name.split(' ')[0] }}</span>
                        </h1>
                    </div>

                    <!-- Clock -->
                    <div class="text-right">
                        <div class="text-6xl font-thin text-white tracking-tight">{{ time }}</div>
                        <div class="text-white/40 font-medium text-xs uppercase tracking-widest mt-1">{{ date }}</div>
                    </div>
                </div>
            </header>

            <!-- Desktop Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Feature Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <component 
                            :is="feature.link ? Link : 'button'"
                            v-for="feature in desktopFeatures" 
                            :key="feature.title"
                            :href="feature.link"
                            @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="[
                                'group relative overflow-hidden flex flex-col justify-between p-5 rounded-3xl shadow-lg border border-white/10 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]',
                                `bg-gradient-to-br ${feature.gradient}`,
                                {
                                    'col-span-2': feature.size === 'large',
                                    'h-40': feature.size === 'large',
                                    'h-32': feature.size === 'medium',
                                },
                                'focus:outline-none focus-visible:ring-2 focus-visible:ring-white'
                              ]">
                            
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                            
                            <!-- Featured Badge -->
                            <div v-if="feature.featured" class="absolute top-4 left-4 z-10 px-2.5 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-bold text-white uppercase tracking-wider">
                                ✨ Popular
                            </div>

                            <!-- History Link -->
                            <Link v-if="feature.historyLink" 
                                  :href="feature.historyLink"
                                  @click.stop
                                  class="absolute top-4 right-4 z-10 flex items-center gap-1 pl-2.5 pr-1.5 py-1 bg-black/20 backdrop-blur-md rounded-full border border-white/10 text-[10px] font-bold text-white uppercase tracking-widest hover:bg-black/40 hover:scale-105 transition-all">
                                <span>History</span>
                                <svg class="w-3 h-3 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </Link>

                            <div class="relative z-0 flex flex-col justify-between h-full">
                                <!-- Icon -->
                                <div class="w-12 h-12 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                    {{ feature.icon }}
                                </div>

                                <!-- Content -->
                                <div class="space-y-1">
                                    <p class="text-white/60 text-xs font-semibold uppercase tracking-wider">{{ feature.subtitle }}</p>
                                    <h3 class="font-black text-white text-xl leading-tight">{{ feature.title }}</h3>
                                </div>
                            </div>
                        </component>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <aside class="lg:col-span-4 space-y-6">
                    
                    <!-- ID Card -->
                    <div class="aspect-[1.6/1] rounded-3xl overflow-hidden shadow-2xl">
                        <div class="h-full bg-gradient-to-br from-slate-800 via-slate-900 to-black p-6 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center text-lg border border-white/10">🏛️</div>
                                <div class="text-right">
                                    <div class="text-[9px] text-white/50 uppercase tracking-widest font-bold">Universal ID</div>
                                    <div class="text-white font-mono font-bold text-sm tracking-widest opacity-80">{{ user.id.toString().padStart(8, '0') }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl text-white font-black tracking-wide uppercase mb-1 truncate">{{ user.name }}</div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-1 bg-blue-500/20 border border-blue-500/30 rounded-full text-[9px] font-bold text-blue-300 uppercase tracking-wider">
                                        {{ isBusinessOwner ? 'Business Owner' : 'Citizen' }}
                                    </span>
                                    <span class="px-2 py-1 bg-green-500/20 border border-green-500/30 rounded-full text-[9px] font-bold text-green-300 uppercase tracking-wider">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Polls -->
                    <Link :href="route('polls.index')" 
                          class="block rounded-3xl overflow-hidden shadow-xl h-40 hover:scale-[1.02] transition-transform">
                        <div class="h-full bg-gradient-to-br from-yellow-600 to-amber-600 p-6 flex flex-col justify-between relative">
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                            <div class="relative flex justify-between items-start">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl shadow-lg">🗳️</div>
                                <span class="text-[10px] font-black bg-white text-amber-600 px-2 py-1 rounded uppercase tracking-widest">Live</span>
                            </div>
                            <div class="relative">
                                <h3 class="font-black text-white text-2xl uppercase tracking-wide">Freedom Poll</h3>
                                <p class="text-white/80 text-xs font-bold mt-1">Share Your Voice</p>
                            </div>
                        </div>
                    </Link>

                    <!-- Map -->
                    <div class="bg-black/30 backdrop-blur-xl border border-white/10 rounded-3xl p-1 h-80 shadow-2xl relative group">
                        <div class="absolute top-4 left-4 z-10 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white border border-white/10 shadow-lg">
                            📍 Live Map & Alerts
                        </div>
                        <div class="w-full h-full rounded-[1.8rem] overflow-hidden opacity-90 group-hover:opacity-100 transition-opacity">
                            <DashboardMap />
                        </div>
                    </div>
                    
                    <!-- Emergency -->
                    <Link :href="route('emergency.index')" 
                          class="block rounded-3xl overflow-hidden shadow-xl h-40 hover:scale-[1.02] transition-transform">
                        <div class="h-full bg-gradient-to-br from-red-600 to-orange-600 p-6 flex flex-col justify-between relative">
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-white/20 rounded-full animate-ping"></div>
                            <div class="relative flex justify-between items-start">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl shadow-lg">📞</div>
                                <span class="text-[10px] font-black bg-white text-red-600 px-2 py-1 rounded uppercase tracking-widest animate-pulse">SOS</span>
                            </div>
                            <div class="relative">
                                <h3 class="font-black text-white text-2xl uppercase tracking-wide">Emergency Call</h3>
                                <p class="text-white/80 text-xs font-bold mt-1">Tap for Command Center</p>
                            </div>
                        </div>
                    </Link>
                </aside>
            </div>
        </div>

        <IssueWizard v-if="showWizard" @close="showWizard = false" />
        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Enhanced animations */
@keyframes gradient-shift {
    0%, 100% { 
        transform: scale(1) rotate(0deg); 
    }
    50% { 
        transform: scale(1.02) rotate(1deg); 
    }
}

.animate-gradient-shift {
    animation: gradient-shift 15s ease-in-out infinite;
}

@keyframes float-orb {
    0%, 100% { 
        transform: translate(0, 0) scale(1); 
    }
    50% { 
        transform: translate(-20px, -20px) scale(1.1); 
    }
}

.animate-float-orb {
    animation: float-orb 15s ease-in-out infinite;
}

.animate-float-orb-delayed {
    animation: float-orb 20s ease-in-out infinite;
    animation-delay: 3s;
}

@keyframes bounce-subtle {
    0%, 100% { 
        transform: translateY(0); 
    }
    50% { 
        transform: translateY(-4px); 
    }
}

.animate-bounce-subtle {
    animation: bounce-subtle 2s ease-in-out infinite;
}

/* Toast animation */
.slide-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-fade-leave-active {
    transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
    transform: translateY(-30px) scale(0.8);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-30px) scale(0.8);
    opacity: 0;
}

/* Stagger animation for app icons */
@media (max-width: 1023px) {
    .grid > * {
        animation: fadeInUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) backwards;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Smooth spring animation */
@keyframes springIn {
    0% {
        opacity: 0;
        transform: scale(0.8) translateY(10px);
    }
    50% {
        transform: scale(1.05) translateY(-5px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Touch feedback */
@media (hover: none) and (pointer: coarse) {
    button:active,
    a:active {
        transform: scale(0.95);
    }
}

/* Reduce motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

/* Smooth font rendering */
* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 0;
    background: transparent;
}

/* Remove default focus outline, we use custom focus-visible */
*:focus {
    outline: none;
}

*:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
    border-radius: 0.5rem;
}
</style>
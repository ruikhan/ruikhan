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

// --- TOAST NOTIFICATION LOGIC ---
const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    carouselTimer = setInterval(nextSlide, 6000);
    miniCarouselTimer = setInterval(nextMiniSlide, 4000);

    if (flashSuccess.value) {
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 4000);
    }

    observeElements();
});

// --- LIVE CLOCK LOGIC ---
const time = ref('');
const date = ref('');
const greeting = ref('Good Morning');

const updateTime = () => {
    const now = new Date();
    time.value = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    
    const hour = now.getHours();
    if (hour < 12) greeting.value = 'Good Morning';
    else if (hour < 18) greeting.value = 'Good Afternoon';
    else greeting.value = 'Good Evening';
};

// --- MAIN CAROUSEL LOGIC ---
const currentSlide = ref(0);
const slides = [
    {
        id: 1,
        title: "System Online",
        subtitle: "All governance modules are fully operational and secured.",
        color: "bg-blue-500",
        image: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop",
        link: route('profile.edit')
    },
    {
        id: 2,
        title: "Typhoon Watch",
        subtitle: "Monitor flood levels and evacuation centers in real-time.",
        color: "bg-indigo-500",
        image: "https://images.unsplash.com/photo-1516912481808-540c3f4fe1bc?q=80&w=2069&auto=format&fit=crop",
        link: route('environment.index')
    },
    {
        id: 3,
        title: "Job Fair 2025",
        subtitle: "500+ Local vacancies available. Apply via the portal.",
        color: "bg-purple-500",
        image: "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?q=80&w=2072&auto=format&fit=crop",
        link: route('jobs.index')
    }
];

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
};

// ✨ NEW: MINI CAROUSEL FOR QUICK ACTIONS
const currentMiniSlide = ref(0);
const miniSlides = [
    { icon: '🎯', title: 'Quick Pay', subtitle: 'Bills & Fees', link: route('bills.create'), color: 'from-green-500 to-emerald-600' },
    { icon: '📋', title: 'New Request', subtitle: 'Documents', link: route('documents.create'), color: 'from-blue-500 to-indigo-600' },
    { icon: '💼', title: 'Find Jobs', subtitle: 'Opportunities', link: route('jobs.index'), color: 'from-purple-500 to-fuchsia-600' },
    { icon: '🏪', title: 'Shop Local', subtitle: 'Marketplace', link: route('marketplace.index'), color: 'from-orange-500 to-pink-600' },
];

const nextMiniSlide = () => {
    currentMiniSlide.value = (currentMiniSlide.value + 1) % miniSlides.length;
};

// ✨ NEW: STATS DATA
// const stats = ref([
//     { label: 'Documents', value: '12', change: '+3', trend: 'up', icon: '📄', color: 'from-blue-500 to-blue-600' },
//     { label: 'Payments', value: '₱2.4K', change: '+8%', trend: 'up', icon: '💰', color: 'from-green-500 to-green-600' },
//     { label: 'Services', value: '8', change: '+2', trend: 'up', icon: '⚡', color: 'from-purple-500 to-purple-600' },
//     { label: 'Rating', value: '4.9', change: '+0.2', trend: 'up', icon: '⭐', color: 'from-amber-500 to-amber-600' },
// ]);

// ✨ NEW: RECENT ACTIVITY
const recentActivity = ref([
    { id: 1, action: 'Document Approved', detail: 'Barangay Clearance', time: '2 mins ago', icon: '✅', color: 'text-green-400' },
    { id: 2, action: 'Payment Received', detail: 'Water Bill - ₱450', time: '1 hour ago', icon: '💳', color: 'text-blue-400' },
    { id: 3, action: 'New Opportunity', detail: 'Sales Associate Position', time: '3 hours ago', icon: '💼', color: 'text-purple-400' },
    { id: 4, action: 'Poll Voted', detail: 'Community Project 2025', time: '5 hours ago', icon: '🗳️', color: 'text-pink-400' },
]);

// ✨ NEW: QUICK ACTIONS
const quickActions = ref([
    { title: 'Report Issue', icon: '🚨', action: 'wizard', color: 'from-red-500 to-pink-600' },
    { title: 'Emergency', icon: '📞', link: route('emergency.index'), color: 'from-orange-500 to-red-600' },
    { title: 'Health Check', icon: '🩺', link: route('health.index'), color: 'from-cyan-500 to-blue-600' },
    { title: 'New Poll', icon: '🗳️', link: route('polls.index'), color: 'from-violet-500 to-purple-600' },
]);

// --- TIMERS ---
let clockTimer;
let carouselTimer;
let miniCarouselTimer;

onUnmounted(() => {
    clearInterval(clockTimer);
    clearInterval(carouselTimer);
    clearInterval(miniCarouselTimer);
});

// --- SCROLL ANIMATION OBSERVER ---
const observeElements = () => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-visible');
                }
            });
        },
        { threshold: 0.1 }
    );

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
};

// --- FEATURE DATA ---
const features = [
    // CORE SERVICES
    { 
        group: 'Core Services', 
        title: 'E-Documents', 
        subtitle: 'Clearances & Permits', 
        icon: '📄', 
        link: route('documents.create'),
        historyLink: route('documents.index'), 
        gradient: 'from-blue-600 to-indigo-700',
        size: 'col-span-1 md:col-span-2',
        delay: '0.1s'
    },
    { 
        group: 'Core Services', 
        title: 'Bills & Fees', 
        subtitle: 'Secure Utility Payments', 
        icon: '💳', 
        link: route('bills.create'),
        gradient: 'from-teal-600 to-emerald-700',
        size: 'col-span-1 md:col-span-1',
        delay: '0.2s'
    },
    { 
        group: 'Core Services', 
        title: 'E-Health', 
        subtitle: 'Telemed & Booking', 
        icon: '🩺', 
        link: route('health.index'),
        gradient: 'from-sky-600 to-cyan-700',
        size: 'col-span-1 md:col-span-1',
        delay: '0.5s'
    },
    
    // COMMUNITY & ACTION
    { 
        group: 'Community & Action', 
        title: 'Issue Report', 
        subtitle: 'Rapid Response System', 
        icon: '🚨', 
        link: null,
        action: 'wizard',
        gradient: 'from-red-600 to-pink-700',
        size: 'col-span-1 md:col-span-1',
        special: true,
        delay: '0.4s'
    },
    { 
        group: 'Community & Action', 
        title: 'Green Guard', 
        subtitle: 'Waste & Air Quality', 
        icon: '♻️', 
        link: route('environment.index'),
        gradient: 'from-indigo-600 to-blue-700',
        size: 'col-span-1 md:col-span-1',
        delay: '0.9s'
    },
    { 
        group: 'Community & Action', 
        title: 'Social Assisstance', 
        subtitle: 'Aid & Support', 
        icon: '🤝', 
        link: route('social.index'),
        gradient: 'from-violet-600 to-purple-700',
        size: 'col-span-2 md:col-span-2',
        delay: '0.7s'
    },
    //     { 
    //     group: 'Community & Action', 
    //     title: 'Polls Vote & Freedom Wall', 
    //     subtitle: 'Citizen Referendum', 
    //     icon: '🗳️', 
    //     link: route('polls.index'),
    //     gradient: 'from-violet-600 to-purple-700',
    //     size: 'col-span-1 md:col-span-1',
    //     delay: '0.7s'
    // },

    
    // MARKETPLACE & BUSINESS
    { 
        group: 'Marketplace & Business', 
        title: 'Marketplace', 
        subtitle: 'Browse Local Businesses', 
        icon: '🌐', 
        link: route('marketplace.index'),
        gradient: 'from-purple-600 to-fuchsia-700',
        size: 'col-span-1 md:col-span-2',
        delay: '0.75s',
        featured: true
    },
    { 
        group: 'Marketplace & Business', 
        title: isBusinessOwner.value ? 'My Business' : 'Business Hub', 
        subtitle: isBusinessOwner.value ? 'Manage Dashboard' : 'Incubation & Register', 
        icon: isBusinessOwner.value ? '📊' : '🏪', 
        link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), 
        gradient: isBusinessOwner.value ? 'from-blue-500 to-purple-600' : 'from-orange-500 to-amber-600',
        size: 'col-span-1 md:col-span-2',
        delay: '0.75s',
        special: true
    },
    
    // LOCAL ECONOMY
    { 
        group: 'Local Economy', 
        title: 'Market Prices', 
        subtitle: 'Live Commodity Rates', 
        icon: '📈', 
        link: route('market.index'),
        gradient: 'from-sky-600 to-cyan-700',
        size: 'col-span-1 md:col-span-1',
        delay: '0.8s'
    },
    { 
        group: 'Local Economy', 
        title: 'Places', 
        subtitle: 'Business Directory', 
        icon: '🏨', 
        link: route('establishments.index'),
        gradient: 'from-lime-600 to-green-700',
        size: 'col-span-1 md:col-span-1',
        delay: '0.3s'
    },
    { 
        group: 'Local Economy', 
        title: 'Local Jobs', 
        subtitle: 'Career Opportunities', 
        icon: '💼', 
        link: route('jobs.index'),
        gradient: 'from-yellow-600 to-red-700',
        size: 'col-span-2 md:col-span-2',
        delay: '1s'
    },
        { 
        group: 'Guidelines & Documentation', 
        title: 'System Architectural', 
        subtitle: 'Browse Local Businesses', 
        icon: '🖧', 
        link: route('system.architecture'),
        gradient: 'from-red-600 to-black-700',
        size: 'col-span-1 md:col-span-2',
        delay: '0.75s',
    },
    { 
        group: 'Guidelines & Documentation',
        title: 'Workflow Documentation',   
        icon: '🛢',
        link: route('system.docs'),
        gradient: 'from-red-600 to-black-700',
        size: 'col-span-1 md:col-span-2',
        delay: '0.75s',
    },
];

const groupedFeatures = features.reduce((acc, feature) => {
    (acc[feature.group] = acc[feature.group] || []).push(feature);
    return acc;
}, {});
</script>

<template>
    <Head title="Command Center" />

    <AuthenticatedLayout>
        
        <!-- Enhanced Toast -->
        <Transition name="slide-fade">
            <div v-if="showFlash && flashSuccess" 
                 class="fixed top-24 right-6 z-50 bg-gradient-to-r from-green-500 to-emerald-600 backdrop-blur-md border border-green-400/50 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 animate-bounce-in">
                <div class="p-2 bg-white/20 rounded-full backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wide">Success</h4>
                    <p class="text-xs font-medium">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="ml-2 p-1 hover:bg-white/20 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </Transition>

        <!-- Enhanced Background -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#0f172a] via-[#020617] to-black"></div>
            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')]"></div>

            <div class="absolute top-0 left-0 w-full h-[60vh] overflow-hidden opacity-30 mix-blend-screen pointer-events-none">
                <svg class="absolute w-[200%] h-full animate-wave-slow top-0 left-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 200" preserveAspectRatio="none">
                    <path d="M0,100 C400,150 800,50 1600,100 L1600,0 L0,0 Z" fill="url(#gradBlue)" fill-opacity="0.4"></path>
                    <defs>
                        <linearGradient id="gradBlue" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0" />
                            <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:0.8" />
                            <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-float-delayed"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 font-sans">
            
            <!-- Enhanced Header with Stats -->
            <div class="mb-8 animate-slide-down">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6">
                    <div class="animate-fade-in-left">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            <p class="text-blue-300/80 text-xs font-bold tracking-widest uppercase">System Operational</p>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight drop-shadow-lg">
                            {{ greeting }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 animate-gradient">{{ user.name.split(' ')[0] }}</span>
                        </h1>
                    </div>
                    <div class="mt-4 md:mt-0 text-right animate-fade-in-right">
                        <div class="text-6xl font-thin text-white tracking-tighter leading-none drop-shadow-lg">{{ time }}</div>
                        <div class="text-white/50 font-medium text-sm uppercase tracking-widest mt-1">{{ date }}</div>
                    </div>
                </div>

                <!-- ✨ NEW: Stats Overview -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-scale-in">
                    <!-- <div v-for="stat in stats" :key="stat.label" class="group relative overflow-hidden rounded-2xl bg-black/40 backdrop-blur-xl border border-white/10 p-4 hover:scale-105 transition-all duration-300">
                        <div :class="`absolute inset-0 bg-gradient-to-br ${stat.color} opacity-20 group-hover:opacity-30 transition-opacity`"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-2xl">{{ stat.icon }}</div>
                                <span :class="[
                                    'text-xs font-bold px-2 py-0.5 rounded-full',
                                    stat.trend === 'up' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'
                                ]">
                                    {{ stat.change }}
                                </span>
                            </div>
                            <div class="text-2xl font-bold text-white mb-0.5">{{ stat.value }}</div>
                            <div class="text-xs text-white/60 font-medium uppercase tracking-wider">{{ stat.label }}</div>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Main Carousel -->
                    <div class="relative h-64 rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/20 group animate-scale-in">
                        <div v-for="(slide, index) in slides" :key="slide.id" 
                             class="absolute inset-0 transition-all duration-1000 ease-in-out"
                             :class="index === currentSlide ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 z-0'">
                            
                            <img :src="slide.image" class="absolute inset-0 w-full h-full object-cover opacity-60">
                            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>
                            
                            <div class="absolute inset-0 overflow-hidden">
                                <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-particle-1"></div>
                                <div class="absolute w-1 h-1 bg-white/20 rounded-full animate-particle-2"></div>
                                <div class="absolute w-1.5 h-1.5 bg-white/25 rounded-full animate-particle-3"></div>
                            </div>
                            
                            <div class="relative z-20 h-full flex flex-col justify-center px-10">
                                <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 w-fit rounded-full text-[10px] font-bold text-white uppercase tracking-widest mb-4 animate-slide-in-left">
                                    Featured News
                                </span>
                                <h2 class="text-4xl font-bold text-white mb-2 leading-tight max-w-lg animate-slide-in-left" style="animation-delay: 0.1s;">{{ slide.title }}</h2>
                                <p class="text-white/70 text-lg mb-6 max-w-md animate-slide-in-left" style="animation-delay: 0.2s;">{{ slide.subtitle }}</p>
                                
                                <Link :href="slide.link" class="flex items-center gap-2 text-white font-bold group/link w-fit animate-slide-in-left" style="animation-delay: 0.3s;">
                                    <span>Open Module</span>
                                    <svg class="w-5 h-5 transition-transform group-hover/link:translate-x-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </Link>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-6 right-8 z-30 flex gap-2">
                            <button v-for="(slide, index) in slides" :key="index" @click="currentSlide = index" 
                                :class="[
                                    'h-1.5 rounded-full transition-all duration-500',
                                    index === currentSlide ? 'bg-white w-8 shadow-lg shadow-white/50' : 'bg-white/30 w-2 hover:bg-white/50'
                                ]">
                            </button>
                        </div>
                    </div>

                    <!-- ✨ NEW: Mini Carousel for Quick Actions -->
                    <div class="relative h-32 rounded-[2rem] overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 shadow-xl border border-white/20 animate-scale-in" style="animation-delay: 0.1s;">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                        
                        <div v-for="(slide, index) in miniSlides" :key="index"
                             class="absolute inset-0 transition-all duration-700 ease-out"
                             :class="index === currentMiniSlide ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-full'">
                            <Link :href="slide.link" class="h-full flex items-center justify-between px-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl border border-white/20 shadow-lg animate-pulse-slow">
                                        {{ slide.icon }}
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-black text-white mb-1">{{ slide.title }}</h3>
                                        <p class="text-white/70 text-sm font-semibold">{{ slide.subtitle }}</p>
                                    </div>
                                </div>
                                <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </Link>
                        </div>

                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                            <button v-for="(slide, index) in miniSlides" :key="index" @click="currentMiniSlide = index"
                                :class="[
                                    'w-1.5 h-1.5 rounded-full transition-all duration-300',
                                    index === currentMiniSlide ? 'bg-white w-6' : 'bg-white/40'
                                ]">
                            </button>
                        </div>
                    </div>

                    <!-- Feature Groups -->
                    <div v-for="(group, groupName, groupIndex) in groupedFeatures" :key="groupName" 
                         class="animate-on-scroll opacity-0 space-y-4"
                         :style="{ 'animation-delay': `${0.2 + groupIndex * 0.15}s` }">
                        
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-white/50 pt-6 border-t border-white/10 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                                {{ groupName }}
                            </h2>
                            <span class="text-xs text-white/30 font-semibold">{{ group.length }} Services</span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="feature in group" :key="feature.title" 
                                 :style="{ 'animation-delay': feature.delay }"
                                 :class="[
                                    'group/card relative overflow-hidden flex flex-col justify-between p-5 rounded-[1.75rem] shadow-xl border border-white/10 transition-all duration-500 hover:scale-[1.03] hover:shadow-2xl hover:shadow-white/10 hover:border-white/30 hover:-translate-y-1',
                                    `bg-gradient-to-br ${feature.gradient}`,
                                    feature.size,
                                    (groupName === 'Core Services' && feature.title === 'E-Documents') ? 'h-52' : 'h-36',
                                    feature.special ? 'ring-2 ring-white/20 ring-offset-2 ring-offset-transparent animate-pulse-slow' : '',
                                    feature.featured ? 'ring-2 ring-purple-500/50 ring-offset-2 ring-offset-transparent' : '',
                                    'animate-on-scroll opacity-0'
                                  ]">
                                
                                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 mix-blend-overlay"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/card:translate-x-full transition-transform duration-1000"></div>
                                
                                <!-- Featured Badge -->
                                <div v-if="feature.featured" class="absolute top-4 left-4 z-20 px-2.5 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-bold text-white uppercase tracking-wider animate-pulse">
                                    ✨ Popular
                                </div>
                                
                                <Link v-if="feature.link" :href="feature.link" class="absolute inset-0 z-10 focus:outline-none"></Link>
                                <button v-else-if="feature.action === 'wizard'" @click="showWizard = true" class="absolute inset-0 z-10 w-full h-full cursor-pointer focus:outline-none"></button>

                                <Link v-if="feature.historyLink" 
                                      :href="feature.historyLink"
                                      class="absolute top-4 right-4 z-20 flex items-center gap-1 pl-3 pr-2 py-1.5 bg-black/20 backdrop-blur-md rounded-full border border-white/10 text-[10px] font-bold text-white uppercase tracking-widest hover:bg-black/40 hover:scale-105 transition-all">
                                    <span>History</span>
                                    <svg class="w-3 h-3 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </Link>

                                <div class="relative z-0 flex flex-col justify-start pointer-events-none h-full">
                                    <div class="flex-grow">
                                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-xl shadow-lg mb-2 group-hover/card:scale-110 group-hover/card:rotate-6 transition-all duration-300">
                                            {{ feature.icon }}
                                        </div>
                                        <p class="text-white/70 text-xs font-semibold uppercase tracking-widest mt-2">{{ feature.subtitle }}</p>
                                    </div>

                                    <div class="mt-auto">
                                        <h3 class="font-black text-white text-xl leading-tight">{{ feature.title }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- ID Card -->
                    <div class="group/id relative aspect-[1.58/1] rounded-[2rem] overflow-hidden shadow-2xl transition-all duration-500 hover:scale-105 hover:shadow-blue-500/20 border border-white/10 animate-scale-in" style="animation-delay: 0.2s;">
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-black"></div>
                        <div class="absolute inset-0 opacity-30 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-400 via-purple-500 to-transparent group-hover/id:opacity-50 transition-opacity"></div>
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400/0 via-purple-400/10 to-pink-400/0 opacity-0 group-hover/id:opacity-100 transition-opacity duration-700"></div>
                        
                        <div class="absolute inset-0 p-6 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center text-lg border border-white/10 group-hover/id:scale-110 transition-transform">🏛️</div>
                                <div class="text-right">
                                    <div class="text-[9px] text-white/60 uppercase tracking-widest font-bold">Universal ID</div>
                                    <div class="text-white font-mono font-bold text-sm tracking-widest opacity-80">{{ user.id.toString().padStart(8, '0') }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="text-2xl text-white font-black tracking-wide uppercase mb-1">{{ user.name }}</div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-2 py-0.5 bg-blue-500/20 border border-blue-500/30 rounded-full text-[9px] font-bold text-blue-300 uppercase tracking-wider animate-pulse-slow">
                                        {{ isBusinessOwner ? 'Business Owner' : 'Citizen' }}
                                    </span>
                                    <span class="px-2 py-0.5 bg-green-500/20 border border-green-500/30 rounded-full text-[9px] font-bold text-green-300 uppercase tracking-wider">Active Status</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <Link :href="route('polls.index')" class="group/sos bg-gradient-to-br from-yellow-600 to-blue-600 border border-blue/20 p-6 rounded-[2rem] shadow-xl shadow-blue-600/30 hover:scale-[1.02] hover:shadow-blue-600/50 transition-all flex flex-col justify-between relative overflow-hidden h-40 animate-scale-in" style="animation-delay: 0.6s;">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                        <div class="absolute -top-3 -right-3 w-8 h-8 bg-white/30 rounded-full animate-ping"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 rounded-full border-4 border-white/20 animate-ping-slow"></div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover/sos:scale-110 group-hover/sos:rotate-12 transition-all">@</div>
                            <span class="text-[10px] font-black bg-white text-shadow-20px text-blue-600 px-2 py-1 rounded uppercase tracking-widest animate-pulse">ENOT-PILI</span>
                        </div>
                        
                        <div class="relative z-10">
                            <h3 class="font-black text-white text-2xl uppercase tracking-wide">Freedom Poll</h3>
                            <p class="text-white/80 text-xs font-bold mt-1">E-PILI Suggestions and Rant</p>
                        </div>
                    </Link>

                    <!-- ✨ NEW: Quick Actions Panel -->
                    <div class="rounded-[2rem] bg-black/40 backdrop-blur-xl border border-white/10 p-5 shadow-2xl animate-scale-in" style="animation-delay: 0.3s;">
                        <h4 class="text-xs font-bold text-white/50 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                            Quick Actions
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <button v-for="action in quickActions" :key="action.title"
                                    @click="action.action === 'wizard' ? showWizard = true : null"
                                    :class="`group relative h-24 rounded-2xl bg-gradient-to-br ${action.color} border border-white/20 p-4 transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden`">
                                <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition-colors"></div>
                                <Link v-if="action.link" :href="action.link" class="absolute inset-0 z-10"></Link>
                                <div class="relative z-0 h-full flex flex-col justify-between">
                                    <div class="text-2xl">{{ action.icon }}</div>
                                    <div class="text-white font-bold text-sm">{{ action.title }}</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="bg-black/40 backdrop-blur-xl border border-white/10 rounded-[2rem] p-1 h-80 shadow-2xl relative group/map animate-scale-in" style="animation-delay: 0.4s;">
                        <div class="absolute top-4 left-4 z-10 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-white border border-white/10 shadow-lg animate-slide-in-down">
                            📍 Live Map & Alerts
                        </div>
                        <div class="w-full h-full rounded-[1.8rem] overflow-hidden opacity-90 group-hover/map:opacity-100 transition-opacity">
                            <DashboardMap />
                        </div>
                    </div>

                    <!-- ✨ NEW: Activity Feed -->
                    <div class="rounded-[2rem] bg-black/40 backdrop-blur-xl border border-white/10 p-5 shadow-2xl max-h-80 overflow-y-auto animate-scale-in" style="animation-delay: 0.5s;">
                        <h4 class="text-xs font-bold text-white/50 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                            Recent Activity
                        </h4>
                        <div class="space-y-3">
                            <div v-for="activity in recentActivity" :key="activity.id" 
                                 class="group flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 transition-all cursor-pointer">
                                <div :class="`text-xl ${activity.color}`">{{ activity.icon }}</div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-sm font-semibold text-white truncate">{{ activity.action }}</h5>
                                    <p class="text-xs text-white/60 truncate">{{ activity.detail }}</p>
                                    <span class="text-[10px] text-white/40 font-medium">{{ activity.time }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Emergency Button -->
                    <Link :href="route('emergency.index')" class="group/sos bg-gradient-to-br from-red-600 to-orange-600 border border-white/20 p-6 rounded-[2rem] shadow-xl shadow-red-600/30 hover:scale-[1.02] hover:shadow-red-600/50 transition-all flex flex-col justify-between relative overflow-hidden h-40 animate-scale-in" style="animation-delay: 0.6s;">
                        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
                        <div class="absolute -top-3 -right-3 w-8 h-8 bg-white/30 rounded-full animate-ping"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 rounded-full border-4 border-white/20 animate-ping-slow"></div>
                        
                        <div class="relative z-10 flex justify-between items-start">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl shadow-lg group-hover/sos:scale-110 group-hover/sos:rotate-12 transition-all">📞</div>
                            <span class="text-[10px] font-black bg-white text-red-600 px-2 py-1 rounded uppercase tracking-widest animate-pulse">SOS</span>
                        </div>
                        
                        <div class="relative z-10">
                            <h3 class="font-black text-white text-2xl uppercase tracking-wide">Emergency Call</h3>
                            <p class="text-white/80 text-xs font-bold mt-1">Tap for Command Center</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <IssueWizard v-if="showWizard" @close="showWizard = false" />
        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
/* Enhanced Animations */
@keyframes waveSlow { 
    0% { transform: translateX(0); } 
    100% { transform: translateX(-50%); } 
}
.animate-wave-slow { animation: waveSlow 30s linear infinite; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-float { animation: float 6s ease-in-out infinite; }

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(20px); }
}
.animate-float-delayed { animation: float-delayed 8s ease-in-out infinite; }

@keyframes slide-down { 
    from { opacity: 0; transform: translateY(-30px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.animate-slide-down { animation: slide-down 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }

@keyframes fade-in-left { 
    from { opacity: 0; transform: translateX(-30px); } 
    to { opacity: 1; transform: translateX(0); } 
}
.animate-fade-in-left { animation: fade-in-left 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }

@keyframes fade-in-right { 
    from { opacity: 0; transform: translateX(30px); } 
    to { opacity: 1; transform: translateX(0); } 
}
.animate-fade-in-right { animation: fade-in-right 0.8s cubic-bezier(0.2, 0.8, 0.2, 1); }

@keyframes scale-in { 
    from { opacity: 0; transform: scale(0.95); } 
    to { opacity: 1; transform: scale(1); } 
}
.animate-scale-in { animation: scale-in 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes slide-in-left { 
    from { opacity: 0; transform: translateX(-20px); } 
    to { opacity: 1; transform: translateX(0); } 
}
.animate-slide-in-left { animation: slide-in-left 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes slide-in-down { 
    from { opacity: 0; transform: translateY(-20px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.animate-slide-in-down { animation: slide-in-down 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes slide-in-up { 
    from { opacity: 0; transform: translateY(20px); } 
    to { opacity: 1; transform: translateY(0); } 
}
.animate-slide-in-up { animation: slide-in-up 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

@keyframes bounce-in { 
    0% { opacity: 0; transform: translateX(100%) scale(0.5); }
    60% { opacity: 1; transform: translateX(-10px) scale(1.05); }
    100% { opacity: 1; transform: translateX(0) scale(1); }
}
.animate-bounce-in { animation: bounce-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }

@keyframes gradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

@keyframes ping-slow {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}
.animate-ping-slow { animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite; }

@keyframes pulse-slow {
    0%, 100% { opacity: 1; }
    50% { opacity: .7; }
}
.animate-pulse-slow { animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }

/* Particle Animations */
@keyframes particle-1 {
    0% { top: 10%; left: 10%; opacity: 0; }
    50% { opacity: 1; }
    100% { top: 90%; left: 90%; opacity: 0; }
}
.animate-particle-1 { animation: particle-1 15s ease-in-out infinite; }

@keyframes particle-2 {
    0% { top: 20%; right: 10%; opacity: 0; }
    50% { opacity: 1; }
    100% { bottom: 20%; right: 90%; opacity: 0; }
}
.animate-particle-2 { animation: particle-2 20s ease-in-out infinite; }

@keyframes particle-3 {
    0% { bottom: 10%; left: 80%; opacity: 0; }
    50% { opacity: 1; }
    100% { top: 10%; left: 20%; opacity: 0; }
}
.animate-particle-3 { animation: particle-3 18s ease-in-out infinite; }

/* Scroll Animation */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s cubic-bezier(0.2, 0.8, 0.2, 1),
                transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.animate-on-scroll.animate-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Slide Fade Transition */
.slide-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.slide-fade-enter-from {
    transform: translateX(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(-20px);
    opacity: 0;
}

/* Smooth Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(30, 41, 59, 0.3);
}

::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.5);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.7);
}
</style>
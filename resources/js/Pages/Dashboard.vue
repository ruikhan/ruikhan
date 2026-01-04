<script setup>
import ChatAssistant from '@/Components/ChatAssistant.vue';
import DashboardMap from '@/Components/DashboardMap.vue';
import IssueWizard from '@/Components/IssueWizard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';

const user = usePage().props.auth.user;
const page = usePage();
const showWizard = ref(false);
const mounted = ref(false);
const currentTime = ref(Date.now());

// Cinematic Intro State
const showIntro = ref(true);
const introPhase = ref('logo');
const currentFeatureIndex = ref(0);
const skipIntro = ref(false);

// Mouse parallax effect
const mouseX = ref(0);
const mouseY = ref(0);

const isBusinessOwner = computed(() => {
    return user.role === 'business_owner' || user.has_business;
});

const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

const time = ref('');
const date = ref('');
const fullDate = ref('');
const greeting = ref('Good Morning');
const dayPeriod = ref('AM');

// Optimized sound with Web Audio API
const audioContext = ref(null);
const playSound = (frequency, duration) => {
    try {
        if (!audioContext.value) {
            audioContext.value = new (window.AudioContext || window.webkitAudioContext)();
        }
        const ctx = audioContext.value;
        const oscillator = ctx.createOscillator();
        const gainNode = ctx.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(ctx.destination);
        
        oscillator.frequency.value = frequency;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.05, ctx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
        
        oscillator.start(ctx.currentTime);
        oscillator.stop(ctx.currentTime + duration);
    } catch (e) {
        // Silent fail
    }
};

const updateTime = () => {
    const now = new Date();
    currentTime.value = now.getTime();
    
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const isPM = hours >= 12;
    
    dayPeriod.value = isPM ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    
    time.value = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    date.value = now.toLocaleDateString('en-US', { weekday: 'long' });
    fullDate.value = now.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
    
    if (hours < 12) greeting.value = 'Good Morning';
    else if (hours < 18) greeting.value = 'Good Afternoon';
    else greeting.value = 'Good Evening';
};

const cinematicFeatures = [
    { icon: '⚡', title: 'Smart Services', subtitle: 'Lightning Fast', description: 'Instant document processing with AI-powered verification', gradient: 'from-blue-500 via-indigo-500 to-purple-600', particles: '💫' },
    { icon: '💳', title: 'Secure Payments', subtitle: 'Bank-Grade Security', description: 'Military-grade encryption protects every transaction', gradient: 'from-emerald-500 via-teal-500 to-cyan-600', particles: '🔒' },
    { icon: '🚨', title: 'Emergency Response', subtitle: '24/7 Monitoring', description: 'Real-time alerts with instant geo-location tracking', gradient: 'from-red-500 via-pink-500 to-rose-600', particles: '🚑' },
    { icon: '🏪', title: 'Digital Marketplace', subtitle: 'Local Economy', description: 'Connect with local businesses and services instantly', gradient: 'from-orange-500 via-amber-500 to-yellow-600', particles: '🛒' }
];

const mobileApps = [
    { icon: '⚡', name: 'Services', link: route('services.landing'), gradient: 'from-blue-500 via-blue-600 to-indigo-600', glow: 'shadow-blue-500/50' },
    { icon: '💳', name: 'Bills', link: route('bills.create'), gradient: 'from-green-500 via-emerald-600 to-teal-600', glow: 'shadow-green-500/50' },
    { icon: '🩺', name: 'Health', link: route('health.index'), gradient: 'from-red-500 via-rose-600 to-pink-600', glow: 'shadow-red-500/50' },
    { icon: '📍', name: 'Places', link: route('establishments.index'), gradient: 'from-purple-500 via-violet-600 to-fuchsia-600', glow: 'shadow-purple-500/50' },
    { icon: '🏪', name: 'Market', link: route('marketplace.index'), gradient: 'from-orange-500 via-amber-600 to-yellow-600', glow: 'shadow-orange-500/50' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: 'from-indigo-500 via-blue-600 to-cyan-600', glow: 'shadow-indigo-500/50' },
    { icon: '💼', name: 'Jobs', link: route('jobs.index'), gradient: 'from-yellow-500 via-orange-600 to-amber-600', glow: 'shadow-yellow-500/50' },
    { icon: '📈', name: 'Prices', link: route('market.index'), gradient: 'from-cyan-500 via-teal-600 to-emerald-600', glow: 'shadow-cyan-500/50' },
    { icon: '♻️', name: 'Green', link: route('environment.index'), gradient: 'from-emerald-500 via-green-600 to-lime-600', glow: 'shadow-emerald-500/50' },
    { icon: '🤝', name: 'Social', link: route('social.index'), gradient: 'from-violet-500 via-purple-600 to-indigo-600', glow: 'shadow-violet-500/50' },
    { icon: '🚨', name: 'Report', link: null, action: 'wizard', gradient: 'from-red-600 via-rose-700 to-pink-700', glow: 'shadow-red-600/60', pulse: true },
    { icon: '🗳️', name: 'Polls', link: route('polls.index'), gradient: 'from-amber-500 via-yellow-600 to-orange-600', glow: 'shadow-amber-500/50' },
];

const desktopFeatures = [
    { title: 'Smart Services', subtitle: 'Permits & Clearances', icon: '⚡', link: route('services.landing'), historyLink: route('services.index'), gradient: 'from-blue-500 to-indigo-600', size: 'large' },
    { title: 'Bills & Fees', subtitle: 'Secure Payments', icon: '💳', link: route('bills.create'), gradient: 'from-teal-500 to-emerald-600', size: 'medium' },
    { title: 'E-Health', subtitle: 'Telemed & Booking', icon: '🩺', link: route('health.index'), gradient: 'from-sky-500 to-cyan-600', size: 'medium' },
    { title: 'Issue Report', subtitle: 'Quick Response', icon: '🚨', link: null, action: 'wizard', gradient: 'from-red-500 to-pink-600', size: 'medium', special: true },
    { title: 'Green Guard', subtitle: 'Waste & Air', icon: '♻️', link: route('environment.index'), gradient: 'from-green-500 to-emerald-600', size: 'medium' },
    { title: 'Social Aid', subtitle: 'Support Programs', icon: '🤝', link: route('social.index'), gradient: 'from-violet-500 to-purple-600', size: 'medium' },
    { title: 'Marketplace', subtitle: 'Local Businesses', icon: '🌐', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', size: 'large', featured: true },
    { title: isBusinessOwner.value ? 'My Business' : 'Business Hub', subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', icon: isBusinessOwner.value ? '📊' : '🏪', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600', size: 'medium' },
    { title: 'Market Prices', subtitle: 'Live Rates', icon: '📈', link: route('market.index'), gradient: 'from-cyan-500 to-blue-600', size: 'medium' },
    { title: 'Places', subtitle: 'Directory', icon: '🏨', link: route('establishments.index'), gradient: 'from-lime-500 to-green-600', size: 'medium' },
    { title: 'Local Jobs', subtitle: 'Opportunities', icon: '💼', link: route('jobs.index'), gradient: 'from-yellow-500 to-orange-600', size: 'medium' },
];

let clockTimer;
let featureInterval;
let introTimers = [];

const handleSkipIntro = () => {
    skipIntro.value = true;
    playSound(800, 0.1);
    introPhase.value = 'fadeout';
    
    // Clear all intro timers
    introTimers.forEach(timer => clearTimeout(timer));
    introTimers = [];
    if (featureInterval) clearInterval(featureInterval);
    
    setTimeout(() => {
        showIntro.value = false;
        nextTick(() => triggerEntranceAnimation());
    }, 500);
};

const startCinematicSequence = () => {
    playSound(523, 0.2);
    
    introTimers.push(setTimeout(() => { 
        if (!skipIntro.value) {
            introPhase.value = 'welcome';
            playSound(659, 0.2);
        }
    }, 1500));
    
    introTimers.push(setTimeout(() => { 
        if (!skipIntro.value) {
            introPhase.value = 'features';
            playSound(784, 0.2);
            startFeatureLoop();
        }
    }, 4000));
    
    introTimers.push(setTimeout(() => { 
        if (!skipIntro.value) {
            introPhase.value = 'stats';
            playSound(880, 0.2);
        }
    }, 16000));
    
    introTimers.push(setTimeout(() => { 
        if (!skipIntro.value) {
            introPhase.value = 'ready';
            playSound(1047, 0.3);
        }
    }, 19000));
    
    introTimers.push(setTimeout(() => {
        if (!skipIntro.value) {
            introPhase.value = 'fadeout';
            playSound(1319, 0.4);
            setTimeout(() => { 
                showIntro.value = false;
                nextTick(() => triggerEntranceAnimation());
            }, 1000);
        }
    }, 21000));
};

const startFeatureLoop = () => {
    featureInterval = setInterval(() => {
        if (skipIntro.value || introPhase.value !== 'features') { 
            clearInterval(featureInterval);
            return;
        }
        currentFeatureIndex.value = (currentFeatureIndex.value + 1) % cinematicFeatures.length;
        playSound(700 + (currentFeatureIndex.value * 100), 0.15);
    }, 3000);
};

const triggerEntranceAnimation = () => { 
    mounted.value = true;
    playSound(1047, 0.2);
};

// Mouse parallax effect for premium feel
const handleMouseMove = (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 20;
    const y = (e.clientY / window.innerHeight - 0.5) * 20;
    mouseX.value = x;
    mouseY.value = y;
};

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    
    if (flashSuccess.value) { 
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 4000);
    }
    
    // Check session storage
    const hasSeenIntro = sessionStorage.getItem('epili-intro-seen');
    if (!hasSeenIntro) {
        startCinematicSequence();
        sessionStorage.setItem('epili-intro-seen', 'true');
    } else {
        showIntro.value = false;
        requestAnimationFrame(() => {
            setTimeout(triggerEntranceAnimation, 50);
        });
    }
    
    // Add mouse parallax
    window.addEventListener('mousemove', handleMouseMove);
});

onUnmounted(() => { 
    clearInterval(clockTimer);
    if (featureInterval) clearInterval(featureInterval);
    introTimers.forEach(timer => clearTimeout(timer));
    window.removeEventListener('mousemove', handleMouseMove);
});

const handleAppClick = (app) => {
    if ('vibrate' in navigator) { navigator.vibrate(10); }
    if (app.action === 'wizard') { showWizard.value = true; }
};

const currentFeature = computed(() => cinematicFeatures[currentFeatureIndex.value]);
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        
        <!-- ==================== CINEMATIC INTRODUCTION ==================== -->
        <Transition name="intro-fade">
            <div v-if="showIntro" class="cinematic-overlay">
                
                <!-- Ultra Premium Background -->
                <div class="cinematic-bg">
                    <div class="cinematic-gradient"></div>
                    
                    <!-- Animated mesh gradient with GPU acceleration -->
                    <div class="cinematic-mesh">
                        <div class="mesh-blob mesh-blob-1"></div>
                        <div class="mesh-blob mesh-blob-2"></div>
                        <div class="mesh-blob mesh-blob-3"></div>
                    </div>
                    
                    <!-- Optimized particles -->
                    <div class="cinematic-particles">
                        <div v-for="i in 40" :key="`dot-${i}`" class="particle-dot"
                             :style="{
                                 left: `${Math.random() * 100}%`,
                                 top: `${Math.random() * 100}%`,
                                 animationDelay: `${Math.random() * 5}s`,
                                 animationDuration: `${4 + Math.random() * 6}s`
                             }"></div>
                        
                        <div v-for="i in 20" :key="`star-${i}`" class="particle-star"
                             :style="{
                                 left: `${Math.random() * 100}%`,
                                 top: `${Math.random() * 100}%`,
                                 animationDelay: `${Math.random() * 3}s`
                             }">✨</div>
                    </div>
                    
                    <!-- Grid overlay -->
                    <div class="cinematic-grid"></div>
                </div>

                <!-- Skip Button -->
                <button @click="handleSkipIntro" class="skip-btn">
                    <span>Skip Intro</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Content -->
                <div class="cinematic-content">
                    
                    <!-- Phase 1: Logo -->
                    <Transition name="phase-fade">
                        <div v-if="introPhase === 'logo'" class="phase-container">
                            <div class="logo-reveal">
                                <div class="logo-glow"></div>
                                <div class="logo-icon">🏛️</div>
                                <div class="logo-ripple"></div>
                            </div>
                            <h2 class="logo-text">E-PILI</h2>
                            <p class="logo-subtitle">Digital Governance Platform</p>
                        </div>
                    </Transition>

                    <!-- Phase 2: Welcome -->
                    <Transition name="phase-fade">
                        <div v-if="introPhase === 'welcome'" class="phase-container">
                            <div class="welcome-badge">
                                <span class="welcome-pulse">
                                    <span class="pulse-ring"></span>
                                    <span class="pulse-dot"></span>
                                </span>
                                <span class="welcome-badge-text">Welcome Back</span>
                            </div>
                            <h1 class="welcome-greeting">{{ greeting }},</h1>
                            <h2 class="welcome-name">{{ user.name.split(' ')[0] }}</h2>
                            <p class="welcome-subtitle">Your command center awaits</p>
                        </div>
                    </Transition>

                    <!-- Phase 3: Features -->
                    <Transition name="phase-fade">
                        <div v-if="introPhase === 'features'" class="phase-container">
                            <TransitionGroup name="feature-slide">
                                <div :key="currentFeatureIndex" class="feature-showcase">
                                    <div class="feature-icon-wrapper">
                                        <div :class="`feature-glow bg-gradient-to-r ${currentFeature.gradient}`"></div>
                                        <div class="feature-particles">
                                            <span v-for="i in 8" :key="i" class="fp" :style="`animation-delay: ${i * 0.1}s`">
                                                {{ currentFeature.particles }}
                                            </span>
                                        </div>
                                        <div class="feature-icon">{{ currentFeature.icon }}</div>
                                    </div>
                                    <p :class="`feature-subtitle text-transparent bg-clip-text bg-gradient-to-r ${currentFeature.gradient}`">
                                        {{ currentFeature.subtitle }}
                                    </p>
                                    <h3 class="feature-title">{{ currentFeature.title }}</h3>
                                    <p class="feature-desc">{{ currentFeature.description }}</p>
                                    
                                    <div class="feature-progress">
                                        <div v-for="(f, i) in cinematicFeatures" :key="i"
                                             :class="['progress-bar', i === currentFeatureIndex && 'active']">
                                        </div>
                                    </div>
                                </div>
                            </TransitionGroup>
                        </div>
                    </Transition>

                    <!-- Phase 4: Stats -->
                    <Transition name="phase-fade">
                        <div v-if="introPhase === 'stats'" class="phase-container">
                            <h3 class="stats-title">Your Activity</h3>
                            <div class="stats-showcase">
                                <div v-for="(stat, i) in quickStats" :key="i" 
                                     class="stat-item"
                                     :style="`animation-delay: ${i * 150}ms`">
                                    <div :class="`stat-glow bg-gradient-to-br ${stat.color}`"></div>
                                    <div class="stat-icon">{{ stat.icon }}</div>
                                    <div class="stat-value">{{ stat.value }}</div>
                                    <div class="stat-label">{{ stat.label }}</div>
                                </div>
                            </div>
                            <p class="stats-loading">Preparing your dashboard...</p>
                        </div>
                    </Transition>

                    <!-- Phase 5: Ready -->
                    <Transition name="phase-fade">
                        <div v-if="introPhase === 'ready'" class="phase-container">
                            <div class="ready-icon">✓</div>
                            <h2 class="ready-title">All Systems Ready</h2>
                            <p class="ready-subtitle">Launching command center</p>
                        </div>
                    </Transition>

                </div>
            </div>
        </Transition>

        <!-- ==================== MAIN DASHBOARD CONTENT ==================== -->
        
        <Transition name="toast-bounce">
            <div v-if="showFlash && flashSuccess" class="toast-notification">
                <div class="toast-icon-wrapper">
                    <div class="toast-icon-pulse"></div>
                    <svg class="toast-icon-svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="toast-content">
                    <p class="toast-text">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="toast-close-btn">
                    <svg class="toast-close-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </Transition>

        <!-- MOBILE LAYOUT -->
        <div class="mobile-layout">
            <div class="time-widget" :class="{ 'animate-fade-in': mounted }">
                <div class="time-widget-container">
                    <div class="time-widget-gradient"></div>
                    <div class="time-widget-glow"></div>
                    <div class="time-widget-content">
                        <div class="time-section">
                            <p class="time-label">{{ date }}</p>
                            <div class="time-display">
                                <h2 class="time-value">{{ time }}</h2>
                                <span class="time-period">{{ dayPeriod }}</span>
                            </div>
                        </div>
                        <div class="date-badge-wrapper">
                            <div class="date-badge">
                                <div class="date-badge-shine"></div>
                                <p class="date-badge-day">{{ new Date().getDate() }}</p>
                                <p class="date-badge-month">{{ fullDate.split(' ')[1] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div v-for="(stat, index) in quickStats" :key="stat.label"
                    class="stat-card" :class="{ 'animate-scale-in': mounted }"
                    :style="`animation-delay: ${100 + index * 80}ms`">
                    <div :class="`stat-card-glow bg-gradient-to-br ${stat.color}`"></div>
                    <div class="stat-card-shine"></div>
                    <div class="stat-icon-wrapper">
                        <div :class="`stat-icon-badge bg-gradient-to-br ${stat.color}`">
                            <span class="stat-icon">{{ stat.icon }}</span>
                        </div>
                    </div>
                    <div class="stat-content">
                        <p class="stat-value">{{ stat.value }}</p>
                        <p class="stat-label">{{ stat.label }}</p>
                    </div>
                    <div class="stat-trend" :class="stat.trend.startsWith('+') ? 'stat-trend-up' : 'stat-trend-down'">
                        {{ stat.trend }}
                    </div>
                    <div class="stat-highlight"></div>
                </div>
            </div>

            <div class="search-wrapper" :class="{ 'animate-slide-up': mounted }" style="animation-delay: 280ms">
                <div class="search-container">
                    <div class="search-glow"></div>
                    <div class="search-bar">
                        <div class="search-highlight"></div>
                        <div class="search-icon-wrapper">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" placeholder="Search services, documents..." class="search-input">
                        <button class="search-voice-btn">
                            <svg class="search-voice-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="apps-section">
                <div class="apps-header" :class="{ 'animate-slide-right': mounted }" style="animation-delay: 360ms">
                    <h3 class="apps-title">Quick Access</h3>
                    <button class="apps-view-all">View All →</button>
                </div>
                <div class="apps-grid">
                    <component :is="app.link ? Link : 'button'"
                        v-for="(app, index) in mobileApps" :key="app.name"
                        :href="app.link" @click="app.action ? handleAppClick(app) : null"
                        class="app-item" :class="{ 'animate-scale-fade': mounted }"
                        :style="`animation-delay: ${440 + (index * 50)}ms`">
                        <div class="app-icon-container">
                            <div v-if="app.pulse" class="app-pulse-ring"></div>
                            <div :class="`app-glow bg-gradient-to-br ${app.gradient} ${app.glow}`"></div>
                            <div :class="`app-icon-wrapper bg-gradient-to-br ${app.gradient}`">
                                <div class="app-icon-shine"></div>
                                <div class="app-icon-reflection"></div>
                                <div class="app-icon-shine-rotate"></div>
                                <span class="app-icon-emoji">{{ app.icon }}</span>
                            </div>
                        </div>
                        <span class="app-name">{{ app.name }}</span>
                    </component>
                </div>
            </div>

            <div class="featured-section">
                <div class="featured-header" :class="{ 'animate-slide-right': mounted }" style="animation-delay: 940ms">
                    <h3 class="featured-title">Featured</h3>
                    <div class="featured-dots">
                        <div class="featured-dot featured-dot-1"></div>
                        <div class="featured-dot featured-dot-2"></div>
                        <div class="featured-dot featured-dot-3"></div>
                    </div>
                </div>
                <div class="featured-cards-grid">
                    <Link :href="route('bills.create')" class="featured-card" :class="{ 'animate-scale-in': mounted }" style="animation-delay: 1020ms">
                        <div class="featured-card-glow featured-card-glow-green"></div>
                        <div class="featured-card-shine"></div>
                        <div class="featured-card-icon featured-card-icon-green">
                            <span class="featured-icon-emoji">💳</span>
                        </div>
                        <div class="featured-card-content">
                            <p class="featured-card-title">Pay Bills</p>
                            <p class="featured-card-desc">Quick & secure payments</p>
                        </div>
                        <div class="featured-card-arrow">
                            <svg class="featured-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </Link>
                    <button @click="showWizard = true" class="featured-card" :class="{ 'animate-scale-in': mounted }" style="animation-delay: 1100ms">
                        <div class="featured-card-glow featured-card-glow-red"></div>
                        <div class="featured-card-shine"></div>
                        <div class="featured-pulse-indicator">
                            <div class="featured-pulse-dot"></div>
                            <div class="featured-pulse-ring"></div>
                        </div>
                        <div class="featured-card-icon featured-card-icon-red">
                            <span class="featured-icon-emoji">🚨</span>
                        </div>
                        <div class="featured-card-content">
                            <p class="featured-card-title">Report</p>
                            <p class="featured-card-desc">Issue or emergency</p>
                        </div>
                        <div class="featured-card-arrow">
                            <svg class="featured-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>
                </div>
                <div class="map-preview-card" :class="{ 'animate-scale-in': mounted }" style="animation-delay: 1180ms">
                    <div class="map-preview-container">
                        <div class="map-badge">
                            <span class="map-badge-pulse">
                                <span class="map-badge-ping"></span>
                                <span class="map-badge-dot"></span>
                            </span>
                            <span class="map-badge-text">Live Map</span>
                        </div>
                        <div class="map-preview-wrapper">
                            <DashboardMap />
                        </div>
                        <div class="map-overlay"></div>
                        <div class="map-info-bar">
                            <div class="map-info-content">
                                <div class="map-info-text">
                                    <p class="map-info-title">Barangay Overview</p>
                                    <p class="map-info-subtitle">View all locations & alerts</p>
                                </div>
                                <div class="map-info-arrow">
                                    <svg class="map-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Link :href="route('emergency.index')" class="floating-emergency-btn">
                <div class="emergency-btn-container">
                    <div class="emergency-pulse-ring emergency-pulse-1"></div>
                    <div class="emergency-pulse-ring emergency-pulse-2"></div>
                    <div class="emergency-pulse-ring emergency-pulse-3"></div>
                    <div class="emergency-glow"></div>
                    <div class="emergency-btn">
                        <div class="emergency-btn-shine"></div>
                        <div class="emergency-btn-shine-rotate"></div>
                        <span class="emergency-icon">🚨</span>
                    </div>
                    <div class="emergency-label">
                        <div class="emergency-label-content">
                            <p class="emergency-label-text">Emergency</p>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT -->
        <div class="desktop-layout">
            <header class="desktop-header" :class="{ 'animate-fade-in': mounted }">
                <div class="desktop-header-content">
                    <div class="greeting-section">
                        <div class="status-badge">
                            <span class="status-pulse">
                                <span class="status-ping"></span>
                                <span class="status-dot"></span>
                            </span>
                            <p class="status-text">System Operational</p>
                        </div>
                        <h1 class="greeting-title">
                            {{ greeting }}, 
                            <span class="greeting-name">{{ user.name.split(' ')[0] }}</span>
                        </h1>
                    </div>
                    <div class="desktop-clock">
                        <div class="clock-time">
                            {{ time }}
                            <span class="clock-period">{{ dayPeriod }}</span>
                        </div>
                        <div class="clock-date">{{ date }}</div>
                    </div>
                </div>
            </header>

            <div class="desktop-grid">
                <div class="desktop-left-column">
                    <div class="desktop-features-grid">
                        <component :is="feature.link ? Link : 'button'"
                            v-for="(feature, index) in desktopFeatures" :key="feature.title"
                            :href="feature.link" @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="['desktop-feature-card', `bg-gradient-to-br ${feature.gradient}`,
                                { 'desktop-feature-large': feature.size === 'large',
                                  'desktop-feature-medium': feature.size === 'medium',
                                  'animate-scale-fade': mounted }]"
                            :style="`animation-delay: ${index * 80}ms`">
                            <div class="feature-pattern"></div>
                            <div class="feature-shine"></div>
                            <div class="feature-shine-rotate"></div>
                            <div v-if="feature.featured" class="feature-badge">✨ Popular</div>
                            <Link v-if="feature.historyLink" :href="feature.historyLink" @click.stop class="feature-history-link">
                                <span>History</span>
                                <svg class="feature-history-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </Link>
                            <div class="feature-content">
                                <div class="feature-icon">{{ feature.icon }}</div>
                                <div class="feature-text">
                                    <p class="feature-subtitle">{{ feature.subtitle }}</p>
                                    <h3 class="feature-title">{{ feature.title }}</h3>
                                </div>
                            </div>
                        </component>
                    </div>
                </div>

                <aside class="desktop-sidebar">
                    <div class="id-card" :class="{ 'animate-scale-in': mounted }">
                        <div class="id-card-content">
                            <div class="id-card-gradient"></div>
                            <div class="id-card-header">
                                <div class="id-card-logo">🏛️</div>
                                <div class="id-card-number">
                                    <div class="id-card-label">Universal ID</div>
                                    <div class="id-card-value">{{ user.id.toString().padStart(8, '0') }}</div>
                                </div>
                            </div>
                            <div class="id-card-footer">
                                <div class="id-card-name">{{ user.name }}</div>
                                <div class="id-card-badges">
                                    <span class="id-card-badge id-card-badge-primary">
                                        {{ isBusinessOwner ? 'Business Owner' : 'Citizen' }}
                                    </span>
                                    <span class="id-card-badge id-card-badge-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('polls.index')" class="sidebar-card sidebar-card-polls"
                          :class="{ 'animate-scale-in': mounted }" style="animation-delay: 200ms">
                        <div class="sidebar-card-content">
                            <div class="sidebar-card-pattern"></div>
                            <div class="sidebar-card-shine"></div>
                            <div class="sidebar-card-header">
                                <div class="sidebar-card-icon">🗳️</div>
                                <span class="sidebar-card-badge">Live</span>
                            </div>
                            <div class="sidebar-card-footer">
                                <h3 class="sidebar-card-title">Freedom Poll</h3>
                                <p class="sidebar-card-subtitle">Share Your Voice</p>
                            </div>
                        </div>
                    </Link>

                    <div class="sidebar-map-card" :class="{ 'animate-scale-in': mounted }" style="animation-delay: 300ms">
                        <div class="sidebar-map-badge">📍 Live Map & Alerts</div>
                        <div class="sidebar-map-wrapper">
                            <DashboardMap />
                        </div>
                    </div>
                    
                    <Link :href="route('emergency.index')" class="sidebar-card sidebar-card-emergency"
                          :class="{ 'animate-scale-in': mounted }" style="animation-delay: 400ms">
                        <div class="sidebar-card-content">
                            <div class="sidebar-card-pattern"></div>
                            <div class="sidebar-emergency-pulse-1"></div>
                            <div class="sidebar-emergency-pulse-2"></div>
                            <div class="sidebar-card-shine"></div>
                            <div class="sidebar-card-header">
                                <div class="sidebar-card-icon">📞</div>
                                <span class="sidebar-card-badge sidebar-emergency-badge">SOS</span>
                            </div>
                            <div class="sidebar-card-footer">
                                <h3 class="sidebar-card-title">Emergency Call</h3>
                                <p class="sidebar-card-subtitle">Tap for Command Center</p>
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

* { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif; 
    -webkit-font-smoothing: antialiased; 
    -moz-osx-font-smoothing: grayscale; 
    letter-spacing: -0.01em; 
}

/* Performance Optimization: Use GPU acceleration */
.cinematic-overlay,
.cinematic-mesh,
.mesh-blob,
.particle-dot,
.particle-star,
.app-icon-wrapper,
.feature-icon,
.stat-card,
.desktop-feature-card {
    transform: translate3d(0, 0, 0);
    will-change: transform;
}

/* ==================== CINEMATIC INTRO STYLES - OPTIMIZED ==================== */
.cinematic-overlay { @apply fixed inset-0 z-[9999] overflow-hidden; }
.cinematic-bg { @apply absolute inset-0; }
.cinematic-gradient { @apply absolute inset-0; background: radial-gradient(ellipse at center, #1a1a2e 0%, #0f0f1e 50%, #000000 100%); }
.cinematic-mesh { @apply absolute inset-0 opacity-40; }
.mesh-blob { 
    @apply absolute rounded-full blur-[120px]; 
    animation: mesh-float 20s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    backface-visibility: hidden;
}
.mesh-blob-1 { 
    @apply w-[600px] h-[600px] top-[10%] left-[10%]; 
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent); 
}
.mesh-blob-2 { 
    @apply w-[500px] h-[500px] bottom-[15%] right-[15%]; 
    background: radial-gradient(circle, rgba(147, 51, 234, 0.3), transparent); 
    animation-delay: 5s; 
}
.mesh-blob-3 { 
    @apply w-[550px] h-[550px] top-[40%] right-[30%]; 
    background: radial-gradient(circle, rgba(236, 72, 153, 0.2), transparent); 
    animation-delay: 10s; 
}

@keyframes mesh-float { 
    0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 
    33% { transform: translate3d(50px, -50px, 0) scale(1.1); } 
    66% { transform: translate3d(-30px, 40px, 0) scale(0.9); } 
}

.cinematic-particles { @apply absolute inset-0; pointer-events: none; }
.particle-dot { 
    @apply absolute w-1 h-1 bg-white rounded-full; 
    animation: particle-float 8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
@keyframes particle-float { 
    0%, 100% { transform: translate3d(0, 0, 0); opacity: 0.2; } 
    50% { transform: translate3d(80px, -100px, 0); opacity: 0.8; } 
}

.particle-star { 
    @apply absolute text-xs; 
    animation: star-twinkle 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
@keyframes star-twinkle { 
    0%, 100% { opacity: 0.3; transform: scale(0.8); } 
    50% { opacity: 1; transform: scale(1.2); } 
}

.cinematic-grid { 
    @apply absolute inset-0 opacity-[0.02]; 
    background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), 
                      linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); 
    background-size: 50px 50px; 
}

.skip-btn { 
    @apply absolute top-8 right-8 z-50 px-6 py-3 
           bg-white/5 backdrop-blur-xl border border-white/10 
           rounded-full text-white text-sm font-semibold 
           hover:bg-white/10 transition-all duration-300 
           flex items-center gap-2 hover:scale-105 active:scale-95; 
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); 
}

.cinematic-content { @apply relative z-10 h-full flex items-center justify-center px-8; }
.phase-container { @apply text-center max-w-5xl; }

/* Logo Phase - Enhanced */
.logo-reveal { @apply relative inline-block mb-8; }
.logo-glow { 
    @apply absolute inset-0 bg-blue-500/30 blur-[100px] rounded-full; 
    animation: logo-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; 
}
@keyframes logo-pulse { 
    0%, 100% { opacity: 0.3; transform: scale(1); } 
    50% { opacity: 0.6; transform: scale(1.2); } 
}
.logo-icon { 
    @apply relative text-9xl; 
    filter: drop-shadow(0 0 40px rgba(59, 130, 246, 0.8)); 
    animation: logo-float 3s cubic-bezier(0.4, 0, 0.2, 1) infinite; 
}
@keyframes logo-float { 
    0%, 100% { transform: translateY(0) rotateY(0deg); } 
    50% { transform: translateY(-20px) rotateY(180deg); } 
}
.logo-ripple { 
    @apply absolute inset-0 border-4 border-blue-500/20 rounded-full; 
    animation: ripple 2s cubic-bezier(0, 0, 0.2, 1) infinite; 
}
@keyframes ripple { 
    0% { transform: scale(0.8); opacity: 1; } 
    100% { transform: scale(2); opacity: 0; } 
}
.logo-text { 
    @apply text-6xl font-black text-white mb-4 tracking-wider; 
    text-shadow: 0 0 60px rgba(59, 130, 246, 0.5); 
    animation: text-glow 2s ease-in-out infinite;
}
@keyframes text-glow {
    0%, 100% { text-shadow: 0 0 60px rgba(59, 130, 246, 0.5); }
    50% { text-shadow: 0 0 80px rgba(59, 130, 246, 0.8); }
}
.logo-subtitle { @apply text-xl text-gray-400 font-light tracking-widest uppercase; }

/* Welcome Phase - Enhanced */
.welcome-badge { 
    @apply inline-flex items-center gap-3 px-6 py-3 rounded-full mb-10 
           bg-blue-500/10 border border-blue-500/30 backdrop-blur-xl; 
}
.welcome-pulse { @apply relative flex h-3 w-3; }
.pulse-ring { @apply animate-ping absolute h-full w-full rounded-full bg-blue-400 opacity-75; }
.pulse-dot { 
    @apply relative rounded-full h-3 w-3 bg-blue-500; 
    box-shadow: 0 0 20px rgba(59, 130, 246, 1); 
}
.welcome-badge-text { @apply text-sm font-bold text-blue-300 uppercase tracking-[0.3em]; }
.welcome-greeting { 
    @apply text-6xl md:text-8xl font-thin text-white mb-6 tracking-tight; 
    animation: text-shimmer 3s ease-in-out infinite; 
}
@keyframes text-shimmer { 
    0%, 100% { opacity: 0.8; } 
    50% { opacity: 1; } 
}
.welcome-name { 
    @apply text-7xl md:text-9xl font-black text-transparent bg-clip-text mb-8; 
    background-image: linear-gradient(135deg, #3b82f6, #8b5cf6, #ec4899, #3b82f6); 
    background-size: 300% auto; 
    animation: gradient-flow 4s ease-in-out infinite; 
}
@keyframes gradient-flow { 
    0%, 100% { background-position: 0% 50%; } 
    50% { background-position: 100% 50%; } 
}
.welcome-subtitle { 
    @apply text-2xl text-gray-400 font-light; 
    animation: fade-in-up 1s cubic-bezier(0.34, 1.56, 0.64, 1) 0.5s both; 
}
@keyframes fade-in-up { 
    from { opacity: 0; transform: translateY(30px); } 
    to { opacity: 1; transform: translateY(0); } 
}

/* Features Phase - Enhanced */
.feature-showcase { @apply space-y-10; }
.feature-icon-wrapper { @apply relative inline-block mb-10; }
.feature-glow { 
    @apply absolute inset-0 blur-[120px] opacity-60 rounded-full; 
    animation: feature-glow-pulse 3s cubic-bezier(0.4, 0, 0.2, 1) infinite; 
}
@keyframes feature-glow-pulse { 
    0%, 100% { opacity: 0.4; transform: scale(1); } 
    50% { opacity: 0.7; transform: scale(1.3); } 
}
.feature-particles { @apply absolute inset-0; }
.fp { 
    @apply absolute text-4xl; 
    animation: fp-orbit 4s cubic-bezier(0.4, 0, 0.2, 1) infinite; 
}
.fp:nth-child(1) { top: 0%; left: 50%; animation-delay: 0s; }
.fp:nth-child(2) { top: 15%; right: 15%; animation-delay: 0.5s; }
.fp:nth-child(3) { right: 0%; top: 50%; animation-delay: 1s; }
.fp:nth-child(4) { bottom: 15%; right: 15%; animation-delay: 1.5s; }
.fp:nth-child(5) { bottom: 0%; left: 50%; animation-delay: 2s; }
.fp:nth-child(6) { bottom: 15%; left: 15%; animation-delay: 2.5s; }
.fp:nth-child(7) { left: 0%; top: 50%; animation-delay: 3s; }
.fp:nth-child(8) { top: 15%; left: 15%; animation-delay: 3.5s; }
@keyframes fp-orbit { 
    0%, 100% { transform: scale(0.5) translateY(0); opacity: 0.3; } 
    50% { transform: scale(1.2) translateY(-20px); opacity: 0.9; } 
}
.feature-icon { 
    @apply relative text-9xl; 
    filter: drop-shadow(0 0 60px rgba(255, 255, 255, 0.5)); 
    animation: feature-float 4s cubic-bezier(0.4, 0, 0.2, 1) infinite; 
}
@keyframes feature-float { 
    0%, 100% { transform: translateY(0) rotate(0deg); } 
    50% { transform: translateY(-30px) rotate(5deg); } 
}
.feature-subtitle { @apply text-base font-bold uppercase tracking-[0.4em] mb-4; }
.feature-title { @apply text-6xl md:text-7xl font-black text-white mb-6; }
.feature-desc { @apply text-2xl text-gray-400 font-light max-w-3xl mx-auto mb-10; }
.feature-progress { @apply flex justify-center gap-4; }
.progress-bar { 
    @apply h-1 rounded-full bg-white/20 transition-all duration-500; 
    width: 60px; 
}
.progress-bar.active { 
    width: 120px; 
    background: linear-gradient(to right, rgba(59, 130, 246, 1), rgba(147, 51, 234, 1));
}

/* Stats Phase - Enhanced */
.stats-title { @apply text-5xl md:text-6xl font-black text-white mb-12; }
.stats-showcase { @apply grid grid-cols-3 gap-8 mb-12; }
.stat-item { 
    @apply relative overflow-hidden rounded-3xl p-10 border border-white/10; 
    background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02)); 
    backdrop-filter: blur(20px); 
    animation: stat-appear 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) both; 
}
@keyframes stat-appear { 
    from { opacity: 0; transform: scale(0.8) translateY(50px); } 
    to { opacity: 1; transform: scale(1) translateY(0); } 
}
.stat-glow { @apply absolute -top-10 -right-10 w-32 h-32 opacity-40 blur-3xl rounded-full; }
.stat-icon { @apply text-5xl mb-4; }
.stat-value { @apply text-5xl font-black text-white mb-2; }
.stat-label { @apply text-sm text-gray-400 font-semibold uppercase tracking-wider; }
.stats-loading { @apply text-lg text-gray-400 animate-pulse; }

/* Ready Phase */
.ready-icon { 
    @apply text-9xl mb-8; 
    animation: ready-bounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); 
}
@keyframes ready-bounce { 
    0% { transform: scale(0); } 
    50% { transform: scale(1.2); } 
    100% { transform: scale(1); } 
}
.ready-title { @apply text-6xl font-black text-white mb-4; }
.ready-subtitle { @apply text-2xl text-gray-400 font-light; }

/* Transitions - Optimized */
.intro-fade-enter-active, .intro-fade-leave-active { 
    transition: opacity 1s cubic-bezier(0.4, 0, 0.2, 1); 
}
.intro-fade-enter-from, .intro-fade-leave-to { opacity: 0; }

.phase-fade-enter-active { 
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); 
}
.phase-fade-leave-active { 
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); 
}
.phase-fade-enter-from { opacity: 0; transform: scale(0.8); }
.phase-fade-leave-to { opacity: 0; transform: scale(1.2); }

.feature-slide-enter-active { 
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1); 
}
.feature-slide-leave-active { 
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); 
}
.feature-slide-enter-from { opacity: 0; transform: translateX(100px) scale(0.9); }
.feature-slide-leave-to { opacity: 0; transform: translateX(-100px) scale(1.1); }

/* Toast - Enhanced */
.toast-notification { 
    @apply fixed top-20 right-4 z-[60] w-[90vw] sm:w-96 
           rounded-2xl shadow-2xl overflow-hidden flex items-center gap-3; 
    background: linear-gradient(135deg, rgba(16, 185, 129, 1), rgba(5, 150, 105, 1)); 
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(255, 255, 255, 0.2); 
}
.toast-icon-wrapper { @apply p-4 relative; }
.toast-icon-pulse { @apply absolute inset-0 bg-white/20 rounded-xl animate-ping; }
.toast-icon-svg { @apply w-12 h-12 relative z-10 text-white; }
.toast-content { @apply flex-1 min-w-0; }
.toast-text { @apply text-sm font-bold text-white truncate drop-shadow-lg; }
.toast-close-btn { 
    @apply flex-shrink-0 p-2 hover:bg-white/20 rounded-lg 
           transition-colors duration-200 active:scale-90; 
}
.toast-close-icon { @apply w-5 h-5 text-white; }

@keyframes toast-bounce { 
    0% { opacity: 0; transform: translateX(100%) scale(0.8); } 
    60% { opacity: 1; transform: translateX(-10px) scale(1.02); } 
    100% { opacity: 1; transform: translateX(0) scale(1); } 
}
.toast-bounce-enter-active { 
    animation: toast-bounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); 
}
.toast-bounce-leave-active { 
    transition: all 0.3s cubic-bezier(0.4, 0, 1, 1); 
}
.toast-bounce-leave-to { opacity: 0; transform: translateX(100%) scale(0.8); }

/* ==================== MOBILE & DESKTOP LAYOUTS - ENHANCED ==================== */

.mobile-layout { @apply lg:hidden w-full max-w-md mx-auto px-4 pb-8 pt-8; }
.desktop-layout { @apply hidden lg:block w-full max-w-7xl mx-auto px-4 pb-20; }

/* Time Widget - Enhanced */
.time-widget { @apply mb-6 opacity-0; }
.time-widget-container { 
    @apply relative overflow-hidden rounded-[2rem] p-5 border border-white/[0.10]; 
    background: linear-gradient(135deg, rgba(28, 28, 30, 1), rgba(44, 44, 46, 1)); 
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8); 
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.time-widget-container:hover {
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.9);
    transform: translateY(-2px);
}
.time-widget-gradient { 
    @apply absolute inset-0; 
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 51, 234, 0.08), rgba(236, 72, 153, 0.08)); 
    animation: gradient-rotate 20s ease-in-out infinite; 
}
@keyframes gradient-rotate { 
    0%, 100% { transform: rotate(0deg) scale(1); } 
    50% { transform: rotate(180deg) scale(1.1); } 
}
.time-widget-glow { 
    @apply absolute top-0 right-0 w-32 h-32 rounded-full blur-3xl; 
    background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent); 
}
.time-widget-content { @apply relative flex items-center justify-between; }
.time-section { @apply flex-1; }
.time-label { @apply text-gray-400 text-xs font-medium mb-1 tracking-wide uppercase; }
.time-display { @apply flex items-baseline gap-2; }
.time-value { 
    @apply text-4xl font-bold text-white tracking-tight drop-shadow-xl;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.time-period { @apply text-lg font-semibold text-gray-400; }
.date-badge-wrapper { @apply flex flex-col items-end; }
.date-badge { 
    @apply relative px-4 py-2 rounded-2xl border border-white/[0.12] 
           shadow-lg overflow-hidden transition-transform duration-300; 
    background: rgba(255, 255, 255, 0.10); 
    backdrop-filter: blur(20px); 
}
.date-badge:hover { @apply scale-105; }
.date-badge-shine { 
    @apply absolute inset-0 bg-gradient-to-br from-white/10 
           via-transparent to-transparent opacity-0 transition-opacity duration-500; 
}
.date-badge:hover .date-badge-shine { @apply opacity-100; }
.date-badge-day { @apply text-2xl font-bold text-white text-center leading-none mb-0.5; }
.date-badge-month { @apply text-[10px] text-gray-400 font-bold uppercase tracking-wider; }

/* Stats - Enhanced */
.stats-grid { @apply mb-6 grid grid-cols-3 gap-3; }
.stat-card { 
    @apply relative overflow-hidden rounded-[1.75rem] p-5 shadow-xl 
           transition-all duration-500 border border-white/[0.10] opacity-0; 
    background: linear-gradient(135deg, rgba(28, 28, 30, 1), rgba(44, 44, 46, 1)); 
}
.stat-card:hover { 
    @apply shadow-2xl; 
    transform: translateY(-4px) scale(1.02);
}
.stat-card:active {
    transform: translateY(-2px) scale(0.98);
}
.stat-card-glow { 
    @apply absolute -top-10 -right-10 w-24 h-24 opacity-25 blur-2xl 
           rounded-full transition-all duration-700; 
}
.stat-card:hover .stat-card-glow { @apply opacity-40 scale-125; }
.stat-card-shine { 
    @apply absolute inset-0 bg-gradient-to-br from-white/[0.05] 
           via-transparent to-transparent opacity-0 transition-opacity duration-500; 
}
.stat-card:hover .stat-card-shine { @apply opacity-100; }
.stat-icon-wrapper { @apply relative mb-3; }
.stat-icon-badge { 
    @apply w-12 h-12 rounded-2xl flex items-center justify-center 
           shadow-lg transition-all duration-500; 
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3); 
}
.stat-card:hover .stat-icon-badge { 
    transform: scale(1.15) rotate(8deg);
}
.stat-icon { 
    @apply text-2xl filter drop-shadow-xl;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.stat-content { @apply relative; }
.stat-value { 
    @apply text-3xl font-bold text-white mb-0.5 tracking-tight 
           transition-transform duration-300; 
}
.stat-card:hover .stat-value { @apply scale-110; }
.stat-label { @apply text-[10px] text-gray-400 font-semibold uppercase tracking-widest; }
.stat-trend { @apply absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full; }
.stat-trend-up { @apply text-green-400 bg-green-500/20 border border-green-500/30; }
.stat-trend-down { @apply text-red-400 bg-red-500/20 border border-red-500/30; }
.stat-highlight { 
    @apply absolute bottom-0 left-0 right-0 h-1 
           bg-gradient-to-r from-transparent via-white/[0.08] 
           to-transparent transition-all duration-500; 
}
.stat-card:hover .stat-highlight { @apply via-white/[0.15]; }

/* Search - Enhanced */
.search-wrapper { @apply mb-6 opacity-0; }
.search-container { @apply relative; }
.search-glow { 
    @apply absolute -inset-0.5 rounded-[2rem] opacity-0 blur-2xl 
           transition-all duration-700; 
    background: linear-gradient(to right, rgba(59, 130, 246, 1), rgba(147, 51, 234, 1), rgba(236, 72, 153, 1)); 
}
.search-container:focus-within .search-glow { @apply opacity-25; }
.search-bar { 
    @apply relative bg-[#1c1c1e] rounded-[2rem] border border-white/[0.10] 
           shadow-xl overflow-hidden transition-all duration-500; 
}
.search-bar:focus-within { 
    @apply border-white/[0.15];
    transform: translateY(-2px);
}
.search-highlight { 
    @apply absolute inset-x-0 top-0 h-px 
           bg-gradient-to-r from-transparent via-white/[0.12] to-transparent; 
}
.search-icon-wrapper { 
    @apply absolute left-4 top-1/2 -translate-y-1/2 
           w-10 h-10 flex items-center justify-center; 
}
.search-icon { 
    @apply w-5 h-5 text-gray-400 transition-all duration-500; 
}
.search-bar:focus-within .search-icon { 
    @apply text-blue-400 scale-110; 
}
.search-input { 
    @apply w-full px-5 py-4 pl-14 bg-transparent text-white 
           placeholder-gray-500 text-[15px] focus:outline-none; 
}
.search-voice-btn { 
    @apply absolute right-3 top-1/2 -translate-y-1/2 
           w-9 h-9 flex items-center justify-center rounded-xl 
           transition-all duration-300 active:scale-90; 
    background: rgba(255, 255, 255, 0.08); 
}
.search-voice-btn:hover { 
    background: rgba(255, 255, 255, 0.15); 
    transform: translateY(-50%) scale(1.05);
}
.search-voice-icon { @apply w-4 h-4 text-gray-400 transition-colors duration-300; }
.search-voice-btn:hover .search-voice-icon { @apply text-blue-400; }

/* Apps - Enhanced */
.apps-section { @apply mb-6; }
.apps-header { @apply flex items-center justify-between mb-4 px-1 opacity-0; }
.apps-title { @apply text-lg font-bold text-white; }
.apps-view-all { 
    @apply text-xs font-semibold text-blue-400 hover:text-blue-300 
           active:scale-95 transition-all duration-300; 
}
.apps-view-all:hover { @apply translate-x-1; }
.apps-grid { @apply grid grid-cols-4 gap-4; }
.app-item { 
    @apply flex flex-col items-center gap-2 rounded-3xl p-2 
           active:scale-90 transition-all duration-500 opacity-0 
           focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500; 
}
.app-icon-container { @apply relative w-full aspect-square; }
.app-pulse-ring { @apply absolute inset-0 bg-red-500 rounded-[1.3rem] animate-ping opacity-40; }
.app-glow { 
    @apply absolute -inset-1 rounded-[1.4rem] opacity-0 blur-lg 
           transition-all duration-500; 
}
.app-item:hover .app-glow { @apply opacity-70; }
.app-icon-wrapper { 
    @apply relative w-full h-full rounded-[1.3rem] shadow-xl 
           flex items-center justify-center transition-all duration-500 overflow-hidden; 
}
.app-item:hover .app-icon-wrapper { 
    transform: scale(1.1) rotate(-2deg);
}
.app-icon-shine { 
    @apply absolute inset-[1px] rounded-[1.2rem] opacity-90; 
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.15), transparent); 
}
.app-icon-reflection { 
    @apply absolute inset-0 rounded-[1.3rem]; 
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.25), transparent, transparent); 
}
.app-icon-shine-rotate { 
    @apply absolute inset-0 rounded-[1.3rem] -translate-x-full 
           transition-transform duration-1000; 
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent); 
}
.app-item:hover .app-icon-shine-rotate { @apply translate-x-full; }
.app-icon-emoji { 
    @apply relative text-[32px] filter drop-shadow-2xl 
           transition-transform duration-300; 
}
.app-item:hover .app-icon-emoji { @apply scale-110; }
.app-name { 
    @apply text-[11px] font-semibold text-gray-300 
           text-center leading-tight w-full px-1; 
}

/* Featured - Enhanced */
.featured-section { @apply space-y-4 mb-6; }
.featured-header { @apply flex items-center justify-between px-1 opacity-0; }
.featured-title { @apply text-lg font-bold text-white; }
.featured-dots { @apply flex gap-1.5; }
.featured-dot { @apply w-1.5 h-1.5 rounded-full animate-pulse shadow-lg; }
.featured-dot-1 { 
    @apply bg-green-400; 
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5); 
}
.featured-dot-2 { 
    @apply bg-blue-400; 
    box-shadow: 0 0 8px rgba(96, 165, 250, 0.5); 
    animation-delay: 0.2s; 
}
.featured-dot-3 { 
    @apply bg-purple-400; 
    box-shadow: 0 0 8px rgba(192, 132, 252, 0.5); 
    animation-delay: 0.4s; 
}
.featured-cards-grid { @apply grid grid-cols-2 gap-3; }
.featured-card { 
    @apply relative overflow-hidden rounded-[1.75rem] p-4 
           border border-white/[0.10] shadow-xl 
           transition-all duration-500 active:scale-[0.97] opacity-0; 
    background: linear-gradient(135deg, rgba(28, 28, 30, 1), rgba(44, 44, 46, 1)); 
}
.featured-card:hover { 
    @apply shadow-2xl;
    transform: translateY(-4px) scale(1.02);
}
.featured-card-glow { 
    @apply absolute -top-10 -right-10 w-24 h-24 opacity-25 
           blur-2xl rounded-full transition-all duration-700; 
}
.featured-card-glow-green { 
    background: radial-gradient(circle, rgba(34, 197, 94, 1), rgba(16, 185, 129, 1)); 
}
.featured-card-glow-red { 
    background: radial-gradient(circle, rgba(239, 68, 68, 1), rgba(236, 72, 153, 1)); 
}
.featured-card:hover .featured-card-glow { @apply opacity-40 scale-125; }
.featured-card-shine { 
    @apply absolute inset-0 bg-gradient-to-br from-white/[0.05] 
           via-transparent to-transparent opacity-0 transition-opacity duration-500; 
}
.featured-card:hover .featured-card-shine { @apply opacity-100; }
.featured-pulse-indicator { @apply absolute top-3 right-3 z-10; }
.featured-pulse-dot { 
    @apply w-2 h-2 bg-red-400 rounded-full animate-pulse shadow-lg; 
    box-shadow: 0 0 8px rgba(248, 113, 113, 0.7); 
}
.featured-pulse-ring { @apply absolute inset-0 bg-red-400 rounded-full animate-ping; }
.featured-card-icon { 
    @apply mb-3 w-12 h-12 rounded-2xl flex items-center justify-center 
           shadow-lg transition-all duration-500; 
}
.featured-card-icon-green { 
    background: linear-gradient(135deg, rgba(34, 197, 94, 1), rgba(16, 185, 129, 1)); 
}
.featured-card-icon-red { 
    background: linear-gradient(135deg, rgba(239, 68, 68, 1), rgba(236, 72, 153, 1)); 
}
.featured-card:hover .featured-card-icon { 
    transform: scale(1.15) rotate(8deg);
}
.featured-icon-emoji { @apply text-2xl; }
.featured-card-content { @apply relative z-10; }
.featured-card-title { @apply text-sm font-bold text-white mb-1; }
.featured-card-desc { @apply text-xs text-gray-400 leading-relaxed; }
.featured-card-arrow { 
    @apply absolute bottom-4 right-4 w-6 h-6 rounded-full 
           flex items-center justify-center transition-all duration-300 z-10; 
    background: rgba(255, 255, 255, 0.10); 
}
.featured-card:hover .featured-card-arrow { 
    background: rgba(255, 255, 255, 0.15); 
    @apply translate-x-1; 
}
.featured-arrow-icon { @apply w-3 h-3 text-gray-400; }

/* Map - Enhanced */
.map-preview-card { 
    @apply relative overflow-hidden rounded-[1.75rem] 
           border border-white/[0.10] shadow-xl opacity-0; 
    background: linear-gradient(135deg, rgba(28, 28, 30, 1), rgba(44, 44, 46, 1)); 
}
.map-preview-container { 
    @apply relative h-52 overflow-hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.map-preview-card:hover .map-preview-container {
    transform: scale(1.02);
}
.map-badge { 
    @apply absolute top-4 left-4 z-10 flex items-center gap-2 
           px-3 py-2 rounded-full border border-white/[0.10] shadow-xl; 
    background: rgba(0, 0, 0, 0.7); 
    backdrop-filter: blur(40px); 
}
.map-badge-pulse { @apply relative flex h-2 w-2; }
.map-badge-ping { 
    @apply animate-ping absolute inline-flex h-full w-full 
           rounded-full bg-green-400 opacity-75; 
}
.map-badge-dot { 
    @apply relative inline-flex rounded-full h-2 w-2 bg-green-400 shadow-lg; 
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5); 
}
.map-badge-text { @apply text-xs font-bold text-white; }
.map-preview-wrapper { @apply w-full h-full; }
.map-overlay { 
    @apply absolute inset-0 bg-gradient-to-t from-[#1c1c1e] 
           via-transparent to-transparent pointer-events-none; 
}
.map-info-bar { 
    @apply absolute bottom-0 left-0 right-0 p-4 
           bg-gradient-to-t from-black/80 via-black/50 to-transparent; 
    backdrop-filter: blur(20px); 
}
.map-info-content { @apply flex items-center justify-between; }
.map-info-text { @apply flex-1; }
.map-info-title { @apply text-sm font-bold text-white drop-shadow-lg; }
.map-info-subtitle { @apply text-xs text-gray-300; }
.map-info-arrow { 
    @apply w-8 h-8 rounded-full flex items-center justify-center 
           transition-all duration-300; 
    background: rgba(255, 255, 255, 0.15); 
}
.map-preview-container:hover .map-info-arrow { 
    background: rgba(255, 255, 255, 0.25); 
    @apply translate-x-1; 
}
.map-arrow-icon { @apply w-4 h-4 text-white; }

/* Emergency Button - Enhanced */
.floating-emergency-btn { @apply fixed bottom-36 right-6 z-40; }
.emergency-btn-container { @apply relative; }
.emergency-pulse-ring { @apply absolute inset-0 rounded-full animate-ping; }
.emergency-pulse-1 { @apply bg-red-400 opacity-25; }
.emergency-pulse-2 { @apply bg-red-500 opacity-20; animation-delay: 0.5s; }
.emergency-pulse-3 { @apply bg-red-600 opacity-15; animation-delay: 1s; }
.emergency-glow { 
    @apply absolute inset-0 bg-red-500 rounded-full blur-2xl 
           opacity-60 transition-opacity duration-500; 
}
.floating-emergency-btn:hover .emergency-glow { @apply opacity-80; }
.emergency-btn { 
    @apply relative w-16 h-16 rounded-full flex items-center justify-center 
           active:scale-90 transition-all duration-300 border-4 border-black/30 overflow-hidden; 
    background: linear-gradient(135deg, rgba(239, 68, 68, 1), rgba(220, 38, 38, 1), rgba(185, 28, 28, 1)); 
    box-shadow: 0 20px 60px rgba(239, 68, 68, 0.7); 
}
.floating-emergency-btn:hover .emergency-btn { 
    transform: scale(1.1) rotate(5deg);
}
.emergency-btn-shine { 
    @apply absolute inset-[2px] rounded-full; 
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.40), rgba(255, 255, 255, 0.20), transparent); 
}
.emergency-btn-shine-rotate { 
    @apply absolute inset-0 rounded-full -translate-x-full 
           transition-transform duration-1000; 
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.30), transparent); 
}
.floating-emergency-btn:hover .emergency-btn-shine-rotate { @apply translate-x-full; }
.emergency-icon { @apply relative text-3xl filter drop-shadow-2xl z-10; }
.emergency-label { 
    @apply absolute -bottom-7 left-1/2 -translate-x-1/2 
           whitespace-nowrap opacity-0 transition-opacity duration-300; 
}
.floating-emergency-btn:hover .emergency-label { @apply opacity-100; }
.emergency-label-content { 
    @apply px-3 py-1 rounded-full border border-white/20 shadow-xl; 
    background: rgba(0, 0, 0, 0.8); 
    backdrop-filter: blur(20px); 
}
.emergency-label-text { @apply text-[10px] font-bold text-white; }

/* Desktop - Enhanced */
.desktop-header { @apply mb-8 opacity-0; }
.desktop-header-content { 
    @apply flex flex-col lg:flex-row justify-between 
           items-start lg:items-end gap-4 mb-6; 
}
.greeting-section { @apply flex-1; }
.status-badge { @apply flex items-center gap-2 mb-2; }
.status-pulse { @apply relative flex h-2.5 w-2.5; }
.status-ping { 
    @apply animate-ping absolute inline-flex h-full w-full 
           rounded-full bg-green-400 opacity-75; 
}
.status-dot { 
    @apply relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500 shadow-lg; 
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5); 
}
.status-text { 
    @apply text-blue-300/70 text-[10px] font-bold tracking-widest uppercase; 
}
.greeting-title { 
    @apply text-5xl font-extrabold text-white tracking-tight;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.greeting-name { 
    @apply text-transparent bg-clip-text bg-gradient-to-r 
           from-blue-400 via-purple-400 to-pink-400; 
    background-size: 200% auto; 
    animation: gradient-x 3s ease infinite; 
}
@keyframes gradient-x { 
    0%, 100% { background-position: 0% 50%; } 
    50% { background-position: 100% 50%; } 
}
.desktop-clock { @apply text-right; }
.clock-time { 
    @apply text-6xl font-thin text-white tracking-tight 
           drop-shadow-2xl flex items-baseline gap-2; 
}
.clock-period { @apply text-2xl font-medium text-gray-400; }
.clock-date { 
    @apply text-white/40 font-medium text-xs uppercase tracking-widest mt-1; 
}

.desktop-grid { @apply grid grid-cols-1 lg:grid-cols-12 gap-6; }
.desktop-left-column { @apply lg:col-span-8 space-y-6; }
.desktop-features-grid { @apply grid grid-cols-2 lg:grid-cols-3 gap-4; }
.desktop-feature-card { 
    @apply relative overflow-hidden flex flex-col justify-between p-5 
           rounded-3xl shadow-xl border border-white/10 
           transition-all duration-500 cursor-pointer opacity-0 
           focus:outline-none focus-visible:ring-2 focus-visible:ring-white; 
}
.desktop-feature-card:hover { 
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
}
.desktop-feature-card:active { 
    transform: translateY(-3px) scale(1.01);
}
.desktop-feature-large { @apply col-span-2 h-40; }
.desktop-feature-medium { @apply h-32; }
.feature-pattern { 
    @apply absolute inset-0 opacity-10; 
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yIDItNCA0LTRoNGEyIDIgMCAwIDEgMCAyczIgMiAyIDRjMCAyLTIgNC00IDRoLTRhMiAyIDAgMCAxIDAtNHoiLz48L2c+PC9nPjwvc3ZnPg==');
}
.feature-shine { 
    @apply absolute inset-0 bg-gradient-to-br from-white/[0.08] 
           via-transparent to-transparent opacity-0 transition-opacity duration-500; 
}
.desktop-feature-card:hover .feature-shine { @apply opacity-100; }
.feature-shine-rotate { 
    @apply absolute inset-0 -translate-x-full transition-transform duration-1000; 
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent); 
}
.desktop-feature-card:hover .feature-shine-rotate { @apply translate-x-full; }
.feature-badge { 
    @apply absolute top-4 left-4 z-10 px-2.5 py-1 rounded-full 
           text-[10px] font-bold text-white uppercase tracking-wider shadow-xl; 
    background: rgba(255, 255, 255, 0.15); 
    backdrop-filter: blur(20px); 
    border: 1px solid rgba(255, 255, 255, 0.25); 
}
.feature-history-link { 
    @apply absolute top-4 right-4 z-10 flex items-center gap-1 
           pl-2.5 pr-1.5 py-1 rounded-full border border-white/15 
           text-[10px] font-bold text-white uppercase tracking-widest 
           transition-all duration-300 shadow-lg; 
    background: rgba(0, 0, 0, 0.3); 
    backdrop-filter: blur(20px); 
}
.feature-history-link:hover { 
    background: rgba(0, 0, 0, 0.5); 
    @apply scale-110; 
}
.feature-history-icon { @apply w-3 h-3 text-white/70; }
.feature-content { @apply relative z-0 flex flex-col justify-between h-full; }
.feature-icon { 
    @apply w-12 h-12 rounded-2xl flex items-center justify-center 
           text-2xl shadow-xl transition-all duration-500; 
    background: rgba(255, 255, 255, 0.2); 
    backdrop-filter: blur(20px); 
}
.desktop-feature-card:hover .feature-icon { 
    transform: scale(1.2) rotate(10deg);
}
.feature-text { @apply space-y-1; }
.feature-subtitle { @apply text-white/60 text-xs font-semibold uppercase tracking-wider; }
.feature-title { @apply font-black text-white text-xl leading-tight drop-shadow-lg; }

.desktop-sidebar { @apply lg:col-span-4 space-y-6; }
.id-card { @apply aspect-[1.6/1] rounded-3xl overflow-hidden shadow-2xl opacity-0; }
.id-card-content { 
    @apply h-full p-6 flex flex-col justify-between relative overflow-hidden; 
    background: linear-gradient(135deg, rgba(51, 65, 85, 1), rgba(30, 41, 59, 1), rgba(0, 0, 0, 1)); 
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.id-card:hover .id-card-content {
    transform: scale(1.02);
}
.id-card-gradient { 
    @apply absolute inset-0; 
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 51, 234, 0.05), transparent); 
    animation: gradient-rotate 20s ease-in-out infinite; 
}
.id-card-header { @apply flex justify-between items-start relative z-10; }
.id-card-logo { 
    @apply w-10 h-10 rounded-full flex items-center justify-center 
           text-lg border border-white/20 shadow-xl; 
    background: rgba(255, 255, 255, 0.15); 
    backdrop-filter: blur(20px); 
}
.id-card-number { @apply text-right; }
.id-card-label { @apply text-[9px] text-white/50 uppercase tracking-widest font-bold; }
.id-card-value { @apply text-white font-mono font-bold text-sm tracking-widest opacity-80; }
.id-card-footer { @apply relative z-10; }
.id-card-name { 
    @apply text-2xl text-white font-black tracking-wide uppercase mb-1 truncate drop-shadow-lg; 
}
.id-card-badges { @apply flex items-center gap-2 flex-wrap; }
.id-card-badge { 
    @apply px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider shadow-lg border; 
}
.id-card-badge-primary { 
    background: linear-gradient(to right, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.25)); 
    @apply text-blue-300 border-blue-500/40; 
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); 
}
.id-card-badge-success { 
    background: linear-gradient(to right, rgba(34, 197, 94, 0.25), rgba(22, 163, 74, 0.25)); 
    @apply text-green-300 border-green-500/40; 
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2); 
}

.sidebar-card { 
    @apply block rounded-3xl overflow-hidden shadow-xl h-40 
           transition-all duration-500 opacity-0; 
}
.sidebar-card:hover { 
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
}
.sidebar-card-content { 
    @apply h-full p-6 flex flex-col justify-between relative overflow-hidden; 
}
.sidebar-card-polls .sidebar-card-content { 
    background: linear-gradient(135deg, rgba(202, 138, 4, 1), rgba(161, 98, 7, 1)); 
}
.sidebar-card-emergency .sidebar-card-content { 
    background: linear-gradient(135deg, rgba(220, 38, 38, 1), rgba(185, 28, 28, 1)); 
}
.sidebar-card-pattern { 
    @apply absolute inset-0 opacity-10; 
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yIDItNCA0LTRoNGEyIDIgMCAwIDEgMCAyczIgMiAyIDRjMCAyLTIgNC00IDRoLTRhMiAyIDAgMCAxIDAtNHoiLz48L2c+PC9nPjwvc3ZnPg=='); 
}
.sidebar-card-shine { 
    @apply absolute inset-0 bg-gradient-to-br from-white/[0.08] 
           via-transparent to-transparent opacity-0 transition-opacity duration-500; 
}
.sidebar-card:hover .sidebar-card-shine { @apply opacity-100; }
.sidebar-card-header { @apply relative flex justify-between items-start z-10; }
.sidebar-card-icon { 
    @apply w-12 h-12 rounded-2xl flex items-center justify-center 
           text-2xl shadow-xl transition-all duration-500; 
    background: rgba(255, 255, 255, 0.25); 
}
.sidebar-card:hover .sidebar-card-icon {
    transform: scale(1.15) rotate(8deg);
}
.sidebar-card-badge { 
    @apply text-[10px] font-black px-2 py-1 rounded uppercase tracking-widest shadow-lg; 
    background: rgba(255, 255, 255, 1); 
}
.sidebar-card-polls .sidebar-card-badge { @apply text-amber-600; }
.sidebar-emergency-badge { 
    @apply animate-pulse text-red-600; 
    background: rgba(255, 255, 255, 1); 
}
.sidebar-card-footer { @apply relative z-10; }
.sidebar-card-title { 
    @apply font-black text-white text-2xl uppercase tracking-wide drop-shadow-lg; 
}
.sidebar-card-subtitle { @apply text-white/80 text-xs font-bold mt-1; }
.sidebar-emergency-pulse-1, .sidebar-emergency-pulse-2 { 
    @apply absolute -top-2 -right-2 w-6 h-6 rounded-full animate-ping; 
}
.sidebar-emergency-pulse-1 { @apply bg-white/30; }
.sidebar-emergency-pulse-2 { @apply bg-white/20; animation-delay: 0.5s; }

.sidebar-map-card { 
    @apply relative rounded-3xl p-1 h-80 shadow-2xl opacity-0; 
    background: rgba(0, 0, 0, 0.3); 
    backdrop-filter: blur(40px); 
    border: 1px solid rgba(255, 255, 255, 0.10); 
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.sidebar-map-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.8);
}
.sidebar-map-badge { 
    @apply absolute top-4 left-4 z-10 px-3 py-1 rounded-full 
           text-xs font-bold text-white border border-white/15 shadow-xl; 
    background: rgba(0, 0, 0, 0.7); 
    backdrop-filter: blur(20px); 
}
.sidebar-map-wrapper { 
    @apply w-full h-full rounded-[1.8rem] overflow-hidden 
           opacity-90 transition-opacity duration-500; 
}
.sidebar-map-card:hover .sidebar-map-wrapper { @apply opacity-100; }

/* Animations - Optimized for Performance */
.animate-fade-in { 
    animation: fade-in 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
}
@keyframes fade-in { 
    from { opacity: 0; transform: translateY(-20px); } 
    to { opacity: 1; transform: translateY(0); } 
}

.animate-scale-in { 
    animation: scale-in 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
}
@keyframes scale-in { 
    from { opacity: 0; transform: scale(0.9); } 
    to { opacity: 1; transform: scale(1); } 
}

.animate-slide-up { 
    animation: slide-up 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
}
@keyframes slide-up { 
    from { opacity: 0; transform: translateY(30px); } 
    to { opacity: 1; transform: translateY(0); } 
}

.animate-slide-right { 
    animation: slide-right 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
}
@keyframes slide-right { 
    from { opacity: 0; transform: translateX(-30px); } 
    to { opacity: 1; transform: translateX(0); } 
}

.animate-scale-fade { 
    animation: scale-fade 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
}
@keyframes scale-fade { 
    from { opacity: 0; transform: scale(0.8); } 
    to { opacity: 1; transform: scale(1); } 
}

/* Responsive optimizations */
@media (max-width: 640px) {
    .time-value { @apply text-3xl; }
    .date-badge { @apply px-3 py-1.5; }
    .date-badge-day { @apply text-xl; }
    
    /* Reduce animation complexity on mobile */
    .mesh-blob {
        animation-duration: 30s;
    }
    
    .particle-dot, .particle-star {
        animation-duration: 12s;
    }
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

/* Print styles */
@media print {
    .cinematic-overlay,
    .floating-emergency-btn,
    .skip-btn {
        display: none !important;
    }
}
</style>
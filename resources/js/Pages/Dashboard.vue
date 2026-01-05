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
const logoUrl = '/images/enotpili.png';

// Simplified intro
const showIntro = ref(true);
const introPhase = ref('logo');

// Subtle parallax
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

// Minimalist sound
const audioContext = ref(null);
const playSound = (frequency, duration) => {
    try {
        if (!audioContext.value) {
            audioContext.value = new (window.AudioContext || window.webkitAudioContext)();
        }
        const ctx = audioContext.value;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        
        osc.connect(gain);
        gain.connect(ctx.destination);
        
        osc.frequency.value = frequency;
        osc.type = 'sine';
        
        gain.gain.setValueAtTime(0.03, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
        
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + duration);
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

const quickStats = [
    { icon: '📄', label: 'Active', value: '3', trend: '+2', color: 'from-blue-500 via-blue-600 to-indigo-600' },
    { icon: '✅', label: 'Done', value: '12', trend: '+5', color: 'from-green-500 via-emerald-600 to-teal-600' },
    { icon: '📊', label: 'Total', value: '15', trend: '+7', color: 'from-purple-500 via-violet-600 to-fuchsia-600' }
];

const mobileApps = [
    { icon: '⚡', name: 'Services', link: route('services.landing'), gradient: 'from-blue-500 to-indigo-600' },
    { icon: '💳', name: 'Bills', link: route('bills.create'), gradient: 'from-green-500 to-emerald-600' },
    { icon: '🩺', name: 'Health', link: route('health.index'), gradient: 'from-red-500 to-rose-600' },
    { icon: '📍', name: 'Places', link: route('establishments.index'), gradient: 'from-purple-500 to-fuchsia-600' },
    { icon: '🏪', name: 'Market', link: route('marketplace.index'), gradient: 'from-orange-500 to-amber-600' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: 'from-indigo-500 to-cyan-600' },
    { icon: '💼', name: 'Jobs', link: route('jobs.index'), gradient: 'from-yellow-500 to-orange-600' },
    { icon: '📈', name: 'Prices', link: route('market.index'), gradient: 'from-cyan-500 to-teal-600' },
    { icon: '♻️', name: 'Green', link: route('environment.index'), gradient: 'from-emerald-500 to-green-600' },
    { icon: '🤝', name: 'Social', link: route('social.index'), gradient: 'from-violet-500 to-purple-600' },
    { icon: '🚨', name: 'Report', link: null, action: 'wizard', gradient: 'from-red-600 to-pink-700', pulse: true },
    { icon: '🗳️', name: 'Polls', link: route('polls.index'), gradient: 'from-amber-500 to-orange-600' },
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
let introTimer;

const handleSkipIntro = () => {
    playSound(600, 0.1);
    if (introTimer) clearTimeout(introTimer);
    showIntro.value = false;
    nextTick(() => triggerEntranceAnimation());
};

const startIntroSequence = () => {
    playSound(440, 0.15);
    
    introTimer = setTimeout(() => {
        showIntro.value = false;
        nextTick(() => triggerEntranceAnimation());
    }, 2000);
};

const triggerEntranceAnimation = () => { 
    mounted.value = true;
    playSound(880, 0.15);
};

const handleMouseMove = (e) => {
    requestAnimationFrame(() => {
        const x = (e.clientX / window.innerWidth - 0.5) * 15;
        const y = (e.clientY / window.innerHeight - 0.5) * 15;
        mouseX.value = x;
        mouseY.value = y;
    });
};

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    
    if (flashSuccess.value) { 
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 4000);
    }
    
    const hasSeenIntro = sessionStorage.getItem('epili-intro-seen');
    if (!hasSeenIntro) {
        startIntroSequence();
        sessionStorage.setItem('epili-intro-seen', 'true');
    } else {
        showIntro.value = false;
        requestAnimationFrame(() => {
            setTimeout(triggerEntranceAnimation, 50);
        });
    }
    
    window.addEventListener('mousemove', handleMouseMove, { passive: true });
});

onUnmounted(() => { 
    clearInterval(clockTimer);
    if (introTimer) clearTimeout(introTimer);
    window.removeEventListener('mousemove', handleMouseMove);
});

const handleAppClick = (app) => {
    if ('vibrate' in navigator) navigator.vibrate(5);
    if (app.action === 'wizard') showWizard.value = true;
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        
        <!-- Elegant Intro -->
        <Transition name="intro-fade">
            <div v-if="showIntro" class="intro-overlay">
                <div class="intro-bg">
                    <div class="intro-gradient"></div>
                    <div class="intro-glow"></div>
                </div>

                <button @click="handleSkipIntro" class="skip-btn">
                    <span>Skip</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div class="intro-content">
                    <div class="intro-logo">
                        <div class="logo-glow"></div>
                        <div class="logo-icon">🏛️</div>
                    </div>
                    <h2 class="intro-title">E-PILI</h2>
                    <p class="intro-subtitle">Digital Governance Platform</p>
                </div>
            </div>
        </Transition>

        <!-- Success Toast -->
        <Transition name="toast">
            <div v-if="showFlash && flashSuccess" class="toast">
                <div class="toast-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="toast-text">{{ flashSuccess }}</p>
                <button @click="showFlash = false" class="toast-close">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </Transition>

        <!-- MOBILE LAYOUT -->
        <div class="mobile-layout">
            <!-- Logo Section -->
            <div class="logo-section" :class="{ 'fade-in': mounted }">
                <div class="logo-container">
                    <div class="logo-glow-ring"></div>
                    <img :src="logoUrl" alt="E-Pili Logo" class="logo-image" />
                </div>
            </div>

            <!-- Developer Carousel -->
            <div class="developer-carousel" :class="{ 'slide-down': mounted }" style="animation-delay: 80ms">
                <div class="carousel-glass">
                    <div class="carousel-gradient"></div>
                    <div class="carousel-content">
                        <div class="dev-avatar">
                            <div class="avatar-glow"></div>
                            <div class="avatar-ring"></div>
                            <div class="avatar-inner">
                                <span class="avatar-icon">👨‍💻</span>
                            </div>
                        </div>
                        <div class="dev-info">
                            <p class="dev-label">Developed by</p>
                            <h3 class="dev-name">Justine Philip Villarosa</h3>
                            <p class="dev-title">Bachelor of Science in Information Technology</p>
                            <div class="dev-badges">
                                <span class="dev-badge">
                                    <span>🎓</span>
                                    <span>BSIT</span>
                                </span>
                                <span class="dev-badge">
                                    <span>⚡</span>
                                    <span>Developer</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-decoration">
                        <div class="decoration-dot dot-1"></div>
                        <div class="decoration-dot dot-2"></div>
                        <div class="decoration-dot dot-3"></div>
                    </div>
                </div>
            </div>

            <!-- Time Widget -->
            <div class="time-widget" :class="{ 'fade-in': mounted }" style="animation-delay: 160ms">
                <div class="widget-glass">
                    <div class="widget-gradient"></div>
                    <div class="time-content">
                        <div class="time-left">
                            <p class="time-label">{{ date }}</p>
                            <div class="time-display">
                                <h2 class="time-value">{{ time }}</h2>
                                <span class="time-period">{{ dayPeriod }}</span>
                            </div>
                        </div>
                        <div class="date-badge">
                            <p class="date-day">{{ new Date().getDate() }}</p>
                            <p class="date-month">{{ fullDate.split(' ')[1] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div v-for="(stat, i) in quickStats" :key="stat.label"
                    class="stat-card" :class="{ 'scale-in': mounted }"
                    :style="`animation-delay: ${240 + i * 60}ms`">
                    <div :class="`stat-glow bg-gradient-to-br ${stat.color}`"></div>
                    <div :class="`stat-icon-bg bg-gradient-to-br ${stat.color}`">
                        <span class="stat-icon">{{ stat.icon }}</span>
                    </div>
                    <div class="stat-info">
                        <p class="stat-value">{{ stat.value }}</p>
                        <p class="stat-label">{{ stat.label }}</p>
                    </div>
                    <div :class="['stat-trend', stat.trend.startsWith('+') ? 'trend-up' : 'trend-down']">
                        {{ stat.trend }}
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-wrapper" :class="{ 'slide-up': mounted }" style="animation-delay: 420ms">
                <div class="search-glass">
                    <div class="search-icon-wrap">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Search services..." class="search-input">
                    <button class="search-voice">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Apps Section -->
            <div class="apps-section">
                <div class="apps-header" :class="{ 'slide-right': mounted }" style="animation-delay: 500ms">
                    <h3 class="apps-title">Quick Access</h3>
                </div>
                <div class="apps-grid">
                    <component :is="app.link ? Link : 'button'"
                        v-for="(app, i) in mobileApps" :key="app.name"
                        :href="app.link" @click="app.action ? handleAppClick(app) : null"
                        class="app-card" :class="{ 'scale-fade': mounted }"
                        :style="`animation-delay: ${580 + (i * 40)}ms`">
                        <div v-if="app.pulse" class="app-pulse"></div>
                        <div :class="`app-glow bg-gradient-to-br ${app.gradient}`"></div>
                        <div :class="`app-icon-wrap bg-gradient-to-br ${app.gradient}`">
                            <span class="app-icon">{{ app.icon }}</span>
                        </div>
                        <span class="app-name">{{ app.name }}</span>
                    </component>
                </div>
            </div>

            <!-- Featured -->
            <div class="featured-section">
                <div class="featured-header" :class="{ 'slide-right': mounted }" style="animation-delay: 1060ms">
                    <h3 class="featured-title">Featured</h3>
                    <div class="status-dots">
                        <div class="dot dot-1"></div>
                        <div class="dot dot-2"></div>
                        <div class="dot dot-3"></div>
                    </div>
                </div>
                <div class="featured-grid">
                    <Link :href="route('bills.create')" class="featured-card" :class="{ 'scale-in': mounted }" style="animation-delay: 1140ms">
                        <div class="featured-glow glow-green"></div>
                        <div class="featured-icon icon-green">
                            <span>💳</span>
                        </div>
                        <div class="featured-content">
                            <p class="featured-title-text">Pay Bills</p>
                            <p class="featured-desc">Quick payments</p>
                        </div>
                        <div class="featured-arrow">→</div>
                    </Link>
                    <button @click="showWizard = true" class="featured-card" :class="{ 'scale-in': mounted }" style="animation-delay: 1220ms">
                        <div class="featured-glow glow-red"></div>
                        <div class="pulse-indicator">
                            <div class="pulse-dot"></div>
                            <div class="pulse-ring"></div>
                        </div>
                        <div class="featured-icon icon-red">
                            <span>🚨</span>
                        </div>
                        <div class="featured-content">
                            <p class="featured-title-text">Report</p>
                            <p class="featured-desc">Emergency</p>
                        </div>
                        <div class="featured-arrow">→</div>
                    </button>
                </div>
                <div class="map-card" :class="{ 'scale-in': mounted }" style="animation-delay: 1300ms">
                    <div class="map-badge">
                        <span class="badge-pulse">
                            <span class="badge-ping"></span>
                            <span class="badge-dot"></span>
                        </span>
                        <span class="badge-text">Live Map</span>
                    </div>
                    <div class="map-wrap">
                        <DashboardMap />
                    </div>
                    <div class="map-overlay"></div>
                    <div class="map-info">
                        <div class="map-info-content">
                            <div>
                                <p class="map-title">Barangay Overview</p>
                                <p class="map-subtitle">View locations & alerts</p>
                            </div>
                            <div class="map-arrow">→</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Button -->
            <Link :href="route('emergency.index')" class="emergency-fab">
                <div class="fab-rings">
                    <div class="fab-ring ring-1"></div>
                    <div class="fab-ring ring-2"></div>
                    <div class="fab-ring ring-3"></div>
                </div>
                <div class="fab-glow"></div>
                <div class="fab-button">
                    <span class="fab-icon">🚨</span>
                </div>
                <div class="fab-label">
                    <span>Emergency</span>
                </div>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT -->
        <div class="desktop-layout">
            <header class="desktop-header" :class="{ 'fade-in': mounted }">
                <div class="header-content">
                    <div class="greeting-wrap">
                        <div class="status-badge">
                            <span class="status-pulse">
                                <span class="status-ping"></span>
                                <span class="status-dot"></span>
                            </span>
                            <p>System Operational</p>
                        </div>
                        <h1 class="greeting-text">
                            {{ greeting }}, 
                            <span class="greeting-name">{{ user.name.split(' ')[0] }}</span>
                        </h1>
                    </div>
                    <div class="clock-display">
                        <div class="clock-time">
                            {{ time }}
                            <span class="clock-period">{{ dayPeriod }}</span>
                        </div>
                        <div class="clock-date">{{ date }}</div>
                    </div>
                </div>
            </header>

            <div class="desktop-grid">
                <div class="desktop-main">
                    <div class="features-grid">
                        <component :is="feature.link ? Link : 'button'"
                            v-for="(feature, i) in desktopFeatures" :key="feature.title"
                            :href="feature.link" @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="['feature-card', `bg-gradient-to-br ${feature.gradient}`,
                                { 'feature-large': feature.size === 'large',
                                  'feature-medium': feature.size === 'medium',
                                  'scale-fade': mounted }]"
                            :style="`animation-delay: ${i * 70}ms`">
                            <div v-if="feature.featured" class="feature-badge">✨ Popular</div>
                            <Link v-if="feature.historyLink" :href="feature.historyLink" @click.stop class="feature-history">
                                <span>History</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </Link>
                            <div class="feature-content">
                                <div class="feature-icon-wrap">
                                    <span class="feature-icon">{{ feature.icon }}</span>
                                </div>
                                <div class="feature-text">
                                    <p class="feature-subtitle">{{ feature.subtitle }}</p>
                                    <h3 class="feature-title">{{ feature.title }}</h3>
                                </div>
                            </div>
                        </component>
                    </div>
                </div>

                <aside class="desktop-sidebar">
                    <!-- Developer Card -->
                    <div class="developer-card" :class="{ 'scale-in': mounted }">
                        <div class="dev-card-content">
                            <div class="dev-card-gradient"></div>
                            <div class="dev-card-header">
                                <div class="dev-card-avatar">
                                    <div class="dev-avatar-glow"></div>
                                    <div class="dev-avatar-ring"></div>
                                    <span class="dev-avatar-icon">👨‍💻</span>
                                </div>
                                <div class="dev-card-badge">Developer</div>
                            </div>
                            <div class="dev-card-body">
                                <p class="dev-card-label">Created & Developed by</p>
                                <h3 class="dev-card-name">Justine Philip Villarosa</h3>
                                <p class="dev-card-degree">Bachelor of Science in Information Technology</p>
                                <div class="dev-card-tags">
                                    <span class="dev-tag">🎓 BSIT</span>
                                    <span class="dev-tag">⚡ Full Stack</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="id-card" :class="{ 'scale-in': mounted }" style="animation-delay: 100ms">
                        <div class="id-card-content">
                            <div class="id-card-header">
                                <div class="id-logo">🏛️</div>
                                <div class="id-number">
                                    <div class="id-label">Universal ID</div>
                                    <div class="id-value">{{ user.id.toString().padStart(8, '0') }}</div>
                                </div>
                            </div>
                            <div class="id-card-footer">
                                <div class="id-name">{{ user.name }}</div>
                                <div class="id-badges">
                                    <span class="id-badge badge-primary">
                                        {{ isBusinessOwner ? 'Business Owner' : 'Citizen' }}
                                    </span>
                                    <span class="id-badge badge-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('polls.index')" class="sidebar-card card-polls"
                          :class="{ 'scale-in': mounted }" style="animation-delay: 300ms">
                        <div class="card-content">
                            <div class="card-header">
                                <div class="card-icon">🗳️</div>
                                <span class="card-badge">Live</span>
                            </div>
                            <div class="card-footer">
                                <h3 class="card-title">Freedom Poll</h3>
                                <p class="card-subtitle">Share Your Voice</p>
                            </div>
                        </div>
                    </Link>

                    <div class="sidebar-map" :class="{ 'scale-in': mounted }" style="animation-delay: 400ms">
                        <div class="map-badge-desk">📍 Live Map & Alerts</div>
                        <div class="map-wrapper">
                            <DashboardMap />
                        </div>
                    </div>
                    
                    <Link :href="route('emergency.index')" class="sidebar-card card-emergency"
                          :class="{ 'scale-in': mounted }" style="animation-delay: 500ms">
                        <div class="card-content">
                            <div class="emergency-pulse-1"></div>
                            <div class="emergency-pulse-2"></div>
                            <div class="card-header">
                                <div class="card-icon">📞</div>
                                <span class="card-badge badge-emergency">SOS</span>
                            </div>
                            <div class="card-footer">
                                <h3 class="card-title">Emergency Call</h3>
                                <p class="card-subtitle">Tap for Command Center</p>
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
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
    -webkit-font-smoothing: antialiased; 
    letter-spacing: -0.01em; 
}

/* GPU Acceleration */
.intro-overlay,
.widget-glass,
.stat-card,
.app-card,
.feature-card {
    transform: translate3d(0, 0, 0);
    will-change: transform;
}

/* ========== ELEGANT INTRO ========== */
.intro-overlay {
    @apply fixed inset-0 z-[9999] flex items-center justify-center;
    background: radial-gradient(ellipse at center, #0a0a0f 0%, #000 100%);
}

.intro-bg {
    @apply absolute inset-0;
}

.intro-gradient {
    @apply absolute inset-0 opacity-30;
    background: radial-gradient(circle at 30% 50%, rgba(59, 130, 246, 0.15), transparent 70%);
}

.intro-glow {
    @apply absolute inset-0 opacity-20;
    background: radial-gradient(circle at 70% 50%, rgba(168, 85, 247, 0.12), transparent 70%);
    animation: glow-pulse 4s ease-in-out infinite;
}

@keyframes glow-pulse {
    0%, 100% { opacity: 0.15; transform: scale(1); }
    50% { opacity: 0.25; transform: scale(1.05); }
}

.skip-btn {
    @apply absolute top-8 right-8 z-50 px-5 py-2.5 
           bg-white/5 backdrop-blur-xl border border-white/10 
           rounded-full text-white text-sm font-semibold 
           hover:bg-white/10 transition-all duration-300 
           flex items-center gap-2 hover:scale-105 active:scale-95;
}

.intro-content {
    @apply text-center z-10;
    animation: intro-enter 1s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes intro-enter {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.intro-logo {
    @apply relative inline-block mb-6;
}

.logo-glow {
    @apply absolute inset-0 bg-blue-500/20 blur-3xl rounded-full;
    animation: logo-pulse 2s ease-in-out infinite;
}

@keyframes logo-pulse {
    0%, 100% { opacity: 0.2; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.1); }
}

.logo-icon {
    @apply relative text-7xl;
    filter: drop-shadow(0 0 30px rgba(59, 130, 246, 0.6));
    animation: logo-float 3s ease-in-out infinite;
}

@keyframes logo-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.intro-title {
    @apply text-5xl font-black text-white mb-3 tracking-wider;
    text-shadow: 0 0 40px rgba(59, 130, 246, 0.4);
}

.intro-subtitle {
    @apply text-lg text-gray-400 font-light tracking-widest uppercase;
}

/* ========== TOAST ========== */
.toast {
    @apply fixed top-20 right-4 z-[60] w-[90vw] sm:w-96 
           rounded-2xl shadow-2xl flex items-center gap-3 p-4;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.toast-icon {
    @apply w-10 h-10 text-white flex-shrink-0;
}

.toast-text {
    @apply text-sm font-semibold text-white flex-1;
}

.toast-close {
    @apply w-5 h-5 text-white hover:scale-110 transition-transform;
}

/* ========== MOBILE LAYOUT ========== */
.mobile-layout {
    @apply lg:hidden w-full max-w-md mx-auto px-4 pb-8 pt-4;
}

.desktop-layout {
    @apply hidden lg:block w-full max-w-7xl mx-auto px-4 pb-20;
}

/* Logo Section */
.logo-section {
    @apply mb-4 flex justify-center opacity-0;
}

.logo-container {
    @apply relative;
}

.logo-glow-ring {
    @apply absolute inset-0;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent 70%);
    filter: blur(20px);
    animation: logo-pulse-mobile 3s ease-in-out infinite;
}

@keyframes logo-pulse-mobile {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}

.logo-image {
    @apply relative w-24 h-24 object-contain;
    filter: drop-shadow(0 10px 30px rgba(59, 130, 246, 0.5));
    animation: logo-float-mobile 4s ease-in-out infinite;
}

@keyframes logo-float-mobile {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

/* Developer Carousel */
.developer-carousel {
    @apply mb-5 opacity-0;
}

.carousel-glass {
    @apply relative overflow-hidden rounded-3xl p-5 border border-white/10;
    background: linear-gradient(135deg, rgba(28, 28, 30, 0.95), rgba(44, 44, 46, 0.95));
    backdrop-filter: blur(40px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.carousel-glass:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9);
    border-color: rgba(255, 255, 255, 0.15);
}

.carousel-gradient {
    @apply absolute inset-0 opacity-40 z-0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 51, 234, 0.08));
    animation: carousel-shift 8s ease-in-out infinite;
}

@keyframes carousel-shift {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(20px); }
}

.carousel-content {
    @apply relative flex items-center gap-4;
}

.carousel-content::before {
    content: '';
    @apply absolute inset-0 -translate-x-full;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
    animation: carousel-shine 8s ease-in-out infinite;
}

@keyframes carousel-shine {
    0%, 90%, 100% { transform: translateX(-100%); }
    45%, 55% { transform: translateX(100%); }
}

.dev-avatar {
    @apply relative flex-shrink-0 z-10;
}

.avatar-glow {
    @apply absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl blur-xl opacity-40;
    animation: avatar-glow 3s ease-in-out infinite;
}

@keyframes avatar-glow {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}

.avatar-ring {
    @apply absolute inset-0 rounded-2xl;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(147, 51, 234, 0.4), rgba(236, 72, 153, 0.4));
    padding: 2px;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: ring-rotate 4s linear infinite;
}

@keyframes ring-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.avatar-inner {
    @apply relative w-16 h-16 rounded-2xl flex items-center justify-center;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(147, 51, 234, 0.3));
    backdrop-filter: blur(20px);
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.3);
}

.avatar-icon {
    @apply text-3xl;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
}

.dev-info {
    @apply flex-1 min-w-0 relative z-10;
}

.dev-label {
    @apply text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1;
}

.dev-name {
    @apply text-base font-black text-white mb-0.5 leading-tight;
    background: linear-gradient(135deg, #fff, #e0e0e0);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: name-shimmer 3s ease-in-out infinite;
}

@keyframes name-shimmer {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}

.dev-title {
    @apply text-xs text-gray-400 leading-tight mb-2;
}

.dev-badges {
    @apply flex items-center gap-2 flex-wrap;
}

.dev-badge {
    @apply inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold border;
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
    color: rgb(147, 197, 253);
    animation: badge-glow 2s ease-in-out infinite;
}

@keyframes badge-glow {
    0%, 100% { box-shadow: 0 0 0 rgba(59, 130, 246, 0); }
    50% { box-shadow: 0 0 12px rgba(59, 130, 246, 0.4); }
}

.carousel-decoration {
    @apply absolute top-3 right-3 z-20;
}

.decoration-dot {
    @apply absolute w-1.5 h-1.5 rounded-full;
    animation: dot-blink 2s ease-in-out infinite;
}

.dot-1 {
    @apply bg-blue-400 top-0 right-0;
    box-shadow: 0 0 8px rgba(96, 165, 250, 0.5);
}

.dot-2 {
    @apply bg-purple-400 top-3 right-0;
    box-shadow: 0 0 8px rgba(192, 132, 252, 0.5);
    animation-delay: 0.3s;
}

.dot-3 {
    @apply bg-pink-400 top-6 right-0;
    box-shadow: 0 0 8px rgba(244, 114, 182, 0.5);
    animation-delay: 0.6s;
}

@keyframes dot-blink {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.3); }
}

/* Time Widget */
.time-widget {
    @apply mb-6 opacity-0;
}

.widget-glass {
    @apply relative overflow-hidden rounded-3xl p-5 border border-white/10;
    background: linear-gradient(135deg, rgba(28, 28, 30, 0.95), rgba(44, 44, 46, 0.95));
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(40px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.widget-glass:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9);
}

.widget-gradient {
    @apply absolute inset-0 opacity-50;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.06), rgba(147, 51, 234, 0.06));
}

.time-content {
    @apply relative flex items-center justify-between;
}

.time-left {
    @apply flex-1;
}

.time-label {
    @apply text-gray-400 text-xs font-medium mb-1 tracking-wide uppercase;
}

.time-display {
    @apply flex items-baseline gap-2;
}

.time-value {
    @apply text-4xl font-bold text-white tracking-tight;
}

.time-period {
    @apply text-lg font-semibold text-gray-400;
}

.date-badge {
    @apply px-4 py-2 rounded-2xl border border-white/10;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    transition: transform 0.3s;
}

.date-badge:hover {
    transform: scale(1.05);
}

.date-day {
    @apply text-2xl font-bold text-white text-center leading-none;
}

.date-month {
    @apply text-[10px] text-gray-400 font-bold uppercase tracking-wider;
}

/* Stats Grid */
.stats-grid {
    @apply mb-6 grid grid-cols-3 gap-3;
}

.stat-card {
    @apply relative overflow-hidden rounded-2xl p-5 border border-white/10 opacity-0;
    background: linear-gradient(135deg, rgba(28, 28, 30, 0.95), rgba(44, 44, 46, 0.95));
    backdrop-filter: blur(40px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8);
}

.stat-glow {
    @apply absolute -top-10 -right-10 w-20 h-20 opacity-20 blur-2xl rounded-full;
    transition: all 0.5s;
}

.stat-card:hover .stat-glow {
    @apply opacity-30 scale-110;
}

.stat-icon-bg {
    @apply w-11 h-11 rounded-xl flex items-center justify-center mb-3;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s;
}

.stat-card:hover .stat-icon-bg {
    transform: scale(1.1) rotate(5deg);
}

.stat-icon {
    @apply text-2xl;
}

.stat-info {
    @apply relative;
}

.stat-value {
    @apply text-3xl font-bold text-white mb-0.5 tracking-tight;
}

.stat-label {
    @apply text-[10px] text-gray-400 font-semibold uppercase tracking-widest;
}

.stat-trend {
    @apply absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full;
}

.trend-up {
    @apply text-green-400 bg-green-500/20 border border-green-500/30;
}

.trend-down {
    @apply text-red-400 bg-red-500/20 border border-red-500/30;
}

/* Search Bar */
.search-wrapper {
    @apply mb-6 opacity-0;
}

.search-glass {
    @apply relative bg-[#1c1c1e] rounded-3xl border border-white/10 shadow-xl flex items-center px-4 py-4;
    backdrop-filter: blur(40px);
    transition: all 0.3s;
}

.search-glass:focus-within {
    @apply border-white/15;
    transform: translateY(-2px);
}

.search-icon-wrap {
    @apply w-10 h-10 flex items-center justify-center;
}

.search-icon {
    @apply w-5 h-5 text-gray-400 transition-all;
}

.search-glass:focus-within .search-icon {
    @apply text-blue-400 scale-110;
}

.search-input {
    @apply flex-1 bg-transparent text-white placeholder-gray-500 text-[15px] focus:outline-none;
}

.search-voice {
    @apply w-9 h-9 flex items-center justify-center rounded-xl transition-all;
    background: rgba(255, 255, 255, 0.08);
}

.search-voice:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: scale(1.05);
}

.search-voice svg {
    @apply w-4 h-4 text-gray-400;
}

/* Apps Grid */
.apps-section {
    @apply mb-6;
}

.apps-header {
    @apply flex items-center justify-between mb-4 px-1 opacity-0;
}

.apps-title {
    @apply text-lg font-bold text-white;
}

.apps-grid {
    @apply grid grid-cols-4 gap-4;
}

.app-card {
    @apply flex flex-col items-center gap-2 rounded-3xl p-2 relative opacity-0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.app-card:active {
    transform: scale(0.92);
}

.app-pulse {
    @apply absolute inset-0 bg-red-500 rounded-[1.3rem] animate-ping opacity-30;
}

.app-glow {
    @apply absolute -inset-1 rounded-[1.4rem] opacity-0 blur-lg;
    transition: opacity 0.3s;
}

.app-card:hover .app-glow {
    @apply opacity-60;
}

.app-icon-wrap {
    @apply relative w-full aspect-square rounded-[1.3rem] shadow-xl flex items-center justify-center overflow-hidden;
    transition: transform 0.3s;
}

.app-card:hover .app-icon-wrap {
    transform: scale(1.08) rotate(-2deg);
}

.app-icon-wrap::before {
    content: '';
    @apply absolute inset-[1px] rounded-[1.2rem];
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.25), transparent);
}

.app-icon {
    @apply relative text-[32px] filter drop-shadow-2xl;
}

.app-name {
    @apply text-[11px] font-semibold text-gray-300 text-center leading-tight;
}

/* Featured Section */
.featured-section {
    @apply space-y-4 mb-6;
}

.featured-header {
    @apply flex items-center justify-between px-1 opacity-0;
}

.featured-title {
    @apply text-lg font-bold text-white;
}

.status-dots {
    @apply flex gap-1.5;
}

.dot {
    @apply w-1.5 h-1.5 rounded-full animate-pulse;
}

.dot-1 {
    @apply bg-green-400;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

.dot-2 {
    @apply bg-blue-400;
    box-shadow: 0 0 8px rgba(96, 165, 250, 0.5);
    animation-delay: 0.2s;
}

.dot-3 {
    @apply bg-purple-400;
    box-shadow: 0 0 8px rgba(192, 132, 252, 0.5);
    animation-delay: 0.4s;
}

.featured-grid {
    @apply grid grid-cols-2 gap-3;
}

.featured-card {
    @apply relative overflow-hidden rounded-2xl p-4 border border-white/10 opacity-0;
    background: linear-gradient(135deg, rgba(28, 28, 30, 0.95), rgba(44, 44, 46, 0.95));
    backdrop-filter: blur(40px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    transition: all 0.3s;
}

.featured-card:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8);
}

.featured-glow {
    @apply absolute -top-10 -right-10 w-20 h-20 opacity-20 blur-2xl rounded-full;
}

.glow-green {
    background: radial-gradient(circle, rgba(34, 197, 94, 1), transparent);
}

.glow-red {
    background: radial-gradient(circle, rgba(239, 68, 68, 1), transparent);
}

.featured-card:hover .featured-glow {
    @apply opacity-30 scale-110;
}

.pulse-indicator {
    @apply absolute top-3 right-3 z-10;
}

.pulse-dot {
    @apply w-2 h-2 bg-red-400 rounded-full animate-pulse;
    box-shadow: 0 0 8px rgba(248, 113, 113, 0.7);
}

.pulse-ring {
    @apply absolute inset-0 bg-red-400 rounded-full animate-ping;
}

.featured-icon {
    @apply mb-3 w-11 h-11 rounded-xl flex items-center justify-center shadow-lg;
    transition: transform 0.3s;
}

.icon-green {
    background: linear-gradient(135deg, rgba(34, 197, 94, 1), rgba(16, 185, 129, 1));
}

.icon-red {
    background: linear-gradient(135deg, rgba(239, 68, 68, 1), rgba(236, 72, 153, 1));
}

.featured-card:hover .featured-icon {
    transform: scale(1.1) rotate(5deg);
}

.featured-icon span {
    @apply text-2xl;
}

.featured-content {
    @apply relative z-10;
}

.featured-title-text {
    @apply text-sm font-bold text-white mb-1;
}

.featured-desc {
    @apply text-xs text-gray-400;
}

.featured-arrow {
    @apply absolute bottom-4 right-4 text-lg text-gray-400;
    transition: all 0.3s;
}

.featured-card:hover .featured-arrow {
    @apply text-white translate-x-1;
}

/* Map Card */
.map-card {
    @apply relative overflow-hidden rounded-2xl border border-white/10 opacity-0;
    background: linear-gradient(135deg, rgba(28, 28, 30, 0.95), rgba(44, 44, 46, 0.95));
    backdrop-filter: blur(40px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    transition: transform 0.3s;
}

.map-card:hover {
    transform: scale(1.01);
}

.map-badge {
    @apply absolute top-4 left-4 z-10 flex items-center gap-2 px-3 py-2 rounded-full border border-white/10;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(40px);
}

.badge-pulse {
    @apply relative flex h-2 w-2;
}

.badge-ping {
    @apply animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75;
}

.badge-dot {
    @apply relative inline-flex rounded-full h-2 w-2 bg-green-400;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

.badge-text {
    @apply text-xs font-bold text-white;
}

.map-wrap {
    @apply w-full h-52;
}

.map-overlay {
    @apply absolute inset-0 bg-gradient-to-t from-[#1c1c1e] via-transparent to-transparent pointer-events-none;
}

.map-info {
    @apply absolute bottom-0 left-0 right-0 p-4;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    backdrop-filter: blur(20px);
}

.map-info-content {
    @apply flex items-center justify-between;
}

.map-title {
    @apply text-sm font-bold text-white;
}

.map-subtitle {
    @apply text-xs text-gray-300;
}

.map-arrow {
    @apply text-lg text-gray-400;
    transition: all 0.3s;
}

.map-card:hover .map-arrow {
    @apply text-white translate-x-1;
}

/* Emergency FAB */
.emergency-fab {
    @apply fixed bottom-36 right-6 z-40;
}

.fab-rings {
    @apply absolute inset-0;
}

.fab-ring {
    @apply absolute inset-0 rounded-full animate-ping;
}

.ring-1 {
    @apply bg-red-400 opacity-25;
}

.ring-2 {
    @apply bg-red-500 opacity-20;
    animation-delay: 0.5s;
}

.ring-3 {
    @apply bg-red-600 opacity-15;
    animation-delay: 1s;
}

.fab-glow {
    @apply absolute inset-0 bg-red-500 rounded-full blur-2xl opacity-60;
}

.fab-button {
    @apply relative w-16 h-16 rounded-full flex items-center justify-center border-4 border-black/30 overflow-hidden;
    background: linear-gradient(135deg, rgba(239, 68, 68, 1), rgba(220, 38, 38, 1));
    box-shadow: 0 20px 60px rgba(239, 68, 68, 0.7);
    transition: transform 0.3s;
}

.emergency-fab:hover .fab-button {
    transform: scale(1.08);
}

.fab-button::before {
    content: '';
    @apply absolute inset-[2px] rounded-full;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), transparent);
}

.fab-icon {
    @apply relative text-3xl z-10;
}

.fab-label {
    @apply absolute -bottom-7 left-1/2 -translate-x-1/2 whitespace-nowrap opacity-0;
    transition: opacity 0.3s;
}

.emergency-fab:hover .fab-label {
    @apply opacity-100;
}

.fab-label span {
    @apply px-3 py-1 rounded-full text-[10px] font-bold text-white;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* ========== DESKTOP LAYOUT ========== */
.desktop-header {
    @apply mb-8 opacity-0;
}

.header-content {
    @apply flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4 mb-6;
}

.greeting-wrap {
    @apply flex-1;
}

.status-badge {
    @apply flex items-center gap-2 mb-2;
}

.status-pulse {
    @apply relative flex h-2.5 w-2.5;
}

.status-ping {
    @apply animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75;
}

.status-dot {
    @apply relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500;
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

.status-badge p {
    @apply text-blue-300/70 text-[10px] font-bold tracking-widest uppercase;
}

.greeting-text {
    @apply text-5xl font-extrabold text-white tracking-tight;
}

.greeting-name {
    @apply text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400;
    background-size: 200% auto;
    animation: gradient-x 3s ease infinite;
}

@keyframes gradient-x {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.clock-display {
    @apply text-right;
}

.clock-time {
    @apply text-6xl font-thin text-white tracking-tight flex items-baseline gap-2;
}

.clock-period {
    @apply text-2xl font-medium text-gray-400;
}

.clock-date {
    @apply text-white/40 font-medium text-xs uppercase tracking-widest mt-1;
}

.desktop-grid {
    @apply grid grid-cols-1 lg:grid-cols-12 gap-6;
}

.desktop-main {
    @apply lg:col-span-8 space-y-6;
}

.features-grid {
    @apply grid grid-cols-2 lg:grid-cols-3 gap-4;
}

.feature-card {
    @apply relative overflow-hidden flex flex-col justify-between p-5 rounded-3xl border border-white/10 opacity-0;
    backdrop-filter: blur(40px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8);
}

.feature-large {
    @apply col-span-2 h-40;
}

.feature-medium {
    @apply h-32;
}

.feature-card::before {
    content: '';
    @apply absolute inset-0 opacity-10;
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yIDItNCA0LTRoNGEyIDIgMCAwIDEgMCAyczIgMiAyIDRjMCAyLTIgNC00IDRoLTRhMiAyIDAgMCAxIDAtNHoiLz48L2c+PC9nPjwvc3ZnPg==');
}

.feature-badge {
    @apply absolute top-4 left-4 z-10 px-2.5 py-1 rounded-full text-[10px] font-bold text-white uppercase tracking-wider;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.feature-history {
    @apply absolute top-4 right-4 z-10 flex items-center gap-1 pl-2.5 pr-1.5 py-1 rounded-full border border-white/15 text-[10px] font-bold text-white uppercase tracking-widest;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(20px);
    transition: all 0.3s;
}

.feature-history:hover {
    background: rgba(0, 0, 0, 0.5);
    @apply scale-105;
}

.feature-content {
    @apply relative z-0 flex flex-col justify-between h-full;
}

.feature-icon-wrap {
    @apply w-11 h-11 rounded-xl flex items-center justify-center;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    transition: transform 0.3s;
}

.feature-card:hover .feature-icon-wrap {
    transform: scale(1.15) rotate(8deg);
}

.feature-icon {
    @apply text-2xl;
}

.feature-text {
    @apply space-y-1 mt-auto;
}

.feature-subtitle {
    @apply text-white/60 text-xs font-semibold uppercase tracking-wider;
}

.feature-title {
    @apply font-black text-white text-xl leading-tight;
}

/* Desktop Sidebar */
.desktop-sidebar {
    @apply lg:col-span-4 space-y-6;
}

.developer-card {
    @apply rounded-3xl overflow-hidden opacity-0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.developer-card:hover {
    transform: translateY(-3px) scale(1.01);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.9);
}

.dev-card-content {
    @apply p-6 relative overflow-hidden;
    background: linear-gradient(135deg, rgba(30, 41, 59, 1), rgba(15, 23, 42, 1));
    min-height: 200px;
}

.dev-card-content::after {
    content: '';
    @apply absolute inset-0 -translate-x-full;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: dev-card-shine 10s ease-in-out infinite;
}

@keyframes dev-card-shine {
    0%, 90%, 100% { transform: translateX(-100%); }
    45%, 55% { transform: translateX(100%); }
}

.dev-card-gradient {
    @apply absolute inset-0 opacity-30 z-0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(147, 51, 234, 0.15), rgba(236, 72, 153, 0.1));
    animation: dev-gradient-shift 10s ease-in-out infinite;
}

@keyframes dev-gradient-shift {
    0%, 100% { transform: translateX(0) rotate(0deg); }
    50% { transform: translateX(30px) rotate(5deg); }
}

.dev-card-header {
    @apply relative flex items-start justify-between mb-4 z-20;
}

.dev-card-avatar {
    @apply relative;
}

.dev-avatar-glow {
    @apply absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl blur-2xl opacity-40;
    animation: dev-avatar-pulse 3s ease-in-out infinite;
}

@keyframes dev-avatar-pulse {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.15); }
}

.dev-avatar-ring {
    @apply absolute inset-0 rounded-2xl;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.6), rgba(147, 51, 234, 0.6), rgba(236, 72, 153, 0.6));
    padding: 3px;
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: dev-ring-spin 5s linear infinite;
}

@keyframes dev-ring-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.dev-avatar-icon {
    @apply relative w-16 h-16 rounded-2xl flex items-center justify-center text-3xl;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(147, 51, 234, 0.3));
    backdrop-filter: blur(20px);
    box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}

.dev-card-badge {
    @apply px-3 py-1.5 rounded-full text-xs font-bold border;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
    border-color: rgba(59, 130, 246, 0.3);
    color: rgb(147, 197, 253);
    backdrop-filter: blur(20px);
    animation: badge-glow 2s ease-in-out infinite;
}

.dev-card-body {
    @apply relative z-20;
}

.dev-card-label {
    @apply text-xs text-gray-400 font-bold uppercase tracking-wider mb-2;
}

.dev-card-name {
    @apply text-2xl font-black text-white mb-2 leading-tight;
    background: linear-gradient(135deg, #fff, #cbd5e1);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: name-shimmer 3s ease-in-out infinite;
}

.dev-card-degree {
    @apply text-sm text-gray-400 mb-4 leading-relaxed;
}

.dev-card-tags {
    @apply flex items-center gap-2 flex-wrap;
}

.dev-tag {
    @apply inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border;
    background: rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
    color: rgb(147, 197, 253);
    transition: all 0.3s;
}

.dev-tag:hover {
    background: rgba(59, 130, 246, 0.25);
    transform: translateY(-2px);
}

.id-card {
    @apply aspect-[1.6/1] rounded-3xl overflow-hidden opacity-0;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
}

.id-card-content {
    @apply h-full p-6 flex flex-col justify-between relative overflow-hidden;
    background: linear-gradient(135deg, rgba(51, 65, 85, 1), rgba(30, 41, 59, 1), rgba(0, 0, 0, 1));
    transition: transform 0.3s;
}

.id-card:hover .id-card-content {
    transform: scale(1.01);
}

.id-card-content::before {
    content: '';
    @apply absolute inset-0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 51, 234, 0.05));
}

.id-card-header {
    @apply flex justify-between items-start relative z-10;
}

.id-logo {
    @apply w-10 h-10 rounded-full flex items-center justify-center text-lg border border-white/20;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
}

.id-number {
    @apply text-right;
}

.id-label {
    @apply text-[9px] text-white/50 uppercase tracking-widest font-bold;
}

.id-value {
    @apply text-white font-mono font-bold text-sm tracking-widest opacity-80;
}

.id-card-footer {
    @apply relative z-10;
}

.id-name {
    @apply text-2xl text-white font-black tracking-wide uppercase mb-1 truncate;
}

.id-badges {
    @apply flex items-center gap-2 flex-wrap;
}

.id-badge {
    @apply px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-wider border;
}

.badge-primary {
    background: linear-gradient(to right, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.25));
    @apply text-blue-300 border-blue-500/40;
}

.badge-success {
    background: linear-gradient(to right, rgba(34, 197, 94, 0.25), rgba(22, 163, 74, 0.25));
    @apply text-green-300 border-green-500/40;
}

.sidebar-card {
    @apply block rounded-3xl overflow-hidden h-40 opacity-0;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    transition: all 0.3s;
}

.sidebar-card:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.8);
}

.card-content {
    @apply h-full p-6 flex flex-col justify-between relative overflow-hidden;
}

.card-polls .card-content {
    background: linear-gradient(135deg, rgba(202, 138, 4, 1), rgba(161, 98, 7, 1));
}

.card-emergency .card-content {
    background: linear-gradient(135deg, rgba(220, 38, 38, 1), rgba(185, 28, 28, 1));
}

.card-content::before {
    content: '';
    @apply absolute inset-0 opacity-10;
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4xIj48cGF0aCBkPSJNMzYgMzRjMC0yIDItNCA0LTRoNGEyIDIgMCAwIDEgMCAyczIgMiAyIDRjMCAyLTIgNC00IDRoLTRhMiAyIDAgMCAxIDAtNHoiLz48L2c+PC9nPjwvc3ZnPg==');
}

.card-header {
    @apply relative flex justify-between items-start z-10;
}

.card-icon {
    @apply w-11 h-11 rounded-xl flex items-center justify-center text-2xl;
    background: rgba(255, 255, 255, 0.2);
    transition: transform 0.3s;
}

.sidebar-card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
}

.card-badge {
    @apply text-[10px] font-black px-2 py-1 rounded uppercase tracking-widest;
    background: rgba(255, 255, 255, 1);
}

.card-polls .card-badge {
    @apply text-amber-600;
}

.badge-emergency {
    @apply animate-pulse text-red-600;
}

.card-footer {
    @apply relative z-10;
}

.card-title {
    @apply font-black text-white text-2xl uppercase tracking-wide;
}

.card-subtitle {
    @apply text-white/80 text-xs font-bold mt-1;
}

.emergency-pulse-1,
.emergency-pulse-2 {
    @apply absolute -top-2 -right-2 w-6 h-6 rounded-full animate-ping;
}

.emergency-pulse-1 {
    @apply bg-white/30;
}

.emergency-pulse-2 {
    @apply bg-white/20;
    animation-delay: 0.5s;
}

.sidebar-map {
    @apply relative rounded-3xl p-1 h-80 opacity-0;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    transition: transform 0.3s;
}

.sidebar-map:hover {
    transform: translateY(-3px);
}

.map-badge-desk {
    @apply absolute top-4 left-4 z-10 px-3 py-1 rounded-full text-xs font-bold text-white border border-white/15;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(20px);
}

.map-wrapper {
    @apply w-full h-full rounded-[1.8rem] overflow-hidden opacity-90;
}

/* ========== ANIMATIONS ========== */
.fade-in {
    animation: fade-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}

.scale-in {
    animation: scale-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes scale-in {
    from { opacity: 0; transform: scale(0.92); }
    to { opacity: 1; transform: scale(1); }
}

.slide-up {
    animation: slide-up 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes slide-up {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-right {
    animation: slide-right 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes slide-right {
    from { opacity: 0; transform: translateX(-25px); }
    to { opacity: 1; transform: translateX(0); }
}

.slide-down {
    animation: slide-down 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes slide-down {
    from { opacity: 0; transform: translateY(-25px); }
    to { opacity: 1; transform: translateY(0); }
}

.scale-fade {
    animation: scale-fade 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes scale-fade {
    from { opacity: 0; transform: scale(0.85); }
    to { opacity: 1; transform: scale(1); }
}

/* ========== TRANSITIONS ========== */
.intro-fade-enter-active,
.intro-fade-leave-active {
    transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.intro-fade-enter-from,
.intro-fade-leave-to {
    opacity: 0;
}

.toast-enter-active {
    animation: toast-enter 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-leave-active {
    transition: all 0.3s ease-in;
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

@keyframes toast-enter {
    from { opacity: 0; transform: translateX(100%) scale(0.9); }
    to { opacity: 1; transform: translateX(0) scale(1); }
}

/* ========== RESPONSIVE ========== */
@media (max-width: 640px) {
    .time-value {
        @apply text-3xl;
    }
    .date-badge {
        @apply px-3 py-1.5;
    }
    .date-day {
        @apply text-xl;
    }
}

/* ========== ACCESSIBILITY ========== */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* ========== PRINT ========== */
@media print {
    .intro-overlay,
    .emergency-fab,
    .skip-btn {
        display: none !important;
    }
}
</style>
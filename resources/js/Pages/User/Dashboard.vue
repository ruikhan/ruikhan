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
const mounted = ref(false);

const isBusinessOwner = computed(() => {
    return user.role === 'business_owner' || user.has_business;
});

const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

const time = ref('');
const date = ref('');
const greeting = ref('Good Morning');
const dayPeriod = ref('AM');

// Notification count (you can make this dynamic)
const notificationCount = ref(3);

// Simplified update (no animations)
const updateTime = () => {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const isPM = hours >= 12;
    
    dayPeriod.value = isPM ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    
    time.value = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    date.value = now.toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long' });
    
    if (hours < 12) greeting.value = 'Good Morning';
    else if (hours < 18) greeting.value = 'Good Afternoon';
    else greeting.value = 'Good Evening';
};

// Reduced stats for better performance
const quickStats = [
    { icon: '📄', label: 'Active', value: '', color: 'from-blue-500 to-blue-600' },
    { icon: '✅', label: 'Done', value: '', color: 'from-green-500 to-green-600' },
    { icon: '📊', label: 'Total', value: '', color: 'from-purple-500 to-purple-600' }
];

// Core mobile apps only (6 essential)
const mobileApps = [
    { icon: '⚡', name: 'Services', link: route('services.landing'), gradient: 'from-blue-500 to-indigo-600' },
    { icon: '💳', name: 'Bills', link: route('bills.create'), gradient: 'from-green-500 to-emerald-600' },
    { icon: '🩺', name: 'Health', link: route('health.index'), gradient: 'from-red-500 to-rose-600' },
    { icon: '🏪', name: 'Market', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: 'from-orange-500 to-amber-600' },
    { icon: '🚨', name: 'Report', link: null, action: 'wizard', gradient: 'from-red-600 to-pink-700' },
];

// Core desktop features only (6 essential)
const desktopFeatures = [
    { title: 'Smart Services', subtitle: 'Permits & Clearances', icon: '⚡', link: route('services.landing'), historyLink: route('services.index'), gradient: 'from-blue-500 to-indigo-600', size: 'large' },
    { title: 'Bills & Fees', subtitle: 'Secure Payments', icon: '💳', link: route('bills.create'), gradient: 'from-teal-500 to-emerald-600', size: 'medium' },
    { title: 'E-Health', subtitle: 'Telemed & Booking', icon: '🩺', link: route('health.index'), gradient: 'from-sky-500 to-cyan-600', size: 'medium' },
    { title: 'Issue Report', subtitle: 'Quick Response', icon: '🚨', link: null, action: 'wizard', gradient: 'from-red-500 to-pink-600', size: 'medium' },
    { title: 'Marketplace', subtitle: 'Local Businesses', icon: '🌐', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', size: 'large' },
    { title: isBusinessOwner.value ? 'My Business' : 'Business Hub', subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', icon: isBusinessOwner.value ? '📊' : '🏪', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600', size: 'medium' },
];

let clockTimer;

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    
    if (flashSuccess.value) { 
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 3000);
    }
    
    // Simple entrance
    setTimeout(() => mounted.value = true, 50);
});

onUnmounted(() => { 
    clearInterval(clockTimer);
});

const handleAppClick = (app) => {
    if (app.action === 'wizard') showWizard.value = true;
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        
        <!-- Simple Toast -->
        <Transition name="toast">
            <div v-if="showFlash && flashSuccess" class="toast">
                <div class="toast-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="toast-text">{{ flashSuccess }}</p>
                <button @click="showFlash = false" class="toast-close">×</button>
            </div>
        </Transition>

        <!-- MOBILE LAYOUT -->
        <div class="mobile-layout">
            <!-- Logo Header -->
            <div class="logo-header" :class="{ 'show': mounted }">
                <div class="logo-container">
                    <div class="logo-icon">
                        <span class="logo-emoji">🏛️</span>
                    </div>
                    <div class="logo-text">
                        <h1 class="logo-title">CityConnect</h1>
                        <p class="logo-subtitle">Smart City Portal</p>
                    </div>
                </div>
            </div>

            <!-- Time Widget with Notification -->
            <div class="time-widget" :class="{ 'show': mounted }">
                <div class="time-content">
                    <div class="time-info">
                        <p class="time-label">{{ date }}</p>
                        <div class="time-display">
                            <h2 class="time-value">{{ time }}</h2>
                            <span class="time-period">{{ dayPeriod }}</span>
                        </div>  
                    </div>
                    
                    <!-- Enhanced Notification Button -->
                    <Link :href="route('notifications.index')" class="notification-button">
                        <div class="notification-icon-wrap">
                            <svg class="notification-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="notificationCount > 0" class="notification-badge">{{ notificationCount }}</span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div v-for="(stat, i) in quickStats" :key="stat.label"
                    class="stat-card" :class="{ 'show': mounted }"
                    :style="`animation-delay: ${i * 50}ms`">
                    <div :class="`stat-icon-bg bg-gradient-to-br ${stat.color}`">
                        <span class="stat-icon">{{ stat.icon }}</span>
                    </div>
                    <div class="stat-info">
                        <p class="stat-value">{{ stat.value }}</p>
                        <p class="stat-label">{{ stat.label }}</p>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-wrapper" :class="{ 'show': mounted }">
                <div class="search-glass">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search services..." class="search-input">
                </div>
            </div>

            <!-- Apps Section (6 core only) -->
            <div class="apps-section">
                <div class="apps-header" :class="{ 'show': mounted }">
                    <h3 class="apps-title">Quick Access</h3>
                </div>
                <div class="apps-grid">
                    <component :is="app.link ? Link : 'button'"
                        v-for="(app, i) in mobileApps" :key="app.name"
                        :href="app.link" @click="app.action ? handleAppClick(app) : null"
                        class="app-card" :class="{ 'show': mounted }"
                        :style="`animation-delay: ${i * 40}ms`">
                        <div :class="`app-icon-wrap bg-gradient-to-br ${app.gradient}`">
                            <span class="app-icon">{{ app.icon }}</span>
                        </div>
                        <span class="app-name">{{ app.name }}</span>
                    </component>
                </div>
            </div>

            <!-- Map Card -->
            <div class="map-card" :class="{ 'show': mounted }">
                <div class="map-badge">
                    <span class="badge-dot"></span>
                    <span class="badge-text">Live Map</span>
                </div>
                <div class="map-wrap">
                    <DashboardMap />
                </div>
            </div>

            <!-- Emergency Button -->
            <Link :href="route('emergency.index')" class="emergency-fab">
                <div class="fab-button">
                    <span class="fab-icon">🚨</span>
                </div>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT -->
        <div class="desktop-layout">
            <!-- Desktop Logo Header -->
            <div class="desktop-logo-header" :class="{ 'show': mounted }">
                <div class="desktop-logo-container">
                    <div class="desktop-logo-icon">🏛️</div>
                    <div class="desktop-logo-text">
                        <h1 class="desktop-logo-title">CityConnect</h1>
                        <p class="desktop-logo-subtitle">Smart City Management Portal</p>
                    </div>
                </div>
                <Link :href="route('emergency.index')" class="desktop-notification-button">
                    <svg class="desktop-notification-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span v-if="notificationCount > 0" class="desktop-notification-badge">{{ notificationCount }}</span>
                </Link>
            </div>

            <header class="desktop-header" :class="{ 'show': mounted }">
                <div class="header-content">
                    <div class="greeting-wrap">
                        <h1 class="greeting-text">
                            {{ greeting }}, 
                            <span class="greeting-name">{{ user.name.split(' ')[0] }}</span>
                        </h1>
                        <p class="greeting-subtitle">{{ date }}</p>
                    </div>
                    <div class="clock-display">
                        <div class="clock-time">
                            {{ time }}
                            <span class="clock-period">{{ dayPeriod }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="desktop-grid">
                <div class="desktop-main">
                    <!-- Core Features Only (6) -->
                    <div class="features-grid">
                        <component :is="feature.link ? Link : 'button'"
                            v-for="(feature, i) in desktopFeatures" :key="feature.title"
                            :href="feature.link" @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="['feature-card', `bg-gradient-to-br ${feature.gradient}`,
                                { 'feature-large': feature.size === 'large',
                                  'feature-medium': feature.size === 'medium',
                                  'show': mounted }]"
                            :style="`animation-delay: ${i * 60}ms`">
                            <Link v-if="feature.historyLink" :href="feature.historyLink" @click.stop class="feature-history">
                                <span>History</span>
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
                    <!-- Emergency Card -->
                    <Link :href="route('emergency.index')" class="sidebar-card emergency-card"
                          :class="{ 'show': mounted }" style="animation-delay: 200ms">
                        <div class="emergency-content">
                            <div class="emergency-icon">📞</div>
                            <div class="emergency-text">
                                <h3 class="emergency-title">Emergency</h3>
                                <p class="emergency-subtitle">Command Center</p>
                            </div>
                        </div>
                    </Link>
                    
                    <!-- Stats Card -->
                    <div class="sidebar-card stats-card" :class="{ 'show': mounted }">
                        <h3 class="card-title">Quick Stats</h3>
                        <div class="stats-list">
                            <div v-for="stat in quickStats" :key="stat.label" class="stat-item">
                                <div :class="`stat-item-icon bg-gradient-to-br ${stat.color}`">
                                    {{ stat.icon }}
                                </div>
                                <div class="stat-item-info">
                                    <p class="stat-item-value">{{ stat.value }}</p>
                                    <p class="stat-item-label">{{ stat.label }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Card -->
                    <div class="sidebar-map" :class="{ 'show': mounted }" style="animation-delay: 100ms">
                        <div class="map-badge-desk">📍 Live Map</div>
                        <div class="map-wrapper">
                            <DashboardMap />
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <IssueWizard v-if="showWizard" @close="showWizard = false" />
        <ChatAssistant />
    </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

* { 
    font-family: 'Inter', -apple-system, sans-serif; 
    -webkit-font-smoothing: antialiased;
}

/* Toast */
.toast {
    @apply fixed top-20 right-4 z-[60] w-[90vw] sm:w-96 
           rounded-xl shadow-2xl flex items-center gap-3 p-3.5;
    background: rgba(16, 185, 129, 0.95);
    backdrop-filter: blur(10px);
}

.toast-icon {
    @apply w-6 h-6 text-white flex-shrink-0;
}

.toast-text {
    @apply text-sm font-semibold text-white flex-1;
}

.toast-close {
    @apply w-6 h-6 text-white text-2xl leading-none hover:opacity-70;
}

/* Mobile Layout */
.mobile-layout {
    @apply lg:hidden w-full max-w-md mx-auto px-4 pb-8 pt-4;
}

.desktop-layout {
    @apply hidden lg:block w-full max-w-7xl mx-auto px-4 pb-20 pt-6;
}

/* Mobile Logo Header */
.logo-header {
    @apply mb-4 opacity-0 transition-all duration-500;
}

.logo-header.show {
    @apply opacity-100;
}

.logo-container {
    @apply flex items-center justify-center gap-3 py-2;
}

.logo-icon {
    @apply w-12 h-12 rounded-2xl flex items-center justify-center;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
    border: 2px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.logo-emoji {
    @apply text-2xl;
}

.logo-text {
    @apply text-center;
}

.logo-title {
    @apply text-xl font-bold text-white;
    background: linear-gradient(135deg, #3b82f6, #9333ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.logo-subtitle {
    @apply text-[10px] text-gray-400 font-medium;
}

/* Time Widget with Notification */
.time-widget {
    @apply mb-5 opacity-0 transition-all duration-300;
}

.time-widget.show {
    @apply opacity-100;
}

.time-content {
    @apply rounded-2xl p-4 border border-white/10 flex items-center justify-between gap-4;
    background: rgba(28, 28, 30, 0.8);
    backdrop-filter: blur(10px);
}

.time-info {
    @apply flex-1;
}

.time-label {
    @apply text-gray-400 text-xs font-medium mb-1;
}

.time-display {
    @apply flex items-baseline gap-2;
}

.time-value {
    @apply text-3xl font-bold text-white;
}

.time-period {
    @apply text-base font-semibold text-gray-400;
}

/* Enhanced Notification Button */
.notification-button {
    @apply flex-shrink-0 relative;
}

.notification-icon-wrap {
    @apply relative w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-200;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.2));
    border: 2px solid rgba(251, 191, 36, 0.3);
}

.notification-button:active .notification-icon-wrap {
    transform: scale(0.95);
}

.notification-icon {
    @apply w-6 h-6 text-yellow-400;
}

.notification-badge {
    @apply absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white border-2 border-[#1c1c1e];
    background: linear-gradient(135deg, #ef4444, #dc2626);
    animation: pulse-badge 2s ease-in-out infinite;
}

@keyframes pulse-badge {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
    }
}

/* Stats Grid */
.stats-grid {
    @apply mb-5 grid grid-cols-3 gap-2.5;
}

.stat-card {
    @apply rounded-xl p-4 border border-white/10 opacity-0 transition-all duration-300;
    background: rgba(28, 28, 30, 0.8);
    backdrop-filter: blur(10px);
}

.stat-card.show {
    @apply opacity-100;
}

.stat-icon-bg {
    @apply w-10 h-10 rounded-lg flex items-center justify-center mb-2;
}

.stat-icon {
    @apply text-xl;
}

.stat-value {
    @apply text-2xl font-bold text-white mb-0.5;
}

.stat-label {
    @apply text-[10px] text-gray-400 font-semibold uppercase;
}

/* Search Bar */
.search-wrapper {
    @apply mb-5 opacity-0 transition-all duration-300;
}

.search-wrapper.show {
    @apply opacity-100;
}

.search-glass {
    @apply bg-[#1c1c1e] rounded-2xl border border-white/10 flex items-center px-4 py-3;
    backdrop-filter: blur(10px);
}

.search-icon {
    @apply w-5 h-5 text-gray-400 mr-3;
}

.search-input {
    @apply flex-1 bg-transparent text-white placeholder-gray-500 text-sm focus:outline-none;
}

/* Apps Section */
.apps-section {
    @apply mb-5;
}

.apps-header {
    @apply flex items-center justify-between mb-3 px-1 opacity-0 transition-all duration-300;
}

.apps-header.show {
    @apply opacity-100;
}

.apps-title {
    @apply text-base font-bold text-white;
}

.apps-grid {
    @apply grid grid-cols-3 gap-3;
}

.app-card {
    @apply flex flex-col items-center gap-2 rounded-2xl p-2.5 opacity-0 transition-all duration-300;
}

.app-card.show {
    @apply opacity-100;
}

.app-card:active {
    transform: scale(0.95);
}

.app-icon-wrap {
    @apply w-full aspect-square rounded-xl flex items-center justify-center;
}

.app-icon {
    @apply text-2xl;
}

.app-name {
    @apply text-[11px] font-semibold text-gray-300 text-center;
}

/* Map Card */
.map-card {
    @apply relative overflow-hidden rounded-2xl border border-white/10 mb-5 opacity-0 transition-all duration-300;
    background: rgba(28, 28, 30, 0.8);
    backdrop-filter: blur(10px);
}

.map-card.show {
    @apply opacity-100;
}

.map-badge {
    @apply absolute top-3 left-3 z-10 flex items-center gap-2 px-3 py-1.5 rounded-full border border-white/10;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
}

.badge-dot {
    @apply w-2 h-2 bg-green-400 rounded-full;
    animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.badge-text {
    @apply text-xs font-bold text-white;
}

.map-wrap {
    @apply w-full h-48;
}

/* Emergency FAB */
.emergency-fab {
    @apply fixed bottom-24 right-5 z-40;
}

.fab-button {
    @apply w-14 h-14 rounded-full flex items-center justify-center border-2 border-black/30 transition-transform duration-200;
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(220, 38, 38));
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.5);
}

.emergency-fab:active .fab-button {
    transform: scale(0.95);
}

.fab-icon {
    @apply text-2xl;
}

/* Desktop Logo Header */
.desktop-logo-header {
    @apply flex items-center justify-between mb-6 opacity-0 transition-all duration-500;
}

.desktop-logo-header.show {
    @apply opacity-100;
}

.desktop-logo-container {
    @apply flex items-center gap-4;
}

.desktop-logo-icon {
    @apply w-16 h-16 text-3xl rounded-2xl flex items-center justify-center;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(147, 51, 234, 0.2));
    border: 2px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.desktop-logo-text {
    @apply space-y-1;
}

.desktop-logo-title {
    @apply text-2xl font-bold;
    background: linear-gradient(135deg, #3b82f6, #9333ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.desktop-logo-subtitle {
    @apply text-sm text-gray-400 font-medium;
}

/* Desktop Notification Button */
.desktop-notification-button {
    @apply relative flex items-center justify-center w-14 h-14 rounded-xl transition-all duration-200;
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.2));
    border: 2px solid rgba(251, 191, 36, 0.3);
}

.desktop-notification-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(251, 191, 36, 0.3);
}

.desktop-notification-icon {
    @apply w-6 h-6 text-yellow-400;
}

.desktop-notification-badge {
    @apply absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white border-2 border-[#0f172a];
    background: linear-gradient(135deg, #ef4444, #dc2626);
    animation: pulse-badge 2s ease-in-out infinite;
}

/* Desktop Layout */
.desktop-header {
    @apply mb-6 opacity-0 transition-all duration-300;
}

.desktop-header.show {
    @apply opacity-100;
}

.header-content {
    @apply flex justify-between items-end gap-4;
}

.greeting-text {
    @apply text-4xl font-bold text-white;
}

.greeting-name {
    @apply text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400;
}

.greeting-subtitle {
    @apply text-sm text-gray-400 mt-1;
}

.clock-display {
    @apply text-right;
}

.clock-time {
    @apply text-5xl font-light text-white flex items-baseline gap-2;
}

.clock-period {
    @apply text-xl font-medium text-gray-400;
}

.desktop-grid {
    @apply grid grid-cols-1 lg:grid-cols-12 gap-6;
}

.desktop-main {
    @apply lg:col-span-8;
}

.features-grid {
    @apply grid grid-cols-2 lg:grid-cols-3 gap-4;
}

.feature-card {
    @apply relative flex flex-col justify-between p-5 rounded-2xl border border-white/10 opacity-0 transition-all duration-300;
    backdrop-filter: blur(10px);
}

.feature-card.show {
    @apply opacity-100;
}

.feature-card:hover {
    transform: translateY(-2px);
}

.feature-large {
    @apply col-span-2 h-36;
}

.feature-medium {
    @apply h-32;
}

.feature-history {
    @apply absolute top-3 right-3 z-10 flex items-center gap-1 px-2 py-1 rounded-full border border-white/15 text-[10px] font-bold text-white;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
}

.feature-content {
    @apply flex flex-col justify-between h-full;
}

.feature-icon-wrap {
    @apply w-10 h-10 rounded-lg flex items-center justify-center;
    background: rgba(255, 255, 255, 0.15);
}

.feature-icon {
    @apply text-2xl;
}

.feature-text {
    @apply space-y-1 mt-auto;
}

.feature-subtitle {
    @apply text-white/60 text-xs font-semibold;
}

.feature-title {
    @apply font-bold text-white text-lg;
}

/* Desktop Sidebar */
.desktop-sidebar {
    @apply lg:col-span-4 space-y-4;
}

.sidebar-card {
    @apply rounded-2xl p-5 border border-white/10 opacity-0 transition-all duration-300;
    background: rgba(28, 28, 30, 0.8);
    backdrop-filter: blur(10px);
}

.sidebar-card.show {
    @apply opacity-100;
}

.card-title {
    @apply text-lg font-bold text-white mb-3;
}

.stats-list {
    @apply space-y-3;
}

.stat-item {
    @apply flex items-center gap-3;
}

.stat-item-icon {
    @apply w-10 h-10 rounded-lg flex items-center justify-center text-lg;
}

.stat-item-value {
    @apply text-xl font-bold text-white;
}

.stat-item-label {
    @apply text-xs text-gray-400;
}

.sidebar-map {
    @apply relative rounded-2xl p-1 h-64 border border-white/10 opacity-0 transition-all duration-300;
    background: rgba(28, 28, 30, 0.8);
    backdrop-filter: blur(10px);
}

.sidebar-map.show {
    @apply opacity-100;
}

.map-badge-desk {
    @apply absolute top-3 left-3 z-10 px-3 py-1 rounded-full text-xs font-bold text-white border border-white/15;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
}

.map-wrapper {
    @apply w-full h-full rounded-xl overflow-hidden;
}

.emergency-card {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.9), rgba(185, 28, 28, 0.9));
}

.emergency-content {
    @apply flex items-center gap-4;
}

.emergency-icon {
    @apply text-4xl;
}

.emergency-title {
    @apply text-xl font-bold text-white;
}

.emergency-subtitle {
    @apply text-sm text-white/80;
}

/* Transitions */
.toast-enter-active {
    animation: toast-in 0.3s ease;
}

.toast-leave-active {
    transition: all 0.2s ease;
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

@keyframes toast-in {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
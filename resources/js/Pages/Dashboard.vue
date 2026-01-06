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
const currentTime = ref(new Date());

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
const notificationCount = ref();

// Particle effect state
const particles = ref([]);

// Time-based theme color
const themeColor = computed(() => {
    const hour = currentTime.value.getHours();
    if (hour >= 5 && hour < 12) return { from: '#3b82f6', to: '#60a5fa' }; // Morning blue
    if (hour >= 12 && hour < 17) return { from: '#f59e0b', to: '#fbbf24' }; // Afternoon amber
    if (hour >= 17 && hour < 21) return { from: '#8b5cf6', to: '#a78bfa' }; // Evening purple
    return { from: '#6366f1', to: '#818cf8' }; // Night indigo
});

// Simplified update with animations
const updateTime = () => {
    const now = new Date();
    currentTime.value = now;
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

// Stats with dynamic loading
const quickStats = ref([
    { icon: '📄', label: 'Active', value: '...', color: 'from-blue-500 to-blue-600', loaded: false },
    { icon: '✅', label: 'Done', value: '...', color: 'from-green-500 to-green-600', loaded: false },
    { icon: '📊', label: 'Total', value: '...', color: 'from-purple-500 to-purple-600', loaded: false }
]);

// Simulate loading stats
const loadStats = async () => {
    await new Promise(resolve => setTimeout(resolve, 500));
    quickStats.value[0].value = '12';
    quickStats.value[0].loaded = true;
    
    await new Promise(resolve => setTimeout(resolve, 200));
    quickStats.value[1].value = '48';
    quickStats.value[1].loaded = true;
    
    await new Promise(resolve => setTimeout(resolve, 200));
    quickStats.value[2].value = '60';
    quickStats.value[2].loaded = true;
};

// Core mobile apps
const mobileApps = [
    { icon: '⚡', name: 'Services', link: route('services.landing'), gradient: 'from-blue-500 to-indigo-600', description: 'Quick access' },
    { icon: '💳', name: 'Bills', link: route('bills.create'), gradient: 'from-green-500 to-emerald-600', description: 'Pay bills' },
    { icon: '🩺', name: 'Health', link: route('health.index'), gradient: 'from-red-500 to-rose-600', description: 'Medical care' },
    { icon: '🏪', name: 'Market', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', description: 'Shop local' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: 'from-orange-500 to-amber-600', description: 'Manage' },
    { icon: '🚨', name: 'Report', link: null, action: 'wizard', gradient: 'from-red-600 to-pink-700', description: 'Get help' },
];

// Core desktop features
const desktopFeatures = [
    { title: 'Smart Services', subtitle: 'Permits & Clearances', icon: '⚡', link: route('services.landing'), historyLink: route('services.index'), gradient: 'from-blue-500 to-indigo-600', size: 'large', description: 'Fast digital processing' },
    { title: 'Bills & Fees', subtitle: 'Secure Payments', icon: '💳', link: route('bills.create'), gradient: 'from-teal-500 to-emerald-600', size: 'medium', description: 'Pay online instantly' },
    { title: 'E-Health', subtitle: 'Telemed & Booking', icon: '🩺', link: route('health.index'), gradient: 'from-sky-500 to-cyan-600', size: 'medium', description: 'Healthcare at home' },
    { title: 'Issue Report', subtitle: 'Quick Response', icon: '🚨', link: null, action: 'wizard', gradient: 'from-red-500 to-pink-600', size: 'medium', description: 'Report problems' },
    { title: 'Marketplace', subtitle: 'Local Businesses', icon: '🌐', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', size: 'large', description: 'Support local economy' },
    { title: isBusinessOwner.value ? 'My Business' : 'Business Hub', subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', icon: isBusinessOwner.value ? '📊' : '🏪', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600', size: 'medium', description: isBusinessOwner.value ? 'Manage business' : 'Start your journey' },
];

// Generate particles
const generateParticles = () => {
    particles.value = Array.from({ length: 20 }, (_, i) => ({
        id: i,
        x: Math.random() * 100,
        y: Math.random() * 100,
        size: Math.random() * 4 + 2,
        duration: Math.random() * 10 + 10,
        delay: Math.random() * 5,
    }));
};

let clockTimer;

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);
    generateParticles();
    
    if (flashSuccess.value) { 
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 4000);
    }
    
    // Staggered entrance animations
    setTimeout(() => mounted.value = true, 100);
    
    // Load stats with animation
    loadStats();
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
        
        <!-- Floating Particles Background -->
        <div class="particles-container">
            <div 
                v-for="particle in particles" 
                :key="particle.id" 
                class="particle"
                :style="{
                    left: particle.x + '%',
                    top: particle.y + '%',
                    width: particle.size + 'px',
                    height: particle.size + 'px',
                    animationDuration: particle.duration + 's',
                    animationDelay: particle.delay + 's',
                }"
            ></div>
        </div>

        <!-- Enhanced Toast -->
        <Transition name="toast">
            <div v-if="showFlash && flashSuccess" class="toast">
                <div class="toast-icon-wrap">
                    <svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="toast-content">
                    <p class="toast-title">Success!</p>
                    <p class="toast-text">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="toast-close">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </Transition>

        <!-- MOBILE LAYOUT -->
        <div class="mobile-layout">
            <!-- Enhanced Logo Header -->
            <div class="logo-header" :class="{ 'show': mounted }">
                <div class="logo-container">
                    <div class="logo-icon" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                        <span class="logo-emoji">🏛️</span>
                        <div class="logo-glow"></div>
                    </div>
                    <div class="logo-text">
                        <h1 class="logo-title">CityConnect</h1>
                        <p class="logo-subtitle">Smart City Portal</p>
                    </div>
                </div>
            </div>

            <!-- Enhanced Time Widget -->
            <div class="time-widget" :class="{ 'show': mounted }">
                <div class="time-bg-orb" :style="{ background: `radial-gradient(circle, ${themeColor.from}40, transparent)` }"></div>
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
                        <div class="notification-icon-wraps" :style="{ background: `linear-gradient(135deg, ${themeColor.from}40, ${themeColor.to}40)` }">
                            <h1 class="size">📬</h1>
                            <span v-if="notificationCount > 0" class="notification-badge">
                                {{ notificationCount }}
                            </span>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Enhanced Stats Grid -->
            <div class="stats-grid">
                <div v-for="(stat, i) in quickStats" :key="stat.label"
                    class="stat-card" :class="{ 'show': mounted, 'loaded': stat.loaded }"
                    :style="`animation-delay: ${i * 100}ms`">
                    <div class="stat-background"></div>
                    <div :class="`stat-icon-bg bg-gradient-to-br ${stat.color}`">
                        <span class="stat-icon">{{ stat.icon }}</span>
                        <div class="stat-icon-glow"></div>
                    </div>
                    <div class="stat-info">
                        <p class="stat-value">{{ stat.value }}</p>
                        <p class="stat-label">{{ stat.label }}</p>
                    </div>
                    <div class="stat-shimmer"></div>
                </div>
            </div>

            <!-- Enhanced Search Bar -->
            <div class="search-wrapper" :class="{ 'show': mounted }">
                <div class="search-glass">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search services, bills, documents..." class="search-input">
                    <button class="search-button" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                        <span>⚡</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Apps Section -->
            <div class="apps-section">
                <div class="apps-header" :class="{ 'show': mounted }">
                    <div>
                        <h3 class="apps-title">Quick Access</h3>
                        <p class="apps-subtitle">Your most used services</p>
                    </div>
                </div>
                <div class="apps-grid">
                    <component :is="app.link ? Link : 'button'"
                        v-for="(app, i) in mobileApps" :key="app.name"
                        :href="app.link" @click="app.action ? handleAppClick(app) : null"
                        class="app-card" :class="{ 'show': mounted }"
                        :style="`animation-delay: ${i * 60}ms`">
                        <div :class="`app-icon-wrap bg-gradient-to-br ${app.gradient}`">
                            <span class="app-icon">{{ app.icon }}</span>
                            <div class="app-glow"></div>
                        </div>
                        <div class="app-info">
                            <span class="app-name">{{ app.name }}</span>
                            <span class="app-description">{{ app.description }}</span>
                        </div>
                        <div class="app-shimmer"></div>
                    </component>
                </div>
            </div>

            <!-- Enhanced Map Card -->
            <div class="map-card" :class="{ 'show': mounted }">
                <div class="map-header">
                    <div class="map-badge" :style="{ background: `linear-gradient(135deg, ${themeColor.from}60, ${themeColor.to}60)` }">
                        <span class="badge-dot"></span>
                        <span class="badge-text">Live Map</span>
                    </div>
                    <Link :href="route('establishments.index')" class="map-expand">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </Link>
                </div>
                <div class="map-wrap">
                    <DashboardMap />
                </div>
            </div>

            <!-- Enhanced Emergency Button -->
            <Link :href="route('emergency.index')" class="emergency-fab">
                <div class="fab-button">
                    <span class="fab-icon">🚨</span>
                    <div class="fab-ring"></div>
                    <div class="fab-glow"></div>
                </div>
                <span class="fab-label">Emergency</span>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT -->
        <div class="desktop-layout">
            <!-- Desktop Logo Header -->
            <div class="desktop-logo-header" :class="{ 'show': mounted }">
                <div class="desktop-logo-container">
                    <div class="desktop-logo-icon" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                        🏛️
                        <div class="desktop-logo-glow"></div>
                    </div>
                    <div class="desktop-logo-text">
                        <h1 class="desktop-logo-title">CityConnect</h1>
                        <p class="desktop-logo-subtitle">Smart City Management Portal</p>
                    </div>
                </div>
                <Link :href="route('emergency.index')" class="emergency-button-desktop">
                    <span class="emergency-icon">🚨</span>
                    <span class="emergency-text">Emergency</span>
                    <div class="emergency-pulse"></div>
                </Link>
            </div>
            
            <!-- Enhanced Desktop Header -->
            <header class="desktop-header" :class="{ 'show': mounted }">
                <div class="header-content">
                    <div class="greeting-wrap">
                        <h1 class="greeting-text">
                            {{ greeting }}, {{ user.name }}!
                        </h1>
                        <p class="greeting-subtitle">{{ date }}</p>
                    </div>
                    <div class="clock-display">
                        <div class="clock-time">
                            <span class="clock-digits">{{ time }}</span>
                            <span class="clock-period">{{ dayPeriod }}</span>
                        </div>
                        <div class="clock-decoration" :style="{ background: `linear-gradient(135deg, ${themeColor.from}40, ${themeColor.to}40)` }"></div>
                    </div>
                </div>
            </header>

            <div class="desktop-grid">
                <div class="desktop-main">
                    <!-- Enhanced Features Grid -->
                    <div class="features-grid">
                        <component :is="feature.link ? Link : 'button'"
                            v-for="(feature, i) in desktopFeatures" :key="feature.title"
                            :href="feature.link" @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="['feature-card', `bg-gradient-to-br ${feature.gradient}`,
                                { 'feature-large': feature.size === 'large',
                                  'feature-medium': feature.size === 'medium',
                                  'show': mounted }]"
                            :style="`animation-delay: ${i * 80}ms`">
                            <div class="feature-overlay"></div>
                            <Link v-if="feature.historyLink" :href="feature.historyLink" @click.stop class="feature-history">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>History</span>
                            </Link>
                            <div class="feature-content">
                                <div class="feature-header">
                                    <div class="feature-icon-wrap">
                                        <span class="feature-icon">{{ feature.icon }}</span>
                                        <div class="feature-icon-glow"></div>
                                    </div>
                                    <div class="feature-text">
                                        <p class="feature-subtitle">{{ feature.subtitle }}</p>
                                        <h3 class="feature-title">{{ feature.title }}</h3>
                                    </div>
                                </div>
                                <p class="feature-description">{{ feature.description }}</p>
                            </div>
                            <div class="feature-shine"></div>
                        </component>
                    </div>
                </div>

                <aside class="desktop-sidebar">
                    <!-- Stats Card -->
                    <div class="sidebar-card stats-card" :class="{ 'show': mounted }">
                        <div class="card-header">
                            <h3 class="card-title">Quick Stats</h3>
                            <div class="card-icon" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                                📊
                            </div>
                        </div>
                        <div class="stats-list">
                            <div v-for="stat in quickStats" :key="stat.label" class="stat-item" :class="{ 'loaded': stat.loaded }">
                                <div :class="`stat-item-icon bg-gradient-to-br ${stat.color}`">
                                    {{ stat.icon }}
                                    <div class="stat-item-glow"></div>
                                </div>
                                <div class="stat-item-info">
                                    <p class="stat-item-value">{{ stat.value }}</p>
                                    <p class="stat-item-label">{{ stat.label }}</p>
                                </div>
                                <div class="stat-item-trend">↗</div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Map Card -->
                    <div class="sidebar-map" :class="{ 'show': mounted }" style="animation-delay: 150ms">
                        <div class="map-header">
                            <div class="map-badge-desk" :style="{ background: `linear-gradient(135deg, ${themeColor.from}60, ${themeColor.to}60)` }">
                                <span class="badge-dot"></span>
                                📍 Live Map
                            </div>
                            <Link :href="route('establishments.index')" class="map-expand-desk">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                            </Link>
                        </div>
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

* { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Floating Particles */
.particles-container {
    position: fixed;
    inset: 0;
    z-index: 2;
    pointer-events: none;
    overflow: hidden;
}

.particle {
    position: absolute;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.8), transparent);
    border-radius: 50%;
    animation: float-particle ease-in-out infinite;
}

@keyframes float-particle {
    0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100vh) translateX(50px);
        opacity: 0;
    }
}

/* Enhanced Toast */
.toast {
    position: fixed;
    top: 5.5rem;
    right: 1rem;
    z-index: 60;
    width: calc(100vw - 2rem);
    max-width: 24rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 1rem;
    background: rgba(16, 185, 129, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

@media (min-width: 1024px) {
    .toast {
        top: 6rem;
        right: 2rem;
    }
}

.toast-icon-wrap {
    width: 2.5rem;
    height: 2.5rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
}

.toast-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: white;
}

.toast-content {
    flex: 1;
    min-width: 0;
}

.toast-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.125rem;
}

.toast-text {
    font-size: 0.75rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
}

.toast-close {
    width: 2rem;
    height: 2rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: white;
    transition: background 0.2s;
}

.toast-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.toast-close svg {
    width: 1.25rem;
    height: 1.25rem;
}

/* Mobile Layout */
.mobile-layout {
    display: block;
    width: 100%;
    max-width: 28rem;
    margin: 0 auto;
    padding: 0 1rem 8rem;
}

@media (min-width: 1024px) {
    .mobile-layout {
        display: none;
    }
}

.desktop-layout {
    display: none;
}

@media (min-width: 1024px) {
    .desktop-layout {
        display: block;
        width: 100%;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 1.5rem 4rem;
    }
}

/* Enhanced Mobile Logo Header */
.logo-header {
    margin-bottom: 1rem;
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.logo-header.show {
    opacity: 1;
    transform: translateY(0);
}

.logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 0.75rem;
}

.logo-icon {
    position: relative;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.logo-glow {
    position: absolute;
    inset: -4px;
    border-radius: 1.25rem;
    background: inherit;
    filter: blur(12px);
    opacity: 0.6;
    z-index: -1;
}

.logo-emoji {
    font-size: 1.75rem;
    position: relative;
    z-index: 1;
}

.logo-text {
    text-align: center;
}

.logo-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.025em;
}

.logo-subtitle {
    font-size: 0.6875rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Enhanced Time Widget */
.time-widget {
    position: relative;
    margin-bottom: 1.25rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.1s;
}

.time-widget.show {
    opacity: 1;
    transform: translateY(0);
}

.time-bg-orb {
    position: absolute;
    inset: -2rem;
    filter: blur(60px);
    opacity: 0.3;
    animation: orb-pulse 4s ease-in-out infinite;
}

@keyframes orb-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.5;
    }
}

.time-content {
    position: relative;
    border-radius: 1.5rem;
    padding: 1.25rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
}

.time-info {
    flex: 1;
}

.time-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.time-display {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.time-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    line-height: 1;
    letter-spacing: -0.02em;
}

.time-period {
    font-size: 1rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
}

/* Enhanced Notification Button */
.notification-button {
    flex-shrink: 0;
}

.notification-icon-wrap {
    position: relative;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.size{
    size:large;
}
.notification-icon-wraps {
    position: relative;
    width: 4rem;
    height: 3.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: yellow;
    background-color: yellow;
    border: 2px solid rgba(231, 231, 227, 0.37);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-button:active .notification-icon-wrap {
    transform: scale(0.95);
}

.notification-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: white;
    position: relative;
    z-index: 1;
}

.notification-badge {
    position: absolute;
    top: -0.375rem;
    right: -0.375rem;
    min-width: 1.25rem;
    height: 1.25rem;
    padding: 0 0.375rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.625rem;
    font-weight: 800;
    color: white;
    border: 2px solid #1c1c1e;
    animation: badge-pulse 2s ease-in-out infinite;
}

@keyframes badge-pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0);
    }
}

/* Enhanced Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.stat-card {
    position: relative;
    border-radius: 1.25rem;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.stat-card.show {
    opacity: 1;
    transform: translateY(0);
}

.stat-background {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.05), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover .stat-background {
    opacity: 1;
}

.stat-icon-bg {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.stat-icon-glow {
    position: absolute;
    inset: -2px;
    border-radius: 0.75rem;
    background: inherit;
    filter: blur(8px);
    opacity: 0.5;
    z-index: -1;
}

.stat-icon {
    font-size: 1.25rem;
    position: relative;
    z-index: 1;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.25rem;
    line-height: 1;
    transition: all 0.3s ease;
}

.stat-card.loaded .stat-value {
    animation: count-up 0.6s ease-out;
}

@keyframes count-up {
    from {
        transform: translateY(10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.stat-label {
    font-size: 0.625rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-shimmer {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    to {
        left: 100%;
    }
}

/* Enhanced Search Bar */
.search-wrapper {
    margin-bottom: 1.5rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.3s;
}

.search-wrapper.show {
    opacity: 1;
    transform: translateY(0);
}

.search-glass {
    position: relative;
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
    border-radius: 1.25rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    padding: 0 0.5rem 0 1rem;
    gap: 0.75rem;
    transition: all 0.3s ease;
}

.search-glass:focus-within {
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.05);
}

.search-icon {
    width: 1.25rem;
    height: 1.25rem;
    color: rgba(255, 255, 255, 0.5);
    flex-shrink: 0;
}

.search-input {
    flex: 1;
    background: transparent;
    color: white;
    placeholder:text-gray-500;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 1rem 0;
    outline: none;
    border: none;
}

.search-button {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    transition: all 0.2s;
}

.search-button:active {
    transform: scale(0.95);
}

/* Enhanced Apps Section */
.apps-section {
    margin-bottom: 1.5rem;
}

.apps-header {
    margin-bottom: 1rem;
    padding: 0 0.25rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.4s;
}

.apps-header.show {
    opacity: 1;
    transform: translateY(0);
}

.apps-title {
    font-size: 1.125rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.25rem;
}

.apps-subtitle {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 600;
}

.apps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}

.app-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 0.5rem;
    border-radius: 1.25rem;
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    opacity: 0;
    transform: translateY(20px) scale(0.95);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.app-card.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.app-card:active {
    transform: scale(0.95);
}

.app-icon-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.25rem;
}

.app-glow {
    position: absolute;
    inset: -4px;
    border-radius: 1rem;
    background: inherit;
    filter: blur(12px);
    opacity: 0.6;
    z-index: -1;
}

.app-icon {
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.app-info {
    text-align: center;
    width: 100%;
}

.app-name {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.125rem;
}

.app-description {
    display: block;
    font-size: 0.625rem;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 600;
}

.app-shimmer {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    opacity: 0;
    animation: shimmer 2s ease-in-out infinite;
}

/* Enhanced Map Card */
.map-card {
    position: relative;
    overflow: hidden;
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 1.5rem;
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.5s;
}

.map-card.show {
    opacity: 1;
    transform: translateY(0);
}

.map-header {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    right: 0.75rem;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.map-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(20px);
}

.badge-dot {
    width: 0.5rem;
    height: 0.5rem;
    background: #10b981;
    border-radius: 50%;
    animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% {
        opacity: 1;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
        opacity: 0.5;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0);
    }
}

.badge-text {
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
}

.map-expand {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.2s;
}

.map-expand:active {
    transform: scale(0.95);
}

.map-expand svg {
    width: 1rem;
    height: 1rem;
}

.map-wrap {
    width: 100%;
    height: 14rem;
}

/* Enhanced Emergency FAB */
.emergency-fab {
    position: fixed;
    bottom: 6rem;
    right: 1rem;
    z-index: 40;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.fab-button {
    position: relative;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(220, 38, 38));
    border: 2px solid rgba(0, 0, 0, 0.3);
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.emergency-fab:active .fab-button {
    transform: scale(0.95);
}

.fab-ring {
    position: absolute;
    inset: -4px;
    border-radius: 9999px;
    border: 2px solid rgb(239, 68, 68);
    animation: fab-ring 2s ease-in-out infinite;
}

@keyframes fab-ring {
    0% {
        transform: scale(1);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

.fab-glow {
    position: absolute;
    inset: -8px;
    border-radius: 9999px;
    background: radial-gradient(circle, rgb(239, 68, 68), transparent);
    filter: blur(16px);
    opacity: 0.6;
    z-index: -1;
}

.fab-icon {
    font-size: 1.5rem;
    position: relative;
    z-index: 1;
    animation: pulse 2s ease-in-out infinite;
}

.fab-label {
    font-size: 0.625rem;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

/* Desktop Layout Enhancements */
.desktop-logo-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.desktop-logo-header.show {
    opacity: 1;
    transform: translateY(0);
}

.desktop-logo-container {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.desktop-logo-icon {
    position: relative;
    width: 4rem;
    height: 4rem;
    font-size: 2rem;
    border-radius: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.desktop-logo-glow {
    position: absolute;
    inset: -6px;
    border-radius: 1.5rem;
    background: inherit;
    filter: blur(16px);
    opacity: 0.6;
    z-index: -1;
    animation: logo-glow 3s ease-in-out infinite;
}

@keyframes logo-glow {
    0%, 100% {
        transform: scale(1);
        opacity: 0.6;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
}

.desktop-logo-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.desktop-logo-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: white;
    letter-spacing: -0.025em;
}

.desktop-logo-subtitle {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 600;
}

.emergency-button-desktop {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(220, 38, 38));
    border: 2px solid rgba(0, 0, 0, 0.2);
    box-shadow: 0 10px 30px rgba(239, 68, 68, 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.emergency-button-desktop:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(239, 68, 68, 0.5);
}

.emergency-icon {
    font-size: 1.5rem;
    position: relative;
    z-index: 1;
    animation: pulse 2s ease-in-out infinite;
}

.emergency-text {
    font-size: 0.875rem;
    font-weight: 700;
    color: white;
    position: relative;
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.emergency-pulse {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
    animation: emergency-pulse 2s ease-in-out infinite;
}

@keyframes emergency-pulse {
    0%, 100% {
        transform: scale(0.8);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
}

/* Enhanced Desktop Header */
.desktop-header {
    margin-bottom: 2rem;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.1s;
}

.desktop-header.show {
    opacity: 1;
    transform: translateY(0);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 2rem;
}

.greeting-text {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.greeting-name {
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.greeting-subtitle {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.6);
    margin-top: 0.5rem;
    font-weight: 600;
}

.clock-display {
    position: relative;
    text-align: right;
}

.clock-decoration {
    position: absolute;
    inset: -1rem;
    border-radius: 1.5rem;
    filter: blur(40px);
    opacity: 0.3;
    z-index: -1;
}

.clock-time {
    display: flex;
    align-items: baseline;
    gap: 0.75rem;
    justify-content: flex-end;
}

.clock-digits {
    font-size: 4rem;
    font-weight: 200;
    color: white;
    line-height: 1;
    letter-spacing: -0.02em;
}

.clock-period {
    font-size: 1.5rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.5);
}

/* Desktop Grid */
.desktop-grid {
    display: grid;
    grid-template-columns: 1fr 22rem;
    gap: 2rem;
}

.desktop-main {
    min-width: 0;
}

/* Enhanced Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.feature-card {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1.5rem;
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    cursor: pointer;
}

.feature-card.show {
    opacity: 1;
    transform: translateY(0);
}

.feature-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.2);
}

.feature-large {
    grid-column: span 2;
    min-height: 10rem;
}

.feature-medium {
    min-height: 9rem;
}

.feature-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.feature-card:hover .feature-overlay {
    opacity: 1;
}

.feature-history {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 0.75rem;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    transition: all 0.2s;
}

.feature-history:hover {
    background: rgba(0, 0, 0, 0.5);
    transform: scale(1.05);
}

.feature-history svg {
    width: 0.875rem;
    height: 0.875rem;
}

.feature-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    height: 100%;
}

.feature-header {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.feature-icon-wrap {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.15);
}

.feature-icon-glow {
    position: absolute;
    inset: -4px;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.2);
    filter: blur(12px);
    opacity: 0.5;
    z-index: -1;
}

.feature-icon {
    font-size: 1.75rem;
    position: relative;
    z-index: 1;
}

.feature-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.feature-subtitle {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.feature-title {
    font-weight: 800;
    color: white;
    font-size: 1.5rem;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.feature-description {
    margin-top: auto;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
}

.feature-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    opacity: 0;
    animation: shine 3s ease-in-out infinite;
}

@keyframes shine {
    0%, 100% {
        transform: translateX(-100%);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
}

/* Desktop Sidebar */
.desktop-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sidebar-card {
    border-radius: 1.5rem;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-card.show {
    opacity: 1;
    transform: translateY(0);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: white;
}

.card-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stats-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

.stat-item-icon {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-item-glow {
    position: absolute;
    inset: -2px;
    border-radius: 0.75rem;
    background: inherit;
    filter: blur(8px);
    opacity: 0.5;
    z-index: -1;
}

.stat-item-info {
    flex: 1;
    min-width: 0;
}

.stat-item-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: white;
    line-height: 1;
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
}

.stat-item.loaded .stat-item-value {
    animation: count-up 0.6s ease-out;
}

.stat-item-label {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 600;
}

.stat-item-trend {
    font-size: 1.25rem;
    color: #10b981;
    flex-shrink: 0;
}

/* Desktop Map */
.sidebar-map {
    position: relative;
    border-radius: 1.5rem;
    padding: 0.75rem;
    min-height: 20rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(28, 28, 30, 0.6);
    backdrop-filter: blur(20px);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-map.show {
    opacity: 1;
    transform: translateY(0);
}

.map-badge-desk {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(20px);
}

.map-expand-desk {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 10;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    transition: all 0.2s;
}

.map-expand-desk:hover {
    transform: scale(1.05);
}

.map-expand-desk svg {
    width: 1rem;
    height: 1rem;
}

.map-wrapper {
    width: 100%;
    height: 100%;
    min-height: 18rem;
    border-radius: 1rem;
    overflow: hidden;
}

/* Transitions */
.toast-enter-active {
    animation: toast-in 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

@keyframes toast-in {
    from {
        opacity: 0;
        transform: translateX(100%) translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0) translateY(0);
    }
}

/* Responsive Enhancements */
@media (min-width: 640px) and (max-width: 1023px) {
    .mobile-layout {
        max-width: 36rem;
        padding: 0 1.5rem 8rem;
    }
    
    .apps-grid {
        gap: 1rem;
    }
}

@media (min-width: 1024px) and (max-width: 1439px) {
    .desktop-grid {
        grid-template-columns: 1fr 20rem;
        gap: 1.5rem;
    }
    
    .greeting-text {
        font-size: 2.5rem;
    }
    
    .clock-digits {
        font-size: 3.5rem;
    }
}

@media (min-width: 1440px) {
    .desktop-layout {
        max-width: 90rem;
    }
}

/* Landscape Mobile Optimizations */
@media (max-height: 600px) and (orientation: landscape) {
    .time-widget {
        margin-bottom: 1rem;
    }
    
    .stats-grid {
        margin-bottom: 1rem;
    }
    
    .apps-section {
        margin-bottom: 1rem;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}

/* Touch device optimizations */
@media (hover: none) {
    .feature-card:hover,
    .stat-item:hover {
        transform: none;
    }
    
    .feature-card:active,
    .stat-item:active {
        transform: scale(0.98);
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .stat-card,
    .app-card,
    .feature-card,
    .sidebar-card {
        border-width: 2px;
        border-color: rgba(255, 255, 255, 0.3);
    }
}
</style>
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
const currentTime = ref(Date.now());

const isBusinessOwner = computed(() => {
    return user.role === 'business_owner' || user.has_business;
});

// Enhanced toast notification
const flashSuccess = computed(() => page.props.flash?.success);
const showFlash = ref(false);

// Enhanced live clock with more precision
const time = ref('');
const date = ref('');
const fullDate = ref('');
const greeting = ref('Good Morning');
const dayPeriod = ref('AM');

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

// Enhanced mobile app shortcuts with better organization
const mobileApps = [
    // Row 1 - Essential Services
    { 
        icon: '⚡', 
        name: 'Services', 
        link: route('services.landing'), 
        gradient: 'from-blue-500 via-blue-600 to-indigo-600', 
        glow: 'shadow-blue-500/50',
        category: 'essential'
    },
    { 
        icon: '💳', 
        name: 'Bills', 
        link: route('bills.create'), 
        gradient: 'from-green-500 via-emerald-600 to-teal-600', 
        glow: 'shadow-green-500/50',
        category: 'essential'
    },
    { 
        icon: '🩺', 
        name: 'Health', 
        link: route('health.index'), 
        gradient: 'from-red-500 via-rose-600 to-pink-600', 
        glow: 'shadow-red-500/50',
        category: 'essential'
    },
    { 
        icon: '📍', 
        name: 'Places', 
        link: route('establishments.index'), 
        gradient: 'from-purple-500 via-violet-600 to-fuchsia-600', 
        glow: 'shadow-purple-500/50',
        category: 'essential'
    },
    
    // Row 2 - Commerce & Business
    { 
        icon: '🏪', 
        name: 'Market', 
        link: route('marketplace.index'), 
        gradient: 'from-orange-500 via-amber-600 to-yellow-600', 
        glow: 'shadow-orange-500/50',
        category: 'commerce'
    },
    { 
        icon: '📊', 
        name: isBusinessOwner.value ? 'Business' : 'Start', 
        link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), 
        gradient: 'from-indigo-500 via-blue-600 to-cyan-600', 
        glow: 'shadow-indigo-500/50',
        category: 'commerce'
    },
    { 
        icon: '💼', 
        name: 'Jobs', 
        link: route('jobs.index'), 
        gradient: 'from-yellow-500 via-orange-600 to-amber-600', 
        glow: 'shadow-yellow-500/50',
        category: 'commerce'
    },
    { 
        icon: '📈', 
        name: 'Prices', 
        link: route('market.index'), 
        gradient: 'from-cyan-500 via-teal-600 to-emerald-600', 
        glow: 'shadow-cyan-500/50',
        category: 'commerce'
    },
    
    // Row 3 - Community & Civic
    { 
        icon: '♻️', 
        name: 'Green', 
        link: route('environment.index'), 
        gradient: 'from-emerald-500 via-green-600 to-lime-600', 
        glow: 'shadow-emerald-500/50',
        category: 'community'
    },
    { 
        icon: '🤝', 
        name: 'Social', 
        link: route('social.index'), 
        gradient: 'from-violet-500 via-purple-600 to-indigo-600', 
        glow: 'shadow-violet-500/50',
        category: 'community'
    },
    { 
        icon: '🚨', 
        name: 'Report', 
        link: null, 
        action: 'wizard', 
        gradient: 'from-red-600 via-rose-700 to-pink-700', 
        glow: 'shadow-red-600/60', 
        pulse: true,
        category: 'community'
    },
    { 
        icon: '🗳️', 
        name: 'Polls', 
        link: route('polls.index'), 
        gradient: 'from-amber-500 via-yellow-600 to-orange-600', 
        glow: 'shadow-amber-500/50',
        category: 'community'
    },
];

// Enhanced quick stats with real-time updates
const quickStats = [
    { label: 'Active Docs', value: '3', icon: '📄', color: 'from-blue-500 to-indigo-600', trend: '+2' },
    { label: 'Pending', value: '1', icon: '⏳', color: 'from-amber-500 to-orange-600', trend: '-1' },
    { label: 'Completed', value: '12', icon: '✅', color: 'from-green-500 to-emerald-600', trend: '+5' },
];

// Enhanced desktop features with better categorization
const desktopFeatures = [
    { 
        title: 'Smart Services',
        subtitle: 'Permits & Clearances', 
        icon: '⚡',
        link: route('services.landing'),
        historyLink: route('services.index'),
        gradient: 'from-blue-500 to-indigo-600',
        size: 'large',
        priority: 1
    },
    { 
        title: 'Bills & Fees', 
        subtitle: 'Secure Payments', 
        icon: '💳', 
        link: route('bills.create'),
        gradient: 'from-teal-500 to-emerald-600',
        size: 'medium',
        priority: 2
    },
    { 
        title: 'E-Health', 
        subtitle: 'Telemed & Booking', 
        icon: '🩺', 
        link: route('health.index'),
        gradient: 'from-sky-500 to-cyan-600',
        size: 'medium',
        priority: 2
    },
    { 
        title: 'Issue Report', 
        subtitle: 'Quick Response', 
        icon: '🚨', 
        link: null,
        action: 'wizard',
        gradient: 'from-red-500 to-pink-600',
        size: 'medium',
        special: true,
        priority: 1
    },
    { 
        title: 'Green Guard', 
        subtitle: 'Waste & Air', 
        icon: '♻️', 
        link: route('environment.index'),
        gradient: 'from-green-500 to-emerald-600',
        size: 'medium',
        priority: 3
    },
    { 
        title: 'Social Aid', 
        subtitle: 'Support Programs', 
        icon: '🤝', 
        link: route('social.index'),
        gradient: 'from-violet-500 to-purple-600',
        size: 'medium',
        priority: 3
    },
    { 
        title: 'Marketplace', 
        subtitle: 'Local Businesses', 
        icon: '🌐', 
        link: route('marketplace.index'),
        gradient: 'from-purple-500 to-fuchsia-600',
        size: 'large',
        featured: true,
        priority: 1
    },
    { 
        title: isBusinessOwner.value ? 'My Business' : 'Business Hub', 
        subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', 
        icon: isBusinessOwner.value ? '📊' : '🏪', 
        link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), 
        gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600',
        size: 'medium',
        special: !isBusinessOwner.value,
        priority: 2
    },
    { 
        title: 'Market Prices', 
        subtitle: 'Live Rates', 
        icon: '📈', 
        link: route('market.index'),
        gradient: 'from-cyan-500 to-blue-600',
        size: 'medium',
        priority: 3
    },
    { 
        title: 'Places', 
        subtitle: 'Directory', 
        icon: '🏨', 
        link: route('establishments.index'),
        gradient: 'from-lime-500 to-green-600',
        size: 'medium',
        priority: 3
    },
    { 
        title: 'Local Jobs', 
        subtitle: 'Opportunities', 
        icon: '💼', 
        link: route('jobs.index'),
        gradient: 'from-yellow-500 to-orange-600',
        size: 'medium',
        priority: 3
    },
];

// Timer management
let clockTimer;

// Enhanced entrance animation with stagger
const triggerEntranceAnimation = () => {
    mounted.value = true;
};

onMounted(() => {
    updateTime();
    clockTimer = setInterval(updateTime, 1000);

    if (flashSuccess.value) {
        showFlash.value = true;
        setTimeout(() => showFlash.value = false, 4000);
    }
    
    // Trigger entrance with slight delay for better effect
    requestAnimationFrame(() => {
        setTimeout(triggerEntranceAnimation, 50);
    });
});

onUnmounted(() => {
    clearInterval(clockTimer);
});

// Handle app click with haptic feedback simulation
const handleAppClick = (app) => {
    if ('vibrate' in navigator) {
        navigator.vibrate(10);
    }
    if (app.action === 'wizard') {
        showWizard.value = true;
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        
        <!-- Enhanced Toast Notification with Better Animation -->
        <Transition name="toast-bounce">
            <div v-if="showFlash && flashSuccess" 
                 class="toast-notification">
                <div class="toast-container">
                    <!-- Icon container with pulse -->
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
            </div>
        </Transition>

        <!-- MOBILE LAYOUT (Enhanced iOS/Android Native Style) -->
        <div class="mobile-layout">
            
            <!-- Enhanced Time Widget with Better Typography -->
            <div class="time-widget" :class="{ 'animate-fade-in': mounted }">
                <div class="time-widget-container">
                    <!-- Animated gradient background -->
                    <div class="time-widget-gradient"></div>
                    
                    <!-- Radial glow effect -->
                    <div class="time-widget-glow"></div>
                    
                    <div class="time-widget-content">
                        <!-- Time Section -->
                        <div class="time-section">
                            <p class="time-label">{{ date }}</p>
                            <div class="time-display">
                                <h2 class="time-value">{{ time }}</h2>
                                <span class="time-period">{{ dayPeriod }}</span>
                            </div>
                        </div>
                        
                        <!-- Enhanced Date Badge -->
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

            <!-- Enhanced Premium Stats Cards with Better Interactions -->
            <div class="stats-grid">
                <div 
                    v-for="(stat, index) in quickStats" 
                    :key="stat.label"
                    class="stat-card"
                    :class="{ 'animate-scale-in': mounted }"
                    :style="`animation-delay: ${100 + index * 80}ms`"
                >
                    <!-- Ambient glow -->
                    <div :class="`stat-card-glow bg-gradient-to-br ${stat.color}`"></div>
                    
                    <!-- Shine effect on hover -->
                    <div class="stat-card-shine"></div>
                    
                    <!-- Icon badge with enhanced animations -->
                    <div class="stat-icon-wrapper">
                        <div :class="`stat-icon-badge bg-gradient-to-br ${stat.color}`">
                            <span class="stat-icon">{{ stat.icon }}</span>
                        </div>
                    </div>
                    
                    <!-- Stats content -->
                    <div class="stat-content">
                        <p class="stat-value">{{ stat.value }}</p>
                        <p class="stat-label">{{ stat.label }}</p>
                    </div>
                    
                    <!-- Trend indicator -->
                    <div class="stat-trend" :class="stat.trend.startsWith('+') ? 'stat-trend-up' : 'stat-trend-down'">
                        {{ stat.trend }}
                    </div>
                    
                    <!-- Enhanced highlight line -->
                    <div class="stat-highlight"></div>
                </div>
            </div>

            <!-- Enhanced Apple-Style Search Bar with Better Focus States -->
            <div class="search-wrapper" :class="{ 'animate-slide-up': mounted }" style="animation-delay: 280ms">
                <div class="search-container">
                    <!-- Enhanced glow on focus -->
                    <div class="search-glow"></div>
                    
                    <!-- Search bar -->
                    <div class="search-bar">
                        <!-- Top highlight -->
                        <div class="search-highlight"></div>
                        
                        <!-- Search icon with animation -->
                        <div class="search-icon-wrapper">
                            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Input field -->
                        <input 
                            type="text" 
                            placeholder="Search services, documents..."
                            class="search-input"
                        >
                        
                        <!-- Voice search button -->
                        <button class="search-voice-btn">
                            <svg class="search-voice-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced iOS-Style App Grid with Better Touch Feedback -->
            <div class="apps-section">
                <div class="apps-header" :class="{ 'animate-slide-right': mounted }" style="animation-delay: 360ms">
                    <h3 class="apps-title">Quick Access</h3>
                    <button class="apps-view-all">
                        View All →
                    </button>
                </div>
                
                <div class="apps-grid">
                    <component 
                        :is="app.link ? Link : 'button'"
                        v-for="(app, index) in mobileApps" 
                        :key="app.name"
                        :href="app.link"
                        @click="app.action ? handleAppClick(app) : null"
                        class="app-item"
                        :class="{ 'animate-scale-fade': mounted }"
                        :style="`animation-delay: ${440 + (index * 50)}ms`"
                    >
                        <!-- App Icon Container with Enhanced Effects -->
                        <div class="app-icon-container">
                            <!-- Pulse effect for emergency -->
                            <div 
                                v-if="app.pulse"
                                class="app-pulse-ring"
                            ></div>
                            
                            <!-- Glow effect -->
                            <div :class="`app-glow bg-gradient-to-br ${app.gradient} ${app.glow}`"></div>
                            
                            <!-- Icon wrapper -->
                            <div :class="`app-icon-wrapper bg-gradient-to-br ${app.gradient}`">
                                <!-- Inner shine -->
                                <div class="app-icon-shine"></div>
                                
                                <!-- Reflection effect -->
                                <div class="app-icon-reflection"></div>
                                
                                <!-- Rotating shine on hover -->
                                <div class="app-icon-shine-rotate"></div>
                                
                                <!-- Icon emoji -->
                                <span class="app-icon-emoji">{{ app.icon }}</span>
                            </div>
                        </div>
                        
                        <!-- App Name with Better Typography -->
                        <span class="app-name">{{ app.name }}</span>
                    </component>
                </div>
            </div>

            <!-- Enhanced Featured Actions with Better Visual Hierarchy -->
            <div class="featured-section">
                <!-- Section Header -->
                <div class="featured-header" :class="{ 'animate-slide-right': mounted }" style="animation-delay: 940ms">
                    <h3 class="featured-title">Featured</h3>
                    <div class="featured-dots">
                        <div class="featured-dot featured-dot-1"></div>
                        <div class="featured-dot featured-dot-2"></div>
                        <div class="featured-dot featured-dot-3"></div>
                    </div>
                </div>

                <!-- Quick Action Cards with Better Interactions -->
                <div class="featured-cards-grid">
                    <!-- Pay Bills Card -->
                    <Link 
                        :href="route('bills.create')"
                        class="featured-card"
                        :class="{ 'animate-scale-in': mounted }"
                        style="animation-delay: 1020ms"
                    >
                        <!-- Enhanced glow -->
                        <div class="featured-card-glow featured-card-glow-green"></div>
                        
                        <!-- Shine effect -->
                        <div class="featured-card-shine"></div>
                        
                        <!-- Icon with better animation -->
                        <div class="featured-card-icon featured-card-icon-green">
                            <span class="featured-icon-emoji">💳</span>
                        </div>
                        
                        <!-- Content -->
                        <div class="featured-card-content">
                            <p class="featured-card-title">Pay Bills</p>
                            <p class="featured-card-desc">Quick & secure payments</p>
                        </div>
                        
                        <!-- Enhanced arrow -->
                        <div class="featured-card-arrow">
                            <svg class="featured-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </Link>
                    
                    <!-- Report Issue Card -->
                    <button
                        @click="showWizard = true"
                        class="featured-card"
                        :class="{ 'animate-scale-in': mounted }"
                        style="animation-delay: 1100ms"
                    >
                        <!-- Enhanced glow -->
                        <div class="featured-card-glow featured-card-glow-red"></div>
                        
                        <!-- Shine effect -->
                        <div class="featured-card-shine"></div>
                        
                        <!-- Enhanced pulse indicator -->
                        <div class="featured-pulse-indicator">
                            <div class="featured-pulse-dot"></div>
                            <div class="featured-pulse-ring"></div>
                        </div>
                        
                        <!-- Icon -->
                        <div class="featured-card-icon featured-card-icon-red">
                            <span class="featured-icon-emoji">🚨</span>
                        </div>
                        
                        <!-- Content -->
                        <div class="featured-card-content">
                            <p class="featured-card-title">Report</p>
                            <p class="featured-card-desc">Issue or emergency</p>
                        </div>
                        
                        <!-- Enhanced arrow -->
                        <div class="featured-card-arrow">
                            <svg class="featured-arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </button>
                </div>

                <!-- Enhanced Map Preview Card -->
                <div class="map-preview-card" :class="{ 'animate-scale-in': mounted }" style="animation-delay: 1180ms">
                    <!-- Map Container -->
                    <div class="map-preview-container">
                        <!-- Enhanced badge with better animation -->
                        <div class="map-badge">
                            <span class="map-badge-pulse">
                                <span class="map-badge-ping"></span>
                                <span class="map-badge-dot"></span>
                            </span>
                            <span class="map-badge-text">Live Map</span>
                        </div>
                        
                        <!-- Map -->
                        <div class="map-preview-wrapper">
                            <DashboardMap />
                        </div>
                        
                        <!-- Enhanced gradient overlay -->
                        <div class="map-overlay"></div>
                        
                        <!-- Enhanced bottom info bar -->
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

            <!-- Enhanced Floating Emergency Button -->
            <Link 
                :href="route('emergency.index')"
                class="floating-emergency-btn"
            >
                <div class="emergency-btn-container">
                    <!-- Multiple enhanced pulse rings -->
                    <div class="emergency-pulse-ring emergency-pulse-1"></div>
                    <div class="emergency-pulse-ring emergency-pulse-2"></div>
                    <div class="emergency-pulse-ring emergency-pulse-3"></div>
                    
                    <!-- Enhanced shadow glow -->
                    <div class="emergency-glow"></div>
                    
                    <!-- Button -->
                    <div class="emergency-btn">
                        <!-- Enhanced inner shine -->
                        <div class="emergency-btn-shine"></div>
                        
                        <!-- Rotating shine -->
                        <div class="emergency-btn-shine-rotate"></div>
                        
                        <!-- Icon -->
                        <span class="emergency-icon">🚨</span>
                    </div>
                    
                    <!-- Enhanced label -->
                    <div class="emergency-label">
                        <div class="emergency-label-content">
                            <p class="emergency-label-text">Emergency</p>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT (Enhanced Dark Design) -->
        <div class="desktop-layout">
            
            <!-- Enhanced Header with Better Typography -->
            <header class="desktop-header" :class="{ 'animate-fade-in': mounted }">
                <div class="desktop-header-content">
                    <!-- Greeting with enhanced animation -->
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

                    <!-- Enhanced Clock with Better Visual Design -->
                    <div class="desktop-clock">
                        <div class="clock-time">
                            {{ time }}
                            <span class="clock-period">{{ dayPeriod }}</span>
                        </div>
                        <div class="clock-date">{{ date }}</div>
                    </div>
                </div>
            </header>

            <!-- Desktop Grid with Enhanced Layout -->
            <div class="desktop-grid">
                
                <!-- Left Column -->
                <div class="desktop-left-column">
                    
                    <!-- Enhanced Feature Grid -->
                    <div class="desktop-features-grid">
                        <component 
                            :is="feature.link ? Link : 'button'"
                            v-for="(feature, index) in desktopFeatures" 
                            :key="feature.title"
                            :href="feature.link"
                            @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="[
                                'desktop-feature-card',
                                `bg-gradient-to-br ${feature.gradient}`,
                                {
                                    'desktop-feature-large': feature.size === 'large',
                                    'desktop-feature-medium': feature.size === 'medium',
                                    'animate-scale-fade': mounted
                                }
                            ]"
                            :style="`animation-delay: ${index * 80}ms`"
                        >
                            <!-- Pattern overlay -->
                            <div class="feature-pattern"></div>
                            
                            <!-- Enhanced shine effect -->
                            <div class="feature-shine"></div>
                            
                            <!-- Rotating shine -->
                            <div class="feature-shine-rotate"></div>
                            
                            <!-- Featured Badge -->
                            <div v-if="feature.featured" class="feature-badge">
                                ✨ Popular
                            </div>

                            <!-- History Link -->
                            <Link v-if="feature.historyLink" 
                                  :href="feature.historyLink"
                                  @click.stop
                                  class="feature-history-link">
                                <span>History</span>
                                <svg class="feature-history-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </Link>

                            <div class="feature-content">
                                <!-- Icon -->
                                <div class="feature-icon">
                                    {{ feature.icon }}
                                </div>

                                <!-- Content -->
                                <div class="feature-text">
                                    <p class="feature-subtitle">{{ feature.subtitle }}</p>
                                    <h3 class="feature-title">{{ feature.title }}</h3>
                                </div>
                            </div>
                        </component>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <aside class="desktop-sidebar">
                    
                    <!-- Enhanced ID Card -->
                    <div class="id-card" :class="{ 'animate-scale-in': mounted }">
                        <div class="id-card-content">
                            <!-- Animated background -->
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

                    <!-- Enhanced Polls Card -->
                    <Link :href="route('polls.index')" 
                          class="sidebar-card sidebar-card-polls"
                          :class="{ 'animate-scale-in': mounted }"
                          style="animation-delay: 200ms">
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

                    <!-- Enhanced Map Card -->
                    <div class="sidebar-map-card" :class="{ 'animate-scale-in': mounted }" style="animation-delay: 300ms">
                        <div class="sidebar-map-badge">
                            📍 Live Map & Alerts
                        </div>
                        <div class="sidebar-map-wrapper">
                            <DashboardMap />
                        </div>
                    </div>
                    
                    <!-- Enhanced Emergency Card -->
                    <Link :href="route('emergency.index')" 
                          class="sidebar-card sidebar-card-emergency"
                          :class="{ 'animate-scale-in': mounted }"
                          style="animation-delay: 400ms">
                        <div class="sidebar-card-content">
                            <div class="sidebar-card-pattern"></div>
                            
                            <!-- Enhanced pulse -->
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

/* ==================== CSS VARIABLES & BASE ==================== */
:root {
    --fluid-xs: clamp(0.625rem, 0.5rem + 0.625vw, 0.75rem);
    --fluid-sm: clamp(0.75rem, 0.65rem + 0.5vw, 0.875rem);
    --fluid-base: clamp(0.875rem, 0.75rem + 0.625vw, 1rem);
    --fluid-lg: clamp(1rem, 0.85rem + 0.75vw, 1.25rem);
    --fluid-xl: clamp(1.125rem, 0.9rem + 1.125vw, 1.5rem);
    --fluid-2xl: clamp(1.5rem, 1.2rem + 1.5vw, 2rem);
    --fluid-3xl: clamp(1.875rem, 1.5rem + 1.875vw, 2.5rem);
    --fluid-4xl: clamp(2.25rem, 1.8rem + 2.25vw, 3rem);
    
    --space-xs: clamp(0.25rem, 0.2rem + 0.25vw, 0.375rem);
    --space-sm: clamp(0.5rem, 0.4rem + 0.5vw, 0.75rem);
    --space-md: clamp(0.75rem, 0.6rem + 0.75vw, 1.125rem);
    --space-lg: clamp(1rem, 0.8rem + 1vw, 1.5rem);
    --space-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);
    --space-2xl: clamp(2rem, 1.6rem + 2vw, 3rem);
}

* {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    letter-spacing: -0.01em;
}

/* ==================== TOAST NOTIFICATION ==================== */
.toast-notification {
    position: fixed;
    top: clamp(4rem, 5vw, 5rem);
    right: var(--space-md);
    z-index: 60;
    width: clamp(280px, 90vw, 384px);
    border-radius: clamp(1rem, 1.5vw, 1.25rem);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
    overflow: hidden;
    background: linear-gradient(135deg, rgb(16, 185, 129), rgb(5, 150, 105));
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.toast-notification::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.1), transparent);
}

.toast-container {
    padding: var(--space-md);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.toast-icon-wrapper {
    position: relative;
    flex-shrink: 0;
}

.toast-icon-pulse {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: clamp(0.75rem, 1vw, 1rem);
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.toast-icon-svg {
    width: clamp(2.5rem, 5vw, 3rem);
    height: clamp(2.5rem, 5vw, 3rem);
    position: relative;
    z-index: 10;
}

.toast-content {
    flex: 1;
    min-width: 0;
}

.toast-text {
    font-size: var(--fluid-sm);
    font-weight: 700;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
}

.toast-close-btn {
    flex-shrink: 0;
    padding: var(--space-xs);
    border-radius: clamp(0.5rem, 0.75vw, 0.75rem);
    transition: background-color 0.2s;
}

.toast-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

.toast-close-btn:active {
    transform: scale(0.9);
}

.toast-close-icon {
    width: clamp(1rem, 2vw, 1.25rem);
    height: clamp(1rem, 2vw, 1.25rem);
    color: white;
}

/* Toast Animation */
@keyframes toast-bounce {
    0% {
        opacity: 0;
        transform: translateX(100%) scale(0.8);
    }
    60% {
        opacity: 1;
        transform: translateX(-10px) scale(1.02);
    }
    100% {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

.toast-bounce-enter-active {
    animation: toast-bounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-bounce-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 1, 1);
}

.toast-bounce-leave-to {
    opacity: 0;
    transform: translateX(100%) scale(0.8);
}

/* ==================== MOBILE LAYOUT ==================== */
.mobile-layout {
    width: 100%;
    max-width: 28rem;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-2xl);
    padding-top: var(--space-lg);
}

@media (min-width: 1024px) {
    .mobile-layout {
        display: none;
    }
}

/* Time Widget */
.time-widget {
    margin-bottom: var(--space-xl);
    opacity: 0;
}

.time-widget-container {
    position: relative;
    overflow: hidden;
    border-radius: clamp(1.5rem, 4vw, 2rem);
    padding: var(--space-lg);
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: linear-gradient(135deg, rgb(28, 28, 30), rgb(44, 44, 46));
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
}

.time-widget-gradient {
    position: absolute;
    inset: 0;
    animation: gradient-rotate 20s ease-in-out infinite;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 51, 234, 0.08), rgba(236, 72, 153, 0.08));
}

.time-widget-glow {
    position: absolute;
    top: 0;
    right: 0;
    width: clamp(6rem, 20vw, 8rem);
    height: clamp(6rem, 20vw, 8rem);
    border-radius: 50%;
    filter: blur(clamp(2rem, 5vw, 3rem));
    background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent);
}

.time-widget-content {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.time-section {
    flex: 1;
}

.time-label {
    color: rgb(156, 163, 175);
    font-size: var(--fluid-xs);
    font-weight: 500;
    margin-bottom: var(--space-xs);
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.time-display {
    display: flex;
    align-items: baseline;
    gap: var(--space-sm);
}

.time-value {
    font-size: clamp(2rem, 8vw, 2.5rem);
    font-weight: 700;
    color: white;
    letter-spacing: -0.03em;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
}

.time-period {
    font-size: var(--fluid-lg);
    font-weight: 600;
    color: rgb(156, 163, 175);
}

.date-badge-wrapper {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.date-badge {
    position: relative;
    padding: var(--space-sm) var(--space-md);
    border-radius: clamp(1rem, 3vw, 1.25rem);
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    overflow: hidden;
    transition: transform 0.3s;
    background: rgba(255, 255, 255, 0.10);
    backdrop-filter: blur(20px);
}

.date-badge:hover {
    transform: scale(1.05);
}

.date-badge-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.5s;
}

.date-badge:hover .date-badge-shine {
    opacity: 1;
}

.date-badge-day {
    font-size: clamp(1.25rem, 5vw, 1.75rem);
    font-weight: 700;
    color: white;
    text-align: center;
    line-height: 1;
    margin-bottom: var(--space-xs);
}

.date-badge-month {
    font-size: clamp(0.625rem, 2vw, 0.7rem);
    color: rgb(156, 163, 175);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Stats Grid */
.stats-grid {
    margin-bottom: var(--space-xl);
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: var(--space-sm);
}

.stat-card {
    position: relative;
    overflow: hidden;
    border-radius: clamp(1.25rem, 4vw, 1.75rem);
    padding: var(--space-lg);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 1px solid rgba(255, 255, 255, 0.10);
    opacity: 0;
    background: linear-gradient(135deg, rgb(28, 28, 30), rgb(44, 44, 46));
}

.stat-card:hover {
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.7);
    transform: scale(1.05) translateY(-2px);
}

.stat-card:active {
    transform: scale(0.95);
}

.stat-card-glow {
    position: absolute;
    top: -2.5rem;
    right: -2.5rem;
    width: clamp(4rem, 15vw, 6rem);
    height: clamp(4rem, 15vw, 6rem);
    opacity: 0.25;
    filter: blur(clamp(1rem, 3vw, 2rem));
    border-radius: 50%;
    transition: all 0.7s;
}

.stat-card:hover .stat-card-glow {
    opacity: 0.4;
    transform: scale(1.25);
}

.stat-card-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.05), transparent);
    opacity: 0;
    transition: opacity 0.5s;
}

.stat-card:hover .stat-card-shine {
    opacity: 1;
}

.stat-icon-wrapper {
    position: relative;
    margin-bottom: var(--space-sm);
}

.stat-icon-badge {
    width: clamp(2.5rem, 10vw, 3rem);
    height: clamp(2.5rem, 10vw, 3rem);
    border-radius: clamp(1rem, 3vw, 1.25rem);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.stat-card:hover .stat-icon-badge {
    transform: scale(1.1) rotate(6deg);
}

.stat-icon {
    font-size: clamp(1.25rem, 5vw, 1.5rem);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
}

.stat-content {
    position: relative;
}

.stat-value {
    font-size: clamp(1.75rem, 7vw, 2rem);
    font-weight: 700;
    color: white;
    margin-bottom: var(--space-xs);
    letter-spacing: -0.02em;
    transition: transform 0.3s;
}

.stat-card:hover .stat-value {
    transform: scale(1.1);
}

.stat-label {
    font-size: clamp(0.625rem, 2vw, 0.7rem);
    color: rgb(156, 163, 175);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.stat-trend {
    position: absolute;
    top: var(--space-xs);
    right: var(--space-xs);
    font-size: var(--fluid-xs);
    font-weight: 700;
    padding: var(--space-xs) var(--space-sm);
    border-radius: 9999px;
}

.stat-trend-up {
    color: rgb(74, 222, 128);
    background: rgba(34, 197, 94, 0.2);
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.stat-trend-down {
    color: rgb(248, 113, 113);
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.stat-highlight {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.08), transparent);
    transition: all 0.5s;
}

.stat-card:hover .stat-highlight {
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.15), transparent);
}

/* Search Bar */
.search-wrapper {
    margin-bottom: var(--space-xl);
    opacity: 0;
}

.search-container {
    position: relative;
}

.search-glow {
    position: absolute;
    inset: -2px;
    border-radius: clamp(1.5rem, 4vw, 2rem);
    opacity: 0;
    filter: blur(clamp(1rem, 3vw, 2rem));
    transition: opacity 0.7s;
    background: linear-gradient(to right, rgb(59, 130, 246), rgb(147, 51, 234), rgb(236, 72, 153));
}

.search-container:focus-within .search-glow {
    opacity: 0.25;
}

.search-bar {
    position: relative;
    background: #1c1c1e;
    border-radius: clamp(1.5rem, 4vw, 2rem);
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    overflow: hidden;
    transition: all 0.5s;
}

.search-bar:focus-within {
    border-color: rgba(255, 255, 255, 0.15);
}

.search-highlight {
    position: absolute;
    inset: 0 0 auto 0;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.12), transparent);
}

.search-icon-wrapper {
    position: absolute;
    left: var(--space-md);
    top: 50%;
    transform: translateY(-50%);
    width: clamp(2rem, 4vw, 2.5rem);
    height: clamp(2rem, 4vw, 2.5rem);
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-icon {
    width: clamp(1rem, 3vw, 1.25rem);
    height: clamp(1rem, 3vw, 1.25rem);
    color: rgb(156, 163, 175);
    transition: all 0.5s;
}

.search-bar:focus-within .search-icon {
    color: rgb(96, 165, 250);
    transform: scale(1.1);
}

.search-input {
    width: 100%;
    padding: var(--space-lg) var(--space-lg) var(--space-lg) clamp(3rem, 12vw, 3.5rem);
    background: transparent;
    color: white;
    font-size: var(--fluid-sm);
}

.search-input::placeholder {
    color: rgb(107, 114, 128);
}

.search-input:focus {
    outline: none;
}

.search-voice-btn {
    position: absolute;
    right: var(--space-sm);
    top: 50%;
    transform: translateY(-50%);
    width: clamp(2rem, 4vw, 2.25rem);
    height: clamp(2rem, 4vw, 2.25rem);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: clamp(0.75rem, 2vw, 1rem);
    transition: all 0.3s;
    background: rgba(255, 255, 255, 0.08);
}

.search-voice-btn:hover {
    background: rgba(255, 255, 255, 0.15);
}

.search-voice-btn:active {
    transform: translateY(-50%) scale(0.9);
}

.search-voice-icon {
    width: clamp(0.875rem, 2vw, 1rem);
    height: clamp(0.875rem, 2vw, 1rem);
    color: rgb(156, 163, 175);
    transition: color 0.3s;
}

.search-voice-btn:hover .search-voice-icon {
    color: rgb(96, 165, 250);
}

/* Apps Section - Will continue in next part */
.apps-section {
    margin-bottom: var(--space-xl);
}

.apps-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-md);
    padding: 0 var(--space-xs);
    opacity: 0;
}

.apps-title {
    font-size: var(--fluid-lg);
    font-weight: 700;
    color: white;
}

.apps-view-all {
    font-size: var(--fluid-xs);
    font-weight: 600;
    color: rgb(96, 165, 250);
    transition: all 0.3s;
}

.apps-view-all:hover {
    color: rgb(147, 197, 253);
    transform: translateX(2px);
}

.apps-view-all:active {
    transform: scale(0.95);
}

.apps-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: var(--space-md);
}

.app-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-sm);
    border-radius: clamp(1.5rem, 5vw, 2rem);
    padding: var(--space-sm);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    opacity: 0;
}

.app-item:active {
    transform: scale(0.9);
}

.app-item:focus {
    outline: none;
}

.app-item:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
}

.app-icon-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
}

.app-pulse-ring {
    position: absolute;
    inset: 0;
    background: rgb(239, 68, 68);
    border-radius: clamp(1rem, 4vw, 1.3rem);
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    opacity: 0.4;
}

.app-glow {
    position: absolute;
    inset: -4px;
    border-radius: clamp(1.1rem, 4.5vw, 1.4rem);
    opacity: 0;
    filter: blur(clamp(0.5rem, 2vw, 1rem));
    transition: all 0.5s;
}

.app-item:hover .app-glow {
    opacity: 0.7;
}

.app-icon-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: clamp(1rem, 4vw, 1.3rem);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.app-item:hover .app-icon-wrapper {
    transform: scale(1.1);
}

.app-item:active .app-icon-wrapper {
    transform: scale(0.9);
}

.app-icon-shine {
    position: absolute;
    inset: 1px;
    border-radius: clamp(0.95rem, 3.8vw, 1.25rem);
    opacity: 0.9;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.15), transparent);
}

.app-icon-reflection {
    position: absolute;
    inset: 0;
    border-radius: clamp(1rem, 4vw, 1.3rem);
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.25), transparent 40%);
}

.app-icon-shine-rotate {
    position: absolute;
    inset: 0;
    border-radius: clamp(1rem, 4vw, 1.3rem);
    transform: translateX(-100%);
    transition: transform 1s;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
}

.app-item:hover .app-icon-shine-rotate {
    transform: translateX(100%);
}

.app-icon-emoji {
    position: relative;
    font-size: clamp(1.75rem, 7vw, 2rem);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    transition: transform 0.3s;
}

.app-item:hover .app-icon-emoji {
    transform: scale(1.1);
}

.app-name {
    font-size: clamp(0.688rem, 2.5vw, 0.75rem);
    font-weight: 600;
    color: rgb(209, 213, 219);
    text-align: center;
    line-height: 1.2;
    width: 100%;
    padding: 0 var(--space-xs);
}

/* Featured Section */
.featured-section {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
    margin-bottom: var(--space-xl);
}

.featured-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 var(--space-xs);
    opacity: 0;
}

.featured-title {
    font-size: var(--fluid-lg);
    font-weight: 700;
    color: white;
}

.featured-dots {
    display: flex;
    gap: clamp(0.25rem, 1vw, 0.375rem);
}

.featured-dot {
    width: clamp(0.25rem, 1vw, 0.375rem);
    height: clamp(0.25rem, 1vw, 0.375rem);
    border-radius: 9999px;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
}

.featured-dot-1 {
    background: rgb(74, 222, 128);
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

.featured-dot-2 {
    background: rgb(96, 165, 250);
    box-shadow: 0 0 8px rgba(96, 165, 250, 0.5);
    animation-delay: 0.2s;
}

.featured-dot-3 {
    background: rgb(192, 132, 252);
    box-shadow: 0 0 8px rgba(192, 132, 252, 0.5);
    animation-delay: 0.4s;
}

.featured-cards-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--space-sm);
}

.featured-card {
    position: relative;
    overflow: hidden;
    border-radius: clamp(1.25rem, 4vw, 1.75rem);
    padding: var(--space-md);
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    opacity: 0;
    background: linear-gradient(135deg, rgb(28, 28, 30), rgb(44, 44, 46));
}

.featured-card:hover {
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.7);
}

.featured-card:active {
    transform: scale(0.97);
}

.featured-card-glow {
    position: absolute;
    top: -2.5rem;
    right: -2.5rem;
    width: clamp(4rem, 15vw, 6rem);
    height: clamp(4rem, 15vw, 6rem);
    opacity: 0.25;
    filter: blur(clamp(1rem, 3vw, 2rem));
    border-radius: 50%;
    transition: all 0.7s;
}

.featured-card-glow-green {
    background: radial-gradient(circle, rgb(34, 197, 94), rgb(16, 185, 129));
}

.featured-card-glow-red {
    background: radial-gradient(circle, rgb(239, 68, 68), rgb(236, 72, 153));
}

.featured-card:hover .featured-card-glow {
    opacity: 0.4;
    transform: scale(1.25);
}

.featured-card-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.05), transparent);
    opacity: 0;
    transition: opacity 0.5s;
}

.featured-card:hover .featured-card-shine {
    opacity: 1;
}

.featured-pulse-indicator {
    position: absolute;
    top: var(--space-sm);
    right: var(--space-sm);
    z-index: 10;
}

.featured-pulse-dot {
    width: clamp(0.375rem, 1.5vw, 0.5rem);
    height: clamp(0.375rem, 1.5vw, 0.5rem);
    background: rgb(248, 113, 113);
    border-radius: 9999px;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    box-shadow: 0 0 8px rgba(248, 113, 113, 0.7);
}

.featured-pulse-ring {
    position: absolute;
    inset: 0;
    background: rgb(248, 113, 113);
    border-radius: 9999px;
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.featured-card-icon {
    margin-bottom: var(--space-sm);
    width: clamp(2.5rem, 10vw, 3rem);
    height: clamp(2.5rem, 10vw, 3rem);
    border-radius: clamp(1rem, 3vw, 1.25rem);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.featured-card-icon-green {
    background: linear-gradient(135deg, rgb(34, 197, 94), rgb(16, 185, 129));
}

.featured-card-icon-red {
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(236, 72, 153));
}

.featured-card:hover .featured-card-icon {
    transform: scale(1.1) rotate(6deg);
}

.featured-icon-emoji {
    font-size: clamp(1.25rem, 5vw, 1.5rem);
}

.featured-card-content {
    position: relative;
    z-index: 10;
}

.featured-card-title {
    font-size: var(--fluid-sm);
    font-weight: 700;
    color: white;
    margin-bottom: var(--space-xs);
}

.featured-card-desc {
    font-size: var(--fluid-xs);
    color: rgb(156, 163, 175);
    line-height: 1.5;
}

.featured-card-arrow {
    position: absolute;
    bottom: var(--space-md);
    right: var(--space-md);
    width: clamp(1.25rem, 4vw, 1.5rem);
    height: clamp(1.25rem, 4vw, 1.5rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    z-index: 10;
    background: rgba(255, 255, 255, 0.10);
}

.featured-card:hover .featured-card-arrow {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(2px);
}

.featured-arrow-icon {
    width: clamp(0.75rem, 2vw, 0.875rem);
    height: clamp(0.75rem, 2vw, 0.875rem);
    color: rgb(156, 163, 175);
}

/* Map Preview Card */
.map-preview-card {
    position: relative;
    overflow: hidden;
    border-radius: clamp(1.25rem, 4vw, 1.75rem);
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    opacity: 0;
    background: linear-gradient(135deg, rgb(28, 28, 30), rgb(44, 44, 46));
}

.map-preview-container {
    position: relative;
    height: clamp(12rem, 50vw, 13rem);
    overflow: hidden;
}

.map-badge {
    position: absolute;
    top: var(--space-md);
    left: var(--space-md);
    z-index: 10;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-sm) var(--space-md);
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(40px);
}

.map-badge-pulse {
    position: relative;
    display: flex;
    height: clamp(0.375rem, 1.5vw, 0.5rem);
    width: clamp(0.375rem, 1.5vw, 0.5rem);
}

.map-badge-ping {
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    position: absolute;
    display: inline-flex;
    height: 100%;
    width: 100%;
    border-radius: 9999px;
    background: rgb(74, 222, 128);
    opacity: 0.75;
}

.map-badge-dot {
    position: relative;
    display: inline-flex;
    border-radius: 9999px;
    height: clamp(0.375rem, 1.5vw, 0.5rem);
    width: clamp(0.375rem, 1.5vw, 0.5rem);
    background: rgb(74, 222, 128);
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

.map-badge-text {
    font-size: var(--fluid-xs);
    font-weight: 700;
    color: white;
}

.map-preview-wrapper {
    width: 100%;
    height: 100%;
}

.map-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, #1c1c1e, transparent 60%);
    pointer-events: none;
}

.map-info-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: var(--space-md);
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.5), transparent);
    backdrop-filter: blur(20px);
}

.map-info-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.map-info-text {
    flex: 1;
}

.map-info-title {
    font-size: var(--fluid-sm);
    font-weight: 700;
    color: white;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
}

.map-info-subtitle {
    font-size: var(--fluid-xs);
    color: rgb(209, 213, 219);
}

.map-info-arrow {
    width: clamp(1.75rem, 5vw, 2rem);
    height: clamp(1.75rem, 5vw, 2rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    background: rgba(255, 255, 255, 0.15);
}

.map-preview-container:hover .map-info-arrow {
    background: rgba(255, 255, 255, 0.25);
    transform: translateX(2px);
}

.map-arrow-icon {
    width: clamp(0.875rem, 2.5vw, 1rem);
    height: clamp(0.875rem, 2.5vw, 1rem);
    color: white;
}

/* Floating Emergency Button */
.floating-emergency-btn {
    position: fixed;
    bottom: clamp(8rem, 30vw, 9rem);
    right: var(--space-xl);
    z-index: 40;
}

@media (min-width: 1024px) {
    .floating-emergency-btn {
        display: none;
    }
}

.emergency-btn-container {
    position: relative;
}

.emergency-pulse-ring {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.emergency-pulse-1 {
    background: rgb(248, 113, 113);
    opacity: 0.25;
}

.emergency-pulse-2 {
    background: rgb(239, 68, 68);
    opacity: 0.2;
    animation-delay: 0.5s;
}

.emergency-pulse-3 {
    background: rgb(220, 38, 38);
    opacity: 0.15;
    animation-delay: 1s;
}

.emergency-glow {
    position: absolute;
    inset: 0;
    background: rgb(239, 68, 68);
    border-radius: 9999px;
    filter: blur(clamp(1rem, 4vw, 2rem));
    opacity: 0.6;
    transition: opacity 0.5s;
}

.floating-emergency-btn:hover .emergency-glow {
    opacity: 0.8;
}

.emergency-btn {
    position: relative;
    width: clamp(3.5rem, 14vw, 4rem);
    height: clamp(3.5rem, 14vw, 4rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    border: 4px solid rgba(0, 0, 0, 0.3);
    overflow: hidden;
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(220, 38, 38), rgb(185, 28, 28));
    box-shadow: 0 20px 60px rgba(239, 68, 68, 0.7);
}

.floating-emergency-btn:active .emergency-btn {
    transform: scale(0.9);
}

.floating-emergency-btn:hover .emergency-btn {
    transform: scale(1.1);
}

.emergency-btn-shine {
    position: absolute;
    inset: 2px;
    border-radius: 9999px;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.40), rgba(255, 255, 255, 0.20), transparent);
}

.emergency-btn-shine-rotate {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    transform: translateX(-100%);
    transition: transform 1s;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.30), transparent);
}

.floating-emergency-btn:hover .emergency-btn-shine-rotate {
    transform: translateX(100%);
}

.emergency-icon {
    position: relative;
    font-size: clamp(1.5rem, 6vw, 1.875rem);
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    z-index: 10;
}

.emergency-label {
    position: absolute;
    bottom: -1.75rem;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s;
}

.floating-emergency-btn:hover .emergency-label {
    opacity: 1;
}

.emergency-label-content {
    padding: var(--space-xs) var(--space-sm);
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(20px);
}

.emergency-label-text {
    font-size: clamp(0.625rem, 2vw, 0.7rem);
    font-weight: 700;
    color: white;
}

/* ==================== DESKTOP LAYOUT ==================== */
.desktop-layout {
    display: none;
    width: 100%;
    max-width: 90rem;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-2xl);
}

@media (min-width: 1024px) {
    .desktop-layout {
        display: block;
    }
}

.desktop-header {
    margin-bottom: var(--space-2xl);
    opacity: 0;
}

.desktop-header-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--space-md);
    margin-bottom: var(--space-xl);
}

@media (min-width: 1024px) {
    .desktop-header-content {
        flex-direction: row;
        align-items: flex-end;
    }
}

.greeting-section {
    flex: 1;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-sm);
}

.status-pulse {
    position: relative;
    display: flex;
    height: clamp(0.5rem, 0.75vw, 0.625rem);
    width: clamp(0.5rem, 0.75vw, 0.625rem);
}

.status-ping {
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    position: absolute;
    display: inline-flex;
    height: 100%;
    width: 100%;
    border-radius: 9999px;
    background: rgb(74, 222, 128);
    opacity: 0.75;
}

.status-dot {
    position: relative;
    display: inline-flex;
    border-radius: 9999px;
    height: clamp(0.5rem, 0.75vw, 0.625rem);
    width: clamp(0.5rem, 0.75vw, 0.625rem);
    background: rgb(34, 197, 94);
    box-shadow: 0 0 8px rgba(74, 222, 128, 0.5);
}

.status-text {
    color: rgba(96, 165, 250, 0.7);
    font-size: clamp(0.625rem, 0.75vw, 0.7rem);
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.greeting-title {
    font-size: clamp(2.5rem, 5vw, 3rem);
    font-weight: 800;
    color: white;
    letter-spacing: -0.03em;
}

.greeting-name {
    background: linear-gradient(to right, rgb(96, 165, 250), rgb(168, 85, 247), rgb(236, 72, 153));
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-size: 200% auto;
    animation: gradient-x 3s ease infinite;
}

.desktop-clock {
    text-align: right;
}

.clock-time {
    font-size: clamp(3rem, 6vw, 3.75rem);
    font-weight: 300;
    color: white;
    letter-spacing: -0.03em;
    filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.5));
    display: flex;
    align-items: baseline;
    gap: var(--space-sm);
}

.clock-period {
    font-size: clamp(1.25rem, 2.5vw, 1.5rem);
    font-weight: 500;
    color: rgb(156, 163, 175);
}

.clock-date {
    color: rgba(255, 255, 255, 0.4);
    font-weight: 500;
    font-size: var(--fluid-xs);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-top: var(--space-xs);
}

/* Desktop Grid */
.desktop-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-xl);
}

@media (min-width: 1024px) {
    .desktop-grid {
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }
}

.desktop-left-column {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
}

@media (min-width: 1024px) {
    .desktop-left-column {
        grid-column: span 8 / span 8;
    }
}

.desktop-features-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--space-md);
}

@media (min-width: 1024px) {
    .desktop-features-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.desktop-feature-card {
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: var(--space-lg);
    border-radius: clamp(1.5rem, 2vw, 1.875rem);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    opacity: 0;
}

.desktop-feature-card:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.7);
}

.desktop-feature-card:active {
    transform: scale(0.95);
}

.desktop-feature-card:focus {
    outline: none;
}

.desktop-feature-card:focus-visible {
    outline: 2px solid white;
}

.desktop-feature-large {
    grid-column: span 2 / span 2;
    height: clamp(8rem, 20vw, 10rem);
}

.desktop-feature-medium {
    height: clamp(7rem, 18vw, 8rem);
}

.feature-pattern {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
}

.feature-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.08), transparent);
    opacity: 0;
    transition: opacity 0.5s;
}

.desktop-feature-card:hover .feature-shine {
    opacity: 1;
}

.feature-shine-rotate {
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    transition: transform 1s;
    background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
}

.desktop-feature-card:hover .feature-shine-rotate {
    transform: translateX(100%);
}

.feature-badge {
    position: absolute;
    top: var(--space-md);
    left: var(--space-md);
    z-index: 10;
    padding: var(--space-xs) var(--space-sm);
    border-radius: 9999px;
    font-size: clamp(0.625rem, 0.75vw, 0.7rem);
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.feature-history-link {
    position: absolute;
    top: var(--space-md);
    right: var(--space-md);
    z-index: 10;
    display: flex;
    align-items: center;
    gap: var(--space-xs);
    padding-left: var(--space-sm);
    padding-right: var(--space-xs);
    padding-top: var(--space-xs);
    padding-bottom: var(--space-xs);
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    font-size: clamp(0.625rem, 0.75vw, 0.7rem);
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(20px);
}

.feature-history-link:hover {
    background: rgba(0, 0, 0, 0.5);
    transform: scale(1.1);
}

.feature-history-icon {
    width: clamp(0.75rem, 1vw, 0.875rem);
    height: clamp(0.75rem, 1vw, 0.875rem);
    color: rgba(255, 255, 255, 0.7);
}

.feature-content {
    position: relative;
    z-index: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

.feature-icon {
    width: clamp(2.5rem, 4vw, 3rem);
    height: clamp(2.5rem, 4vw, 3rem);
    border-radius: clamp(1rem, 1.5vw, 1.25rem);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(1.25rem, 2vw, 1.5rem);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(20px);
}

.desktop-feature-card:hover .feature-icon {
    transform: scale(1.25) rotate(12deg);
}

.feature-text {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
}

.feature-subtitle {
    color: rgba(255, 255, 255, 0.6);
    font-size: var(--fluid-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.feature-title {
    font-weight: 900;
    color: white;
    font-size: clamp(1rem, 1.5vw, 1.25rem);
    line-height: 1.2;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5));
}

/* Desktop Sidebar */
.desktop-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
}

@media (min-width: 1024px) {
    .desktop-sidebar {
        grid-column: span 4 / span 4;
    }
}

.id-card {
    aspect-ratio: 1.6 / 1;
    border-radius: clamp(1.5rem, 2vw, 1.875rem);
    overflow: hidden;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.7);
    opacity: 0;
}

.id-card-content {
    height: 100%;
    padding: var(--space-xl);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, rgb(51, 65, 85), rgb(30, 41, 59), rgb(0, 0, 0));
}

.id-card-gradient {
    position: absolute;
    inset: 0;
    animation: gradient-rotate 20s ease-in-out infinite;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 51, 234, 0.05), transparent);
}

.id-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 10;
}

.id-card-logo {
    width: clamp(2rem, 3vw, 2.5rem);
    height: clamp(2rem, 3vw, 2.5rem);
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--fluid-lg);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
}

.id-card-number {
    text-align: right;
}

.id-card-label {
    font-size: clamp(0.563rem, 0.65vw, 0.625rem);
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 700;
}

.id-card-value {
    color: white;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: var(--fluid-sm);
    letter-spacing: 0.1em;
    opacity: 0.8;
}

.id-card-footer {
    position: relative;
    z-index: 10;
}

.id-card-name {
    font-size: clamp(1.25rem, 2vw, 1.5rem);
    color: white;
    font-weight: 900;
    letter-spacing: 0.025em;
    text-transform: uppercase;
    margin-bottom: var(--space-xs);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5));
}

.id-card-badges {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    flex-wrap: wrap;
}

.id-card-badge {
    padding: var(--space-xs) var(--space-sm);
    border-radius: 9999px;
    font-size: clamp(0.563rem, 0.65vw, 0.625rem);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    border: 1px solid;
}

.id-card-badge-primary {
    background: linear-gradient(to right, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.25));
    color: rgb(147, 197, 253);
    border-color: rgba(59, 130, 246, 0.4);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.id-card-badge-success {
    background: linear-gradient(to right, rgba(34, 197, 94, 0.25), rgba(22, 163, 74, 0.25));
    color: rgb(134, 239, 172);
    border-color: rgba(34, 197, 94, 0.4);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
}

.sidebar-card {
    display: block;
    border-radius: clamp(1.5rem, 2vw, 1.875rem);
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    height: clamp(8rem, 20vw, 10rem);
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    opacity: 0;
}

.sidebar-card:hover {
    transform: scale(1.05);
}

.sidebar-card-content {
    height: 100%;
    padding: var(--space-xl);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

.sidebar-card-polls .sidebar-card-content {
    background: linear-gradient(135deg, rgb(202, 138, 4), rgb(161, 98, 7));
}

.sidebar-card-emergency .sidebar-card-content {
    background: linear-gradient(135deg, rgb(220, 38, 38), rgb(185, 28, 28));
}

.sidebar-card-pattern {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
}

.sidebar-card-shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.08), transparent);
    opacity: 0;
    transition: opacity 0.5s;
}

.sidebar-card:hover .sidebar-card-shine {
    opacity: 1;
}

.sidebar-card-header {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    z-index: 10;
}

.sidebar-card-icon {
    width: clamp(2.5rem, 4vw, 3rem);
    height: clamp(2.5rem, 4vw, 3rem);
    border-radius: clamp(1rem, 1.5vw, 1.25rem);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(1.25rem, 2vw, 1.5rem);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.25);
}

.sidebar-card-badge {
    font-size: clamp(0.625rem, 0.75vw, 0.7rem);
    font-weight: 900;
    padding: var(--space-xs) var(--space-sm);
    border-radius: clamp(0.375rem, 0.5vw, 0.5rem);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    background: rgb(255, 255, 255);
}

.sidebar-card-polls .sidebar-card-badge {
    color: rgb(161, 98, 7);
}

.sidebar-emergency-badge {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    background: rgb(255, 255, 255);
    color: rgb(185, 28, 28);
}

.sidebar-card-footer {
    position: relative;
    z-index: 10;
}

.sidebar-card-title {
    font-weight: 900;
    color: white;
    font-size: clamp(1.25rem, 2vw, 1.5rem);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5));
}

.sidebar-card-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: var(--fluid-xs);
    font-weight: 700;
    margin-top: var(--space-xs);
}

.sidebar-emergency-pulse-1,
.sidebar-emergency-pulse-2 {
    position: absolute;
    top: -0.5rem;
    right: -0.5rem;
    width: clamp(1.25rem, 2vw, 1.5rem);
    height: clamp(1.25rem, 2vw, 1.5rem);
    border-radius: 9999px;
    animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

.sidebar-emergency-pulse-1 {
    background: rgba(255, 255, 255, 0.3);
}

.sidebar-emergency-pulse-2 {
    background: rgba(255, 255, 255, 0.2);
    animation-delay: 0.5s;
}

.sidebar-map-card {
    position: relative;
    border-radius: clamp(1.5rem, 2vw, 1.875rem);
    padding: var(--space-xs);
    height: clamp(18rem, 40vw, 20rem);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.7);
    opacity: 0;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(40px);
    border: 1px solid rgba(255, 255, 255, 0.10);
}

.sidebar-map-badge {
    position: absolute;
    top: var(--space-md);
    left: var(--space-md);
    z-index: 10;
    padding: var(--space-xs) var(--space-sm);
    border-radius: 9999px;
    font-size: var(--fluid-xs);
    font-weight: 700;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(20px);
}

.sidebar-map-wrapper {
    width: 100%;
    height: 100%;
    border-radius: clamp(1.4rem, 1.9vw, 1.8rem);
    overflow: hidden;
    opacity: 0.9;
    transition: opacity 0.5s;
}

.sidebar-map-card:hover .sidebar-map-wrapper {
    opacity: 1;
}

/* ==================== ANIMATIONS ==================== */
@keyframes gradient-rotate {
    0%, 100% { 
        transform: rotate(0deg) scale(1); 
    }
    50% { 
        transform: rotate(180deg) scale(1.1); 
    }
}

@keyframes gradient-x {
    0%, 100% { 
        background-position: 0% 50%; 
    }
    50% { 
        background-position: 100% 50%; 
    }
}

@keyframes fade-in {
    from { 
        opacity: 0; 
        transform: translateY(-20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.animate-fade-in {
    animation: fade-in 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-scale-in {
    animation: scale-in 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slide-up 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes slide-right {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-slide-right {
    animation: slide-right 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes scale-fade {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-scale-fade {
    animation: scale-fade 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 640px) {
    .time-value {
        font-size: clamp(1.75rem, 7vw, 2rem);
    }
    
    .date-badge {
        padding: var(--space-sm) var(--space-md);
    }
    
    .date-badge-day {
        font-size: clamp(1rem, 4vw, 1.25rem);
    }
    
    .apps-grid {
        gap: var(--space-sm);
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

@media (hover: none) and (pointer: coarse) {
    button:active,
    a:active {
        transform: scale(0.95);
    }
}

* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

*:focus {
    outline: none;
}

*:focus-visible {
    outline: 2px solid rgb(59, 130, 246);
    outline-offset: 2px;
    border-radius: 0.5rem;
}
</style>
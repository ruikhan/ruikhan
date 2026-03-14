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

const notificationCount = ref();

// Enhanced particle system
const particles = ref([]);
const connectingLines = ref([]);

// Enhanced theme with more gradients
const themeColor = computed(() => {
    const hour = currentTime.value.getHours();
    if (hour >= 5 && hour < 12) return { 
        from: '#3b82f6', to: '#60a5fa', accent: '#06b6d4',
        name: 'morning', glow: 'rgba(59, 130, 246, 0.4)'
    };
    if (hour >= 12 && hour < 17) return { 
        from: '#f59e0b', to: '#fbbf24', accent: '#fb923c',
        name: 'afternoon', glow: 'rgba(245, 158, 11, 0.4)'
    };
    if (hour >= 17 && hour < 21) return { 
        from: '#8b5cf6', to: '#a78bfa', accent: '#c084fc',
        name: 'evening', glow: 'rgba(139, 92, 246, 0.4)'
    };
    return { 
        from: '#6366f1', to: '#818cf8', accent: '#4f46e5',
        name: 'night', glow: 'rgba(99, 102, 241, 0.4)'
    };
});

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

const quickStats = ref([
    { icon: '📄', label: 'Active', value: '...', color: 'from-blue-500 to-blue-600', loaded: false },
    { icon: '✅', label: 'Done', value: '...', color: 'from-green-500 to-green-600', loaded: false },
    { icon: '📊', label: 'Total', value: '...', color: 'from-purple-500 to-purple-600', loaded: false }
]);

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

const mobileApps = [
    { icon: '⚡', name: 'Services', link: route('services.landing'), gradient: 'from-blue-500 to-indigo-600', description: 'Quick access' },
    { icon: '💳', name: 'Bills', link: route('bills.create'), gradient: 'from-green-500 to-emerald-600', description: 'Pay bills' },
    { icon: '🩺', name: 'Health', link: route('health.index'), gradient: 'from-red-500 to-rose-600', description: 'Medical care' },
    { icon: '🏪', name: 'Market', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', description: 'Shop local' },
    { icon: '📊', name: isBusinessOwner.value ? 'Business' : 'Start', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: 'from-orange-500 to-amber-600', description: 'Manage' },
    { icon: '🚨', name: 'Report', link: null, action: 'wizard', gradient: 'from-red-600 to-pink-700', description: 'Get help' },
];

const desktopFeatures = [
    { title: 'Smart Services', subtitle: 'Permits & Clearances', icon: '⚡', link: route('services.landing'), historyLink: route('services.index'), gradient: 'from-blue-500 to-indigo-600', size: 'large', description: 'Fast digital processing' },
    { title: 'Bills & Fees', subtitle: 'Secure Payments', icon: '💳', link: route('bills.create'), gradient: 'from-teal-500 to-emerald-600', size: 'medium', description: 'Pay online instantly' },
    { title: 'E-Health', subtitle: 'Telemed & Booking', icon: '🩺', link: route('health.index'), gradient: 'from-sky-500 to-cyan-600', size: 'medium', description: 'Healthcare at home' },
    { title: 'Issue Report', subtitle: 'Quick Response', icon: '🚨', link: null, action: 'wizard', gradient: 'from-red-500 to-pink-600', size: 'medium', description: 'Report problems' },
    { title: 'Marketplace', subtitle: 'Local Businesses', icon: '🌐', link: route('marketplace.index'), gradient: 'from-purple-500 to-fuchsia-600', size: 'large', description: 'Support local economy' },
    { title: isBusinessOwner.value ? 'My Business' : 'Business Hub', subtitle: isBusinessOwner.value ? 'Dashboard' : 'Register Now', icon: isBusinessOwner.value ? '📊' : '🏪', link: isBusinessOwner.value ? route('business.dashboard') : route('business.register'), gradient: isBusinessOwner.value ? 'from-blue-500 to-indigo-600' : 'from-orange-500 to-amber-600', size: 'medium', description: isBusinessOwner.value ? 'Manage business' : 'Start your journey' },
];

// Enhanced particle generation with connections
const generateParticles = () => {
    particles.value = Array.from({ length: 30 }, (_, i) => ({
        id: i,
        x: Math.random() * 100,
        y: Math.random() * 100,
        size: Math.random() * 3 + 1,
        duration: Math.random() * 15 + 15,
        delay: Math.random() * 5,
        speedX: (Math.random() - 0.5) * 0.5,
        speedY: (Math.random() - 0.5) * 0.5,
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
    
    setTimeout(() => mounted.value = true, 100);
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
        
        <!-- Enhanced Floating Particles with Glow -->
        <div class="particles-container">
            <div 
                v-for="particle in particles" 
                :key="particle.id" 
                class="particle-enhanced"
                :style="{
                    left: particle.x + '%',
                    top: particle.y + '%',
                    width: particle.size + 'px',
                    height: particle.size + 'px',
                    animationDuration: particle.duration + 's',
                    animationDelay: particle.delay + 's',
                    '--speed-x': particle.speedX,
                    '--speed-y': particle.speedY
                }"
            >
                <div class="particle-glow" :style="{ background: themeColor.glow }"></div>
            </div>
        </div>

        <!-- Mesh Gradient Background -->
        <div class="mesh-background">
            <div class="mesh-orb mesh-orb-1" :style="{ background: `radial-gradient(circle, ${themeColor.from}40, transparent)` }"></div>
            <div class="mesh-orb mesh-orb-2" :style="{ background: `radial-gradient(circle, ${themeColor.to}30, transparent)` }"></div>
            <div class="mesh-orb mesh-orb-3" :style="{ background: `radial-gradient(circle, ${themeColor.accent}20, transparent)` }"></div>
        </div>

        <!-- Ultra Enhanced Toast -->
        <Transition name="toast">
            <div v-if="showFlash && flashSuccess" class="toast-ultra">
                <div class="toast-background"></div>
                <div class="toast-shine"></div>
                <div class="toast-icon-wrap-ultra">
                    <div class="toast-icon-glow"></div>
                    <svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="toast-content">
                    <p class="toast-title">Success!</p>
                    <p class="toast-text">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="toast-close-ultra">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </Transition>

        <!-- MOBILE LAYOUT -->
        <div class="mobile-layout">
            <!-- Ultra Enhanced Logo Header -->
            <div class="logo-header-ultra" :class="{ 'show': mounted }">
                <div class="logo-container-ultra">
                    <div class="logo-icon-ultra" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                        <span class="logo-emoji">🏛️</span>
                        <div class="logo-glow-ultra"></div>
                        <div class="logo-ring"></div>
                    </div>
                    <div class="logo-text-ultra">
                        <h1 class="logo-title-ultra">CityConnect</h1>
                        <p class="logo-subtitle-ultra">Smart City Portal</p>
                    </div>
                </div>
            </div>

            <!-- Ultra Enhanced Time Widget -->
            <div class="time-widget-ultra" :class="{ 'show': mounted }">
                <div class="time-bg-mesh" :style="{ background: `radial-gradient(ellipse at top left, ${themeColor.from}20, transparent)` }"></div>
                <div class="time-content-glass">
                    <div class="time-info-enhanced">
                        <p class="time-label-ultra">{{ date }}</p>
                        <div class="time-display-ultra">
                            <h2 class="time-value-ultra">{{ time }}</h2>
                            <span class="time-period-ultra">{{ dayPeriod }}</span>
                        </div>  
                    </div>
                    
                    <Link :href="route('notifications.index')" class="notification-button-ultra">
                        <div class="notification-icon-container" :style="{ background: `linear-gradient(135deg, ${themeColor.from}60, ${themeColor.to}60)` }">
                            <span class="notification-emoji">📬</span>
                            <span v-if="notificationCount > 0" class="notification-badge-ultra">
                                {{ notificationCount }}
                            </span>
                            <div class="notification-pulse"></div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Ultra Enhanced Stats Grid -->
            <div class="stats-grid-ultra">
                <div v-for="(stat, i) in quickStats" :key="stat.label"
                    class="stat-card-ultra" :class="{ 'show': mounted, 'loaded': stat.loaded }"
                    :style="`animation-delay: ${i * 100}ms`">
                    <div class="stat-glass-bg"></div>
                    <div class="stat-border-glow" :style="{ background: `linear-gradient(135deg, ${themeColor.from}40, ${themeColor.to}40)` }"></div>
                    <div :class="`stat-icon-container bg-gradient-to-br ${stat.color}`">
                        <span class="stat-icon-ultra">{{ stat.icon }}</span>
                        <div class="stat-icon-shine"></div>
                    </div>
                    <div class="stat-info-ultra">
                        <p class="stat-value-ultra">{{ stat.value }}</p>
                        <p class="stat-label-ultra">{{ stat.label }}</p>
                    </div>
                    <div class="stat-wave"></div>
                </div>
            </div>

            <!-- Ultra Enhanced Search Bar -->
            <div class="search-wrapper-ultra" :class="{ 'show': mounted }">
                <div class="search-glass-ultra">
                    <div class="search-border-gradient"></div>
                    <svg class="search-icon-ultra" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Search services, bills, documents..." class="search-input-ultra">
                    <button class="search-button-ultra" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                        <span>⚡</span>
                        <div class="button-glow"></div>
                    </button>
                </div>
            </div>

            <!-- Ultra Enhanced Apps Section -->
            <div class="apps-section-ultra">
                <div class="apps-header-ultra" :class="{ 'show': mounted }">
                    <div>
                        <h3 class="apps-title-ultra">Quick Access</h3>
                        <p class="apps-subtitle-ultra">Your most used services</p>
                    </div>
                </div>
                <div class="apps-grid-ultra">
                    <component :is="app.link ? Link : 'button'"
                        v-for="(app, i) in mobileApps" :key="app.name"
                        :href="app.link" @click="app.action ? handleAppClick(app) : null"
                        class="app-card-ultra" :class="{ 'show': mounted }"
                        :style="`animation-delay: ${i * 60}ms`">
                        <div class="app-card-glass"></div>
                        <div class="app-card-border"></div>
                        <div :class="`app-icon-container bg-gradient-to-br ${app.gradient}`">
                            <span class="app-icon-ultra">{{ app.icon }}</span>
                            <div class="app-icon-glow-ultra"></div>
                        </div>
                        <div class="app-info-ultra">
                            <span class="app-name-ultra">{{ app.name }}</span>
                            <span class="app-description-ultra">{{ app.description }}</span>
                        </div>
                        <div class="app-shimmer-ultra"></div>
                    </component>
                </div>
            </div>

            <!-- Ultra Enhanced Map Card -->
            <div class="map-card-ultra" :class="{ 'show': mounted }">
                <div class="map-glass-overlay"></div>
                <div class="map-header-ultra">
                    <div class="map-badge-ultra" :style="{ background: `linear-gradient(135deg, ${themeColor.from}80, ${themeColor.to}80)` }">
                        <span class="badge-dot-ultra"></span>
                        <span class="badge-text-ultra">Live Map</span>
                    </div>
                    <Link :href="route('establishments.index')" class="map-expand-ultra">
                        <div class="expand-glow"></div>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </Link>
                </div>
                <div class="map-wrap-ultra">
                    <DashboardMap />
                </div>
            </div>

            <!-- Ultra Enhanced Emergency FAB -->
            <Link :href="route('emergency.index')" class="emergency-fab-ultra">
                <div class="fab-button-ultra">
                    <span class="fab-icon-ultra">🚨</span>
                    <div class="fab-ring-ultra"></div>
                    <div class="fab-glow-ultra"></div>
                    <div class="fab-pulse-wave"></div>
                </div>
                <span class="fab-label-ultra">Emergency</span>
            </Link>
        </div>

        <!-- DESKTOP LAYOUT -->
        <div class="desktop-layout">
            <!-- Ultra Enhanced Desktop Logo Header -->
            <div class="desktop-logo-header-ultra" :class="{ 'show': mounted }">
                <div class="desktop-logo-container-ultra">
                    <div class="desktop-logo-icon-ultra" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                        🏛️
                        <div class="desktop-logo-glow-ultra"></div>
                        <div class="desktop-logo-ring"></div>
                    </div>
                    <div class="desktop-logo-text-ultra">
                        <h1 class="desktop-logo-title-ultra">CityConnect</h1>
                        <p class="desktop-logo-subtitle-ultra">Smart City Management Portal</p>
                    </div>
                </div>
                <Link :href="route('emergency.index')" class="emergency-button-desktop-ultra">
                    <div class="emergency-bg-gradient"></div>
                    <span class="emergency-icon-ultra">🚨</span>
                    <span class="emergency-text-ultra">Emergency</span>
                    <div class="emergency-pulse-ultra"></div>
                </Link>
            </div>
            
            <!-- Ultra Enhanced Desktop Header -->
            <header class="desktop-header-ultra" :class="{ 'show': mounted }">
                <div class="header-content-ultra">
                    <div class="greeting-wrap-ultra">
                        <h1 class="greeting-text-ultra">
                            {{ greeting }}, <span class="greeting-name-ultra">{{ user.name }}</span>!
                        </h1>
                        <p class="greeting-subtitle-ultra">{{ date }}</p>
                    </div>
                    <div class="clock-display-ultra">
                        <div class="clock-decoration-ultra" :style="{ background: `radial-gradient(ellipse, ${themeColor.from}40, transparent)` }"></div>
                        <div class="clock-time-ultra">
                            <span class="clock-digits-ultra">{{ time }}</span>
                            <span class="clock-period-ultra">{{ dayPeriod }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="desktop-grid">
                <div class="desktop-main">
                    <!-- Ultra Enhanced Features Grid -->
                    <div class="features-grid-ultra">
                        <component :is="feature.link ? Link : 'button'"
                            v-for="(feature, i) in desktopFeatures" :key="feature.title"
                            :href="feature.link" @click="feature.action === 'wizard' ? showWizard = true : null"
                            :class="['feature-card-ultra', `bg-gradient-to-br ${feature.gradient}`,
                                { 'feature-large': feature.size === 'large',
                                  'feature-medium': feature.size === 'medium',
                                  'show': mounted }]"
                            :style="`animation-delay: ${i * 80}ms`">
                            <div class="feature-glass-overlay"></div>
                            <div class="feature-mesh-bg"></div>
                            <Link v-if="feature.historyLink" :href="feature.historyLink" @click.stop class="feature-history-ultra">
                                <div class="history-glow"></div>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>History</span>
                            </Link>
                            <div class="feature-content-ultra">
                                <div class="feature-header-ultra">
                                    <div class="feature-icon-wrap-ultra">
                                        <span class="feature-icon-ultra">{{ feature.icon }}</span>
                                        <div class="feature-icon-glow-ultra"></div>
                                    </div>
                                    <div class="feature-text-ultra">
                                        <p class="feature-subtitle-ultra">{{ feature.subtitle }}</p>
                                        <h3 class="feature-title-ultra">{{ feature.title }}</h3>
                                    </div>
                                </div>
                                <p class="feature-description-ultra">{{ feature.description }}</p>
                            </div>
                            <div class="feature-shine-ultra"></div>
                        </component>
                    </div>
                </div>

                <aside class="desktop-sidebar">
                    <!-- Ultra Enhanced Stats Card -->
                    <div class="sidebar-card-ultra stats-card-ultra" :class="{ 'show': mounted }">
                        <div class="sidebar-glass-bg"></div>
                        <div class="card-header-ultra">
                            <h3 class="card-title-ultra">Quick Stats</h3>
                            <div class="card-icon-ultra" :style="{ background: `linear-gradient(135deg, ${themeColor.from}, ${themeColor.to})` }">
                                📊
                                <div class="card-icon-glow"></div>
                            </div>
                        </div>
                        <div class="stats-list-ultra">
                            <div v-for="stat in quickStats" :key="stat.label" class="stat-item-ultra" :class="{ 'loaded': stat.loaded }">
                                <div :class="`stat-item-icon-ultra bg-gradient-to-br ${stat.color}`">
                                    {{ stat.icon }}
                                    <div class="stat-item-icon-glow"></div>
                                </div>
                                <div class="stat-item-info-ultra">
                                    <p class="stat-item-value-ultra">{{ stat.value }}</p>
                                    <p class="stat-item-label-ultra">{{ stat.label }}</p>
                                </div>
                                <div class="stat-item-trend-ultra">↗</div>
                            </div>
                        </div>
                    </div>

                    <!-- Ultra Enhanced Desktop Map -->
                    <div class="sidebar-map-ultra" :class="{ 'show': mounted }" style="animation-delay: 150ms">
                        <div class="sidebar-map-glass"></div>
                        <div class="map-header-ultra">
                            <div class="map-badge-desk-ultra" :style="{ background: `linear-gradient(135deg, ${themeColor.from}80, ${themeColor.to}80)` }">
                                <span class="badge-dot-ultra"></span>
                                📍 Live Map
                            </div>
                            <Link :href="route('establishments.index')" class="map-expand-desk-ultra">
                                <div class="expand-glow"></div>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                            </Link>
                        </div>
                        <div class="map-wrapper-ultra">
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

* { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Enhanced Floating Particles with Glow */
.particles-container {
    position: fixed;
    inset: 0;
    z-index: 2;
    pointer-events: none;
    overflow: hidden;
}

.particle-enhanced {
    position: absolute;
    border-radius: 50%;
    animation: float-particle-enhanced ease-in-out infinite;
    will-change: transform, opacity;
}

.particle-glow {
    position: absolute;
    inset: -200%;
    border-radius: 50%;
    filter: blur(20px);
    opacity: 0.6;
}

@keyframes float-particle-enhanced {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0;
    }
    5% {
        opacity: 0.8;
    }
    95% {
        opacity: 0.8;
    }
    100% {
        transform: translate(calc(var(--speed-x) * 100vw), calc(-120vh + var(--speed-y) * 100vh)) scale(1.5);
        opacity: 0;
    }
}

/* Mesh Gradient Background */
.mesh-background {
    position: fixed;
    inset: 0;
    z-index: 1;
    pointer-events: none;
    overflow: hidden;
}

.mesh-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    animation: mesh-float 20s ease-in-out infinite;
}

.mesh-orb-1 {
    top: 10%;
    left: 10%;
    width: 600px;
    height: 600px;
    animation-delay: 0s;
}

.mesh-orb-2 {
    bottom: 10%;
    right: 10%;
    width: 700px;
    height: 700px;
    animation-delay: 5s;
}

.mesh-orb-3 {
    top: 50%;
    left: 50%;
    width: 500px;
    height: 500px;
    animation-delay: 10s;
}

@keyframes mesh-float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(50px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-30px, 40px) scale(0.9);
    }
}

/* Ultra Enhanced Toast */
.toast-ultra {
    position: fixed;
    top: 5.5rem;
    right: 1rem;
    z-index: 60;
    width: calc(100vw - 2rem);
    max-width: 24rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 1.25rem;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95));
    backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    overflow: hidden;
}

@media (min-width: 1024px) {
    .toast-ultra {
        top: 6rem;
        right: 2rem;
    }
}

.toast-background {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
    opacity: 0.5;
}

.toast-shine {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: toast-shine 3s ease-in-out infinite;
}

@keyframes toast-shine {
    to {
        transform: translateX(100%) translateY(100%);
    }
}

.toast-icon-wrap-ultra {
    position: relative;
    width: 3rem;
    height: 3rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.toast-icon-glow {
    position: absolute;
    inset: -4px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.6), transparent);
    border-radius: 50%;
    filter: blur(12px);
    animation: icon-glow-pulse 2s ease-in-out infinite;
}

@keyframes icon-glow-pulse {
    0%, 100% {
        opacity: 0.6;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}

.toast-icon {
    width: 1.75rem;
    height: 1.75rem;
    color: white;
    position: relative;
    z-index: 1;
}

.toast-content {
    flex: 1;
    min-width: 0;
}

.toast-title {
    font-size: 1rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.25rem;
    letter-spacing: -0.01em;
}

.toast-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.95);
}

.toast-close-ultra {
    width: 2.25rem;
    height: 2.25rem;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: white;
    transition: all 0.2s;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.toast-close-ultra:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.toast-close-ultra svg {
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

/* Ultra Enhanced Mobile Logo Header */
.logo-header-ultra {
    margin-bottom: 1.25rem;
    opacity: 0;
    transform: translateY(-30px) scale(0.95);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.logo-header-ultra.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.logo-container-ultra {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 1rem;
}

.logo-icon-ultra {
    position: relative;
    width: 4rem;
    height: 4rem;
    border-radius: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(20px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.logo-glow-ultra {
    position: absolute;
    inset: -6px;
    border-radius: 1.5rem;
    background: inherit;
    filter: blur(20px);
    opacity: 0.7;
    z-index: -1;
    animation: logo-glow-pulse 3s ease-in-out infinite;
}

@keyframes logo-glow-pulse {
    0%, 100% {
        opacity: 0.7;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
}

.logo-ring {
    position: absolute;
    inset: -10px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 1.75rem;
    animation: logo-ring-spin 8s linear infinite;
}

@keyframes logo-ring-spin {
    to {
        transform: rotate(360deg);
    }
}

.logo-emoji {
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.logo-text-ultra {
    text-align: center;
}

.logo-title-ultra {
    font-size: 1.5rem;
    font-weight: 900;
    color: white;
    letter-spacing: -0.03em;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.logo-subtitle-ultra {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.65);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-top: 0.25rem;
}

/* Ultra Enhanced Time Widget */
.time-widget-ultra {
    position: relative;
    margin-bottom: 1.5rem;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: 0.1s;
}

.time-widget-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.time-bg-mesh {
    position: absolute;
    inset: -3rem;
    filter: blur(80px);
    opacity: 0.4;
    animation: mesh-pulse 5s ease-in-out infinite;
}

@keyframes mesh-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.4;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.6;
    }
}

.time-content-glass {
    position: relative;
    border-radius: 2rem;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}

.time-content-glass::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.time-content-glass:hover::before {
    opacity: 1;
}

.time-info-enhanced {
    flex: 1;
}

.time-label-ultra {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8125rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.time-display-ultra {
    display: flex;
    align-items: baseline;
    gap: 0.75rem;
}

.time-value-ultra {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    line-height: 1;
    letter-spacing: -0.03em;
    text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.time-period-ultra {
    font-size: 1.125rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.6);
}

/* Ultra Enhanced Notification Button */
.notification-button-ultra {
    flex-shrink: 0;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.notification-button-ultra:active {
    transform: scale(0.92);
}

.notification-icon-container {
    position: relative;
    width: 4.5rem;
    height: 4rem;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(20px);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.notification-button-ultra:hover .notification-icon-container {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
}

.notification-emoji {
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.notification-badge-ultra {
    position: absolute;
    top: -0.5rem;
    right: -0.5rem;
    min-width: 1.5rem;
    height: 1.5rem;
    padding: 0 0.5rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6875rem;
    font-weight: 900;
    color: white;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: 2px solid #1c1c1e;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
    animation: badge-pulse-ultra 2s ease-in-out infinite;
}

@keyframes badge-pulse-ultra {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5), 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    50% {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.6), 0 0 0 6px rgba(239, 68, 68, 0);
    }
}

.notification-pulse {
    position: absolute;
    inset: -4px;
    border-radius: 1.25rem;
    background: inherit;
    filter: blur(16px);
    opacity: 0.5;
    animation: notification-pulse-anim 3s ease-in-out infinite;
}

@keyframes notification-pulse-anim {
    0%, 100% {
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

/* Ultra Enhanced Stats Grid */
.stats-grid-ultra {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card-ultra {
    position: relative;
    border-radius: 1.5rem;
    padding: 1.25rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.stat-card-ultra.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.stat-glass-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.stat-card-ultra:hover .stat-glass-bg {
    opacity: 1;
}

.stat-border-glow {
    position: absolute;
    inset: -2px;
    border-radius: 1.5rem;
    filter: blur(12px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.stat-card-ultra:hover .stat-border-glow {
    opacity: 1;
}

.stat-icon-container {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    overflow: hidden;
}

.stat-icon-shine {
    position: absolute;
    inset: -4px;
    border-radius: 1rem;
    background: inherit;
    filter: blur(12px);
    opacity: 0.6;
    z-index: -1;
}

.stat-icon-ultra {
    font-size: 1.5rem;
    position: relative;
    z-index: 1;
}

.stat-value-ultra {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    margin-bottom: 0.375rem;
    line-height: 1;
    letter-spacing: -0.02em;
    transition: all 0.3s ease;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.stat-card-ultra.loaded .stat-value-ultra {
    animation: count-up-ultra 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes count-up-ultra {
    from {
        transform: translateY(15px) scale(0.8);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

.stat-label-ultra {
    font-size: 0.6875rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.stat-wave {
    position: absolute;
    bottom: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
    animation: wave-slide 3s ease-in-out infinite;
}

@keyframes wave-slide {
    to {
        left: 100%;
    }
}

/* Ultra Enhanced Search Bar */
.search-wrapper-ultra {
    margin-bottom: 2rem;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: 0.3s;
}

.search-wrapper-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.search-glass-ultra {
    position: relative;
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    padding: 0 0.75rem 0 1.25rem;
    gap: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.search-glass-ultra:focus-within {
    border-color: rgba(255, 255, 255, 0.25);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4), 0 0 0 4px rgba(255, 255, 255, 0.08);
}

.search-border-gradient {
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.5), rgba(168, 85, 247, 0.5));
    border-radius: 1.5rem;
    opacity: 0;
    filter: blur(12px);
    transition: opacity 0.3s;
    z-index: -1;
}

.search-glass-ultra:focus-within .search-border-gradient {
    opacity: 1;
}

.search-icon-ultra {
    width: 1.5rem;
    height: 1.5rem;
    color: rgba(255, 255, 255, 0.6);
    flex-shrink: 0;
    transition: color 0.3s;
}

.search-glass-ultra:focus-within .search-icon-ultra {
    color: rgba(255, 255, 255, 0.9);
}

.search-input-ultra {
    flex: 1;
    background: transparent;
    color: white;
    font-size: 0.9375rem;
    font-weight: 500;
    padding: 1.25rem 0;
    outline: none;
    border: none;
}

.search-input-ultra::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

.search-button-ultra {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.button-glow {
    position: absolute;
    inset: -4px;
    background: inherit;
    filter: blur(16px);
    opacity: 0.6;
    z-index: -1;
}

.search-button-ultra:active {
    transform: scale(0.92);
}

/* Ultra Enhanced Apps Section */
.apps-section-ultra {
    margin-bottom: 2rem;
}

.apps-header-ultra {
    margin-bottom: 1.25rem;
    padding: 0 0.25rem;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: 0.4s;
}

.apps-header-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.apps-title-ultra {
    font-size: 1.25rem;
    font-weight: 900;
    color: white;
    margin-bottom: 0.375rem;
    letter-spacing: -0.02em;
}

.apps-subtitle-ultra {
    font-size: 0.8125rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 600;
}

.apps-grid-ultra {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.app-card-ultra {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 0.75rem;
    border-radius: 1.5rem;
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.15);
    opacity: 0;
    transform: translateY(30px) scale(0.9);
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.app-card-ultra.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.app-card-ultra:active {
    transform: scale(0.92);
}

.app-card-glass {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.app-card-ultra:hover .app-card-glass {
    opacity: 1;
}

.app-card-border {
    position: absolute;
    inset: -2px;
    border-radius: 1.5rem;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.5), rgba(168, 85, 247, 0.5));
    filter: blur(12px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.app-card-ultra:hover .app-card-border {
    opacity: 1;
}

.app-icon-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.375rem;
    overflow: hidden;
}

.app-icon-glow-ultra {
    position: absolute;
    inset: -6px;
    border-radius: 1.25rem;
    background: inherit;
    filter: blur(20px);
    opacity: 0.7;
    z-index: -1;
}

.app-icon-ultra {
    font-size: 2.5rem;
    position: relative;
    z-index: 1;
}

.app-info-ultra {
    text-align: center;
    width: 100%;
}

.app-name-ultra {
    display: block;
    font-size: 0.8125rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.25rem;
    letter-spacing: -0.01em;
}

.app-description-ultra {
    display: block;
    font-size: 0.6875rem;
    color: rgba(255, 255, 255, 0.6);
    font-weight: 600;
}

.app-shimmer-ultra {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    opacity: 0;
    animation: shimmer-ultra 2.5s ease-in-out infinite;
}

@keyframes shimmer-ultra {
    to {
        transform: translateX(100%) translateY(100%);
    }
}

/* Ultra Enhanced Map Card */
.map-card-ultra {
    position: relative;
    overflow: hidden;
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    margin-bottom: 2rem;
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: 0.5s;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.map-card-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.map-glass-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), transparent);
    pointer-events: none;
    z-index: 1;
}

.map-header-ultra {
    position: absolute;
    top: 1rem;
    left: 1rem;
    right: 1rem;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.map-badge-ultra {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 1.125rem;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(30px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.badge-dot-ultra {
    width: 0.625rem;
    height: 0.625rem;
    background: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 12px rgba(16, 185, 129, 0.8);
    animation: pulse-dot-ultra 2.5s ease-in-out infinite;
}

@keyframes pulse-dot-ultra {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 12px rgba(16, 185, 129, 0.8), 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
        opacity: 0.6;
        transform: scale(0.9);
        box-shadow: 0 0 16px rgba(16, 185, 129, 1), 0 0 0 6px rgba(16, 185, 129, 0);
    }
}

.badge-text-ultra {
    font-size: 0.8125rem;
    font-weight: 800;
    color: white;
}

.map-expand-ultra {
    position: relative;
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(30px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: white;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.expand-glow {
    position: absolute;
    inset: -4px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
    filter: blur(12px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.map-expand-ultra:active {
    transform: scale(0.9);
}

.map-expand-ultra:hover .expand-glow {
    opacity: 1;
}

.map-expand-ultra svg {
    width: 1.125rem;
    height: 1.125rem;
}

.map-wrap-ultra {
    width: 100%;
    height: 16rem;
    border-radius: 2rem;
    overflow: hidden;
}

/* Ultra Enhanced Emergency FAB */
.emergency-fab-ultra {
    position: fixed;
    bottom: 6rem;
    right: 1rem;
    z-index: 40;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.625rem;
}

.fab-button-ultra {
    position: relative;
    width: 4rem;
    height: 4rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(220, 38, 38));
    border: 3px solid rgba(0, 0, 0, 0.3);
    box-shadow: 0 15px 40px rgba(239, 68, 68, 0.6);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.emergency-fab-ultra:active .fab-button-ultra {
    transform: scale(0.9);
}

.fab-ring-ultra {
    position: absolute;
    inset: -6px;
    border-radius: 9999px;
    border: 3px solid rgb(239, 68, 68);
    animation: fab-ring-ultra 2.5s ease-in-out infinite;
}

@keyframes fab-ring-ultra {
    0% {
        transform: scale(1);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.6);
        opacity: 0;
    }
}

.fab-glow-ultra {
    position: absolute;
    inset: -12px;
    border-radius: 9999px;
    background: radial-gradient(circle, rgb(239, 68, 68), transparent);
    filter: blur(24px);
    opacity: 0.7;
    z-index: -1;
}

.fab-pulse-wave {
    position: absolute;
    inset: 0;
    border-radius: 9999px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
    animation: fab-pulse-wave 3s ease-in-out infinite;
}

@keyframes fab-pulse-wave {
    0%, 100% {
        transform: scale(0.8);
        opacity: 0;
    }
    50% {
        transform: scale(1.3);
        opacity: 1;
    }
}

.fab-icon-ultra {
    font-size: 1.75rem;
    position: relative;
    z-index: 1;
    animation: pulse 2.5s ease-in-out infinite;
}

.fab-label-ultra {
    font-size: 0.6875rem;
    font-weight: 800;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-shadow: 0 3px 6px rgba(0, 0, 0, 0.6);
}

/* Ultra Enhanced Desktop Logo Header */
.desktop-logo-header-ultra {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2.5rem;
    opacity: 0;
    transform: translateY(-30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.desktop-logo-header-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.desktop-logo-container-ultra {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.desktop-logo-icon-ultra {
    position: relative;
    width: 4.5rem;
    height: 4.5rem;
    font-size: 2.25rem;
    border-radius: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(20px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
}

.desktop-logo-glow-ultra {
    position: absolute;
    inset: -8px;
    border-radius: 1.75rem;
    background: inherit;
    filter: blur(24px);
    opacity: 0.7;
    z-index: -1;
    animation: logo-glow-desktop 3.5s ease-in-out infinite;
}

@keyframes logo-glow-desktop {
    0%, 100% {
        transform: scale(1);
        opacity: 0.7;
    }
    50% {
        transform: scale(1.15);
        opacity: 1;
    }
}

.desktop-logo-ring {
    position: absolute;
    inset: -12px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 2rem;
    animation: desktop-logo-ring-spin 10s linear infinite;
}

@keyframes desktop-logo-ring-spin {
    to {
        transform: rotate(360deg);
    }
}

.desktop-logo-text-ultra {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.desktop-logo-title-ultra {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    letter-spacing: -0.03em;
    text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.desktop-logo-subtitle-ultra {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.65);
    font-weight: 700;
}

.emergency-button-desktop-ultra {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 2rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, rgb(239, 68, 68), rgb(220, 38, 38));
    border: 3px solid rgba(0, 0, 0, 0.2);
    box-shadow: 0 15px 40px rgba(239, 68, 68, 0.5);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.emergency-button-desktop-ultra:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 50px rgba(239, 68, 68, 0.6);
}

.emergency-bg-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.emergency-button-desktop-ultra:hover .emergency-bg-gradient {
    opacity: 1;
}

.emergency-icon-ultra {
    font-size: 1.75rem;
    position: relative;
    z-index: 1;
    animation: pulse 2.5s ease-in-out infinite;
}

.emergency-text-ultra {
    font-size: 1rem;
    font-weight: 800;
    color: white;
    position: relative;
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.emergency-pulse-ultra {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.4), transparent);
    animation: emergency-pulse-desktop 2.5s ease-in-out infinite;
}

@keyframes emergency-pulse-desktop {
    0%, 100% {
        transform: scale(0.7);
        opacity: 0;
    }
    50% {
        transform: scale(1.3);
        opacity: 1;
    }
}

/* Ultra Enhanced Desktop Header */
.desktop-header-ultra {
    margin-bottom: 2.5rem;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: 0.1s;
}

.desktop-header-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.header-content-ultra {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 2.5rem;
}

.greeting-text-ultra {
    font-size: 3.5rem;
    font-weight: 900;
    color: white;
    line-height: 1.1;
    letter-spacing: -0.03em;
    text-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.greeting-name-ultra {
    background: linear-gradient(135deg, #60a5fa, #a78bfa, #06b6d4);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: greeting-gradient 6s ease-in-out infinite;
}

@keyframes greeting-gradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.greeting-subtitle-ultra {
    font-size: 1.125rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 0.625rem;
    font-weight: 600;
}

.clock-display-ultra {
    position: relative;
    text-align: right;
}

.clock-decoration-ultra {
    position: absolute;
    inset: -1.5rem;
    border-radius: 2rem;
    filter: blur(50px);
    opacity: 0.4;
    z-index: -1;
    animation: clock-decoration-pulse 4s ease-in-out infinite;
}

@keyframes clock-decoration-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.4;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.6;
    }
}

.clock-time-ultra {
    display: flex;
    align-items: baseline;
    gap: 1rem;
    justify-content: flex-end;
}

.clock-digits-ultra {
    font-size: 4.5rem;
    font-weight: 300;
    color: white;
    line-height: 1;
    letter-spacing: -0.03em;
    text-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.clock-period-ultra {
    font-size: 1.75rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.6);
}

/* Desktop Grid */
.desktop-grid {
    display: grid;
    grid-template-columns: 1fr 24rem;
    gap: 2.5rem;
}

.desktop-main {
    min-width: 0;
}

/* Ultra Enhanced Features Grid */
.features-grid-ultra {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.feature-card-ultra {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2rem;
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(30px) saturate(180%);
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.feature-card-ultra.show {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.feature-card-ultra:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: rgba(255, 255, 255, 0.25);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
}

.feature-large {
    grid-column: span 2;
    min-height: 12rem;
}

.feature-medium {
    min-height: 10rem;
}

.feature-glass-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.feature-card-ultra:hover .feature-glass-overlay {
    opacity: 1;
}

.feature-mesh-bg {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.feature-card-ultra:hover .feature-mesh-bg {
    opacity: 1;
}

.feature-history-ultra {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(20px);
    font-size: 0.8125rem;
    font-weight: 800;
    color: white;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.feature-history-ultra:hover {
    background: rgba(0, 0, 0, 0.6);
    transform: scale(1.08);
}

.history-glow {
    position: absolute;
    inset: -4px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
    filter: blur(12px);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
}

.feature-history-ultra:hover .history-glow {
    opacity: 1;
}

.feature-history-ultra svg {
    width: 1rem;
    height: 1rem;
}

.feature-content-ultra {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    height: 100%;
}

.feature-header-ultra {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.feature-icon-wrap-ultra {
    position: relative;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

.feature-icon-glow-ultra {
    position: absolute;
    inset: -6px;
    border-radius: 1.25rem;
    background: rgba(255, 255, 255, 0.3);
    filter: blur(16px);
    opacity: 0.6;
    z-index: -1;
}

.feature-icon-ultra {
    font-size: 2rem;
    position: relative;
    z-index: 1;
}

.feature-text-ultra {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.feature-subtitle-ultra {
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.8125rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.feature-title-ultra {
    font-weight: 900;
    color: white;
    font-size: 1.75rem;
    line-height: 1.2;
    letter-spacing: -0.03em;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.feature-description-ultra {
    margin-top: auto;
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
    line-height: 1.6;
}

.feature-shine-ultra {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    opacity: 0;
    animation: shine-ultra 4s ease-in-out infinite;
}

@keyframes shine-ultra {
    0%, 100% {
        transform: translateX(-100%) translateY(-100%);
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
    gap: 2rem;
}

.sidebar-card-ultra {
    border-radius: 2rem;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.sidebar-card-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.sidebar-glass-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), transparent);
}

.card-header-ultra {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.card-title-ultra {
    font-size: 1.5rem;
    font-weight: 900;
    color: white;
    letter-spacing: -0.02em;
}

.card-icon-ultra {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.card-icon-glow {
    position: absolute;
    inset: -4px;
    border-radius: 1rem;
    background: inherit;
    filter: blur(16px);
    opacity: 0.6;
    z-index: -1;
}

.stats-list-ultra {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.stat-item-ultra {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem;
    border-radius: 1.25rem;
    background: rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.stat-item-ultra:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
}

.stat-item-icon-ultra {
    position: relative;
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.stat-item-icon-glow {
    position: absolute;
    inset: -3px;
    border-radius: 1rem;
    background: inherit;
    filter: blur(12px);
    opacity: 0.6;
    z-index: -1;
}

.stat-item-info-ultra {
    flex: 1;
    min-width: 0;
}

.stat-item-value-ultra {
    font-size: 1.75rem;
    font-weight: 900;
    color: white;
    line-height: 1;
    margin-bottom: 0.375rem;
    transition: all 0.3s ease;
    letter-spacing: -0.02em;
}

.stat-item-ultra.loaded .stat-item-value-ultra {
    animation: count-up-ultra 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.stat-item-label-ultra {
    font-size: 0.8125rem;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 700;
}

.stat-item-trend-ultra {
    font-size: 1.5rem;
    color: #10b981;
    flex-shrink: 0;
}

/* Ultra Enhanced Desktop Map */
.sidebar-map-ultra {
    position: relative;
    border-radius: 2rem;
    padding: 1rem;
    min-height: 22rem;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(28, 28, 30, 0.7);
    backdrop-filter: blur(30px) saturate(180%);
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.sidebar-map-ultra.show {
    opacity: 1;
    transform: translateY(0);
}

.sidebar-map-glass {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), transparent);
    border-radius: 2rem;
}

.map-badge-desk-ultra {
    position: absolute;
    top: 2rem;
    left: 2rem;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.625rem 1.125rem;
    border-radius: 9999px;
    font-size: 0.8125rem;
    font-weight: 800;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(30px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.map-expand-desk-ultra {
    position: absolute;
    top: 2rem;
    right: 2rem;
    z-index: 10;
    width: 3rem;
    height: 3rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(30px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: white;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.map-expand-desk-ultra:hover {
    transform: scale(1.08);
}

.map-expand-desk-ultra svg {
    width: 1.125rem;
    height: 1.125rem;
}

.map-wrapper-ultra {
    width: 100%;
    height: 100%;
    min-height: 20rem;
    border-radius: 1.5rem;
    overflow: hidden;
}

/* Transitions */
.toast-enter-active {
    animation: toast-in-ultra 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.toast-leave-active {
    transition: all 0.4s ease;
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(120%) translateY(-20px);
}

@keyframes toast-in-ultra {
    from {
        opacity: 0;
        transform: translateX(120%) translateY(-20px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateX(0) translateY(0) scale(1);
    }
}

/* Responsive */
@media (min-width: 640px) and (max-width: 1023px) {
    .mobile-layout {
        max-width: 36rem;
        padding: 0 1.5rem 8rem;
    }
    
    .apps-grid-ultra {
        gap: 1.25rem;
    }
}

@media (min-width: 1024px) and (max-width: 1439px) {
    .desktop-grid {
        grid-template-columns: 1fr 22rem;
        gap: 2rem;
    }
    
    .greeting-text-ultra {
        font-size: 3rem;
    }
    
    .clock-digits-ultra {
        font-size: 4rem;
    }
}

@media (min-width: 1440px) {
    .desktop-layout {
        max-width: 90rem;
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
    .feature-card-ultra:hover,
    .stat-item-ultra:hover {
        transform: none;
    }
    
    .feature-card-ultra:active,
    .stat-item-ultra:active {
        transform: scale(0.97);
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .stat-card-ultra,
    .app-card-ultra,
    .feature-card-ultra,
    .sidebar-card-ultra {
        border-width: 2px;
        border-color: rgba(255, 255, 255, 0.4);
    }
}
</style>